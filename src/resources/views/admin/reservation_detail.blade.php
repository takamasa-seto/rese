<x-admin-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <!-- 予約詳細 -->
        <div>
            <h1 class="text-xl">予約詳細</h1>
            <div class="mt-3">
                <p>[店名]</p>
                <p class="ml-3">{{$shop['name']}}</p>
            </div>
            <div class="mt-3">
                <p>[予約者]</p>
                <p class="ml-3">{{$user['name']}}</p>
            </div>
            <div class="mt-3">
                <p>[メール]</p>
                <p class="ml-3">{{$user['email']}}</p>
            </div>
            <div class="mt-3">
                <p>[開始時刻]</p>
                <p class="ml-3">{{$reservation['start_time']}}</p>
            </div>
            <div class="mt-3">
                <p>[座席]</p>
                @foreach ($reservation->tables as $table)
                <p class="ml-3">{{$table['name']}}</p>
                @endforeach
            </div>
            <div class="mt-3">
                <P>[スコア]</p>
                <p class="ml-3">
                    {{$reservation->feedback?$reservation->feedback->score:"未入力"}}
                </p>
            </div>
            <div class="mt-3">
                <P>[コメント]</p>
                <p class="ml-3">
                    {{$reservation->feedback?$reservation->feedback->comment:"未入力"}}
                </p>
            </div>
            <div class="mt-3 ml-3"">
                <a class="block text-center text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20" href="{{ url('/admin/reservations') }}">戻る</a>
            </div>
        </div>
    </div>
</x-admin-layout>