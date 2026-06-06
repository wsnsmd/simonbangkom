@php
    $tahunSekarang = date('Y');
    $pilihanTahun = range($tahunSekarang - 1, $tahunSekarang);
@endphp
<div class="flex items-center gap-2">
    <iconify-icon icon="heroicons:calendar-solid" class="text-slate-800 dark:text-white text-xl"></iconify-icon>
    <form action="{{ route('update.tahun') }}" method="POST" id="form-ganti-tahun">
        @csrf
        <select name="tahun" id="select-tahun"
                class="bg-transparent border-none font-bold text-sm dark:text-white cursor-pointer focus:ring-0"
                onchange="document.getElementById('form-ganti-tahun').submit()">
            @foreach($pilihanTahun as $tahun)
                <option value="{{ $tahun }}"
                    {{ session('apps_tahun') == $tahun ? 'selected' : '' }}
                    class="text-slate-800">
                    {{ $tahun }}
                </option>
            @endforeach
        </select>
    </form>
</div>
