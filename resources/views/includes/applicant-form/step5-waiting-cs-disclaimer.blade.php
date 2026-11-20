@php
    /**
     * @var bool $cs_deal_status
     * @var bool $cs_processing
     */
@endphp


<div class="flex flex-col items-center justify-center w-full">
    <div class="text-center">
        @if($cs_processing && ! $cs_deal_status)
            <p class="text-2xl font-bold">{{ __("CS Processing engaged") }}</p>
        @endif
        @if($cs_deal_status)
            <p class="text-2xl font-bold">{{ __("CS Deal engaged") }}</p>
        @endif
    </div>
</div>
