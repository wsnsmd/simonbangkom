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
                            Avatar::create(auth()->user()->name)->setDimension(400)->setFontSize(240)->toBase64() }}" alt="" class="w-full h-full object-cover rounded-full">
                            <a
                                href="profile-setting"
                                class="absolute right-2 h-8 w-8 bg-slate-50 text-slate-600 rounded-full shadow-sm flex flex-col items-center
                                    justify-center md:top-[140px] top-[100px]">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </a>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-2xl font-medium text-slate-900 dark:text-slate-200 mb-[3px]">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-sm font-light text-slate-600 dark:text-slate-400 capitalize">
                            {{ auth()->user()->roles()->first()?->name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <div class="lg:col-span-4 col-span-12">
                <div class="card h-full">
                    <header class="card-header">
                        <h4 class="card-title">Info</h4>
                    </header>
                    <div class="card-body p-6">
                        <ul class="list space-y-8">
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:envelope"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                        EMAIL
                                    </div>
                                    <a href="mailto:{{ auth()->user()->email ?: 'N/A' }}" class="text-base text-slate-600 dark:text-slate-50">
                                        {{ auth()->user()->email ?: 'N/A' }}
                                    </a>
                                </div>
                            </li>
                            <!-- end single list -->
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:phone-arrow-up-right"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                        TELP / HANDPHONE
                                    </div>
                                    <a href="{{ auth()->user()->phone }}" class="text-base text-slate-600 dark:text-slate-50">
                                        {{ auth()->user()->phone ?: 'N/A' }}
                                    </a>
                                </div>
                            </li>
                            <!-- end single list -->
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:map"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                        LOKASI
                                    </div>
                                    <div class="text-base text-slate-600 dark:text-slate-50 break-words">
                                        <?php
                                        if (!auth()->user()->city && !auth()->user()->post_code && !auth()->user()->lokasi) {
                                            $str = 'N/A';
                                        } else {
                                            $address = [];
                                            if (auth()->user()->city) {
                                                array_push($address, auth()->user()->city);
                                            }
                                            if (auth()->user()->post_code) {
                                                array_push($address, auth()->user()->post_code);
                                            }
                                            if (auth()->user()->lokasi) {
                                                array_push($address, auth()->user()->lokasi);
                                            }

                                            $str = '';
                                            $i = 0;
                                            foreach ($address as $value) {
                                                $str = $str . $value . ', ';
                                            }
                                            $str = Str::substr($str, 0, strlen($str) - 2);
                                        }
                                        ?>
                                        {{ $str !== '' ? $str : 'N/A' }}
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
                        <h4 class="card-title">Edit Profil
                        </h4>
                    </header>
                    <div class="card-body px-5 py-6">

                        {{-- Alert start --}}
                        @if (session('message'))
                            <x-alert :message="session('message')" :type="'success'" />
                            <br />
                        @endif
                        @if (auth()->user()->getPendingEmail())
                            <x-alert :message="__(
                                'Please check your email to verify your new email address. You cant use your new email to login until you verify it.',
                            )" :type="'danger'" />
                            <br />
                        @endif
                        {{-- Alert end --}}


                        <form action="{{ route('profiles.update', auth()->user()) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div class="input-area">
                                    <label for="name" class="form-label">
                                        {{ __('Nama') }}
                                    </label>
                                    <input name="name" type="text" id="name" class="form-control"
                                        placeholder="{{ __('Nama...') }}" required
                                        value="{{ auth()->user()->name }}">
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div class="input-area">
                                    <label for="email" class="form-label">
                                        {{ __('Email') }}
                                    </label>
                                    <input name="email" type="email" id="email" class="form-control"
                                        placeholder="{{ __('Email...') }}" required
                                        value="{{ auth()->user()->email }}">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div class="input-area">
                                    <label for="phone" class="form-label">
                                        {{ __('Telp / Handphone') }}
                                    </label>
                                    <input name="phone" type="tel" id="phone" class="form-control"
                                        placeholder="{{ __('Telp / Handphone...') }}" value="{{ auth()->user()->phone }}">
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                                <div class="input-area">
                                    <label for="state" class="form-label">
                                        {{ __('Kota') }}
                                    </label>
                                    <input name="city" type="text" id="state" class="form-control"
                                        placeholder="{{ __('Kota...') }}" value="{{ auth()->user()->city }}">
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>
                                <div class="input-area">
                                    <label for="password" class="form-label">
                                        {{ __('Password') }}
                                    </label>
                                    <input name="password" type="password" id="password" class="form-control"
                                        placeholder="{{ __('Password...') }}" value="">
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    <span class="text-xs text-gray-400 space-y-1 pl-2">
                                        * Kosongkan jika tidak ingin merubah password.
                                    </span>
                                </div>
                                <div class="input-area">
                                    <label for="lokasi" class="form-label">
                                        {{ __('Foto') }}
                                    </label>
                                    <input onchange="imagePreview(event, 'profilePagePreviewId')" name="photo"
                                        type="file" placeholder="Default input"
                                        class="form-control
                                    p-[0.565rem] pl-2">
                                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-sm btn-dark mt-3">
                                    {{ __('Simpan') }}
                                </button>
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
