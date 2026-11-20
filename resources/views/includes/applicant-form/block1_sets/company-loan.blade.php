@php
    /** @var \App\Models\Section $section */
    $count = !empty($section->json['block1_options'][3]) ? 4 : 3;
@endphp

<div class="flex flex-wrap">
    <div
        class="flex flex-col items-center justify-start flex-shrink-0 w-full mt-8 sm:mt-0 sm:w-1/{{ $count }} gap-2">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full p-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="#3faf4f" viewBox="0 0 256 256">
                <path
                    d="M221.87,83.16A104.1,104.1,0,1,1,195.67,49l22.67-22.68a8,8,0,0,1,11.32,11.32l-96,96a8,8,0,0,1-11.32-11.32l27.72-27.72a40,40,0,1,0,17.87,31.09,8,8,0,1,1,16-.9,56,56,0,1,1-22.38-41.65L184.3,60.39a87.88,87.88,0,1,0,23.13,29.67,8,8,0,0,1,14.44-6.9Z">
                </path>
            </svg>
        </div>
        <div class="relative px-1 lg:px-4">
            <h3 class="mx-auto text-sm font-semibold leading-tight text-center text-white md:leading-normal">
                {{ $section->json['block1_options'][0] ?? '' }}
            </h3>
        </div>
    </div>
    <div
        class="flex flex-col items-center justify-start flex-shrink-0 w-full mt-8 sm:mt-0 sm:w-1/{{ $count }} gap-2">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full p-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="#3faf4f" viewBox="0 0 256 256">
                <path
                    d="M119.76,217.94A8,8,0,0,1,112,224a8.13,8.13,0,0,1-2-.24l-32-8a8,8,0,0,1-2.5-1.11l-24-16a8,8,0,1,1,8.88-13.31l22.84,15.23,30.66,7.67A8,8,0,0,1,119.76,217.94Zm132.69-96.46a15.89,15.89,0,0,1-8,9.25l-23.68,11.84-55.08,55.09a8,8,0,0,1-7.6,2.1l-64-16a8.06,8.06,0,0,1-2.71-1.25L35.86,142.87,11.58,130.73a16,16,0,0,1-7.16-21.46L29.27,59.58h0a16,16,0,0,1,21.46-7.16l22.06,11,53-15.14a8,8,0,0,1,4.4,0l53,15.14,22.06-11a16,16,0,0,1,21.46,7.16l24.85,49.69A15.9,15.9,0,0,1,252.45,121.48Zm-46.18,12.94L179.06,80H147.24L104,122c12.66,8.09,32.51,10.32,50.32-7.63a8,8,0,0,1,10.68-.61l34.41,27.57Zm-187.54-18,17.69,8.85L61.27,75.58,43.58,66.73ZM188,152.66l-27.71-22.19c-19.54,16-44.35,18.11-64.91,5a16,16,0,0,1-2.72-24.82.6.6,0,0,1,.08-.08L137.6,67.06,128,64.32,77.58,78.73,50.21,133.46l49.2,35.15,58.14,14.53Zm49.24-36.24L212.42,66.73l-17.69,8.85,24.85,49.69Z">
                </path>
            </svg>
        </div>
        <div class="relative px-1 lg:px-4">
            <h3 class="mx-auto text-sm font-semibold leading-tight text-center text-white md:leading-normal">
                {{ $section->json['block1_options'][1] ?? '' }}
            </h3>
        </div>
    </div>
    <div
        class="flex flex-col items-center justify-start flex-shrink-0 w-full mt-8 sm:mt-0 sm:w-1/{{ $count }} gap-2">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full p-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="#3faf4f" viewBox="0 0 256 256">
                <path
                    d="M184,24H72A16,16,0,0,0,56,40V76a16.07,16.07,0,0,0,6.4,12.8L114.67,128,62.4,167.2A16.07,16.07,0,0,0,56,180v36a16,16,0,0,0,16,16H184a16,16,0,0,0,16-16V180.36a16.09,16.09,0,0,0-6.35-12.77L141.27,128l52.38-39.6A16.05,16.05,0,0,0,200,75.64V40A16,16,0,0,0,184,24Zm0,16V56H72V40Zm0,176H72V180l56-42,56,42.35Zm-56-98L72,76V72H184v3.64Z">
                </path>
            </svg>
        </div>
        <div class="relative px-1 lg:px-4">
            <h3 class="mx-auto text-sm font-semibold leading-tight text-center text-white md:leading-normal">
                {{ $section->json['block1_options'][2] ?? '' }}
            </h3>
        </div>
    </div>
    @if (!empty($section->json['block1_options'][3]))
        <div
            class="flex flex-col items-center justify-start flex-shrink-0 w-full mt-8 sm:mt-0 sm:w-1/{{ $count }} gap-2">
            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="#3faf4f"
                    viewBox="0 0 256 256">
                    <path
                        d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm16-40a8,8,0,0,1-8,8,16,16,0,0,1-16-16V128a8,8,0,0,1,0-16,16,16,0,0,1,16,16v40A8,8,0,0,1,144,176ZM112,84a12,12,0,1,1,12,12A12,12,0,0,1,112,84Z">
                    </path>
                </svg>
            </div>
            <div class="relative px-1 lg:px-4">
                <h3 class="mx-auto text-sm font-semibold leading-tight text-center text-white md:leading-normal">
                    {{ $section->json['block1_options'][3] ?? '' }}
                </h3>
            </div>
        </div>
    @endif
</div>
