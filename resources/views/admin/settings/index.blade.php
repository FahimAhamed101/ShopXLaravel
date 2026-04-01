@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="row g-0">
                <div class="col-12 col-md-3 border-end">
                    <div class="card-body">
                        <h4 class="subheader">Business settings</h4>
                        <div class="list-group list-group-transparent">
                            <a href="{{ route('admin.settings.index') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center active">General Settings</a>

                            <a href="{{ route('admin.commission-settings.index') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center">Commission Settings</a>
                            <a href="{{ route('admin.logo-settings.index') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center">Logo Settings</a>
                            <a href="{{ route('admin.site-settings.index') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center">Site Settings</a>
                        </div>

                    </div>
                </div>
                <div class="col-12 col-md-9 d-flex flex-column">
                    @yield('settings_contents')
                </div>
            </div>
        </div>
    </div>
@endsection
