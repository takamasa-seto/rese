<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\Table;

class AddShopsCsv extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("店舗の作成を開始します．．．");
        $shopSplFileObject = new \SplFileObject(__DIR__ . '/data/shops.csv');
        $shopSplFileObject->setFlags(
            \SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE
        );

        $count = 0;
        foreach ($shopSplFileObject as $key => $row) {
            if ($key === 0) {
                continue;
            }

            $shop = Shop::create([
                'name' => trim($row[0]),
                'region' => trim($row[1]),
                'genre' => trim($row[2]),
                'description' => trim($row[3]),
                'image_url' => trim($row[4]),
                'operation_pattern' => trim($row[5]),
                'time_per_reservation' => trim($row[6])
            ]);

            for ($i = 1; $i <= trim($row[7]); $i++) {
                Table::create([
                    'shop_id' => $shop->id,
                    'name' => '16名様席' . (string)$i,
                    'seat_num' => 16
                ]);
            }
            for ($i = 1; $i <= trim($row[8]); $i++) {
                Table::create([
                    'shop_id' => $shop->id,
                    'name' => '8名様席' . (string)$i,
                    'seat_num' => 8
                ]);
            }
            for ($i = 1; $i <= trim($row[9]); $i++) {
                Table::create([
                    'shop_id' => $shop->id,
                    'name' => '4名様席' . (string)$i,
                    'seat_num' => 4
                ]);
            }
            for ($i = 1; $i <= trim($row[10]); $i++) {
                Table::create([
                    'shop_id' => $shop->id,
                    'name' => '2名様席' . (string)$i,
                    'seat_num' => 2
                ]);
            }
            for ($i = 1; $i <= trim($row[11]); $i++) {
                Table::create([
                    'shop_id' => $shop->id,
                    'name' => 'カウンター席' . (string)$i,
                    'seat_num' => 1
                ]);
            }

            $count++;
        }

        $this->command->info("店舗を{$count}件、作成しました。");

    }
}
