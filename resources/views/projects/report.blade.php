<x-guest-layout>
    
    <div class="max-w-4xl p-8 mx-auto">

        <div class="mb-6 -ml-2">
            <x-jet-application-mark class="block h-12 w-auto" />
        </div>

        <header class="">
            <p class="font-semibold text-2xl text-gray-600 leading-tight">
                {{ $project->name }}
            </p>
            <h1 class="font-bold text-4xl text-rose-800 leading-tight">
                {{ $report_name }}
            </h1>
        </header>

        <div class="grid grid-cols-6 my-8 gap-2">

            <div class="bg-gray-100 p-4 col-span-3">
                <x-time :time="$report_start_at" /> &mdash; <x-time :time="$report_end_at" />
            </div>
            
            <div class="bg-gray-100 p-4 col-span-2">
                {{ $working_days }} WDs
            </div>

        </div>

        

        <div class="py-8 ">

            <table class="table-fixed">
                <thead class="text-gray-600 sticky top-0 bg-white bg-opacity-80 backdrop-blur backdrop-filter">
                    <tr>
                        <td class="pr-2 py-2 w-2/12">{{ __('Day') }}</td>
                        <td class="px-2 py-2 w-2/12">{{ __('Date') }}</td>
                        <td class="px-2 py-2 w-1/12 text-right">{{ __('Hours') }}</td>
                        <td class="pl-2 py-2 w-7/12">{{ __('Activities') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dailySummary as $daily)
                        <tr class="border-b border-gray-300">
                            <td class="pr-2 py-2 align-top w-2/12">{{ $daily['day']->localeDayOfWeek }}</td>
                            <td class="px-2 py-2 align-top w-2/12">{{ $daily['day']->toDateString() }}</td>
                            <td class="px-2 py-2 align-top w-1/12 tabular-nums text-right">{{ number_format(round( $daily['duration'] / \Carbon\Carbon::MINUTES_PER_HOUR, 1), 1) }}</td>
                            <td class="pl-2 py-2 align-top w-7/12">{{ $daily['activities'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-gray-600">
                        <td class="pr-2 py-2 w-2/12">{{ __('Day') }}</td>
                        <td class="px-2 py-2 w-2/12">{{ __('Date') }}</td>
                        <td class="px-2 py-2 w-1/12 text-right">{{ __('Hours') }}</td>
                        <td class="pl-2 py-2 w-7/12 whitespace-normal">{{ __('Activities') }}</td>
                    </tr>
                </tfoot>
            </table>

            


        </div>
    </div>
</x-guest-layout>
