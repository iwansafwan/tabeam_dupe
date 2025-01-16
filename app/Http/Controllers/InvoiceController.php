<?php

namespace App\Http\Controllers;

use App\Models\GeneralFund;
use App\Models\Invoice;
use App\Models\Ratio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvoiceController extends Controller
{


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created invoice 
     * To submit payment
     */
    public function donator_submit_payment(Request $request)
    {
        $request->validate([
            'donator_id' => 'required|exists:users,id',
            'password' => 'required',
            'amount' => ['required', 'numeric', 'min:1', 'regex:/^\d+(\.\d{1,2})?$/'],
            'donation_type' => 'required',
            'notes' => 'nullable',
        ]);

        // Round the amount to 2 decimal places
        // $request->merge([
        //     'amount' => round($request->amount, 2)
        // ]);

        if ($request->donation_type == 'general') {
            $request->validate([
                'general_fund_id' => 'required',
            ]);

        } elseif ($request->donation_type == 'section') {
            $request->validate([
                'ratio_id' => 'required',
                'fund_id' => 'required',
            ]);

        } else {
            $request->validate([
                'fund_id' => 'required',
            ]);
        }

        // retrieve donator record
        $donator = User::find($request->donator_id);

        // chech if password matches
        if (!Hash::check($request->password, $donator->password)) {
            return redirect()->back()->with('error', 'The provide password is incorrect');
        }
        if ($request->donation_type == 'general') {

            $general_fund = GeneralFund::find($request->general_fund_id);

            if ($general_fund) {
                // steps to handle current amount and total amount calculated
                $current_total = $general_fund->collected_amount;
                $new_total = $current_total + $request->amount;

                // update collected amount
                $general_fund->update([
                    'collected_amount' => $new_total,
                ]);

                // store new invoice
                $invoice = new Invoice();
                $invoice->donator_id = $request->donator_id;
                $invoice->general_fund_id = $request->general_fund_id;
                $invoice->donation_type = $request->donation_type;
                $invoice->amount = $request->amount;
                $invoice->notes = $request->notes ?? null;
                $invoice->save();
            } else {
                return redirect()->back()->with('error', 'General fund not found');
            }
        } elseif ($request->donation_type == 'section') {

            $ratio_section = Ratio::find($request->ratio_id);

            if ($ratio_section) {

                // get new total and current total amount
                $current_total = $ratio_section->total_collected;
                $new_total = $current_total + $request->amount;

                // update new total
                $ratio_section->update([
                    'total_collected' => $new_total,
                ]);

                // store new invoice
                $invoice = new Invoice();
                $invoice->donator_id = $request->donator_id;
                $invoice->fund_id = $request->fund_id;
                $invoice->ratio_id = $request->ratio_id;
                $invoice->donation_type = $request->donation_type;
                $invoice->amount = $request->amount;
                $invoice->notes = $request->notes ?? null;
                $invoice->save();

            } else {

                return redirect()->back()->with('error', 'Section / Category fund not found');

            }
        } else {

            // store invoice for donation type main fund
            $invoice = new Invoice();
            $invoice->donator_id = $request->donator_id;
            $invoice->fund_id = $request->fund_id;
            $invoice->donation_type = $request->donation_type;
            $invoice->amount = $request->amount;
            $invoice->notes = $request->notes ?? null;
            $invoice->save();
        }
        return redirect()->route('donator.transaction')->with('success', 'Payment submitted successfully');
    }

    // view transaction list for donator
    public function donator_transaction_list()
    {

        // retrieve donator 
        $donator = Auth::user();
        // assign invoice in transaction variable
        $transactions = Invoice::where('donator_id', $donator->id)->orderBy('created_at', 'desc')->paginate(10);

        return view('donator.transaction', [
            'transactions' => $transactions,
        ]);
    }

    // view transaction list for treasurer
    public function treasurer_transaction_list()
    {

        // retrieve treasurer
        $treasurer = Auth::user();

        // assign invoice relate to treasurer_id into transacation variable
        $transactions = Invoice::whereHas('fund', function ($query) use ($treasurer) {
            $query->where('treasurer_id', $treasurer->id);
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('treasurer.transaction')->with('transactions', $transactions);
    }

    // view transaction list for admin
    public function admin_transaction_list()
    {

        // assign invoice into variable transaction
        $transactions = Invoice::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.transaction')->with('transactions', $transactions);
    }

    // view all transaction list of donator by admin
    public function admin_view_donator($id)
    {

        $donator_id = $id;
        $donator = User::find($donator_id);

        if ($donator) {

            $transactions = Invoice::where('donator_id', $donator->id)->orderBy('created_at', 'desc')->paginate(10);

            return view('admin.donator_detail', [
                'donator' => $donator,
                'transactions' => $transactions,
            ]);

        } else {

            return redirect()->back()->with('error', 'Donator account not found.');

        }

    }
}
