<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8 divide-y-2">
        <div class="my-6">
            <h1 class="text-2xl">{{ $shop['name'] }}の口コミ</h1>
            <a class="underline text-sm" href="{{ url('/detail/'.$shop['id']) }}">店舗詳細へ</a>
        </div>
        <div class="divide-y-2">
            @foreach ($reviews as $review)
            <div class="py-2">
                <div>
                    @for ($counter = 0; $counter < 5; $counter++)
                        <span class="{{ $counter < $review['star'] ? 'text-blue-600' : 'text-gray-200'}}">★</span>
                    @endfor
                </div>
                <div>
                    {{ $review['comment'] }}
                </div>
                <div class="w-56">
                    @if (!is_null($review['image_url']))
                        <img src="{{ $review['image_url'] }}">
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
    </div>
</x-app-layout>