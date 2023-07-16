<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'shop_id' => ['required', 'numeric'],
            'date' => ['required', 'date_format:Y-m-d', 'after:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'number_of_people' => ['required', 'numeric', 'min:1'],
            'time_per_reservation' => ['required', 'date_format:H:i:s']
        ];
    }

    public function messages()
    {
        return [
            'shop_id.required' => '店舗IDを入力してください',
            'shop_id.numeric' => '店舗IDは整数を入力してください',
            'date.required' => '日付を入力してください',
            'date.date_format' => '日付はY-m-dフォーマットで入力してください',
            'date.after' => '日付は本日より後の日付を入力してください',
            'start_time.required' => '開始時間を入力してください',
            'start_time.date_format' => '開始時間はH:iフォーマットで入力してください',
            'number_of_people.required' => '予約人数を入力してください',
            'number_of_people.numeric' => '予約人数は整数を入力してください',
            'number_of_people.min' => '予約人数は1人以上を入力してください',
            'time_per_reservation.required' => '予約時間の長さを入力してください',
            'time_per_reservation.date_format' => '予約時間の長さはH:i:sフォーマットで入力してください'
        ];
    }
}
