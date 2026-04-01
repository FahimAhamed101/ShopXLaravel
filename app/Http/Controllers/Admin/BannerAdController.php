<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerAd;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BannerAdController extends Controller implements HasMiddleware
{
    use FileUploadTrait;

    static function Middleware(): array
    {
        return [
            new Middleware('permission:Advertisement Management')
        ];
    }

    function index(): View
    {
        $banners = BannerAd::all()->groupBy('banner_id');
        return view('admin.banner-ad.index', compact('banners'));
    }

    function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'banner_id' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'title' => 'nullable|string',
            'url' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $this->uploadFile($request->file('image'));
        }

        BannerAd::updateOrCreate(
            ['banner_id' => $request->banner_id],
            $validatedData
        );

        AlertService::updated();

        return back();
    }
}
