<x-app-layout>
  <script>
    function on_reserve_num_changed(reserve_num) {
      show_txt = reserve_num + '人';
      document.getElementById('reserve_num_trigger').innerHTML = show_txt;
      document.getElementById('reserve_num_confirm').innerHTML = show_txt;
      document.getElementById('reserve_num_input').value = reserve_num;
    }
    function on_reserve_time_changed(reserve_time) {
      document.getElementById('reserve_time_trigger').innerHTML = reserve_time;
      document.getElementById('reserve_time_confirm').innerHTML = reserve_time;
      document.getElementById('reserve_time_input').value = reserve_time;
    }
  </script>

  <div class="max-w-7xl mx-auto px-4 lg:px-8 flex justify-between flex-wrap">
    
    <!-- 店の詳細表示 -->
    <div class="w-full md:w-6/12 mt-6">
      <!-- 店名 -->
      <div class="flex items-center">
        <a class="bg-white rounded shadow-md w-6 pl-2 font-bold" href="{{url('/') }}"> < </a>
        <h1 class="pl-2 font-bold text-2xl">{{ $shop['name'] }}</h1>
      </div>
      
      <!-- 画像 -->
      <div class="pt-5">
        <img class="w-full" src="{{ $shop['image_url'] }}">
      </div>

      <!-- 分類 -->
      <div class="pt-5">
        <span>#{{ $shop['region'] }}</span>
        <span>#{{ $shop['genre'] }}</span>
      </div>

      <!-- 説明 -->
      <div class="pt-5">
        <p>{{ $shop['description']}}</p>
      </div>

      <!-- 口コミ一覧へのリンク -->
      <div class="pt-5">
        <a href="{{ url('/review/shop_index/'.$shop['id']) }}" class="bg-blue-500 text-white block w-full py-2 text-center">全ての口コミ情報</a>
      </div>

      <!-- 自身の口コミを表示 -->
      @if( Auth::check() )
        @if( is_null($my_review) )
          <div class="py-5">
            <a href="{{ url('/review/add/'.$shop['id']) }}" class="underline">口コミを投稿する</a>
          </div>
        @else
          <div class="py-5">
            <hr>
            <div class="flex justify-end text-sm">
              <div class="mr-4">
                <a href="" class="underline">口コミを編集</a>
              </div>
              <div>
                <form method="POST" action="{{ url('/review/delete') }}">
                  @csrf
                  <input type="hidden" name="shop_id" value="{{ $shop['id'] }}">
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  <button type="submit" class="underline">口コミを削除</button>
                </form>
              </div>
            </div>
            <div>
                @for ($counter = 0; $counter < 5; $counter++)
                    <span class="{{ $counter < $my_review['star'] ? 'text-blue-600' : 'text-gray-200'}}">★</span>
                @endfor
            </div>
            <div>
              <p>{{$my_review['comment']}}</p>
            </div>
            <div class="w-56">
                @if (!is_null($my_review['image_url']))
                    <img src="{{ $my_review['image_url'] }}">
                @endif
            </div>
            <hr>
          </div>
        @endif
      @endif

    </div>

    <!-- 予約 -->
    <div class="w-full h-72 md:w-5/12 md:h-auto max-md:mt-5 bg-blue-600 rounded p-2 md:p-5 relative">
      <h1 class="text-white font-bold text-2xl">予約</h1>
      <p class="text-white">{{ $time_explanation }}</p>
      <!-- 日付の選択 -->
      <form method="GET" action="{{ url('/detail/'.$shop['id']) }}" name="calender_form">
        @csrf
        <input class="rounded py-1 mt-3" type="date" id="date" name="date" value="{{ $reserve_date }}" min="{{ $tomorrow }}" onchange="document.calender_form.submit()" />
      </form>
      
      <!-- 時間のドロップダウン -->
      <div class="flex items-center mt-3">
        <x-dropdown align="left" width="48">
            <x-slot name="trigger">
                <button class="flex items-center bg-white rounded hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                    <div class="w-48 text-left py-1 px-3">
                      <span id="reserve_time_trigger">{{ empty($time_array) ? "定休日" : $time_array[0] }}</span>
                    </div>

                    <div class="ml-1">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>
            <x-slot name="content">
              @foreach ($time_array as $time)
                <x-dropdown-link onclick="on_reserve_time_changed('{{$time}}')">{{$time}}</x-dropdown-link>
              @endforeach
            </x-slot>
        </x-dropdown>
      </div>

      <!-- 人数のドロップダウン -->
      <div class="flex items-center mt-3">
        <x-dropdown align="left" width="48">
            <x-slot name="trigger">
                <button class="flex items-center bg-white rounded hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                    <div class="w-48 text-left py-1 px-3">
                      <span id="reserve_num_trigger">{{ empty($num_array) ? "予約できません" : $num_array[0] . '人' }}</span>
                    </div>

                    <div class="ml-1">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>
            <x-slot name="content">
              @foreach ($num_array as $num)
                <x-dropdown-link onclick="on_reserve_num_changed('{{$num}}')">{{$num}}人</x-dropdown-link>
              @endforeach                  
            </x-slot>
        </x-dropdown>
      </div>

      <!-- 予約内容の表示 -->
      <div class="max-md:hidden bg-blue-500 rounded mt-4 p-4">
        <table class="text-white">
          <tr>
            <td>
              <span>店名</span>
            </td>
            <td class="pl-8">
              <span>{{ $shop['name'] }}</span>
            </td>
          </tr>
          <tr>
            <td>
              <span>日付</span>
            </td>
            <td class="pl-8">
              <span id="reserve_date_confirm">{{ $reserve_date }}</span>
            </td>
          </tr>
          <tr>
            <td>
              <span>時刻</span>
            </td>
            <td class="pl-8">
              <span id="reserve_time_confirm">{{ empty($time_array) ? '' : $time_array[0] }}</span>
            </td>
          </tr>
          <tr>
            <td>
              <span>人数</span>
            </td>
            <td class="pl-8">
              <span id="reserve_num_confirm">{{ empty($num_array) ? '' : $num_array[0] . '人' }}</span>
            </td>
          </tr>
        </table>
      </div>

      <form method="POST" action="{{ url('/reserve') }}" >
        @csrf
        <input type="hidden" id="reserve_shop_input" name="shop_id" value="{{ $shop['id'] }}">
        <input type="hidden" id="reserve_date_input" name="date" value="{{ $reserve_date }}">
        <input type="hidden" id="reserve_time_input" name="start_time" value="{{ empty($time_array) ? '' : $time_array[0] }}">
        <input type="hidden" id="reserve_num_input" name="number_of_people" value="{{ empty($num_array) ? '' : $num_array[0] }}">
        <input type="hidden" id="reserve_length" name="time_per_reservation" value="{{ $shop['time_per_reservation'] }}">
        <div class="text-red-600">
          @error('shop_id')
            ※{{ $message }} <BR>
          @enderror
          @error('date')
            ※{{ $message }} <BR>
          @enderror
          @error('start_time')
            ※{{ $message }} <BR>
          @enderror
          @error('number_of_people')
            ※{{ $message }} <BR>
          @enderror
          @error('time_per_reservation')
            ※{{ $message }} <BR>
          @enderror
        </div>
        <button type="submit" class="bg-blue-700 text-white disabled:text-blue-500 w-full py-4 rounded-b absolute bottom-0 left-0" {{ empty($time_array) ? 'disabled' : '' }}>予約する</button>
      </form>
      
    </div>

  </div>
</x-app-layout>