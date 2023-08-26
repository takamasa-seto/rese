<?php

namespace App\Http\Controllers;

use Auth;
use DateTime;
use Arr;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;
use App\Models\Shop;
use App\Models\Favorite;
use App\Models\Table;
use App\Models\Review;
use App\Http\Requests\UpdateShopRequest;
use App\Http\Requests\AddShopRequest;
use App\Http\Traits\Content;
use App\Consts\SortOptConst;

class ShopController extends Controller
{
    use Content;
    /*
        店の一覧表示
    */
    public function index(Request $request)
    {
        $all_area = 'All area';     // 全地域のフィルタに表示する文字列
        $all_genre = 'All genre';     // 全ジャンルのフィルタに表示する文字列

        /* ドロップダウンリストに表示する文字列の生成 */
        $shops = Shop::all();
        $regions = array($all_area);
        $genres = array($all_genre);
        foreach ($shops as $shop) {
            $regions[] = $shop['region'];
            $genres[] = $shop['genre'];
        }
        $regions = array_unique($regions);
        $genres = array_unique($genres);

        /* お気に入り店リストを生成 */
        $favorites = array();
        if ( Auth::check() ) {
            $tmp_favorites = Favorite::select()->UserSearch(Auth::id())->get();
            foreach ($tmp_favorites as $tmp_favorite) {
                $favorites[] = $tmp_favorite['shop_id'];
            }
        }

        /* ソートオプションの取得 */
        $sort_option = session()->has('sort_option') ? session('sort_option') : SortOptConst::RANDOM;
        if( $request->has('sort_option') ) $sort_option = $request->sort_option;
        session(['sort_option' => $sort_option]);
        
        /* region検索キーの取得 */
        $search_region = session()->has('search_region') ? session('search_region') : $all_area;
        if( $request->has('search_region') ) $search_region = $request->search_region;
        session(['search_region' => $search_region]);
        if ($search_region == $all_area) $search_region = NULL;
        
        /* genre検索キーの取得 */
        $search_genre = session()->has('search_genre') ? session('search_genre') : $all_genre;
        if( $request->has('search_genre') ) $search_genre = $request->search_genre;
        session(['search_genre' => $search_genre]);
        if ($search_genre == $all_genre) $search_genre = NULL;
 
        $search_name = session()->has('search_name') ? session('search_name') : NULL;
        if( $request->has('search_name') ) {
            $search_name = $request->search_name;
            session(['search_name' => $search_name]);
        }

        /* 検索実行　*/
        $shops = Shop::select()
            ->RegionSearch($search_region)
            ->GenreSearch($search_genre)
            ->NameSearch($search_name);

        if (SortOptConst::RANDOM == $sort_option) {
            $shops = $shops->inRandomOrder();
            $shops = $shops->get();
        } elseif (SortOptConst::DESCENDING == $sort_option or SortOptConst::ASCENDING == $sort_option) {
            $shops = $shops->get();
            $shops = Arr::sort($shops, function($value) {
                $reviews = $value->reviews()->get();
                return $reviews->isEmpty() ? 0 : $reviews->avg('star');
            });  //Arr::sortはコレクションもソートできる.戻り値は配列
            if (SortOptConst::DESCENDING == $sort_option) $shops = array_reverse($shops);
        }
        /* viewに渡す用にデータを加工 */
        foreach ($shops as $shop) {
            $shop['image_url'] = Storage::url($shop['image_url']);
            $shop['favorite'] = in_array($shop['id'], $favorites);
        }

        return view('shop_index', compact('shops', 'regions', 'genres'));
    }

    /*
        店の予約時間リストを取得する（プライベート関数）
    */
    private function getTimeArray($date, $operation_pattern, $time_per_reservation)
    {
        $time_array = array();
        $explanation = "";
        $tmp_date = new DateTime($date);
        $w = (int)date_format( $tmp_date, 'w');
        $minus_min = $this->timeToMin($time_per_reservation);
        switch( $operation_pattern ) {
            case 1:
                $explanation = "営業時間11:00～22:00、月曜定休";
                if ( 1 != $w ) {
                    $start_time = new DateTime($tmp_date->format('Y-m-d'.' 11:00:00'));
                    $end_time = new DateTime($tmp_date->format('Y-m-d'.' 22:00:00'));
                    $end_time->modify('-'.$minus_min.' minutes');
                    for ($tmp_time = $start_time; $tmp_time <= $end_time; $tmp_time->modify('+30 minutes')) {
                        $time_array[] = $tmp_time->format('H:i');
                    }
                }
                break;
            case 2:
                $explanation = "営業時間17:00～23:00、年中無休";
                $start_time = new DateTime($tmp_date->format('Y-m-d'.' 17:00:00'));
                $end_time = new DateTime($tmp_date->format('Y-m-d'.' 23:00:00'));
                $end_time->modify('-'.$minus_min.' minutes');
                for ($tmp_time = $start_time; $tmp_time <= $end_time; $tmp_time->modify('+30 minutes')) {
                    $time_array[] = $tmp_time->format('H:i');
                }
                break;
        }
        return array($explanation, $time_array);
    }

