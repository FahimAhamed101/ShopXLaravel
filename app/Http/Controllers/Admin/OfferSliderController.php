<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfferSlider;
use App\Services\AlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class OfferSliderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:Section Management')];
    }

    public function index(): View
    {
        return view('admin.offer-slider.index', ['offers' => OfferSlider::latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.offer-slider.create');
    }

    public function store(Request $request): RedirectResponse
    {
        OfferSlider::create($this->validated($request));
        AlertService::created('Offer created successfully.');

        return to_route('admin.offer-sliders.index');
    }

    public function edit(OfferSlider $offerSlider): View
    {
        return view('admin.offer-slider.edit', ['offer_slider' => $offerSlider]);
    }

    public function update(Request $request, OfferSlider $offerSlider): RedirectResponse
    {
        $offerSlider->update($this->validated($request));
        AlertService::updated('Offer updated successfully.');

        return to_route('admin.offer-sliders.index');
    }

    public function destroy(OfferSlider $offerSlider): JsonResponse
    {
        $offerSlider->delete();

        return response()->json(['status' => 'success', 'message' => 'Offer deleted successfully.']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
