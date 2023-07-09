<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="m-6">
            {!! QrCode::size(300)->generate(url('/admin/reservations/detail', ['reservation_id' => $reservation_id])); !!}
        </div>
        <div>
            <a class="ml-6 block text-center text-blue-800 bg-white border-solid border border-blue-800 hover:bg-gray-200 rounded w-20" href="{{ url('/my_page') }}">戻る</a>
        </div>
    </div>
</x-app-layout>