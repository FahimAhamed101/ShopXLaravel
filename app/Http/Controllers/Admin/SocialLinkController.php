<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SocialLinkController extends Controller implements HasMiddleware
{
    use FileUploadTrait;

    public static function middleware(): array
    {
        return [new Middleware('permission:Section Management')];
    }

    public function index(): View
    {
        return view('admin.social-link.index', ['socialLinks' => SocialLink::latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.social-link.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['icon'] = $this->uploadFile($request->file('icon'));
        SocialLink::create($data);
        AlertService::created('Social link created successfully.');

        return to_route('admin.social-links.index');
    }

    public function edit(SocialLink $socialLink): View
    {
        return view('admin.social-link.edit', ['social_link' => $socialLink]);
    }

    public function update(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $data = $this->validated($request);
        if ($request->hasFile('icon')) {
            $data['icon'] = $this->uploadFile($request->file('icon'), $socialLink->icon);
        }
        $socialLink->update($data);
        AlertService::updated('Social link updated successfully.');

        return to_route('admin.social-links.index');
    }

    public function destroy(SocialLink $socialLink): JsonResponse
    {
        $this->deleteFile($socialLink->icon);
        $socialLink->delete();

        return response()->json(['status' => 'success', 'message' => 'Social link deleted successfully.']);
    }

    private function validated(Request $request, bool $creating = false): array
    {
        $data = $request->validate([
            'icon' => [$creating ? 'required' : 'nullable', 'image', 'max:2048'],
            'url' => ['required', 'url:http,https', 'max:2048'],
        ]);
        $data['status'] = $request->boolean('status');

        return $data;
    }
}
