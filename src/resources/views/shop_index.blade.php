<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <!-- 検索 -->
        <div class="flex justify-end">
            <div class="h-6 px-2 bg-white rounded-md shadow-md mb-2 divide-x flex items-center">
                <!-- regionのドロップダウン -->
                <div class="h-full flex items-center">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-xs font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ session('search_region') }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @foreach ($regions as $region)
                            <form method="GET" action="{{ url('/') }}">
                                @csrf
                                <input type="hidden" name="search_region" value="{{ $region }}">
                                <button class="text-xs block text-gray-500 hover:text-gray-700 focus:text-gray-700" type="submit">
                                    {{ $region }}
                                </button>
                            </form>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                </div>
                <!-- genreのドロップダウン -->
                <div class="pl-1 h-full flex items-center">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-xs font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ session('search_genre') }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @foreach ($genres as $genre)
                            <form method="GET" action="{{ url('/') }}">
                                @csrf
                                <input type="hidden" name="search_genre" value="{{ $genre }}">
                                <button class="text-xs block  text-gray-500 hover:text-gray-700 focus:text-gray-700" type="submit">
                                    {{ $genre }}
                                </button>
                            </form>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                </div>
                <!-- 店舗名検索 -->
                <div class="h-full flex items-center">
                    <span class="fill-gray-500 h-5 w-5">
                        <svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z">
                            </path>
                        </svg>
                    </span>
                    <form method="GET" action="{{ url('/') }}">
                        <input class="inline-block w-32 px-1 py-0.5 text-gray-500 text-xs border-none" type="text" id="search_name" name="search_name" value= "{{ Session::has('search_name') ? Session::get('search_name') : '' }}" placeholder="Search ..." onchange="this.closest('form').submit();" oninput="this.style.color='#374151'"/>
                    </form>
                </div>
            </div>
        </div>

        <!-- カード一覧 -->
        <div class="flex justify-between flex-wrap">
            @foreach ($shops as $shop)
            <div class="w-56 bg-white rounded-md shadow-md mb-4">
                <div>
                    <img class="w-full h-28 object-cover rounded-t-md" src={{ $shop['image_url'] }}>
                </div>
                <div class="p-3">
                    <h2 class="font-bold">{{ $shop['name'] }}</h2>
                    <div class="text-xs">
                        <span>#{{ $shop['region'] }}</span>
                        <span>#{{ $shop['genre'] }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <button class="text-xs h-6 rounded-md bg-blue-600 text-white px-3">詳しくみる</button>
                        <button class="text-2xl text-gray-100">&#9829;</button>
                    </div>
                </div>            
            </div>
            @endforeach
        </div>

    </div>
    
</x-app-layout>
