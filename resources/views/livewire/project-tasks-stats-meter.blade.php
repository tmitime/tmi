<div>

    <div class="mb-2"><span class="font-bold">{{ $working_days }}</span> working days accounted</div>

    {{-- Trick to prevent pruning of class from Tailwind --}}
    {{-- bg-yellow-500 --}}
    {{-- bg-blue-600 --}}

    <div class="relative h-3 rounded-md bg-gray-50 flex overflow-hidden">
        @foreach ($stats as $type => $percentage)
            <div class="mark w-1/6 h-3 {{ $colors[$type] }}" style="width:{{ $percentage }}%" title="{{ $percentage }}%"></div>
        @endforeach
    </div>

    <div class="space-x-4 pt-2">
        @foreach ($stats as $type => $percentage)
            <span class="inline-flex items-center gap-1"><span class="rounded-full w-3 h-3 {{ $colors[$type] }}"></span>{{ $type }}</span>
        @endforeach
    </div>
</div>
