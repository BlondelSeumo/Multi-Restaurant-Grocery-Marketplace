<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Shop;
use App\Models\ShopTag;
use App\Models\ShopTranslation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 101,
                'uuid' => Str::uuid(),
                'firstname' => 'Admin',
                'lastname' => 'Admin',
                'email' => 'admin@githubit.com',
                'phone' => '998911902494',
                'birthday' => '1991-08-10',
                'gender' => 'male',
                'email_verified_at' => now(),
                'password' => bcrypt('githubit'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 102,
                'uuid' => Str::uuid(),
                'firstname' => 'User',
                'lastname' => 'User',
                'email' => 'user@gmail.com',
                'phone' => '998911902595',
                'birthday' => '1993-12-30',
                'gender' => 'male',
                'email_verified_at' => now(),
                'password' => bcrypt('user123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 103,
                'uuid' => Str::uuid(),
                'firstname' => 'Seller',
                'lastname' => 'Seller',
                'email' => 'sellers@githubit.com',
                'phone' => '998911902696',
                'birthday' => '1990-12-31',
                'gender' => 'male',
                'email_verified_at' => now(),
                'password' => bcrypt('seller'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 104,
                'uuid' => Str::uuid(),
                'firstname' => 'Manager',
                'lastname' => 'Manager',
                'email' => 'manager@githubit.com',
                'phone' => '998911902616',
                'birthday' => '1990-12-31',
                'gender' => 'male',
                'email_verified_at' => now(),
                'password' => bcrypt('manager'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 105,
                'uuid' => Str::uuid(),
                'firstname' => 'Moderator',
                'lastname' => 'Moderator',
                'email' => 'moderator@githubit.com',
                'phone' => '998911902116',
                'birthday' => '1990-12-31',
                'gender' => 'male',
                'email_verified_at' => now(),
                'password' => bcrypt('moderator'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 106,
                'uuid' => Str::uuid(),
                'firstname' => 'Delivery',
                'lastname' => 'Delivery',
                'email' => 'delivery@githubit.com',
                'phone' => '998911912116',
                'birthday' => '1990-12-31',
                'gender' => 'male',
                'email_verified_at' => now(),
                'password' => bcrypt('delivery'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        if (app()->environment() == 'local') {

            foreach ($users as $user) {
                User::updateOrInsert(['id' => data_get($user, 'id')], $user);
            }

            User::find(101)->syncRoles('admin');
            User::find(102)->syncRoles('user');
            User::find(103)->syncRoles('seller');
            User::find(104)->syncRoles('manager');
            User::find(105)->syncRoles('moderator');
            User::find(106)->syncRoles('deliveryman');

            $shop = Shop::create([
                'uuid'              => Str::uuid(),
                'user_id'           => 103,
                'location'          => [
                    'latitude'          => -69.3453324,
                    'longitude'         => 69.3453324,
                ],
                'phone'             => '+1234567',
                'show_type'         => 1,
                'open'              => 1,
                'background_img'    => 'url.webp',
                'logo_img'          => 'url.webp',
                'status'            => 'approved',
                'status_note'       => 'approved',
                'mark'              => 'mark',
                'delivery_time'     => [
                    'from'              => '10',
                    'to'                => '90',
                    'type'              => 'minute',
                ],
                'type'              => 2,
            ]);

            ShopTranslation::create([
                'shop_id'       => $shop->id,
                'description'   => 'shop desc',
                'title'         => 'shop title',
                'locale'        => data_get(Language::languagesList()->first(), 'locale', 'en'),
                'address'       => 'address',
            ]);

            $shop->tags()->sync(ShopTag::pluck('id')->toArray());

        }

    }

}
