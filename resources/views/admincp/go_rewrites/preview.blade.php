@extends('layouts.public')

@section('content')

    <main class="main flex py-8 max-w-4xl justify-start items-center mx-auto">
        <div class="content-wrapper w-full">
            <div class="admin-page-title-wrap mb-6">
                <h1 class="text-2xl font-medium text-gray-700">{{ $page_title }}</h1>
            </div>

            @if (config('app.go_rewrites_enabled') !== true)
                <p class="alert alert-danger w-full">
                    Go Rewrites functionality is disabled. Please enable it in config.
                </p>
            @endif

            <div class="content-wrapper__form-wrapper">
                <div class="bg-white rounded-xl mb-6 px-6 py-5 text-gray-600 text-sm border border-gray-300">
                    @if (!empty($go_rewrites))
                        <h5 class="text-base font-semibold mb-2">Active Rewrites</h5>
                        <ul class="list-disc pl-4 mb-6 text-sm">
                            @foreach ($go_rewrites as $go_rewrite)
                                <li>
                                    <a href="{{ route('admincp.go-rewrites.preview', $go_rewrite->id) }}"
                                       rel="noopener noreferrer">
                                        [{{ $go_rewrite->id }}] {{ $go_rewrite->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <h5 class="text-base font-semibold mb-2">Input</h5>
                    <p>
                        <strong><span class="opacity-50">(s5)</span> utm_source:</strong> {{ $go_link->utm_data->utm_source }}
                        <br/>
                        <strong><span class="opacity-50">(s4)</span> utm_content:</strong> {{ $go_link->utm_data->utm_content }}
                        <br/>
                        <strong><span class="opacity-50">(s3)</span> gclid:</strong> {{ $go_link->utm_data->gclid }}
                        <br/>
                        <strong><span class="opacity-50">(s2)</span> utm_campaign:</strong> {{ $go_link->utm_data->utm_campaign }}
                        <br/>
                        <strong><span class="opacity-50">(s1)</span> prefix:</strong> {{ s1() }}
                    </p>
                </div>

                <div class="bg-white rounded-xl mb-6 pt-5 text-gray-600 text-sm border border-gray-300">
                    <h5 class="text-base font-semibold mb-2 px-6">Generated Links</h5>
                    <ul>
                        @foreach ($lenders as $lender)
                            <li class="mb-6 hover:bg-gray-50 px-6 py-3">
                                <div class="flex flex-row items-center">
                                    <div class="mr-4">
                                        <img src="{{ $lender->image }}"
                                             style="height: 20px;"
                                             alt="Lender {{ $lender->name }}"/>
                                    </div>

                                    <div>
                                        <strong>{{ $lender->name }}</strong>
                                        <br/>
                                        <span class="text-sm">
                                        {{ $lender->go_link }}
                                    </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </main>

@endsection
