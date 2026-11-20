@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Images' }}</h1>

            <div class="flex flex-col items-start sm:flex-row sm:items-center">
                <form class="flex items-center" action="{{ route('admincp.images.store') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <input
                        class="px-3 py-2 mr-2 text-xs text-gray-700 bg-white border rounded border-primary-normal"
                        name="image" type="file" accept="image/*" required />
                    <button
                        class="flex flex-row items-center px-3 py-2 text-sm font-bold text-white rounded bg-primary-normal hover:bg-primary-dark"
                        type="submit">
                        <svg class="w-5 h-5 mr-1 fill-current">
                            <path
                                d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z" />
                        </svg>
                        Upload image
                    </button>
                </form>
            </div>
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <div class="grid w-full grid-cols-2 gap-4 lg:grid-cols-4 xl:grid-cols-6">
                    @forelse ($images as $image)
                        <div class="relative w-full p-1 bg-white border rounded-lg group">
                            <form class="absolute top-0 right-0 hidden mt-2 mr-2 group-hover:block"
                                  action="{{ route('admincp.images.destroy', ['image' => $image]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="p-1 text-red-500 transition-all duration-200 bg-white rounded-full hover:shadow-lg hover:bg-gray-100 hover:text-red-600"
                                    type="submit" title="Delete image"
                                    onclick="return confirm('Are you sure you want to delete this image?');">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                         fill="currentColor">
                                        <path fill-rule="evenodd"
                                              d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                            <div class="w-full h-48 mb-1">
                                <img class="object-cover object-center w-full h-full max-w-full max-h-full rounded-lg aspect-auto"
                                     src="{{ asset($image->path) }}"
                                     title="{{ asset($image->path) }}"
                                     loading="lazy"
                                />
                            </div>
                            <span class="block text-center text-gray-500 text-sm">{{ basename($image->path)  }}</span>
                            <button
                                class="w-full text-sm text-center text-gray-800 underline break-words hover:text-primary-normal"
                                type="button" id="copy_image_url_button"
                                title="{{ asset($image->path) }}"
                                data-url="{{ asset($image->path) }}">
                                Copy URL
                            </button>
                        </div>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="p-2">
                                    No Images uploaded yet.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </div>

                {{ $images->links('admincp.includes.pagination') }}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        const activeSection = document.getElementById("sidebar-images");
        activeSection.classList.add("text-stone-900", "bg-primary-normal/40", "font-semibold");

        const copyImageURLButtons = document.querySelectorAll("#copy_image_url_button");

        copyImageURLButtons.forEach(button => {
            button.addEventListener("click", () => {
                const url = button.dataset.url;

                navigator.clipboard.writeText(url).then(() => {
                    button.innerHTML = "Copied ✔️";

                    setInterval(() => {
                        button.innerHTML = "Copy URL";
                    }, 1000);
                }, () => {
                    button.innerHTML = "Failed to copy ❌";

                    setInterval(() => {
                        button.innerHTML = "Copy URL";
                    }, 1000);
                });
            })
        })
    </script>
@endsection
