@props(['user', 'width' => 'w-8', 'height' => 'h-8'])

<x-avatar {{ $attributes->merge(['class' => $width . ' ' . $height]) }} src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" title="{{ $user->name }}" aria-label="{{ $user->name }}" />
