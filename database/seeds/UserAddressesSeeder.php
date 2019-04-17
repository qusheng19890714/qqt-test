<?php

use Illuminate\Database\Seeder;
use App\Models\UserAddress;
use App\Models\User;

class UserAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user_ids = User::all()->pluck('id')->toArray();

        //获取faker实例
        $faker = app(Faker\Generator::class);

        $user_addresses = factory(UserAddress::class)
                          ->times(100)
                          ->make()
                          ->each(function($user_address) use ($user_ids, $faker) {

                              $user_address->user_id = $faker->randomElement($user_ids);

                          });

        // 将数据集合转换为数组，并插入到数据库中
        UserAddress::insert($user_addresses->toArray());
    }
}
