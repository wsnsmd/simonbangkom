<x-app-layout>
    <div>
        <div class="mb-6">
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
        </div>

        {{--Alert start--}}
        @if (session('message'))
        <x-alert :message="session('message')" :type="'success'" class="mt-3" />
        @endif
        @if (session('error'))
        <x-alert :message="session('error')" :type="'danger'" />
        @endif
        {{--Alert end--}}

        <div class="card p-6">
            <div class="grid grid-cols-4 gap-5">
                <div class="xl:col-span-8 col-span-12">
                    <div class="grid md:grid-cols-4 sm:grid-cols-2 grid-cols-1 gap-3">
                        <div class=" bg-info-500 rounded-md p-4 bg-opacity-[0.15] dark:bg-opacity-50 text-center">
                            <div class="text-info-500 mx-auto h-10 w-10 flex flex-col items-center justify-center rounded-full bg-white text-2xl mb-4">
                                <iconify-icon icon="heroicons:users-20-solid"></iconify-icon>
                            </div>
                            <span class="block text-sm text-slate-600 font-medium dark:text-white mb-1">
                                Total PNS
                            </span>
                            <span class="block mb- text-2xl text-slate-900 dark:text-white font-medium">
                                {{ number_format($total_pns, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class=" bg-warning-500 rounded-md p-4 bg-opacity-[0.15] dark:bg-opacity-50 text-center">
                            <div class="text-warning-500 mx-auto h-10 w-10 flex flex-col items-center justify-center rounded-full bg-white text-2xl mb-4">
                                <iconify-icon icon="heroicons-outline:chart-pie"></iconify-icon>
                            </div>
                            <span class="block text-sm text-slate-600 font-medium dark:text-white mb-1">
                                Rata-Rata JP
                            </span>
                            <span class="block mb- text-2xl text-slate-900 dark:text-white font-medium">
                                {{ number_format($average_jp, 2) }} JP
                            </span>
                        </div>

                        <div class=" bg-success-500 rounded-md p-4 bg-opacity-[0.15] dark:bg-opacity-50 text-center">
                            <div class="text-success-500 mx-auto h-10 w-10 flex flex-col items-center justify-center rounded-full bg-white text-2xl mb-4">
                                <iconify-icon icon="heroicons:clock"></iconify-icon>
                            </div>
                            <span class="block text-sm text-slate-600 font-medium dark:text-white mb-1">
                                Tanggal Update
                            </span>
                            <span class="block mb- text-2xl text-slate-900 dark:text-white font-medium">
                                {{ $tgl->format('d-m-Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--JPPD table start--}}
        <div class="card mt-5">
            <header class=" card-header noborder">
                <h3 class="font-medium text-lg text-black font-Inter dark:text-white text-center mb-5 lg:mb-0 lg:text-left">{{ __('Perangkat Daerah') }}</h3>
                @role(['super-admin', 'admin'])
                <div class="justify-center sm:justify-end flex  gap-3 items-center flex-wrap">
                    {{--Refresh Button start--}}
                    <a class="shift-Away btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2.5" href="{{ route('dashboard.refresh') }}" data-tippy-content="Refresh - Tarik Data" data-tippy-theme="dark">
                        <iconify-icon icon="mdi:refresh" class="text-xl "></iconify-icon>
                    </a>
                    <a class="shift-Away btn btn-sm inline-flex justify-center btn-success rounded-[25px] items-center !p-2.5" data-tippy-content="Export Excel" data-tippy-theme="dark" href="javascript:;" onclick="event.preventDefault(); document.getElementById('export').submit();">
                        <iconify-icon icon="mdi:file-excel" class="text-lg mr-10"></iconify-icon> Excel
                    </a>
                    <form id="export" action="{{ route('export.data') }}" method="post" style="display: none;">
                        @csrf
                        <input type="hidden" id="tahun" name="tahun" value="{{ session('apps_tahun') }}">
                    </form>
                </div>
                @endrole
            </header>
            <div class="card-body px-6 pb-6">
                <div class="overflow-x-auto -mx-6 dashcode-data-table">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden ">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700" id="data-table">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th w-32">
                                            #
                                        </th>
                                        <th scope="col" class="table-th">
                                            {{ __('NAMA PD') }}
                                        </th>
                                        <th scope="col" class="table-th">
                                            {{ __('JUMLAH PEGAWAI') }}
                                        </th>
                                        <th scope="col" class="table-th w-44">
                                            {{ __('TOTAL JP') }}
                                        </th>
                                        <th scope="col" class="table-th w-44">
                                            {{ __('RATA-RATA JP') }}
                                        </th>
                                        <th scope="col" class="table-th w-10">
                                            {{ __('AKSI') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                    @forelse($pd as $key => $item)
                                    <tr class="border border-slate-100 even:bg-slate-50 dark:border-slate-900 relative">
                                        <td class="table-td sticky left-0">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="table-td">
                                            {{ $item['lokasi'] }}
                                        </td>
                                        <td class="table-td">
                                            {{ $item['jumlah_pegawai'] }}
                                        </td>
                                        <td class="table-td">
                                            {{ $item['total_jp'] }}
                                        </td>
                                        <td class="table-td">
                                            @if($item['rata_rata_jp'] >= 20)
                                            <span class="badge bg-primary-500 text-white capitalize">{{ $item['rata_rata_jp'] }}</span>
                                            @else
                                            <span class="badge bg-danger-500 text-white capitalize">{{ $item['rata_rata_jp'] }}</span>
                                            @endif
                                        </td>
                                        <td class="table-td">
                                            <div class="action-btns space-x-2 flex">
                                                <a class="action-btn" href="{{ route('dashboard.detail', $item['id_skpd']) }}" data-tippy-content="Detail" data-tippy-theme="dark">
                                                    <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="border border-slate-100 dark:border-slate-900 relative">
                                        <td class="table-cell text-center" colspan="5">
                                            <img src="images/result-not-found.svg" alt="page not found" class="w-64 m-auto" />
                                            <h2 class="text-xl text-slate-700 mb-8 -mt-4">{{ __('No results found.') }}</h2>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--JPPD table end--}}

        <div class="card mt-5">
            <header class="card-header">
                <h4 class="card-title">Data Grafis
                </h4>
            </header>
            <div class="card-body px-6 pb-6">
                <div id="barchart" class="barchart"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script type="module">
        $("#data-table, .data-table").DataTable({
            dom: "<'grid grid-cols-12 gap-5 px-6 mt-6'<'col-span-4'l><'col-span-8 flex justify-end'f><'#pagination.flex items-center'>><'min-w-full't><'flex justify-end items-center'p>",
            paging: true,
            ordering: false,
            info: false,
            searching: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            language: {
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: `<iconify-icon icon="ic:round-keyboard-arrow-left"></iconify-icon>`,
                    next: `<iconify-icon icon="ic:round-keyboard-arrow-right"></iconify-icon>`,
                },
                search: "Search:",
            },
        });

        var options = {
            series: [{
                data: @json($jp_rata),
            }],
            chart: {
                type: 'bar',
                height: 1000
            },
            plotOptions: {
                bar: {
                    borderRadius: 2,
                    horizontal: true,
                    distributed: true,
                    dataLabels: {
                        position: 'bottom'
                    },
                }
            },
            annotations: {
                xaxis: [{
                    x: 20,
                    borderColor: '#00E396',
                    label: {
                        borderColor: '#00E396',
                        style: {
                            color: '#fff',
                            background: '#00E396',
                        },
                        text: '20 JP',
                    }
                }],
            },
            colors: @json($warna),
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#fff']
                },
                formatter: function(val, opt) {
                    return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
                },
                offsetX: 0,
                dropShadow: {
                    enabled: true
                }
            },
            xaxis: {
                categories: @json($lokasi),
            },
            yaxis: {
                labels: {
                    show: false
                }
            },
            tooltip: {
                theme: 'dark',
                x: {
                    show: true
                },
                y: {
                    title: {
                        formatter: function() {
                            return ''
                        }
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#barchart"), options);
        chart.render();
    </script>
    @endpush
</x-app-layout>
