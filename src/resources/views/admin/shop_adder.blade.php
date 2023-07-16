<x-admin-layout>
    <script>
        // テーブル情報を追加するスクリプト
        function add_table() {
            //座席名と人数の取得
            var tbl_name = document.getElementById("tblname").value;
            var tbl_num = document.getElementById("tblnum").value;
            // p 要素の作成と属性の指定
            var newAnchor = document.createElement("p");
            var newTxt = document.createTextNode( tbl_name + ':' + tbl_num);
            newAnchor.appendChild( newTxt );
        
            // li 要素の作成
            var newLi = document.createElement("li");
            newLi.appendChild ( newAnchor );
            var list = document.getElementById("TblList");
            list.appendChild( newLi );
        
            // フォームに追加
            frm = document.getElementById("shop_form");
            var newInput = document.createElement("input");
            newInput.setAttribute('type', 'hidden');
            newInput.setAttribute('name', 'tables[' + tbl_name + ']');
            newInput.setAttribute('value', tbl_num);
            frm.appendChild( newInput );
        }
    </script>

    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <!-- 店舗情報の追加 -->
        <div>
            <h1 class="text-xl">店舗情報の新規作成</h1>
            <div class="ml-3">
              <form method="POST" action="{{ url('admin/shop_add') }}" enctype="multipart/form-data" id="shop_form">
                @csrf
                <div>
                    {{ session('message') }}
                </div>
                <div>
                    <label for="name" class="block">店舗名</label>
                    <input type="text" name="name" class="ml-3" value="{{ old('name') }}">
                    <div class="text-red-600">
                        @error('name')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="region" class="block">地域</label>
                    <input type="text" name="region" class="ml-3" value="{{ old('region') }}">
                    <div class="text-red-600">
                        @error('region')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="genre" class="block">ジャンル</label>
                    <input type="text" name="genre" class="ml-3" value="{{ old('genre') }}">
                    <div class="text-red-600">
                        @error('genre')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="operation_pattern" class="block">営業パターン</label>
                    <input type="number" min=0 name="operation_pattern" class="ml-3 w-16" value="{{ old('operation_pattern', 1) }}">
                    <div class="text-red-600">
                        @error('operation_pattern')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="time_per_reservation" class="block">1予約あたりの座席確保時間</label>
                    <input type="time" min="00:30" max="05:00" name="time_per_reservation" class="ml-3" value="{{ old('time_per_reservation', '02:00') }}">
                    <div class="text-red-600">
                        @error('time_per_reservation')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="description" class="block">店舗概要</label>
                    <textarea name="description" class="w-72 h-32 ml-3">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label for="image_file" class="block">画像ファイルの登録(アップロード)</label>
                    <input type="file" name="image_file"  class="ml-3">
                    <div class="text-red-600">
                        @error('image_file')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="block">座席の登録(座席名:定員)</label>
                    <input type="text" id="tblname" class="ml-3">
                    <input type="number" min=0 id="tblnum" class="ml-3 w-16">
                    <input type="button" onclick="add_table();" class="ml-3 text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20" value="座席追加">
                    <ul class="ml-3" id="TblList"></ul>
                    <div class="text-red-600">
                        @error('tables')
                            ※{{ $message }}
                        @enderror
                    </div>
                    <div class="text-red-600">
                        @error('tables.*')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="my-3 mx-auto">
                    <button type="submit" class="text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20">登録</button>
                </div>
              </form>
            </div>
        </div>
    </div>
</x-admin-layout>