<x-admin-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <!-- 表示する店舗の選択 -->
        <div class="w-fit h-6 px-2 bg-white rounded-md shadow-md mb-2 ">
            <x-dropdown align="left" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        <div>{{ array_search(session('shop_index'), $shop_list) }}</div>

                        <div class="ml-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>
                <x-slot name="content">
                  @foreach ($shop_list as $shop_id)
                  <form method="GET" action="{{ url('/admin/edit') }}">
                      @csrf
                      <input type="hidden" name="shop_index" value="{{ $shop_id }}">
                      <button class="block text-gray-500 hover:text-gray-700 focus:text-gray-700" type="submit">
                          {{ array_search($shop_id, $shop_list) }}
                      </button>
                  </form>
                  @endforeach
                </x-slot>
            </x-dropdown>
        </div>
        <!-- 店舗情報の更新 -->
        <div>
            <h1 class="text-xl">店舗情報の更新</h1>
            <div class="ml-3">
              <form method="POST" action="{{ url('admin/shop_update') }}" enctype="multipart/form-data">
                @csrf
                <div>
                    {{ session('message') }}
                </div>
                <input type="hidden" name="id" value="{{ $shop['id'] }}">
                <div>
                    <label for="name" class="block">店舗名</label>
                    <input type="text" name="name" class="ml-3" value="{{ old('name', $shop['name']) }}">
                    <div class="text-red-600">
                        @error('name')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="region" class="block">地域</label>
                    <input type="text" name="region" class="ml-3" value="{{ old('region', $shop['region']) }}">
                    <div class="text-red-600">
                        @error('region')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="genre" class="block">ジャンル</label>
                    <input type="text" name="genre" class="ml-3" value="{{ old('genre', $shop['genre']) }}">
                    <div class="text-red-600">
                        @error('genre')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="description" class="block">店舗概要</label>
                    <textarea name="description" class="w-72 h-32 ml-3">{{ old('description', $shop['description']) }}</textarea>
                </div>
                <div>
                    <label for="image_file" class="block">画像ファイルの変更(アップロード)</label>
                    <input type="file" name="image_file"  class="ml-3">
                    <div class="text-red-600">
                        @error('image_file')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="my-3 mx-auto">
                    <button type="submit" class="text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20">更新</button>
                </div>
              </form>
            </div>
        </div>
    </div>
</x-admin-layout>