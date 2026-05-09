<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class WishlistController extends Controller
{
    public function index(): View
    {
        $wishlistItems = Schema::hasTable('wishlists')
            ? Wishlist::query()->with('product')->where('user_id', auth('web')->id())->latest('id')->get()
            : collect();

        return view('frontend.pages.wishlist', compact('wishlistItems'));
    }

    public function create(): RedirectResponse
    {
        abort(404);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless(Schema::hasTable('wishlists'), 404);

        $data = $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        Wishlist::query()->firstOrCreate([
            'user_id' => auth('web')->id(),
            'product_id' => $data['product_id'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to wishlist.',
        ]);
    }

    public function show(string $id): RedirectResponse
    {
        return redirect()->route('wishlist.index');
    }

    public function edit(string $id): RedirectResponse
    {
        return redirect()->route('wishlist.index');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        return redirect()->route('wishlist.index');
    }

    public function destroy(int $wishlist): RedirectResponse|JsonResponse
    {
        if (Schema::hasTable('wishlists')) {
            Wishlist::query()
                ->where('id', $wishlist)
                ->where('user_id', auth('web')->id())
                ->delete();
        }

        AlertService::updated('Wishlist updated successfully.');

        if (request()->expectsJson()) {
            return response()->json(['status' => 'success']);
        }

        return back();
    }
}
