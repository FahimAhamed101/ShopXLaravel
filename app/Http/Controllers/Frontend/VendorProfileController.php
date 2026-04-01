<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VendorProfileController extends Controller
{
    use FileUploadTrait;

    function index() : View {
        return view('vendor-dashboard.profile.index');
    }

    function update(Request $request) : RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . user()->id],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = user();

        if($request->hasFile('avatar')) {
            $avatar = $this->uploadFile($request->file('avatar'));
            $user->avatar = $avatar;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        AlertService::updated();

        return back();
    }

    function updatePassword(Request $request) : RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        $user = user();
        $user->password = bcrypt($request->password);
        $user->save();

        AlertService::updated();

        return back();
    }
}
