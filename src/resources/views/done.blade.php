<x-app-layout>
  <div class="max-w-7xl mx-auto px-4 lg:px-8">
    <div class="w-64 h-48 mx-auto bg-white rounded-md shadow-md pt-12">
      @if ($is_succeeded)
        <p class="text-center">
          {{ $is_update ? "ご予約を変更しました。" : "ご予約ありがとうございます。" }}
        </p>
        <div class="text-center mt-8">
          <a class="text-xs h-6 rounded-md bg-blue-600 text-white px-3 py-1" href="{{ url('/my_page') }}">マイページへ</a>
        </div>
      @else
        <p class="text-center">定員のためご予約できませんでした。</p>
        <div class="text-center mt-8">
          <a class="text-xs h-6 rounded-md bg-blue-600 text-white px-3 py-1" href="{{ url('/detail/'.$shop_id) }}">戻る</a>
        </div>
      @endif
      
    </div>
  </div>
</x-app-layout>