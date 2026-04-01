@extends('frontend.layouts.app')

@section('contents')
@php
    $contactSection = \App\Models\ContactSectionSetting::first();
@endphp
    <x-frontend.breadcrumb :items="[['label' => 'Home', 'url' => '/'], ['label' => 'Contact']]" />
    <div class="page-content pt-70">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @if($contactSection?->map_url)
                    <section class="comtact_map">
                        <iframe
                            src="{{ $contactSection?->map_url }}"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </section>
                    @endif
                    <div class="row mt-50 mb-55">
                        <div class="col-12">
                            <section class="mb-50">
                                <div class="row mb-60">
                                    @if($contactSection?->title_one)
                                    <div class="col-md-4 mb-4 mb-md-0">
                                        <div class="contact_box">
                                            <h4 class="mb-15 text-brand">{{ $contactSection?->title_one }}</h4>
                                            <p>{{ $contactSection?->address_one ?? 'N/A' }}</p>
                                            <p><b>Phone:</b> {{ $contactSection?->phone_one ?? 'N/A' }}</p>
                                            <p><b>Email:</b> {{ $contactSection?->email_one ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($contactSection?->title_two)
                                    <div class="col-md-4 mb-4 mb-md-0">
                                        <div class="contact_box">
                                            <h4 class="mb-15 text-brand">{{ $contactSection?->title_two}}</h4>
                                            <p>{{ $contactSection?->address_two?? 'N/A' }}</p>
                                            <p><b>Phone:</b> {{ $contactSection?->phone_two ?? 'N/A' }}</p>
                                            <p><b>Email:</b> {{ $contactSection?->email_two ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($contactSection?->title_three)
                                    <div class="col-md-4 mb-4 mb-md-0">
                                        <div class="contact_box">
                                            <h4 class="mb-15 text-brand">{{ $contactSection?->title_three}}</h4>
                                            <p>{{ $contactSection?->address_three ?? 'N/A' }}</p>
                                            <p><b>Phone:</b> {{ $contactSection?->phone_three ?? 'N/A' }}</p>
                                            <p><b>Email:</b> {{ $contactSection?->email_three ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </section>
                            <section class="contact_address">
                                <div class="row justify-content-center">
                                    <div class="col-xl-12">
                                        <div class="contact-from-area padding-20-row-col">
                                            <h5 class="text-brand mb-10">Contact form</h5>
                                            <h2 class="mb-10">Drop Us a Line</h2>
                                            <p class="text-muted mb-30 font-sm">Your email address will not be
                                                published.
                                                Required fields are marked *</p>
                                            <form class="contact-form-style mt-30" id="contact-form" action="{{ route('contact.store') }}"
                                                method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="input-style mb-20">
                                                            <input name="name" placeholder="First Name" type="text" />
                                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="input-style mb-20">
                                                            <input name="email" placeholder="Your Email" type="email" />
                                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="input-style mb-20">
                                                            <input name="subject" placeholder="Subject" type="text" />
                                                            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="textarea-style mb-30">
                                                            <textarea rows="6" name="message" placeholder="Message"></textarea>
                                                            <x-input-error :messages="$errors->get('message')" class="mt-2" />
                                                        </div>
                                                        <button class="btn" type="submit">Send message</button>
                                                    </div>
                                                </div>
                                            </form>
                                            <p class="form-messege"></p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
