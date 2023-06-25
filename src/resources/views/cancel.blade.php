<x-app-layout>
  <div class="max-w-7xl mx-auto px-4 lg:px-8">
    <div class="w-64 mx-auto bg-white rounded-md shadow-md pt-6">
      <p class="text-center">ご予約をキャンセルします</p>
        <table class="mx-auto mt-3">
          <tr>
            <td>
              <span>店名</span>
            </td>
            <td class="pl-8">
              <span>{{ $shop_name }}</span>
            </td>
          </tr>
          <tr>
            <td>
              <span>日付</span>
            </td>
            <td class="pl-8">
              <span>{{ $date }}</span>
            </td>
          </tr>
          <tr>
            <td>
              <span>時刻</span>
            </td>
            <td class="pl-8">
              <span>{{ $start_time }}</span>
            </td>
          </tr>
          <tr>
            <td>
              <span>人数</span>
            </td>
            <td class="pl-8">
              <span>{{ $number_of_people }}</span>
            </td>
          </tr>
        </table>
      <div class="flex justify-between py-6 px-12">
        <div>
          <form method="POST" action="{{ url('/reserve/delete') }}">
            @csrf
            <input type="hidden" name="reservation_id" value="{{ $reservation_id }}">
            <button class="h-8 rounded-md bg-blue-600 text-white px-3" type="submit">
              確認
            </button>
          </form>
        </div>
        <div>
          <a class="block h-8 rounded-md bg-blue-600 text-white px-3 pt-1" href="{{ url('/my_page') }}">戻る</a>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>