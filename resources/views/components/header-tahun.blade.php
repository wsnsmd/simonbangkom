@php
    $tahunSekarang = date('Y');
    $pilihanTahun = range($tahunSekarang - 1, $tahunSekarang);
@endphp

<div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600 shadow-sm">
    <iconify-icon icon="heroicons:calendar-solid" class="text-slate-500 dark:text-slate-300 text-lg"></iconify-icon>

    <span class="text-slate-600 dark:text-slate-300 text-sm font-medium">Tahun:</span>

    <form action="{{ route('update.tahun') }}" method="POST" id="form-ganti-tahun">
        @csrf
        <select name="tahun" id="select-tahun"
                class="bg-transparent border-none font-bold text-sm text-slate-800 dark:text-white cursor-pointer focus:ring-0 focus:outline-none p-0 pr-6"
                onchange="document.getElementById('form-ganti-tahun').submit()">
            @foreach($pilihanTahun as $tahun)
                <option value="{{ $tahun }}"
                    {{ session('apps_tahun') == $tahun ? 'selected' : '' }}
                    class="text-slate-800 bg-white">
                    {{ $tahun }}
                </option>
            @endforeach
        </select>
    </form>
</div>
