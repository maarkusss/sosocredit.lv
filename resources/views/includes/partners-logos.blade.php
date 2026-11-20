<section class="section" id="partners">
    <div class="section__content">
        <div class="section__content__heading-wrapper">
            <h2 class="section__content__heading">
                @lang('main.partners')
            </h2>
        </div>
        {{-- STEPS --}}
        <ul class="grid grid-cols-2 md:grid-cols-4 pt-0">
            <li class="h-[48px] my-4">
                <img src="/images/logos/tf-bank.png"
                     class="max-h-[48px] mx-auto px-2 mx:px-0"
                     loading="lazy"
                     alt="" />
            </li>
            <li class="h-[48px] my-4">
                <img src="/images/logos/ferratum.png"
                     class="max-h-[48px] mx-auto px-2 mx:px-0"
                     loading="lazy"
                     alt="" />
            </li>
            <li class="h-[48px] my-4">
                <img src="/images/logos/finea.png"
                     class="max-h-[48px] mx-auto px-2 mx:px-0"
                     loading="lazy"
                     alt="" />
            </li>
            <li class="h-[48px] my-4">
                <img src="/images/logos/bino.png"
                     class="max-h-[48px] mx-auto px-2 mx:px-0"
                     loading="lazy"
                     alt="" />
            </li>
            <li class="h-[48px] my-4">
                <img src="/images/logos/credit24.png"
                     class="max-h-[48px] mx-auto px-2 mx:px-0"
                     loading="lazy"
                     alt="" />
            </li>
            <li class="h-[48px] my-4">
                <img src="/images/logos/dalidali.svg"
                     class="max-h-[48px] mx-auto px-2 mx:px-0"
                     loading="lazy"
                     alt="" />
            </li>
            <li class="h-[48px] my-4">
                <img src="/images/logos/avafin.png"
                     class="max-h-[48px] mx-auto px-2 mx:px-0"
                     loading="lazy"
                     alt="" />
            </li>
            <li class="h-[48px] my-4">
                @if (app()->getLocale() === 'ru')
                    <img src="/images/logos/citi-partneri-ru.png"
                         class="max-h-[48px] mx-auto px-2 mx:px-0"
                         loading="lazy"
                         alt="" />
                @else
                    <img src="/images/logos/citi-partneri.png"
                         class="max-h-[48px] mx-auto px-2 mx:px-0"
                         loading="lazy"
                         alt="" />
                @endif
            </li>
        </ul>
    </div>
</section>
