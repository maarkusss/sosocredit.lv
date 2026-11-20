@php
    use App\Models\Section;

    /** @var Section $section */
    /** @var array $faqs */
    /** @var array $applicant_form_types */
@endphp

@extends('layouts.public')

@section('content')
    <main>
        <div>
            <!-- Hero block -->
            @include('includes.hero-block')

            {{-- How to apply for credit section --}}
            @if (!empty($section->json) && !empty($section->json['block3_title']))
                @include('includes.applicant-form-landing-page.block3')
            @endif

            <section class="pb-0">
                <div class="section_double">
                    {{-- Benefits block --}}
                    @if (!empty($section->json) && !empty($section->json['block1_title']))
                        <div class="bg-[#3FAF4F] hidden md:block"></div>
                        <div class="left-content bg-[#3FAF4F] py-12" id="why-unifying-loans">
                            <div class="flex flex-col items-center justify-center mt-2 mb-6 md:mt-0">
                                <h2 class="text-xl font-bold leading-7 text-white">
                                    {{ $section->json['block1_title'] }}
                                </h2>
                            </div>
                            {{-- STEPS --}}
                            @if (!empty($section->json['block1_set']))
                                @include('includes.applicant-form.block1_sets.' . $section->json['block1_set'])
                            @else
                                @include('includes.applicant-form.block1_sets.default')
                            @endif
                        </div>
                    @endif

                    {{-- Terms block --}}
                    @if (!empty($section->json) && !empty($section->json['swap_2_and_3']))
                        <div class="right-content">
                            @if (!empty($section->json) && !empty($section->json['block2_title']))
                                @include('includes.applicant-form-landing-page.block2')
                            @endif
                        </div>
                        <div class="hidden md:block"></div>
                    @else
                        <div class="right-content">
                            @if (!empty($section->json) && !empty($section->json['block2_title']))
                                @include('includes.applicant-form-landing-page.block2')
                            @endif
                        </div>
                        <div class="hidden md:block"></div>
                    @endif
                </div>
            </section>

            {{-- Apply Now --}}
            @if (!empty($section->json) && !empty($section->json['bottom_slogan']))
                @include('includes.applicant-form-landing-page.promo_block')
            @endif

            {{-- FAQ --}}
            @if ($faqs)
                @include('includes.applicant-form-landing-page.faqs')
            @endif

            {{-- Why chosing US --}}
            @include('includes.applicant-form-landing-page.why_us')

            {{-- Google reviews --}}
            @if (!empty($google_reviews) && count($google_reviews) > 0)
                @include('includes.applicant-form-landing-page.google_reviews', [
                    'reviews' => $google_reviews,
                ])
            @endif

            {{-- App install --}}
            @include('includes.applicant-form-landing-page.app-install')
        </div>
    </main>
@endsection
