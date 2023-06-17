<x-app-layout>
  <script>
    function on_reserve_num_changed(reserve_num) {
      show_txt = reserve_num + '人';
      document.getElementById('reserve_num_trigger').innerHTML = show_txt;
      document.getElementById('reserve_num_confirm').innerHTML = show_txt;
      document.getElementById('reserve_num_input').innerHTML = reserve_num;
    }
    function on_reserve_time_changed(reserve_time) {
      document.getElementById('reserve_time_trigger').innerHTML = reserve_time;
      document.getElementById('reserve_time_confirm').innerHTML = reserve_time;
      document.getElementById('reserve_time_input').innerHTML = reserve_time;
    }
  </script>

  <div class="max-w-7xl mx-auto px-4 lg:px-8 flex justify-between flex-wrap">
    
    <!-- 店の詳細表示 -->
    <div class="w-full sm:w-6/12 mt-6">
      <!-- 店名 -->
      <div class="flex items-center">
        <a class="bg-white rounded shadow-md w-6 pl-2 font-bold" href="{{url( '/') }}"> < </a>
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

    </div>

    <!-- 予約 -->
    <div class="w-full h-96 sm:w-5/12 sm:h-auto max-sm:mt-5 bg-blue-600 rounded p-2 sm:p-5 relative">
      <h1 class="text-white font-bold text-2xl">予約</h1>

      <!-- 日付の選択 -->
      <form method="GET" action="{{ url('/detail/'.$shop['id']) }}" name="calender_form">
        @csrf
        <input class="rounded py-1 mt-5" type="date" id="date" name="date" value="{{ $reserve_date }}" min="{{ $today }}" onchange="document.calender_form.submit()" />
      </form>
      
      <!-- 時間のドロップダウン -->
      <div class="flex items-center mt-3">
        <x-dropdown align="left" width="48">
            <x-slot name="trigger">
                <button class="flex items-center bg-white rounded hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                    <div class="w-48 text-left py-1 px-3">
                      <span id="reserve_time_trigger">{{ empty($time_array) ? "予約できません" : $time_array[0] }}</span>
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
      <div class="bg-blue-500 rounded mt-4 p-2 sm:p-4">
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

      <form action="" >
        <input type="hidden" id="reserve_shop_input" value="{{ $shop['id'] }}">
        <input type="hidden" id="reserve_date_input" value="{{ $reserve_date }}">
        <input type="hidden" id="reserve_time_input" value="{{ empty($time_array) ? '' : $time_array[0] }}">
        <input type="hidden" id="reserve_num_input" value="{{ empty($num_array) ? '' : $num_array[0] }}">
        <button class="bg-blue-700 text-white w-full py-4 rounded-b absolute bottom-0 left-0">予約する</button>  
      </form>
      
    </div>

  </div>
</x-app-layout>