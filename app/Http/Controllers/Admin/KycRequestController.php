<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Services\AlertService;
use App\Services\MailService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KycRequestController extends Controller implements HasMiddleware
{
    static function Middleware() : array
    {
        return [
            new Middleware('permission:KYC Management')
        ];
    }

    function index(): View
    {
        $kycRequests = Kyc::paginate(25);
        return view('admin.kyc.index', compact('kycRequests'));
    }

    function pending(): View
    {
        $kycRequests = Kyc::whereStatus('pending')->paginate(25);
        return view('admin.kyc.pending', compact('kycRequests'));
    }

    function rejected(): View
    {
        $kycRequests = Kyc::whereStatus('rejected')->paginate(25);
        return view('admin.kyc.rejected', compact('kycRequests'));
    }


    function show(Kyc $kyc_request): View
    {
        return view('admin.kyc.show', compact('kyc_request'));
    }


    function download(Kyc $kyc_request): StreamedResponse
    {
        return Storage::disk('local')->download($kyc_request->document_scan_copy);
    }

    function update(Kyc $kyc_request, Request $request): RedirectResponse
    {
        $kyc_request->update([
            'status' => $request->status
        ]);


        if ($kyc_request->status == 'approved') {
            MailService::send(
                to: $kyc_request->user->email,
                subject: 'KYC Application Has Been Approved',
                body: 'Congratulations! Your KYC Application Has Been Approved.'
            );
        } elseif ($kyc_request->status == 'rejected') {
            MailService::send(
                to: $kyc_request->user->email,
                subject: 'KYC Application Has Been Rejected',
                body: 'Sorry! Your KYC Application Has Been Rejected.'
            );
        }


        AlertService::updated();

        return redirect()->route('admin.kyc.index');
    }
}
