<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use App\Models\GeneralFund;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class HomeController extends Controller
{
    // test navbar
    // public function testNavbar()
    // {
    //     return view('admin.testnavbar');
    // }

    // landing page
    public function landing_page()
    {
        // retrive g_fund
        $general_fund = GeneralFund::first();

        // retrieve all funds
        $funds = Fund::with('treasurer')->where('status', 'active')->paginate(10);

        return view('welcome', [
            'g_fund' => $general_fund,
            'funds' => $funds,
        ]);
    }

    // view admin dashboard
    public function admin_dashboard()
    {
        // Count users with 'donator' role
        $donatorCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'donator');
        })->count();

        // Count users with 'treasurer' role
        $treasurerCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'treasurer');
        })->count();

        // Count all funds
        $fundCount = Fund::count();

        // Count all invoices
        $invoiceCount = Invoice::count();

        return view('admin.dashboard', compact('donatorCount', 'treasurerCount', 'fundCount', 'invoiceCount'));

    }

    // view treasurer dashboard
    public function treasurer_dashboard()
    {

        $treasurer = Auth::user();
        $fundCount = Fund::where('treasurer_id', $treasurer->id)->count();

        // Calculate the total invoice amount for funds managed by the treasurer that have NOT been transferred to a GeneralFund
        $overallCollected = Invoice::whereHas('fund', function ($query) use ($treasurer) {
            $query->where('treasurer_id', $treasurer->id)
                ->whereNull('general_fund_id'); // Ensure the fund is not transferred to a GeneralFund
        })->sum('amount');

        return view('treasurer.dashboard', compact('fundCount', 'overallCollected'));
    }

    // view donator dashboard
    public function donator_dashboard()
    {

        $donator = Auth::user();

        $invoiceCount = Invoice::where('donator_id', $donator->id)->count();

        // Calculate total donations based on the sum of the 'amount' column in the invoices
        $totalDonation = Invoice::where('donator_id', $donator->id)->sum('amount');

        $g_fund = GeneralFund::first();

        $funds = Fund::with('treasurer')->where('status', 'active')->paginate(10);

        return view('donator.dashboard', compact('totalDonation', 'invoiceCount', 'g_fund', 'funds'));

    }

    //show list of user either donator or treasurer 
    public function admin_users()
    {

        // Get users with the role of "treasurer" or "donator"
        $treasurers = User::whereRelation('roles', 'name', 'treasurer')->paginate(10);
        $donators = User::whereRelation('roles', 'name', 'donator')->paginate(10);

        return view('admin.users')->with('treasurers', $treasurers)
            ->with('donators', $donators);

    }
    // store admin create treasurer
    public function admin_create_treasurer(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\password::defaults()],
            'contact_number' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_number' => $request->contact_number,
        ]);

        // Find the role i want to assign 
        $userRole = Role::where('name', 'treasurer')->first(); //if the treasurer role exist

        // Attach role (treasurer) to the new user
        $user->roles()->attach($userRole, ['created_at' => now(), 'updated_at' => now()]);

        if ($user) {

            session()->flash('success', 'Treasurer account successfully created.');
            return redirect()->back();

        } else {

            session()->flash('error', 'Treasurer account failed to create.');
            return redirect()->back();

        }
    }
}
