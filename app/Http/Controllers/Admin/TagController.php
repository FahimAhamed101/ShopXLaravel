<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

class TagController extends Controller implements HasMiddleware
{
    public static function Middleware(): array
    {
        return [
            new Middleware('permission:Tags Management'),
        ];
    }

    public function index(): View
    {
        $tags = Tag::orderByDesc('id')->paginate(20);

        return view('admin.tag.index', compact('tags'));
    }

    public function create(): View
    {
        return view('admin.tag.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable'],
        ]);

        $tag = new Tag();
        $tag->name = $data['name'];
        $tag->slug = Str::slug($data['name']);
        $tag->is_active = $request->has('status') ? 1 : 0;
        $tag->save();

        AlertService::created('Tag created successfully.');

        return to_route('admin.tags.index');
    }

    public function edit(Tag $tag): View
    {
        return view('admin.tag.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable'],
        ]);

        $tag->name = $data['name'];
        $tag->slug = Str::slug($data['name']);
        $tag->is_active = $request->has('status') ? 1 : 0;
        $tag->save();

        AlertService::updated('Tag updated successfully.');

        return to_route('admin.tags.index');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        AlertService::deleted('Tag deleted successfully.');

        return response()->json(['status' => 'success', 'message' => 'Tag deleted successfully']);
    }
}
