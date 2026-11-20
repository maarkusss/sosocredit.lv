@extends('layouts.public')

@section('content')

    <main>
        <section class="py-12 min-h-[400px]">
            <div class="max-w-screen-xl px-4 mx-auto">
                <div class="flex flex-col items-center justify-center">
                    <div class="flex flex-col items-center justify-start w-full max-w-screen-md mb-8">
                        <h1 class="mb-4 text-3xl font-semibold text-center text-gray-800 sm:text-4xl md:text-5xl">
                            @lang('main.thanks_your_email_confirmed')
                        </h1>
                        <p class="text-base text-center text-gray-600 sm:text-lg">
                            @lang('main.we_will_reply_via_email_within_couple_minutes')
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

@endsection
