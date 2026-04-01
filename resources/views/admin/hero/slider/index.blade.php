@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Sliders</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">Create Slider</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Banner</th>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Status</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sliders as $slider)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img style="width: 100px" src="{{ asset($slider->image) }}" alt=""></td>
                                    <td>{{ $slider->title }}</td>
                                    <td>{{ $slider->sub_title }}</td>
                                    @if ($slider->is_active)
                                        <td class="text-secondary"><span class="badge bg-success-lt">Active</span></td>
                                    @else
                                        <td class="text-secondary"><span class="badge bg-danger-lt">Inactive</span></td>
                                    @endif

                                    <td>
                                        <a href="{{ route('admin.sliders.edit', $slider) }}"><i class="ti ti-edit"></i></a>
                                        <a class="text-danger delete-item"
                                            href="{{ route('admin.sliders.destroy', $slider) }}"><i
                                                class="ti ti-trash"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No Sliders</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $sliders->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
