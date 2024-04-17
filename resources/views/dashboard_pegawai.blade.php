<x-app-layout>
    <div class="space-y-5 profile-page">
        <div class="profiel-wrap px-[35px] pb-10 md:pt-[84px] pt-10 rounded-lg bg-white dark:bg-slate-800 lg:flex lg:space-y-0
                space-y-6 justify-between items-end relative z-[1]">
            <div class="bg-slate-900 dark:bg-slate-700 absolute left-0 top-0 md:h-1/2 h-[150px] w-full z-[-1] rounded-t-lg"></div>
            <div class="profile-box flex-none md:text-start text-center">
                <div class="md:flex items-end md:space-x-6 rtl:space-x-reverse">
                    <div class="flex-none">
                        <div class="md:h-[186px] md:w-[186px] h-[140px] w-[140px] md:ml-0 md:mr-0 ml-auto mr-auto md:mb-0 mb-4 rounded-full ring-4
                                ring-slate-100 relative">
                            <img src="{{ auth()->user()->getFirstMediaUrl('profile-image') ?:
                            Avatar::create($data_pegawai['nama'])->setDimension(400)->setFontSize(240)->toBase64() }}" alt="" class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-2xl font-medium text-slate-900 dark:text-slate-200 mb-[3px]">
                            {{ $data_pegawai['nama'] }}
                        </div>
                        <div class="text-sm font-light text-slate-600 dark:text-slate-400 capitalize">
                            {{ $data_pegawai['opd'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <div class="lg:col-span-4 col-span-12">
                <div class="card h-full">
                    <header class="card-header">
                        <h4 class="card-title">Info Pengembangan Kompetensi</h4>
                    </header>
                    <div class="card-body p-6">
                        <ul class="list space-y-8">
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:check-badge"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                        TOTAL JP
                                    </div>
                                    <span class="text-base text-slate-600 dark:text-slate-50">
                                        {{ $bangkom['total_jp'] }}
                                    </span>
                                </div>
                            </li>
                            <!-- end single list -->
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:light-bulb"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                        TEKNIS
                                    </div>
                                    <span class="text-base text-slate-600 dark:text-slate-50">
                                        {{ $bangkom['jumlah_jam_teknis'] }}
                                    </span>
                                </div>
                            </li>
                            <!-- end single list -->
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:building-office-2"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                        FUNGSIONAL
                                    </div>
                                    <div class="text-base text-slate-600 dark:text-slate-50 break-words">
                                        {{ $bangkom['jumlah_jam_fungsional'] }}
                                    </div>
                                </div>
                            </li>
                            <!-- end single list -->
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:briefcase"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                        STRUKTURAL
                                    </div>
                                    <div class="text-base text-slate-600 dark:text-slate-50 break-words">
                                        {{ $bangkom['jumlah_jam_struktural'] }}
                                    </div>
                                </div>
                            </li>
                            <!-- end single list -->
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:presentation-chart-bar"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                        SEMINAR
                                    </div>
                                    <div class="text-base text-slate-600 dark:text-slate-50 break-words">
                                        {{ $bangkom['jumlah_jam_seminar'] }}
                                    </div>
                                </div>
                            </li>
                            <!-- end single list -->
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:academic-cap"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                        KURSUS
                                    </div>
                                    <div class="text-base text-slate-600 dark:text-slate-50 break-words">
                                        {{ $bangkom['jumlah_jam_kursus'] }}
                                    </div>
                                </div>
                            </li>
                            <!-- end single list -->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-8 col-span-12">
                <div class="card ">
                    <header class="card-header">
                        <h4 class="card-title">Profile Pegawai
                        </h4>
                    </header>
                    <div class="card-body px-5 py-6">
                        <form>
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div class="input-area">
                                    <label for="nip" class="form-label">
                                        {{ __('NIP') }}
                                    </label>
                                    <input name="nip" type="text" id="nip" class="form-control"
                                        placeholder="{{ __('NIPPegawai') }}" required readonly
                                        value="{{ $data_pegawai['nip_baru'] }}">
                                </div>
                                <div class="input-area">
                                    <label for="name" class="form-label">
                                        {{ __('Nama') }}
                                    </label>
                                    <input name="name" type="text" id="name" class="form-control"
                                        placeholder="{{ __('Nama Pegawai') }}" required readonly
                                        value="{{ $data_pegawai['nama'] }}">
                                </div>
                                <div class="input-area">
                                    <label for="pd" class="form-label">
                                        {{ __('Perangkat Daerah') }}
                                    </label>
                                    <input name="pd" type="text" id="pd" class="form-control"
                                        placeholder="{{ __('Perangkat Daerah') }}" value="{{ $data_pegawai['skpd'] }}" readonly>
                                </div>
                                <div class="input-area">
                                    <label for="jabatan" class="form-label">
                                        {{ __('Jabatan') }}
                                    </label>
                                    <input name="jabatan" type="text" id="jabatan" class="form-control"
                                        placeholder="{{ __('Jabatan Pegawai') }}" value="{{ $data_pegawai['jabatan'] }}" readonly>
                                </div>
                                <div class="input-area">
                                    <label for="pangkat" class="form-label">
                                        {{ __('Pangkat/Golongan') }}
                                    </label>
                                    <input name="pangkat" type="text" id="pangkat" class="form-control"
                                        placeholder="{{ __('Pangkat/Golongan') }}" value="{{ $data_pegawai['pangkat'] . ' (' . $data_pegawai['golongan'] . ')' }}" readonly>
                                </div>
                                <div class="input-area">
                                    <label for="email" class="form-label">
                                        {{ __('Email') }}
                                    </label>
                                    <input name="email" type="email" id="email" class="form-control"
                                        placeholder="{{ __('Email Pegawai') }}" required readonly
                                        value="{{ $data_pegawai['email'] }}">
                                </div>
                                <div class="input-area">
                                    <label for="phone" class="form-label">
                                        {{ __('No Handphone') }}
                                    </label>
                                    <input name="phone" type="tel" id="phone" class="form-control"
                                        placeholder="{{ __('Handphone Pegawai') }}" value="{{ $data_pegawai['no_hape'] }}" readonly>
                                </div>
                                <div class="input-area">
                                    <label for="postcode" class="form-label">
                                        {{ __('Alamat') }}
                                    </label>
                                    <textarea rows="5" class="form-control" readonly>{{ $data_pegawai['alamat'] }}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let imagePreview = function(event, id) {
                let output = document.getElementById(id);
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src) // free memory
                }
            };
        </script>
    @endpush
</x-app-layout>
