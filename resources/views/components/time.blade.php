@props(['time', 'format' => 'M j, Y', 'default' => null])

<time {{ $attributes->merge(['datetime' => $time]) }}>
    {{ optional($time)->format($format) ?? $default }}
</time>