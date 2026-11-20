@for ($i = 0; $i < $reviews->count(); $i++)
    <div class="w-2 h-2 mr-2 @if($i > 0) bg-opacity-25 @endif rounded-full bg-primary-normal"
         id="bullet_review_{{ $i }}"></div>
@endfor
