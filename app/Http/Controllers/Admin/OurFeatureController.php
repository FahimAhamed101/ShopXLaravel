<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurFeature;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class OurFeatureController extends Controller implements HasMiddleware
{
    use FileUploadTrait;

    public static function middleware(): array
    {
        return [new Middleware('permission:Section Management')];
    }

    public function index(): View
    {
        return view('admin.our-feature.index', ['features' => OurFeature::latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.our-feature.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['icon'] = $this->uploadFile($request->file('icon'));
        OurFeature::create($data);
        AlertService::created('Feature created successfully.');

        return to_route('admin.our-features.index');
    }

    public function edit(OurFeature $ourFeature): View
    {
        return view('admin.our-feature.edit', ['our_feature' => $ourFeature]);
    }

    public function update(Request $request, OurFeature $ourFeature): RedirectResponse
    {
        $data = $this->validated($request);
        if ($request->hasFile('icon')) {
            $data['icon'] = $this->uploadFile($request->file('icon'), $ourFeature->icon);
        }
        $ourFeature->update($data);
        AlertService::updated('Feature updated successfully.');

        return to_route('admin.our-features.index');
    }

    public function destroy(OurFeature $ourFeature): JsonResponse
    {
        $this->deleteFile($ourFeature->icon);
        $ourFeature->delete();

        return response()->json(['status' => 'success', 'message' => 'Feature deleted successfully.']);
    }

    private function validated(Request $request, bool $creating = false): array
    {
        $data = $request->validate([
            'icon' => [$creating ? 'required' : 'nullable', 'image', 'max:2048'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
        ]);
        $data['status'] = $request->boolean('status');

        return $data;
    }
}
