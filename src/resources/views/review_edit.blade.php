<x-app-layout>
    <script>
        function ShowLength( str ) {
            document.getElementById("inputlength").innerHTML = str.length;
        }
    </script>

    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <form method="POST" action="{{ url('/review/update') }}" enctype="multipart/form-data">
        @csrf
            <div class="md:flex md:justify-between md:flex-wrap md:divide-x-2">
                <div class="md:w-2/5">
                    <p class="text-center text-2xl my-6">今回のご利用はいかがでしたか？</p>
                    <div class="w-56 bg-white rounded-md shadow-md mb-4 mx-auto">
                        <div>
                            <img class="w-full h-28 object-cover rounded-t-md" src="{{ $shop['image_url'] }}">
                        </div>
                        <div class="p-3">
                            <h2 class="font-bold">{{ $shop['name'] }}</h2>
                            <div class="text-xs">
                                <span>#{{ $shop['region'] }}</span>
                                <span>#{{ $shop['genre'] }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <a class="text-xs h-6 rounded-md bg-blue-600 text-white px-3 pt-1" href="{{ url('/detail/'.$shop['id']) }}">詳しくみる</a>
                                @if( Auth::check() )
                                    <button class="text-2xl {{ $shop['favorite'] ? 'text-red-500' : 'text-gray-100' }}" type="submit" disabled>&#9829; </button>
                                @endif
                            </div>
                        </div>            
                    </div>
                </div>
                <div class="md:w-3/5 px-6">
                    <p>体験を評価してください</p>
                    <div class="flex flex-row-reverse justify-end">
                        <input type="radio" id="star5" name="star" value=5 class="hidden peer" {{ 5 == $review['star'] ? "checked" : "" }}>
                        <label for="star5" class="relative py-0 px-[5px] text-gray-200 cursor-pointer text-[35px] hover:text-blue-600 peer-hover:text-blue-600 peer-checked:text-blue-600">★</label>
                        <input type="radio" id="star4" name="star" value=4 class="hidden peer" {{ 4 == $review['star'] ? "checked" : "" }}>
                        <label for="star4" class="relative py-0 px-[5px] text-gray-200 cursor-pointer text-[35px] hover:text-blue-600 peer-hover:text-blue-600 peer-checked:text-blue-600">★</label>
                        <input type="radio" id="star3" name="star" value=3 class="hidden peer" {{ 3 == $review['star'] ? "checked" : "" }}>
                        <label for="star3" class="relative py-0 px-[5px] text-gray-200 cursor-pointer text-[35px] hover:text-blue-600 peer-hover:text-blue-600 peer-checked:text-blue-600">★</label>
                        <input type="radio" id="star2" name="star" value=2 class="hidden peer" {{ 2 == $review['star'] ? "checked" : "" }}>
                        <label for="star2" class="relative py-0 px-[5px] text-gray-200 cursor-pointer text-[35px] hover:text-blue-600 peer-hover:text-blue-600 peer-checked:text-blue-600">★</label>
                        <input type="radio" id="star1" name="star" value=1 class="hidden peer" {{ 1 == $review['star'] ? "checked" : "" }}>
                        <label for="star1" class="relative py-0 px-[5px] text-gray-200 cursor-pointer text-[35px] hover:text-blue-600 peer-hover:text-blue-600 peer-checked:text-blue-600">★</label>
                    </div>
                    <div class="text-red-600">
                        @error('star')
                            ※{{ $message }}
                        @enderror
                    </div>
                    <p class="mt-6">コメント</p>
                    <div>
                        <textarea name="comment" class="w-full h-32" onkeyup="ShowLength(value)">{{ $review['comment'] }}</textarea>
                        <div class="text-xs text-end">
                            <span id="inputlength">0</span>
                            <span>/400(最大文字数)</span>
                        </div>    
                        <div class="text-red-600">
                            @error('comment')
                                ※{{ $message }}
                            @enderror
                        </div>
                    </div>
                    <p class="mt-6">画像の編集</p>
                    <div class="w-56">
                        @if (!is_null($review['image_url']))
                            <img src="{{ $review['image_url'] }}">
                        @endif
                    </div>
                    <div>
                        <div>
                            <input type="radio" value="0" id="img_edit_mode0" name="img_edit_mode" checked>
                            <label for="img_edit_mode0">変更しない</label>    
                        </div>
                        <div>
                            <input type="radio" value="1" id="img_edit_mode1" name="img_edit_mode">
                            <label for="img_edit_mode1">削除する</label>
                        </div>
                        <div>
                            <input type="radio" value="2" id="img_edit_mode2" name="img_edit_mode">
                            <label for="img_edit_mode2">置換する</label>
                            <div class="ml-8">
                                <input type="file" name="image_file">
                                <div class="text-red-600">
                                    @error('image_file')
                                        ※{{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="my-6 text-center">
                <input type="hidden" name="shop_id" value="{{ $shop['id'] }}">
                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                <button type="submit" class="text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded-full w-80">口コミを変更</button>
            </div>
        </form>

    </div>
</x-app-layout>