<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use App\Models\GeneralFund;
use App\Models\Invoice;
use App\Models\Ratio;
use App\Models\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FundController extends Controller
{

    // List all fund created (treasurer)
    public function create_fund()
    {
        $treasurer = Auth::user();

        if ($treasurer) {

            $funds = Fund::where('treasurer_id', $treasurer->id)->paginate(10);

            return view('treasurer.create_fund')->with('funds', $funds)
                ->with('treasurer', $treasurer);

        } else {

            return redirect()->back()->with('error', 'Treasurer Account not found.');

        }
    }

    // create new fund (treasurer)
    public function store(Request $request)
    {
        $request->validate([
            'treasurer_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:100000',
            'target_amount' => 'required',
            'end_date' => 'required|date|after:today',
            'ratio' => 'required|array|min:1', // Ensure ratio is an array and has at least one item

            //* to make sure all index in the array applied the condition to each element
            'ratio.*.category_name' => 'required', // Validate category IDs
            'ratio.*.percentage' => 'required|numeric|min:0|max:100', // Validate percentages between 0 and 100
        ]);

        // find total percentage
        $totalpercentage = array_sum(array_column($request->input('ratio'), 'percentage'));
        // condition if total percentage = 100
        if ($totalpercentage !== 100) {
            return back()->withErrors(['ratio' => 'Total Percentage must be equal to 100%.'])->withInput();
        }

        // create new model for fund
        $fund = new Fund();
        $fund->treasurer_id = $request->treasurer_id;
        $fund->name = $request->name;
        $fund->target_amount = $request->target_amount;
        $fund->end_date = $request->end_date;
        $fund->description = $request->description;
        $fund->status = 'active';
        $fund->image = 'temp_image';

        $fund->save();

        if (!empty($fund)) {

            if ($request->has('image')) {

                // set name for image
                $file_name = $fund->id . '.' . $request->image->extension();
                // request image into variable file
                $file = $request->image;

                // assign name for image path
                $file_folder = public_path('fund_image/');
                // check if image save have the public path directory
                if (!File::isDirectory($file_folder)) {
                    File::makeDirectory($file_folder, 0777, true, true);
                }
                //  Move an uploaded file to a specified directory on the server with a specified filename
                $file->move($file_folder, $file_name);

                // Update the poster field with the correct file name
                $fund->update([
                    'image' => $file_name
                ]);
            }

            // QR SECTION
            // Assign route or page for qrcode
            $qrContent = route('funds.fund_details', ['id' => $fund->id]);
            // set name for qr image path
            $qrCodePath = public_path('fund_qrcodes/');
            $qrCodeFileName = $fund->id . '_qcode.png';

            // check if qr directory exist
            if (!File::isDirectory($qrCodePath)) {
                File::makeDirectory($qrCodePath, 0777, true, true);
            }

            // Generate and save qr code
            QrCode::format('png')->size(300)->generate($qrContent, $qrCodePath . $qrCodeFileName, );
            // Save QR code path in laragon record
            $fund->update([
                'qr_code' => $qrCodeFileName,
            ]);

            // RATIO SECTION
            // save ratio
            foreach ($request->input('ratio') as $ratio) {
                // calculate percent_amount
                $percentAmount = ($ratio['percentage'] / 100) * $fund->target_amount;

                // store ratio
                Ratio::create([
                    'fund_id' => $fund->id,
                    'category_name' => $ratio['category_name'],
                    'percentage' => $ratio['percentage'],
                    'percent_amount' => $percentAmount,
                    'total_collected' => 0,
                ]);
            }
            return redirect()->back()->with('success', 'Fund successfully created.');

        } else {
            return redirect()->back()->with('error', 'Fund failed to create.');
        }
    }

    // View fund detail (qr route/donator)
    public function fund_details($id)
    {
        $fund = Fund::with('ratio')->findOrFail($id);
        if ($fund) {
            // sum up all amount from invoice and assign to variable
            $totalCollected = Invoice::where('fund_id', $id)->sum('amount');
            $fundGoal = $fund->target_amount;

            // calculate progress perccentage
            $progressPercentage = 0;
            if ($fundGoal > 0) {
                $progressPercentage = ($totalCollected / $fundGoal) * 100;
            }
            // Ensure the progress is between 0 and 100
            $progressPercentage = min(max($progressPercentage, 0), 100);

            return view('donator.fund_detail', [
                'fund' => $fund,
                'totalCollected' => $totalCollected,
                'progressPercentage' => $progressPercentage,
            ]);
        } else {
            return redirect()->back()->with('error', 'Fund detail not found');
        }
    }

    // view fund detail (guest)
    public function guest_fund_details($id)
    {
        $fund = Fund::with('ratio')->findOrFail($id);
        if ($fund) {
            // sum up all amount from invoice and assign to variable
            $totalCollected = Invoice::where('fund_id', $id)->sum('amount');
            $fundGoal = $fund->target_amount;

            // calculate progress perccentage
            $progressPercentage = 0;
            if ($fundGoal > 0) {
                $progressPercentage = ($totalCollected / $fundGoal) * 100;
            }
            // Ensure the progress is between 0 and 100
            $progressPercentage = min(max($progressPercentage, 0), 100);

            return view('guest_fund_detail', [
                'fund' => $fund,
                'totalCollected' => $totalCollected,
                'progressPercentage' => $progressPercentage,
            ]);
        } else {

            return redirect()->back()->with('error', 'Fund detail not found');
        }
    }

    // view fund detail (treasurer)
    public function show($id)
    {
        // find fund id that have relation with ratio 
        $fund = Fund::with('ratio')->find($id);
        if ($fund) {

            $totalCollected = Invoice::where('fund_id', $id)->sum('amount');

            return view('treasurer.fund_detail')->with('fund', $fund)->with('totalCollected', $totalCollected);
        } else {

            return redirect()->back()->with('error', 'Fund not found.');

        }
    }

    // view fund detail (admin)
    public function admin_show($id)
    {
        // retrieve specific fund with treasurer and ratio id
        $fund = Fund::with('ratio')->with('treasurer')->find($id);

        if ($fund) {

            $totalCollected = Invoice::where('fund_id', $id)->sum('amount');

            return view('admin.fund_detail', [
                'fund' => $fund,
                'totalCollected'=> $totalCollected,
            ]);

        } else {

            return redirect()->back()->with('error', 'Fund not found.');

        }
    }

    // edit fund (treasurer)
    public function edit($id)
    {
        $fund = Fund::with('ratio')->find($id);
        if ($fund) {

            $treasurer = Auth::user();
            return view('treasurer.edit_fund')
                ->with('fund', $fund)
                ->with('treasurer', $treasurer);

        } else {
            return redirect()->back()->with('error', 'Fund not found.');
        }

    }

    // update fund (treasurer)
    public function update(Request $request)
    {

        // validate all request
        $request->validate([
            'fund_id' => 'required',
            'treasurer_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:100000',
            'target_amount' => 'required',
            'end_date' => 'required|date|after:today',
            'ratio' => 'required|array|min:1',
            'ratio.*.category_name' => 'required',
            'ratio.*.percentage' => 'required|numeric|min:0|max:100',
        ]);

        // retrieve fund id
        $fund = Fund::findOrFail($request->fund_id);
        // calculate all percentage in ratio model
        $totalPercentage = array_sum(array_map('floatval', array_column($request->input('ratio'), 'percentage')));

        if ($totalPercentage != 100) {
            return redirect()->back()->with('error', 'The total percentage must equal 100%.');
        }

        // update image if new image upload
        if ($request->has('image')) {
            $file_name = $fund->id . '.' . $request->image->extension();
            $file = $request->image;

            $file_folder = public_path('fund_image/');
            if (!File::isDirectory($file_folder)) {
                File::makeDirectory($file_folder, 0777, true, true);
            }
            // delete old image path
            $oldImagePath = $file_folder . $fund->image;
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            // Save the new image
            $file->move($file_folder, $file_name);
            $fund->image = $file_name;
        }

        // Update fund details
        $fund->treasurer_id = $request->treasurer_id;
        $fund->name = $request->name;
        $fund->target_amount = $request->target_amount;
        $fund->end_date = $request->end_date;
        $fund->description = $request->description;
        $fund->save();

        // === Update ratios ===
        $existingRatioIds = [];
        foreach ($request->input('ratio') as $key => $ratioData) {
            if (isset($ratioData['id']) && $ratioData['id'] > 0) {
                // update existing ratio
                $existingRatioIds[] = $ratioData['id'];
                Ratio::where('id', $ratioData['id'])->update([
                    'category_name' => $ratioData['category_name'],
                    'percentage' => $ratioData['percentage'],
                    'percent_amount' => ($ratioData['percentage'] / 100) * $fund->target_amount,
                    'total_collected' => 0,
                ]);
            } else {
                // create new rati0
                $newRatio = Ratio::create([

                    'fund_id' => $fund->id,
                    'category_name' => $ratioData['category_name'],
                    'percentage' => $ratioData['percentage'],
                    'percent_amount' => ($ratioData['percentage'] / 100) * $fund->target_amount,
                    'total_collected' => 0,

                ]);

                // Add the new ratio ID to the array
                $existingRatioIds[] = $newRatio->id;
            }
        }
        // remove deleted ratio
        Ratio::where('fund_id', $fund->id)->whereNotIn('id', $existingRatioIds)->delete();
        return redirect()->route('treasurer.create_fund')->with('success', 'Fund successfully updated.');
    }

    // View list of fund (admin)
    public function admin_fund()
    {

        // retrieve g_fund
        $general_fund = GeneralFund::first();
        // retrieve fund
        $funds = Fund::with('treasurer')->paginate(10);

        return view('admin.funds', [
            'funds' => $funds,
            'g_fund' => $general_fund,
        ]);
    }


    // view specific treasurer detail (admin)
    public function admin_view_treasurer($id)
    {

        $treasurer_id = $id;
        $treasurer = User::find($treasurer_id);

        if ($treasurer) {

            $funds = Fund::where('treasurer_id', $treasurer->id)->orderBy('created_at', 'desc')->paginate(10);

            return view('admin.treasurer_detail', [
                'funds' => $funds,
                'treasurer' => $treasurer,
            ]);

        } else {

            return redirect()->back()->with('error', 'Treasurer account not found');

        }

    }

    // donator scan qr (donator)
    public function donator_scan_qr()
    {
        return view('donator.scan_qr');
    }


    // donate to general fund (donator)
    public function donator_donate_general($fund)
    {
        // check current user
        $donator = Auth::user();

        $general_fund_id = $fund;

        return view('donator.donate_page', [
            'donator' => $donator,
            'general_fund_id' => $general_fund_id,
        ]);
    }

    // donate to main fund (donator)
    public function donator_donate_main($fund)
    {
        // check current user
        $donator = Auth::user();

        $fund_id = $fund;

        return view('donator.donate_page', [
            'donator' => $donator,
            'fund_id' => $fund_id,
        ]);
    }

    // donate to specific section (donator)
    public function donator_donate_section($fund, $section)
    {
        // check current user
        $donator = Auth::user();

        // assign fund id and section id 
        $fund_id = $fund;
        $ratio_id = $section;

        return view('donator.donate_page', [
            'donator' => $donator,
            'fund_id' => $fund_id,
            'ratio_id' => $ratio_id,
        ]);
    }

    // terminate fund and direct money to general fund (treasurer)
    public function treasurer_terminate_fund(Request $request)
    {
        // validate req for treasurer id and fund id
        $request->validate([
            'treasurer_id' => 'required',
            'fund_id' => 'required',
        ]);

        //retrieve general fund
        $general_fund = GeneralFund::first();

        // check if general exist
        // retrieve current total amount in g_fund
        if ($general_fund) {

            // retrieve fund id req
            $fund = Fund::find($request->fund_id);
            $current_total = $general_fund->collected_amount;

            // check if fund exist
            if ($fund) {

                // retrieve all transaction/invoice in the req fund
                $invoices = Invoice::where('fund_id', $request->fund_id)->get();

                // initialize new total
                $new_total = 0;

                // check if invoices exist
                if (count($invoices) > 0) {

                    // retrieve all transaction/invoice in the req fund
                    foreach ($invoices as $inv) {
                        $new_total += $inv->amount;

                        $inv->update([
                            'general_fund_id' => $general_fund->id,
                        ]);
                    }
                }
                // add total from all invoice in the fund
                $current_total += $new_total;

                // update new collected amount in general fund
                $general_fund->update([
                    'collected_amount' => $current_total,
                ]);

                // update fund status to terminate
                $fund->update([
                    'status' => 'terminated',
                ]);

                return redirect()->back()->with('success', 'Fund successfully terminated and fund collected transferred to general fund.');

            } else {

                return redirect()->back()->with('error', "Treasurer's fund not found.");

            }
        }
    }

    // update fund status at app.blade 
    public function update_fund_status()
    {
        // Get the current date and time
        $now = Carbon::now();

        // Check if there are any funds to process
        if (!Fund::exists()) {
            return response()->json(['message' => 'No funds found to update.']);
        }

        // Process funds in batches for better performance
        Fund::chunk(100, function ($funds) use ($now) {
            foreach ($funds as $fund) {
                // Skip updating if status is already 'terminated'
                if ($fund->status === 'terminated') {
                    continue;
                }

                // Update status based on the current date
                $newStatus = $now->lessThan($fund->end_date) ? 'active' : 'ended';

                // Save only if the status is different
                if ($fund->status !== $newStatus) {
                    $fund->status = $newStatus;
                    $fund->save();
                }
            }
        });

        return response()->json(['message' => 'Fund statuses updated successfully.']);

    }

    // search active fund for (donator)
    public function donator_search_fund(Request $request)
    {


        $donator = Auth::user();

        $invoiceCount = Invoice::where('donator_id', $donator->id)->count();

        // Calculate total donations based on the sum of the 'amount' column in the invoices
        $totalDonation = Invoice::where('donator_id', $donator->id)->sum('amount');

        $g_fund = GeneralFund::first();


        $funds = Fund::with('treasurer')
            ->where('status', 'active')
            ->when($request->has('searchkey'), function ($query) use ($request) {
                $searchKey = $request->searchkey;

                // Try to parse the search key as a date
                $parsedDate = null;
                try {
                    $parsedDate = Carbon::createFromFormat('d/m/Y', $searchKey)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Not a valid date, ignore parsing
                }

                $query->where(function ($subQuery) use ($searchKey, $parsedDate) {
                    $subQuery->where('name', 'LIKE', '%' . $searchKey . '%');

                    // Add date filter only if a valid date was parsed
                    if ($parsedDate) {
                        $subQuery->orWhereDate('created_at', $parsedDate);
                    }
                });
            })
            ->get();

        return view('donator.dashboard', [
            'funds' => $funds,
            'totalDonation' => $totalDonation,
            'invoiceCount' => $invoiceCount,
            'g_fund' => $g_fund
        ]);
    }

    // search active fund for (treasurer)
    public function treasurer_search_fund(Request $request)
    {

        $treasurer = Auth::user();

        if ($treasurer) {

            $funds = Fund::with('treasurer')
                ->where('treasurer_id', $treasurer->id)
                ->when($request->has('searchkey'), function ($query) use ($request) {
                    $searchKey = $request->searchkey;

                    // Try to parse the search key as a date
                    $parsedDate = null;
                    try {
                        $parsedDate = Carbon::createFromFormat('d/m/Y', $searchKey)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Not a valid date, ignore parsing
                    }

                    $query->where(function ($subQuery) use ($searchKey, $parsedDate) {
                        $subQuery->where('name', 'LIKE', '%' . $searchKey . '%');

                        // Add date filter only if a valid date was parsed
                        if ($parsedDate) {
                            $subQuery->orWhereDate('created_at', $parsedDate);
                        }
                    });
                })
                ->get();

            return view('treasurer.create_fund')->with('funds', $funds)
                ->with('treasurer', $treasurer);

        }
    }
}
