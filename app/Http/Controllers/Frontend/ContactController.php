<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('frontend.pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $model = new Contact();
        $table = $model->getTable();

        if (Schema::hasTable($table)) {
            $payload = array_filter($data, fn ($value, $key) => Schema::hasColumn($table, $key), ARRAY_FILTER_USE_BOTH);

            if ($payload !== []) {
                if (Schema::hasColumn($table, 'created_at')) {
                    $payload['created_at'] = now();
                }

                if (Schema::hasColumn($table, 'updated_at')) {
                    $payload['updated_at'] = now();
                }

                DB::table($table)->insert($payload);
            }
        }

        AlertService::created('Your message has been sent.');

        return back();
    }
}
