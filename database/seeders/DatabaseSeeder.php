<?php

namespace Database\Seeders;

use App\Models\GeneralFund;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // call roleandadminseeder
        $this->call(RoleAndAdminSeeder::class);

        // call generalfundseeder
        $this->call(GeneralFundSeeder::class);

        $this->check_general_qr_code();
    }
    public function check_general_qr_code()
    {
        // find available g_fund
        $general_fund = GeneralFund::first();

        if ($general_fund->qr_code == NULL || $general_fund->qr_code == '') {
            // generate qr content
            $qrContent = route('general_funds.fund_details', ['id' => $general_fund->id]);
            // define name and file path for qr 
            $qrCodePath = public_path('general_fund_qrcodes/');
            $qrCodeFileName = $general_fund->id . '_qrcode.png';

            // ensure the directory exist
            if (!File::isDirectory($qrCodePath)) {
                File::makeDirectory($qrCodePath, 0777, true, true);
            }

            // generate and save qr code
            QrCode::format('png')->size(300)->generate($qrContent, $qrCodePath . $qrCodeFileName);
            // Update general fund record in the laragon (insert qrcode path)
            DB::table('general_funds')->where('id', $general_fund->id)->update([
                'qr_code' => $qrCodeFileName,
            ]);
        }
    }
}
