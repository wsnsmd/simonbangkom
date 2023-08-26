<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf
    {{-- Username --}}
    <div class="fromGroup">
        <label for="username" class="block capitalize form-label">{{ __('Username') }}</label>
        <div class="relative ">
            <input type="text" name="username" id="username"
                class="form-control py-2 @error('username') !border !border-red-500 @enderror"
                placeholder="{{ __('Username Anda') }}" autofocus
                value="{{ old('username') }}">
            <x-input-error :messages="$errors->get('username')" class="mt-2"/>
        </div>
    </div>

    {{-- Password --}}
    <div class="fromGroup">
        <label for="password" class="block capitalize form-label">{{ __('Password') }}</label>
        <div class="relative ">
            <input type="password" name="password" class="form-control py-2 @error('password') !border !border-red-500 @enderror" placeholder="{{ __('Password Anda') }}" id="password" autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>
    </div>

    {{-- Tahun --}}
    <div class="fromGroup">
        <label for="tahun" class="block capitalize form-label">{{ __('Tahun') }}</label>
        <div class="relative ">
            <select name="tahun" id="tahun" class="form-control @error('tahun') !border !border-red-500 @enderror">
                <option value="{{ env('APP_TAHUN') }}" class="py-1 inline-block font-Inter font-normal text-sm text-slate-600">{{ env('APP_TAHUN') }}</option>
            </select>
            <x-input-error :messages="$errors->get('tahun')" class="mt-2"/>
        </div>
    </div>

    <div class="flex justify-normal">
        <div class="flex-none w-32 captcha">
            <span>{!! captcha_img('flat') !!}</span>
        </div>
        <div class="flex-none mx-2">
            <button type="button" class="btn btn-light inline-flex h-9 w-9 items-center justify-center" class="reload" id="reload">
                <iconify-icon class="text-xl" icon="heroicons:arrow-path-solid"></iconify-icon>
            </button>
        </div>
        <div class="flex-auto">
            <div class="relative">
            <input type="text" name="captcha" id="captcha" class="form-control py-2 @error('captcha') !border !border-red-500 @enderror" placeholder="{{ __('Captcha') }}">
            @error('captcha')
            <iconify-icon class="absolute top-1/2 right-3 -translate-y-1/2 text-danger-500 text-xl" icon="mdi:warning-octagon-outline"></iconify-icon>
            @enderror
            </div>
        </div>
    </div>

    <button type="submit"
            class="btn btn-dark block w-full text-center">
        {{ __('Login') }}
    </button>
</form>

@push('scripts')
<script type="module">
    $("#reload").click(function () {
        $.ajax({
            type: "GET",
            url: "{{ route('reload.captcha') }}",
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });
</script>
@endpush
