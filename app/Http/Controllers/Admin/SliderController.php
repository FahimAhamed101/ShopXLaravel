<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SliderController extends Controller implements HasMiddleware
{
    use FileUploadTrait;

    public static function middleware(): array
    {
        return [new Middleware('permission:Section Management')];
    }

    public function index(): View
    {
        return view('admin.hero.slider.index', ['sliders' => Slider::latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.hero.slider.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['image'] = $this->uploadFile($request->file('image'));
        Slider::create($data);
        AlertService::created('Slider created successfully.');

        return to_route('admin.sliders.index');
    }

    public function edit(Slider $slider): View
    {
        return view('admin.hero.slider.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider): RedirectResponse
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile($request->file('image'), $slider->image);
        }
        $slider->update($data);
        AlertService::updated('Slider updated successfully.');

        return to_route('admin.sliders.index');
    }

    public function destroy(Slider $slider): JsonResponse
    {
        $this->deleteFile($slider->image);
        $slider->delete();

        return response()->json(['status' => 'success', 'message' => 'Slider deleted successfully.']);
    }

    private function validated(Request $request, bool $creating = false): array
    {
        $data = $request->validate([
            'image' => [$creating ? 'required' : 'nullable', 'image', 'max:3048'],
            'title' => ['required', 'string', 'max:255'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'btn_url' => ['nullable', 'string', 'max:2048'],
        ]);
        $data['is_active'] = $request->boolean('status');

        return $data;
    }
}