    /*
        店の予約人数リストを取得する（プライベート関数）
    */
    private function getNumArray($shop_id)
    {
        $tables = Table::select()->ShopIdSearch($shop_id)->get();
        $max_num = 0;
        foreach( $tables as $table ) {
            if( $max_num < $table->seat_num ) {
                $max_num = $table->seat_num;
            }
        }
        
        $num_array = array();
        for( $i = 1; $i <= $max_num; $i++ ) {
            $num_array[] = $i;
        }

        return $num_array;
    }

    /*
        店の詳細表示
    */
    public function detail(Request $request, $shop_id)
    {
        $shop = Shop::find($shop_id);
        $shop['image_url'] = Storage::url($shop['image_url']);
        
        $tomorrow = now()->addDay()->format('Y-m-d');
        if( $request->has('date') ) {
            $reserve_date = $request->date;
        } else {
            $reserve_date = session()->has('date') ? session('date') : $tomorrow;
        }
        session(['date' => $reserve_date]);
        
        [$time_explanation, $time_array] = $this->getTimeArray($reserve_date, $shop->operation_pattern, $shop->time_per_reservation);
        $num_array = $this->getNumArray($shop['id']);

        $my_review = null;
        if( Auth::check() ){
            $tmp_review = Review::select()->UserSearch(Auth::id())->ShopSearch($shop_id)->get();
            $my_review = $tmp_review->isEmpty() ? null : $tmp_review->toArray()[0];
            if ( !is_null($my_review) ) {
                $my_review['image_url'] = empty($my_review['image_url']) ? null : Storage::url($my_review['image_url']);
            }
        }
        return view('shop_detail', compact('shop', 'tomorrow', 'reserve_date', 'time_explanation', 'time_array', 'num_array', 'my_review'));
    }

    /*
        ストレージに画像を保存する(プライベート)
    */
    private function myStoreImage($img_file)
    {
        $path = '';
        if(isset($img_file)) {
            $filename = $img_file->getClientOriginalName();
            $path = 'rese\image\\'.$filename;
            if('local' == env('FILESYSTEM_DRIVER')) {
                Storage::putFileAs('', $img_file, 'public\\'.$path);
            } else {
                Storage::putFileAs('', $img_file, $path);
            }
            
        }
        return $path;
    }

    /*
        店舗情報の更新
    */
    public function update(UpdateShopRequest $request)
    {
        if (!$this->isShopStaff(Auth::user()->role)) return redirect('admin/login');

        //ストレージに画像を登録
        $image = $request->file('image_file');
        $path = $this->myStoreImage($image);

        //更新情報を作成
        $update_info = [
            'name' => $request->name,
            'region' => $request->region,
            'genre' => $request->genre,
            'description' => $request->description
        ];
        if(!empty($path)) $update_info['image_url'] = $path;

        //更新
        $shop = Shop::find($request->id);
        $shop->update($update_info);

        $message = '店舗情報を更新しました。';   
        return redirect('/admin/edit') ->with('message', $message);
    }


    /*
        店舗情報の追加
    */
    public function store(AddShopRequest $request)
    {
        if (!$this->isShopStaff(Auth::user()->role)) return redirect('admin/login');
        
        //ストレージに画像を登録
        $image = $request->file('image_file');
        $path = $this->myStoreImage($image);

        //店舗情報を作成
        $new_info = [
            'name' => $request->name,
            'region' => $request->region,
            'genre' => $request->genre,
            'operation_pattern' => $request->operation_pattern,
            'time_per_reservation' => $request->time_per_reservation,
            'description' => $request->description
        ];
        $new_info['image_url'] = empty($path)? null: $path;

        //追加
        $shop = Shop::create($new_info);

        // テーブル（座席）を追加
        if ( isset($request->tables) ) {
            foreach( $request->tables as $key => $value ) {
                Table::create([
                    'shop_id' => $shop->id,
                    'name' => $key,
                    'seat_num' => $value
                ]);
            }
        }

        // 自身を管理者に登録
        $admin = Admin::find(Auth::user()->id);
        if ( 1 == $admin->role ) {
            //店舗責任者の場合は店舗を登録
            $admin->shops()->syncWithoutDetaching([$shop->id]);
        }

        $message = '店舗を新規登録しました。';   
        return redirect('/admin/new_shop') ->with('message', $message);
    }

