<x-admin-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8 divide-y-2">
        <!-- regionのドロップダウン -->
        <div class="py-2 h-full flex items-center">
            <div class="w-40">
            <x-dropdown align="left" width="44">
                <x-slot name="trigger">
                    <button class="flex justify-between items-center text-md font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        <div>{{ session('search_shop') }}</div>

                        <div class="ml-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>
                <x-slot name="content">
                    @foreach ($shop_indexes as $shop_index)
                    <form method="GET" action="{{ url('/admin/review/manager') }}">
                        @csrf
                        <input type="hidden" name="search_shop" value="{{ $shop_index }}">
                        <button class="text-md block text-gray-500 hover:text-gray-700 focus:text-gray-700" type="submit">
                            {{ $shop_index }}
                        </button>
                    </form>
                    @endforeach
                </x-slot>
            </x-dropdown>
            </div>
        </div>

        <div class="divide-y-2">
            @foreach ($reviews as $review)
            <div class="py-2">
                <div>
                    <p>店舗名: {{ $review['shop_name'] }}</p>
                </div>
                <div>
                    <p>投稿者: {{ $review['user_name'] }}</p>
                </div>
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
                <div class="mt-2">
                    <form method="POST" action="{{ url('review/delete') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $review['user_id'] }}">
                        <input type="hidden" name="shop_id" value="{{ $review['shop_id'] }}">
                        <button class="text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20" type="submit">
                            削除
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        
    </div>
</x-app-layout>