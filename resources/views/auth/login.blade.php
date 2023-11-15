<x-guest-layout>
    <div class="auth-box2 h-full flex flex-col justify-center">
        <div class="mobile-logo text-center mb-6 lg:hidden flex justify-center">
            <div class="mb-10 inline-flex items-center justify-center">
                <x-application-logo />
            </div>
        </div>
        <div class="text-center 2xl:mb-10 mb-4">
            <div class="inline-flex items-center justify-center">
                <img src="{{ getSettings('logo') }}" class="black_logo w-20 h-20" alt="logo">
            </div>
            <h4 class="font-medium mb-0"> {{ __('SiMon BangKom') }}</h4>
            <div class="text-slate-500 text-base">Sistem Monitoring Pengembangan Kompetensi</div>
        </div>

        <!-- START::LOGIN FORM -->
        <x-login-form></x-login-form>
        <!-- END::LOGIN FORM -->
    </div>
</x-guest-layout>