    /*
        店舗情報のCSVインポート画面表示(管理者用)
    */
    public function createCsvImporter(Request $request)
    {
        if ( !Auth::guard('admin')->check() ) return redirect('admin/login'); 
        if (!$this->isAdmin(Auth::guard('admin')->user()->role)) return redirect('admin/login');

        return view('/admin/shop_csv_importer');
    }

    /*
        CSVファイルのヘッダ定義
    */
    const CSV_HEADER = [
        "name",
        "region",
        "genre",
        "description",
        "image_url",
        "operation_pattern",
        "time_per_reservation",
        "table_sixteen_num",
        "table_eight_num",
        "table_four_num",
        "table_two_num",
        "table_one_num"
    ];

    /* CSVの各レコードのバリデーション */
    private function csvValidate($data)
    {
        // バリデーションルール
        $rules = [
            'name' => ['required', 'string', 'max:50'],
            'region' => ['required', Rule::in(['東京都', '大阪府', '福岡県'])],
            'genre' => ['required', Rule::in(['寿司', '焼肉', 'イタリアン', '居酒屋', 'ラーメン'])],
            'description' => ['required', 'string', 'max:400'],
            'image_url' => ['active_url',  'ends_with:.jpeg,.jpg,.png'],
            'operation_pattern' => ['required', 'numeric', 'between:1,2'],
            'time_per_reservation' => ['required', 'date_format:H:i'],
            'table_sixteen_num' => ['required', 'numeric', 'between:0,255'],
            'table_eight_num' => ['required', 'numeric', 'between:0,255'],
            'table_four_num' => ['required', 'numeric', 'between:0,255'],
            'table_two_num' => ['required', 'numeric', 'between:0,255'],
            'table_one_num' => ['required', 'numeric', 'between:0,255']
        ];

        // バリデーション対象項目
        $attributes = [
            'name' => '店舗名',
            'region' => '地域',
            'genre' => 'ジャンル',
            'description' => '店舗概要',
            'image_url' => '画像URL',
            'operation_pattern' => '営業パターン',
            'time_per_reservation' => '予約確保時間',
            'table_sixteen_num' => '16名席数',
            'table_eight_num' => '8名席数',
            'table_four_num' => '4名席数',
            'table_two_num' => '2名席数',
            'table_one_num '=> '1名席数'
        ];

        $error_list = [];  //エラーのリスト

        // 各行でバリデーションを行う
        foreach ($data as $row => $value) {
            $validator = Validator::make($value->toArray(), $rules, __('validation'), $attributes);
            //エラー時
            if($validator->fails()) {
                //エラーメッセージを「xx行目:エラーメッセージ」にする
                $num = $row + 1;
                $error_msg = array_map(fn($message) => "{$num}行目: {$message}", $validator->errors()->all());
                $error_list = array_merge($error_list, $error_msg);
            }
        }

        if(!empty($error_list)) {
            //配列を改行コード区切りの文字列にする
            $excep_msg = implode("\n", $error_list);
            throw new Exception($excep_msg);
        }

        return;
    }

