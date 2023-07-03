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
                  @foreach ($shop_list as $shop)
                  <form method="GET" action="{{ url('/admin/reservations') }}">
                      @csrf
                      <input type="hidden" name="shop_index" value="{{ $shop }}">
                      <button class="block text-gray-500 hover:text-gray-700 focus:text-gray-700" type="submit">
                          {{ array_search($shop, $shop_list) }}
                      </button>
                  </form>
                  @endforeach
                </x-slot>
            </x-dropdown>
        </div>
        <!-- 予約一覧 -->
        <div>
            <h1>予約一覧</h1>
            <table>
                <tr class="border-t border-black [&>th]:text-left [&>th]:p-4">
                    <th>予約者</th>
                    <th>開始時刻</th>
                    <th>人数</th>
                    <th>テーブル</th>
                </tr>
                @foreach ($reservations as $reservation)
                <tr class="border-t border-black [&>td]:text-left [&>td]:p-4">
                    <td class="text-ellipsis overflow-hidden whitespace-nowrap hover:whitespace-normal hover:break-words">{{ $reservation['user_name'] }}</td>
                    <td class="text-ellipsis overflow-hidden whitespace-nowrap hover:whitespace-normal hover:break-words">{{ $reservation['start_time'] }}</td>
                    <td class="text-ellipsis overflow-hidden whitespace-nowrap hover:whitespace-normal hover:break-words">{{ $reservation['number_of_people'] }}</td>
                    <td class="text-ellipsis overflow-hidden whitespace-nowrap hover:whitespace-normal hover:break-words">
                        @foreach ($reservation['tables'] as $table)
                        {{ $table }}<br>
                        @endforeach
                    </td>
                </tr>
                @endforeach
                
            </table>
        </div>
    </div>
</x-admin-layout>