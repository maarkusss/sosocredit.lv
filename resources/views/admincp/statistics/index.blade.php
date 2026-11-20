@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Statistics' }}</h1>
            <div>
                <form class="flex flex-col items-start sm:flex-row sm:items-center">
                    <label>
                        <input
                                class="px-3 py-2 mt-2 text-gray-700 bg-white border-gray-300 rounded sm:mt-0 focus:border-primary-dark focus:ring-primary-dark"
                                name="datePicker"
                                type="text"
                                placeholder="Select Date.."
                                id="datepickr-element"
                                value="{{ request()->datePicker }}">
                    </label>
                    <button
                            class="flex flex-row items-center px-3 py-2 mt-2 text-sm font-medium text-white bg-gray-700 rounded sm:mt-0 sm:ml-2 hover:bg-gray-800"
                            type="submit">
                        Apply
                    </button>
                </form>
            </div>
        </div>
        <div class="content-wrapper__form-wrapper">
            <div id="chart">
            </div>
        </div>
        <div class="content-wrapper__form-wrapper mt-3">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                    <tr class="border-b">
                        <th class="table__column"></th>
                        <th class="table__column">Confirmed</th>
                        <th class="table__column">Waiting</th>
                        <th class="table__column">Clicks</th>
                        <th class="table__column">EPC</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lenders as $lender)
                        <tr class="table__row">
                            <th class="table__column">
                                {{ $lender['name'] }}
                            </th>
                            <td class="table__cell">
                                <span>{{ $lender['confirmed'] }}</span>
                            </td>
                            <td class="table__cell">
                                <span>{{ $lender['waiting'] }}</span>
                            </td>
                            <td class="table__cell">
                                <span>{{ $lender['clicks'] }}</span>
                            </td>
                            <td class="table__cell">
                                <span class="font-medium"
                                      style="{{ $lender['style'] }}">{{ sprintf('%.2f', $lender['epc']) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>No lenders to show!</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        let options = {
            chart: {
                type: "area",
                toolbar: {
                    tools: {
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        reset: false,
                        pan: false
                    }
                },
                height: '400px'
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: "Commissions",
                data: [{!! implode(', ', $chart_data['commissions']) !!}]
            },
                {
                    name: "Clicks",
                    data: [{!! implode(', ', $chart_data['clicks']) !!}]
                },
                {
                    name: "Visitors",
                    data: [{!! implode(', ', $chart_data['visitors']) !!}]
                },

            ],
            xaxis: {
                categories: ["{!! implode('", "', array_keys($chart_data['clicks'])) !!}"]
            }
        };
    </script>
@endsection
