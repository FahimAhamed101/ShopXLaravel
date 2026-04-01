@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card mb-4">
            <div class="card-body">
                <div class="accordion accordion-tabs" id="accordion-tabs">
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-1-tabs" aria-expanded="true">
                                Send Newsletter
                                <div class="accordion-button-toggle">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/chevron-down -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                        <path d="M6 9l6 6l6 -6"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div id="collapse-1-tabs" class="accordion-collapse collapse show" data-bs-parent="#accordion-tabs"
                            style="">
                            <div class="accordion-body">
                               <form action="{{ route('admin.newsletter.send') }}" method="POST" >
                                @csrf
                                <div class="from-group">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject">
                                    <x-input-error for="subject" class="mt-2" :messages="$errors->get('subject')" />
                                </div>
                                <div class="from-group mt-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea name="message" id="editor" class="form-control" placeholder="Message"></textarea>
                                    <x-input-error for="message" class="mt-2" :messages="$errors->get('message')" />
                                </div>
                                <div class="from-group mt-3">
                                    <button class="btn btn-primary" type="submit">Send Newsletter</button>
                                </div>
                               </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Subscribers</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="order_table table m-0 mt-20">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscribers as $subscriber)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $subscriber->email }}</td>
                                        <td>
                                            @if ($subscriber->is_verified)
                                                <span class="badge bg-success-lt">Verified</span>
                                            @else
                                                <span class="badge bg-danger-lt">Unverified</span>
                                            @endif
                                        </td>
                                        <td>{{ date('Y-m-d h:i A', strtotime($subscriber->created_at)) ?? 'N/A' }}</td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No data found</td>
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $subscribers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
