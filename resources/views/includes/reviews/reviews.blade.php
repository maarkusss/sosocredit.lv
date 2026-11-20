@foreach ($reviews as $review_index => $review)
    <div class="flex flex-col flex-shrink-0 w-full p-4 bg-white rounded-lg shadow-lg"
         style="scroll-snap-align: start;"
         id="review_{{ $review_index }}">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-12 h-12 mr-3 @if (!empty($is_applicant_page)) bg-green-200 @else bg-red-200 @endif rounded-full">
                <img class="w-full h-full rounded-full"
                     src="{{ $review->image }}"
                     alt="{{ $review->name }}"
                     loading="lazy"
                />
            </div>
            <span class="text-base font-bold leading-none text-left text-gray-800">
                {{ $review->name }}
            </span>
        </div>
        <p class="mt-3 text-base font-normal leading-tight text-gray-700">
            {{ $review->text }}
        </p>
    </div>
@endforeach

<script>
    // Reviews
    const container = document.querySelector("#container_review");
    const reviews = document.querySelectorAll("[id^='review']");
    const reviewBullets = document.querySelectorAll("[id^='bullet_review']");

    isElementInViewport = (element) => {
        const rect = element.getBoundingClientRect();

        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    container.addEventListener("scroll", () => {
        reviews.forEach((el, key) => {
            if (isElementInViewport(el)) {
                document.querySelector("#bullet_review_" + (key - 1))?.classList.add("bg-opacity-100");
                document.querySelector("#bullet_review_" + (key - 1))?.classList.remove("bg-opacity-25");
            } else {
                document.querySelector("#bullet_review_" + (key - 1))?.classList.remove("bg-opacity-100");
                document.querySelector("#bullet_review_" + (key - 1))?.classList.add("bg-opacity-25");
            }
        });
    });

    reviewBullets.forEach((bullet, key) => {
        bullet.addEventListener("click", () => {
            document.querySelector("#review_" + (key + 1)).scrollIntoView({
                behavior: "smooth",
                block: "nearest",
                inline: "start"
            });
        });
    });
</script>
