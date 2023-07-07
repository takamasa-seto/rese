<x-admin-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <!-- お知らせメールの作成 -->
        <div>
            <h1 class="text-xl">お知らせメールの作成</h1>
            <div class="ml-3">
              <form method="POST" action="{{ url('admin/send') }}">
                @csrf
                <div>
                    {{ session('message') }}
                </div>
                <div>
                    <label for="title" class="block">タイトル</label>
                    <input type="text" name="title" class="ml-3" value="">
                </div>
                <div>
                    <label for="content" class="block">内容</label>
                    <textarea name="content" class="w-72 h-32 ml-3"></textarea>
                </div>
                <div class="my-3 mx-auto">
                    <button type="submit" class="text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20">送信</button>
                </div>
              </form>
              <div class="my-3 mx-auto">
                    <a class="block text-center text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20" href="{{ url('admin/index') }}">戻る</a>
              </div>
            </div>
        </div>
    </div>
</x-admin-layout>