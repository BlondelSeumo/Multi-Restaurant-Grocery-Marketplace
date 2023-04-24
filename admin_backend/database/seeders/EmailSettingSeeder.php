<?php

namespace Database\Seeders;

use App\Models\EmailSetting;
use Illuminate\Database\Seeder;

class EmailSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'smtp_auth'     => '1',
                'smtp_debug'    => '0',
                'host'          => 'ssl://smtp.gmail.com',
                'port'          => '465',
                'password'      => 'password',
                'from_to'       => 'gmail@gmail.com',
                'from_site'     => 'foodyman.vercel.app',
                'ssl'           => ['ssl' => [
                                        'verify_peer'       => false,
                                        'verify_peer_name'  => false,
                                        'allow_self_signed' => true
                                    ]],
            ],
            [
                'smtp_auth'     => '1',
                'smtp_debug'    => '0',
                'host'          => 'ssl://smtp.yandex.ru',
                'port'          => '465',
                'password'      => 'password',
                'from_to'       => 'yandex@yandex.ru',
                'from_site'     => 'foodyman.vercel.app',
                'ssl'           => ['ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true
                    ]],
            ],
        ];

        foreach ($data as $item) {
            EmailSetting::create([
                'smtp_auth'     => data_get($item, 'smtp_auth'),
                'smtp_debug'    => data_get($item, 'smtp_debug'),
                'host'          => data_get($item, 'host'),
                'port'          => data_get($item, 'port'),
                'password'      => data_get($item, 'password'),
                'from_to'       => data_get($item, 'from_to'),
                'from_site'     => data_get($item, 'from_site'),
                'ssl'           => data_get($item,'ssl', ['ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]]),
            ]);
        }
    }
}
