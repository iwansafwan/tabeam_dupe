<?php

namespace Database\Seeders;

use App\Models\GeneralFund;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\Clock\now;

class GeneralFundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create general fund
        $general_fund= DB::table('general_funds')->insertGetId([
            'name' => 'General Fund',
            'collected_amount'=> 0,
            'created_at' =>now(),
            'updated_at' => now(),
        ]);
    }
}
