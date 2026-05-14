@php
    $previewImage = filled($image) && ! \Illuminate\Support\Str::startsWith($image, ['http://', 'https://', 'data:'])
        ? asset($image)
        : $image;
@endphp

<div style="background-image: url({{ $previewImage }});background-size: cover;" {{ $attributes->merge(['id' => $imagePreviewId, 'class' => 'ms-2 mb-3']) }}>
    <label for="{{ $imageUploadId }}" id="{{ $imageLabelId }}">Choose File</label>
    <input type="file" name="{{ $name }}" id="{{ $imageUploadId }}" />
</div>
