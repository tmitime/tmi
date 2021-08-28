<div class=" p-4">
    <h3 class="font-bold mb-3 flex justify-between items-center">
        <span>{{ __('This week') }}</span>
        <span class="text-gray-600 font-normal">
            {{ $start_date->toFormattedDateString()}} &mdash; {{ $end_date->toFormattedDateString()}} 
        </span>
    </h3>

    {{-- TODO: remove assumption of five days working week --}}
    <div class="relative grid grid-cols-5 grid-rows-9 grid-flow-row gap-1 items-stretch" style="height: 288px">

        <div class="absolute top-0 w-full h-full z-0 grid grid-rows-8 col-span-5 grid-flow-row" style="grid-row: span 8 / span 8">
            @foreach (range(0, 8) as $item)
                <div class="z-0 h-8 relative border-b border-gray-200 text-gray-300 col-span-5">{{ $item > 0 ? $item : '' }}</div>
            @endforeach
        </div>

        @foreach ($entries as $entry)
            
            <div 
                class="z-10 relative p-4 text-center overflow-hidden"
                style="grid-row-start: 2;grid-row-end: 10;grid-column-start:{{ $entry->step }}"
                 >
                 @php
                     $height = min(round($entry->time * 256 / 480), 256);
                 @endphp
                 <div style="height: {{ $height }}px"
                    class="rounded-md shadow bg-gradient-to-b from-blue-500 to-blue-600 border-blue-700 w-full -my-4">
                    
                    </div>
                 
                 <div class="absolute text-center py-2 px-6 top-0 {{ $height > 64 ? 'text-blue-50' : 'text-blue-800'}}">{{ $entry->time }} minutes<br/>{{ $entry->tasks }} tasks</div>
                </div>
        @endforeach

        <div class="row-start-1 text-sm uppercase text-gray-600 text-center">Mon</div>
        <div class="row-start-1 text-sm uppercase text-gray-600 text-center">Tue</div>
        <div class="row-start-1 text-sm uppercase text-gray-600 text-center">Wed</div>
        <div class="row-start-1 text-sm uppercase text-gray-600 text-center">Thu</div>
        <div class="row-start-1 text-sm uppercase text-gray-600 text-center">Fri</div>
    </div>
</div>