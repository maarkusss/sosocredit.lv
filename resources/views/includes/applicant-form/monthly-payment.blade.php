<div class="flex items-center self-stretch justify-between mt-4">
    <div class="inline-flex flex-col items-start justify-center w-full gap-1 pr-2">
        <div class="justify-start">
            <span class="text-base font-bold leading-tight text-stone-800">
                @lang('Monthly Payment'):
            </span>
            <span class="text-base font-medium leading-tight text-red-600">*</span>
        </div>
        <div class="justify-start w-full text-xs font-normal leading-none text-stone-800">
            @lang('Monthly Payment info')
        </div>
    </div>
    <div class="px-2 md:px-4 py-1 md:py-2 bg-loan-normal/10 rounded-lg flex justify-center items-center gap-2.5">
        <div class="flex justify-start text-xl font-bold leading-9 text-[#0E3A13] lg:text-2xl">
            <p id="payment_monthly_amount"></p>&nbsp;€
        </div>
    </div>
</div>