    /*
        CSVファイルからの店舗情報登録(管理者用)
    */
    public function storeFromCsv(Request $request)
    {
        if ( !Auth::guard('admin')->check() ) return redirect('admin/login'); 
        if (!$this->isAdmin(Auth::guard('admin')->user()->role)) return redirect('admin/login');

        try {
            $this->mySaveCsv($request);
            $data = $this->myCsv2Collection();
            $data = $this->myGetHeaderCollection($data);
            $this->csvValidate($data);
            $this->myImageUrlDownload($data);           
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return redirect('/admin/shop_csv_importer')->with('error', $error_msg);
        }

        //データベースに登録
        foreach( $data as $value) {
            //店舗の登録
            $shop_info = $value->only([
                'name',
                'region',
                'genre',
                'description',
                'image_url',
                'operation_pattern',
                'time_per_reservation'
            ])->toArray();
            $shop = Shop::create($shop_info);

            //テーブル(座席)の追加
            $this->tableAdd($shop->id, $value['table_sixteen_num'], 16);
            $this->tableAdd($shop->id, $value['table_eight_num'], 8);
            $this->tableAdd($shop->id, $value['table_four_num'], 4);
            $this->tableAdd($shop->id, $value['table_two_num'], 2);
            $this->tableAdd($shop->id, $value['table_one_num'], 1);

        }
        $message = $request->file('csv_file')->getClientOriginalName()."からのインポートに成功しました";
        return redirect('/admin/shop_csv_importer')->with('message', $message);
    }

    /*
        CSVファイルのストレージ保存(プライベート関数)
    */
    private function mySaveCsv(Request $request)
    {
        // ファイルを保存
        if($request->hasFile('csv_file')) {
            $csv_file = $request->file('csv_file');
            if($csv_file->getClientOriginalExtension() !== "csv") {
                throw new Exception("拡張子が不正です。");
            }
            $path = 'rese\shops.csv';
            if('local' == env('FILESYSTEM_DRIVER')) {
                Storage::putFileAs('', $csv_file, 'public\\'.$path);
            } else {
                Storage::putFileAs('', $csv_file, $path);
            }
        } else {
            throw new Exception("CSVファイルの取得に失敗しました。");
        }
    }

    /*
        CSVファイルの情報をコレクションに格納(プライベート関数)
    */
    private function myCsv2Collection()
    {
        // ファイル内容取得
        $path = 'rese\shops.csv';
        if('local' == env('FILESYSTEM_DRIVER')) {
            $csv = Storage::get('public\\'.$path);
        } else {
            $csv = Storage::get($path);
        }
        // 改行コードを統一
        $csv = str_replace(array("\r\n","\r"), "\n", $csv);
        // 行単位のコレクション作成
        $data = collect(explode("\n", $csv))->reject(function ($name) {
            return empty($name);  //空の要素を削除
        });

        return $data;
    }

    /*
        コレクション情報をHEADER情報をkeyにもつコレクションに変換(プライベート関数)
    */
    private function myGetHeaderCollection($data)
    {
        // header作成と項目数チェック
        $header = collect(self::CSV_HEADER);
        $fileHeader = collect(explode(",", $data->shift()));
        if($header->count() !== $fileHeader->count()) {
            throw new Exception("項目数エラー");
        }
        
        // 連想配列のコレクションを作成
        try {
            $file_data = $data->map(function ($oneline) use ($header) {
                return $header->combine(collect(explode(",", $oneline)));
            });
        } catch (Exception $e) {
            throw new Exception("項目数エラー");
        }

        return $file_data;
    }

    /*
        画像URLからファイルをダウンロードしてストレージ保存
        画像URLのカラムをストレージのURLに差し替える(プライベート関数)
    */
    private function myImageUrlDownload($data)
    {
        foreach($data as $row => $value) {
            $num = $row + 1;
            
            //ファイル名と拡張子名を取得
            $flname = substr($value['image_url'], strrpos($value['image_url'], '/') + 1);
            $ext = substr($value['image_url'], strrpos($value['image_url'], '.') + 1);
            //画像をダウンロード(失敗するとfalseが返ってくる)
            try {
                $img_file = file_get_contents($value['image_url']);
            } catch (Exception $e) {
                throw new Exception("{$num}行目: 画像URLのファイル読込に失敗しました。");
            }
            if($img_file) {
                $path = 'rese\image\\'.$flname;
                if('local' == env('FILESYSTEM_DRIVER')) {
                    Storage::put('public\\'.$path, $img_file, );
                } else {
                    Storage::put($path, $img_file);
                }
                $value['image_url'] = $path;
            } else {
                throw new Exception("{$num}行目: 画像URLのファイル読込に失敗しました。");
            }
        }

        return;
    }

    /*
        テーブル数、座席数に応じたテーブルをデータベースに登録する(プライベート関数)
    */
    private function tableAdd($shop_id, $table_num, $seat_num)
    {
        for($id = 1; $id <= $table_num; $id++) {
            Table::create([
                'shop_id' => $shop_id,
                'name' => "{$seat_num}名様席{$id}",
                'seat_num' => $seat_num
            ]);
        }
    }

}
