<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AddAdminsCsv extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("管理者の作成を開始します．．．");
        $adminSplFileObject = new \SplFileObject(__DIR__ . '/data/admins.csv');
        $adminSplFileObject->setFlags(
            \SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE
        );

        $count = 0;
        foreach ($adminSplFileObject as $key => $row) {
            if ($key === 0) {
                continue;
            }

            $shop = Admin::create([
                'name' => trim($row[0]),
                'email' => trim($row[1]),
                'password' => trim($row[2]),
                'role' => 0
            ]);

            $count++;
        }

        $this->command->info("管理者を{$count}名、作成しました。");

    }
}
