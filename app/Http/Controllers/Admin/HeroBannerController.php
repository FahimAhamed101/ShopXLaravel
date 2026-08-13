<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class HeroBannerController extends Controller implements HasMiddleware
{
    use FileUploadTrait;

    public static function middleware(): array
    {
        return [new Middleware('permission:Section Management')];
    }

    public function index(): View
    {
        return view('admin.hero.banner.index', ['heroBanner' => HeroBanner::first()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'banner_one' => ['nullable', 'image', 'max:3048'],
            'title_one' => ['required', 'string', 'max:255'],
            'btn_url_one' => ['nullable', 'string', 'max:2048'],
            'banner_two' => ['nullable', 'image', 'max:3048'],
            'title_two' => ['required', 'string', 'max:255'],
            'btn_url_two' => ['nullable', 'string', 'max:2048'],
        ]);
        $heroBanner = HeroBanner::firstOrNew(['id' => 1]);

        foreach (['banner_one', 'banner_two'] as $field) {
            unset($data[$field]);
            if ($request->hasFile($field)) {
                $data[$field] = $this->uploadFile($request->file($field), $heroBanner->{$field});
            }
        }

        $heroBanner->fill($data)->save();
        AlertService::updated('Hero banners updated successfully.');

        return back();
    }
}
