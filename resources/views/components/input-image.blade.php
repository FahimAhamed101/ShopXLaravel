@props(['name', 'image'])

@php
    $previewImage = filled($image) && ! \Illuminate\Support\Str::startsWith($image, ['http://', 'https://', 'data:'])
        ? asset($image)
        : $image;
@endphp

<div style="background-image: url({{ $previewImage }});background-size: cover;" {{ $attributes->merge(['id'=>'image-preview', 'class'=>'ms-2 mb-3']) }}>
    <label for="image-upload" id="image-label">Choose File</label>
    <input type="file" name="{{ $name }}" id="image-upload" />
</div>
