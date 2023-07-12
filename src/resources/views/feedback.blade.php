<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <!-- フィードバックの入力 -->
        <div>
            <h1 class="text-xl">フィードバック情報の入力</h1>
            <div class="ml-3">
                <form method="POST" action="{{ url('feedback/store') }}">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation['id'] }}">
                    <div>
                        <h2>店舗名</h2>
                        <p class="ml-6">{{ $shop['name'] }}</p>
                    </div>
                    <div>
                        <h2>来店日時</h2>
                        <p class="ml-6">{{ $reservation['start_time'] }}</p>
                    </div>
                    <div>
                        <h2>スコア(5段階評価)</h2>
                        <select class="ml-6" name="score">
                            @for ( $i = 1; $i < 6; $i++ )
                            <option value="{{ $i }}" 
                            @if ( $reservation->feedback )
                                @if ( $i == $reservation->feedback->score )
                                    selected
                                @endif
                            @else
                                @if ( $i == 3 )
                                    selected
                                @endif
                            @endif>
                                {{ $i }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <h2>コメント</h2>
                        <textarea name="comment" class="w-72 h-32 ml-6">{{ $reservation->feedback?$reservation->feedback->comment:"" }}</textarea>
                    </div>
                    <div class="my-3 mx-auto">
                        <button type="submit" class="text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20">送信</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>