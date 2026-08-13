<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = Schema::hasTable('addresses')
            ? Address::query()->where('user_id', auth('web')->id())->latest('id')->get()
            : collect();

        return view('frontend.dashboard.address.index', compact('addresses'));
    }

    public function create(): View
    {
        return view('frontend.dashboard.address.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Schema::hasTable('addresses'), 404);

        $data = $this->validatedData($request);
        $address = new Address;
        $this->fillAddress($address, $data);
        $address->save();

        AlertService::created('Address created successfully.');

        return $request->input('return_to') === 'checkout'
            ? redirect()->route('checkout.index')->with('address_created', $address->id)
            : redirect()->route('address.index');
    }

    public function show(string $id): RedirectResponse
    {
        return redirect()->route('address.edit', $id);
    }

    public function edit(int $address): View
    {
        $address = $this->ownedAddress($address);

        return view('frontend.dashboard.address.edit', compact('address'));
    }

    public function update(Request $request, int $address): RedirectResponse
    {
        $address = $this->ownedAddress($address);
        $data = $this->validatedData($request);

        $this->fillAddress($address, $data);
        $address->save();

        AlertService::updated('Address updated successfully.');

        return redirect()->route('address.index');
    }

    public function destroy(int $address): RedirectResponse
    {
        $address = $this->ownedAddress($address);
        $wasDefault = (bool) $address->is_default;
        $address->delete();

        if ($wasDefault && Schema::hasColumn('addresses', 'is_default')) {
            Address::query()
                ->where('user_id', auth('web')->id())
                ->latest('id')
                ->first()
                ?->update(['is_default' => 1]);
        }

        AlertService::updated('Address deleted successfully.');

        return redirect()->route('address.index');
    }

    public function setDefault(int $address): RedirectResponse
    {
        $address = $this->ownedAddress($address);

        Address::query()
            ->where('user_id', auth('web')->id())
            ->update(['is_default' => 0]);
        $address->update(['is_default' => 1]);

        AlertService::updated('Default address updated successfully.');

        return redirect()->route('address.index');
    }

    protected function ownedAddress(int $id): Address
    {
        return Address::query()
            ->where('id', $id)
            ->where('user_id', auth('web')->id())
            ->firstOrFail();
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:255', Rule::in(config('countries', []))],
            'address' => ['required', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    protected function fillAddress(Address $address, array $data): void
    {
        if (Schema::hasColumn('addresses', 'user_id')) {
            $address->user_id = auth('web')->id();
        }

        $hasOtherAddress = Address::query()
            ->where('user_id', auth('web')->id())
            ->when($address->exists, fn ($query) => $query->where('id', '!=', $address->id))
            ->exists();
        $shouldBeDefault = (bool) ($data['is_default'] ?? false) || ! $hasOtherAddress;

        if ($shouldBeDefault && Schema::hasColumn('addresses', 'is_default')) {
            Address::query()
                ->where('user_id', auth('web')->id())
                ->when($address->exists, fn ($query) => $query->where('id', '!=', $address->id))
                ->update(['is_default' => 0]);
        }

        $address->fill(array_filter([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? '',
            'name' => trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? '')),
            'phone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? '',
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? '',
            'zip' => $data['zip'] ?? '',
            'zip_code' => $data['zip'] ?? '',
            'postal_code' => $data['zip'] ?? '',
            'country' => $data['country'] ?? '',
            'address' => $data['address'],
            'is_default' => (int) $shouldBeDefault,
        ], fn ($value, $key) => Schema::hasColumn('addresses', $key), ARRAY_FILTER_USE_BOTH));
    }
}
