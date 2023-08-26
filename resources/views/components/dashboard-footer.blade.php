<!-- BEGIN: Footer For Desktop and tab -->
<footer id="footer">
    <div class="site-footer px-6 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-300 py-4 ltr:ml-[248px] rtl:mr-[248px]">
        <div class="grid md:grid-cols-2 grid-cols-1 md:gap-5">
        <div class="text-center ltr:md:text-start rtl:md:text-right text-sm">
            {{ __('© BPSDM Prov. Kaltim') }}
        </div>
        </div>
    </div>
</footer>
  <!-- END: Footer For Desktop and tab -->

<div class="bg-white bg-no-repeat custom-dropshadow footer-bg dark:bg-slate-700 flex justify-around items-center
      backdrop-filter backdrop-blur-[40px] fixed left-0 bottom-0 w-full z-[9999] bothrefm-0 py-[12px] px-4 md:hidden">
    <a href="#" class="relative bg-white bg-no-repeat backdrop-filter backdrop-blur-[40px] rounded-full footer-bg dark:bg-slate-700
        h-[65px] w-[65px] z-[-1] -mt-[40px] flex justify-center items-center">
      <div class="h-[50px] w-[50px] rounded-full relative left-[0px] hrefp-[0px] custom-dropshadow">
        <img src="{{
                auth()->user()->getFirstMediaUrl('profile-image', 'preview') ?:
                Avatar::create(auth()->user()->name)->toBase64() }}" alt="" class="w-full h-full rounded-full border-2 border-slate-100"
        >
      </div>
    </a>
</div>

