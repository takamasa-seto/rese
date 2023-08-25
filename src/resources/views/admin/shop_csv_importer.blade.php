<x-admin-layout>

    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <!-- CSVのアップロード -->
        <div>
            <h1 class="text-xl">店舗情報のCSV一括登録</h1>
            <div class="ml-3">
              <form method="POST" action="{{ url('admin/store_from_csv') }}" enctype="multipart/form-data" id="shop_form">
                @csrf
                <label for="csv_file" class="block">CSVファイルの指定</label>
                <input type="file" name="csv_file"  class="ml-3">
                <div class="mt-3">
                    <div>
                        {{ session('message') }}
                    </div>
                    <div class="text-red-600">
                        {!! nl2br(e(session('error'))) !!}
                    </div>
                </div>
                <div class="mx-auto">
                    <button type="submit" class="text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20">一括登録</button>
                </div>
              </form>
            </div>
        </div>
    </div>
</x-admin-layout>