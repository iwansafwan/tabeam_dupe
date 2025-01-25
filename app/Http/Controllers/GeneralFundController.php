<?php

namespace App\Http\Controllers;

use App\Models\GeneralFund;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GeneralFundController extends Controller
{

    // show detail general fund (admin)
    public function show()
    {
        $fund = GeneralFund::first();
        if ($fund) {

            return view('admin.general_fund_detail')->with('fund', $fund);

        } else {
            return redirect()->back()->with('error', 'Fund detail not found');

        }

    }

    // show detail of general fund (guest)
    public function guest_general_fund_details()
    {
        $fund = GeneralFund::first();
        if ($fund) {

            return view('guest_general_fund_detail', [
                'fund' => $fund,
            ]);

        } else {

            return redirect()->back()->with('error', 'Fund detail not found');

        }
    }

    // show detail of general fund (donator)
    public function fund_details($id)
    {

        $fund = GeneralFund::findOrFail($id);

        // dd($fund);

        if ($fund) {

            return view('donator.general_fund_detail', [
                'fund' => $fund
            ]);

        } else {

            return redirect()->back()->with('error', 'Fund detail not found.');

        }

    }

    // generate qr code for general fund (dekat app.blade)
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
            return response()->json(['message' => 'QR code general fund successfully updated']);
        }
        return response()->json(['message' => 'QR code general fund is generated']);
    }
}
