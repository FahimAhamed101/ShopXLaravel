<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewsLetterController extends Controller
{
    public function subscribeNewsletter(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        foreach (['subscribers', 'newsletters'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'email')) {
                continue;
            }

            DB::table($table)->updateOrInsert(
                ['email' => $data['email']],
                array_filter([
                    'email' => $data['email'],
                    'created_at' => Schema::hasColumn($table, 'created_at') ? now() : null,
                    'updated_at' => Schema::hasColumn($table, 'updated_at') ? now() : null,
                ], fn ($value) => $value !== null)
            );

            break;
        }

        AlertService::created('Subscribed successfully.');

        return back();
    }
}
