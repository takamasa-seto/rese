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

    <!-- 予約変更 -->
    <div class="w-full h-72 md:w-5/12 md:h-auto max-md:mt-5 bg-blue-600 rounded p-2 md:p-5">
      <h1 class="text-white font-bold text-2xl">予約変更</h1>
      <p class="text-white">{{ $shop['name'] }}</p>
      <p class="text-white">{{ $time_explanation }}</p>
      <!-- 日付の選択 -->
      <form method="GET" action="{{ url('/reserve/edit') }}" name="calender_form">
        @csrf
        <input class="rounded py-1 mt-3" type="date" id="date" name="date" value="{{ $reserve_date }}" min="{{ $today }}" onchange="document.calender_form.submit()" />
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
      <div class="mt-4 flex justify-start">
        <form method="POST" action="{{ url('/reserve/update') }}" >
          @csrf
          <input type="hidden" id="reserve_id" name="reservation_id" value="{{ $reservation_id }}">
          <input type="hidden" id="reserve_shop_input" name="shop_id" value="{{ $shop['id'] }}">
          <input type="hidden" id="reserve_date_input" name="date" value="{{ $reserve_date }}">
          <input type="hidden" id="reserve_time_input" name="start_time" value="{{ empty($time_array) ? '' : $time_array[0] }}">
          <input type="hidden" id="reserve_num_input" name="number_of_people" value="{{ empty($num_array) ? '' : $num_array[0] }}">
          <input type="hidden" id="reserve_length" name="time_per_reservation" value="{{ $shop['time_per_reservation'] }}">
          <button type="submit" class="text-white bg-blue-600 border-solid border border-white hover:bg-gray-200 rounded w-20" {{ empty($time_array) ? 'disabled' : '' }}>変更</button>
        </form>
        <form method="GET" action="{{ url('/reserve/cancel') }}">
          <input type="hidden" id="reserve_id" name="reservation_id" value="{{ $reservation_id }}">
          <button type="submit" class="ml-4 text-white bg-blue-600 border-solid border border-white hover:bg-gray-200 rounded w-20">キャンセル</button>        
        </form>
        <a class="ml-4 text-center text-white bg-blue-600 border-solid border border-white hover:bg-gray-200 rounded w-20" href="{{ url('/my_page') }}">戻る</a>
      </div>
    </div>

  </div>
</x-app-layout>