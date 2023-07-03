<x-admin-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <!--管理者登録-->
        <div>
            <h1 class="text-lg">管理者登録</h1>
            <form method="POST" action="{{ url('admin/add') }}" class="p-3">
                @csrf
                <div>
                    {{ session('message') }}
                </div>
                <div class="mb-1">
                    <label for="name" class="inline-block w-12">名前</label>
                    <input type="text" name="name" value="{{ old('name') }}">
                    <div class="text-red-600">
                        @error('name')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="mb-1">
                    <label for="email" class="inline-block w-12">Email</label>
                    <input type="text" name="email" value="{{ old('email') }}">
                    <div class="text-red-600">
                        @error('email')
                            ※{{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="mb-1">
                    <label for="role" class="inline-block w-12">役割</label>
                    <select name="role">
                        @foreach ( App\Consts\RoleConst::ROLE_LIST as $key => $val )
                        <option value="{{ $key }}" @if($key == old('role')) selected @endif>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label for="shop" class="inline-block w-12">店舗</label>
                    <select name="shop">
                        @foreach ( $shop_list as $key => $value )
                        <option value="{{ $value }}" @if($value === (int)old('shop')) selected @endif>{{ $key }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ml-8">
                    <button class="text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20" type="submit">
                        登録
                    </button>
                </div>
            </form>
        </div>
        <!--管理者一覧-->
        <div class="mt-3">
            <h1 class="text-lg">管理者一覧</h1>
            <table>
                <tr class="border-t border-black [&>th]:text-left [&>th]:p-4">
                    <th>名前</th>
                    <th>Email</th>
                    <th>役割</th>
                    <th>店舗</th>
                </tr>
                @foreach ($admin_list as $admin)
                <tr class="border-t border-black [&>td]:text-left [&>td]:p-4">
                    <td class="text-ellipsis overflow-hidden whitespace-nowrap hover:whitespace-normal hover:break-words">{{ $admin['name'] }}</td>
                    <td class="text-ellipsis overflow-hidden whitespace-nowrap hover:whitespace-normal hover:break-words">{{ $admin['email'] }}</td>
                    <td class="text-ellipsis overflow-hidden whitespace-nowrap hover:whitespace-normal hover:break-words">{{ App\Consts\RoleConst::ROLE_LIST[$admin['role']] }}</td>
                    <td class="text-ellipsis overflow-hidden whitespace-nowrap hover:whitespace-normal hover:break-words">
                        @foreach ($admin['shops'] as $shop)
                        {{ $shop['shop_name'] }}<br>
                        @endforeach
                    </td>
                    <td>
                        <form method="POST" action="{{ url('admin/delete') }}">
                            @csrf
                            <input type="hidden" name="admin_id" value="{{ $admin['id'] }}">
                            <button class="text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20" type="submit">
                                削除
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                
            </table>
        </div>
    </div>

</x-admin-layout>
