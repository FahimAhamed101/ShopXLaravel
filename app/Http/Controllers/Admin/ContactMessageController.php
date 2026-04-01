<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ContactMessageController extends Controller implements HasMiddleware
{
    static function Middleware(): array
    {
        return [
            new Middleware('permission:Contact Management')
        ];
    }

    function index(): View
    {
        $messages = Contact::paginate(30);
        return view('admin.contact.index', compact('messages'));
    }

    function destroy(Contact $contact_message): JsonResponse
    {
        $contact_message->delete();
        return response()->json(['status' => 'success', 'message' => 'Message deleted successfully.']);
    }
}
