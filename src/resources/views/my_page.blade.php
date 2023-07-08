<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
      <h1 class="text-xl">{{ Auth::user()->name }}さん</h1>
      <div class="w-full md:flex md:justify-between">
        <!-- 予約状況 -->
        <div class="md:w-5/12">
          <h2 class="text-xl mb-2 mt-4">予約状況</h2>
          <!-- 予約カード一覧 -->
          <div class="flex justify-between flex-wrap">
            @foreach ($reservations as $reservation)
              <div class="w-4/5 h-48 bg-blue-600 text-white rounded-md shadow-md mb-4 px-3 py-3 relative">
                <div class="flex justify-between">
                    <h3>予約{{  $reservation['reservation_num'] }}</h3>
                    <!-- QRコード表示 -->
                    <form method="GET" action="{{ url('/qr_code') }}">
                        <input type="hidden" name="reservation_id" value="{{ $reservation['id'] }}">
                        <button type="submit" class="text-sm text-white bg-blue-600 border-solid border border-white hover:bg-gray-200 rounded w-16">QRコード</button>
                    </form>
                </div>
                <table class="ml-3">
                  <tr>
                    <td>
                      <span>店名</span>
                    </td>
                    <td class="pl-8">
                      <span>{{ $reservation['shop_name'] }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <span>日付</span>
                    </td>
                    <td class="pl-8">
                      <span>{{ $reservation['date'] }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <span>時刻</span>
                    </td>
                    <td class="pl-8">
                      <span>{{ $reservation['start_time'] }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <span>人数</span>
                    </td>
                    <td class="pl-8">
                      <span>{{ $reservation['number_of_people'] }}</span>
                    </td>
                  </tr>
                </table>
                <form method="GET" action="{{ url('/reserve/edit') }}">
                  <input type="hidden" name="reservation_id" value="{{ $reservation['id'] }}">
                  <button type="submit" class="bg-blue-700 text-white disabled:text-blue-500 w-full py-2 rounded-b absolute bottom-0 left-0">変更</button>
                </form>

              </div>
            @endforeach
          </div>
        </div>
        <!-- お気に入り店舗 -->
        <div class="md:w-7/12">
          <h2 class="text-xl mb-2 mt-4">お気に入り店舗</h2>
          <!-- お気に入りカード一覧 -->
          <div class="flex justify-between flex-wrap">
            @foreach ($favorites as $shop)
              <div class="w-56 bg-white rounded-md shadow-md mb-4">
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
                              <form method="POST" action="{{ url('/favorite') }}">
                                  @csrf
                                  <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                                  <input type="hidden" name="shop_id" value="{{ $shop['id'] }}">
                                  <button class="text-2xl {{ $shop['favorite'] ? 'text-red-500' : 'text-gray-100' }}" type="submit">&#9829;</button>
                              </form>
                          @endif
                      </div>
                  </div>            
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
</x-app-layout>