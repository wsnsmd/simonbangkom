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

        {{--JPPD table start--}}
        <div class="card mt-5">
            <header class="card-header noborder">
                <h3 class="font-medium text-lg text-black font-Inter dark:text-white text-center mb-5 lg:mb-0 lg:text-left">{{ $pd->lokasi }}</h3>
                {{-- Excel Button start--}}
                <div class="justify-center sm:justify-end flex  gap-3 items-center flex-wrap">
                    <a class="shift-Away btn btn-sm inline-flex justify-center btn-success rounded-[25px] items-center !p-2.5" data-tippy-content="Export Excel" data-tippy-theme="dark" href="javascript:;" onclick="event.preventDefault(); document.getElementById('export').submit();">
                        <iconify-icon icon="mdi:file-excel" class="text-lg mr-2"></iconify-icon> Excel
                    </a>
                    <form id="export" action="{{ route('export.opd') }}" method="post" style="display: none;">
                        @csrf
                        <input type="hidden" id="opd" name="opd" value="{{ $pd->lokasi }}">
                        <input type="hidden" id="tahun" name="tahun" value="{{ session('apps_tahun') }}">
                    </form>
                </div>
                {{-- Excel Button end --}}
            </header>
            <div class="card-body px-6 pb-6 -pt-10">
                <div class="overflow-x-auto -mx-6 dashcode-data-table">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden ">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700" id="data-table">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th w-24">
                                            #
                                        </th>
                                        <th scope="col" class="table-th w-48">
                                            {{ __('NIP') }}
                                        </th>
                                        <th scope="col" class="table-th">
                                            {{ __('NAMA') }}
                                        </th>
                                        <th scope="col" class="table-th">
                                            {{ __('JABATAN') }}
                                        </th>
                                        <th scope="col" class="table-th md:w-44">
                                            {{ __('TOTAL JP') }}
                                        </th>
                                        <th scope="col" class="table-th">
                                            {{ __('AKSI') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                    @forelse($pegawai as $key => $item)
                                    <tr class="border border-slate-100 even:bg-slate-50 dark:border-slate-900 relative">
                                        <td class="table-td sticky left-0">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="table-td">
                                            {{ $item['nip_baru'] }}
                                        </td>
                                        <td class="table-td">
                                            {{ $item['glr_depan']. ' ' . trim($item['nama']) . (!empty($item['glr_belakang']) ? ', ' . $item['glr_belakang'] : '') }}
                                        </td>
                                        <td class="table-td">
                                            {{ strtoupper($item['jabatan']) }}
                                        </td>
                                        <td class="table-td">
                                            @if($item['total_jp'] >= 20)
                                            <span class="badge bg-primary-500 text-white capitalize">{{ $item['total_jp'] }}</span>
                                            @else
                                            <span class="badge bg-danger-500 text-white capitalize">{{ $item['total_jp'] }}</span>
                                            @endif
                                        </td>
                                        <td class="table-td">
                                            <div class="action-btns space-x-2 flex">
                                                <a class="action-btn" href="{{ route('dashboard.pegawai', $item['nip_baru']) }}" data-tippy-content="Detail" data-tippy-theme="dark">
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
    </div>

    @push('scripts')
    <script type="module">
        $("#data-table").DataTable({
            dom: "<'grid grid-cols-12 gap-5 px-6 mt-6'<'col-span-4'l><'col-span-8 flex justify-end'f><'#pagination.flex items-center'>><'min-w-full't><'flex justify-end items-center'p>",
            paging: true,
            ordering: true,
            info: true,
            lengthMenu: [10,25,50,100],
            pageLength: 25,
            bLengthChange: true,
            searching: true,
            language: {
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: `<iconify-icon icon="ic:round-keyboard-arrow-left"></iconify-icon>`,
                    next: `<iconify-icon icon="ic:round-keyboard-arrow-right"></iconify-icon>`,
                },
            },
        });

        tippy(".shift-Away", {
            placement: "top",
            animation: "shift-away",
        });
    </script>
    @endpush
</x-app-layout>
