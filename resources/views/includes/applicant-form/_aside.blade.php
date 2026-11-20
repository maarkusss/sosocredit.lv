<div
    class="w-72 p-6 bg-white rounded-lg shadow-[0px_4px_8px_0px_rgba(0,0,0,0.10)] inline-flex flex-col justify-start items-start gap-4">
    <div class="self-stretch justify-start text-base font-bold leading-tight text-neutral-900">{{ $loantype }}</div>
    <div class="flex flex-col items-start self-stretch justify-start gap-4">
        <div class="flex flex-col items-start self-stretch justify-start gap-2 text-sm leading-tight text-neutral-900">
            <div class="inline-flex items-center self-stretch justify-between">
                <div class="justify-start font-medium">@lang('main.sum')</div>
                <div class="justify-start font-bold">{{ currency_formatted($application['amount']) }}</div>
            </div>
            <div class="inline-flex items-center self-stretch justify-between">
                <div class="justify-start font-medium">@lang('main.term') (@lang('main.month'))</div>
                <div class="justify-start font-bold">{{ $application['term_months'] }}</div>
            </div>
        </div>
        <a @click="formEditModalClosed = false"
            class="relative w-full text-sm font-bold leading-tight text-center rounded cursor-pointer h-9 outline outline-1 outline-primary-normal text-loan-normal hover:bg-zinc-50 place-content-center">
            @lang('applicant_form.edit_application')
        </a>
    </div>
</div>
