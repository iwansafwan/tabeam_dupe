<?php

use App\Http\Controllers\FundController;
use App\Http\Controllers\GeneralFundController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/funds/update_fund_status', [FundController::class, 'update_fund_status'])->name('funds.update_fund_status');

// Guest Route
Route::get('/', [HomeController::class, 'landing_page'])->name('landing_page');
Route::get('/guest/general_fund_detail', [GeneralFundController::class, 'guest_general_fund_details'])->name('guest.general_fund_details');
Route::get('/guest/fund_detail/{id}', [FundController::class, 'guest_fund_details'])->name('guest.fund_details');

//Admin Route
Route::middleware(['auth', 'verified', 'can:admin-only'])->group(function () {

    Route::get('/admin/dashboard', [HomeController::class, 'admin_dashboard'])->name('admin.dashboard');
    Route::get('/admin/funds', [FundController::class, 'admin_fund'])->name('admin.funds');
    Route::get('/admin/fund/{id}', [FundController::class, 'admin_show'])->name('admin.view');
    Route::get('/admin/transaction', [InvoiceController::class, 'admin_transaction_list'])->name('admin.transaction');
    Route::post('/admin/create_treasurer', [HomeController::class, 'admin_create_treasurer'])->name('admin.create_treasurer');
    Route::get('/admin/users', [HomeController::class, 'admin_users'])->name('admin.users');
    Route::get('/admin/donator/{id}', [InvoiceController::class, 'admin_view_donator'])->name('admin.view_donator');
    Route::get('/admin/treasurer/{id}', [FundController::class, 'admin_view_treasurer'])->name('admin.view_treasurer');
    Route::get('/admin/general_fund', [GeneralFundController::class, 'show'])->name('admin.general_fund');
    Route::get('/admin/generate-pdf', [PDFController::class, 'generatePDF'])->name('admin.create_pdf');
    // Route::get('/testnavbar', [HomeController::class, 'testnavbar'])->name('testnavbar');

});


//Treasurer Route
Route::middleware(['auth', 'verified', 'can:treasurer-only'])->group(function () {

    Route::get('/treasurer/dashboard', [HomeController::class, 'treasurer_dashboard'])->name('treasurer.dashboard');
    Route::get('/treasurer/create_fund', [FundController::class, 'create_fund'])->name('treasurer.create_fund');
    Route::post('/treasurer/submit_fund', [FundController::class, 'store'])->name('treasurer.submit_fund');
    Route::get('/treasurer/fund/{id}', [FundController::class, 'show'])->name('treasurer.view');
    Route::get('/treasurer/edit_fund/{id}', [FundController::class, 'edit'])->name('treasurer.edit_fund');
    Route::post('/treasurer/update_fund', [FundController::class, 'update'])->name('treasurer.update_fund');
    Route::post('/treasurer/terminate_fund', [FundController::class, 'treasurer_terminate_fund'])->name('treasurer.terminate_fund');
    Route::get('/treasurer/transaction', [InvoiceController::class, 'treasurer_transaction_list'])->name('treasurer.transaction');
    Route::get('/treasurer/funds/search', [FundController::class, 'treasurer_search_fund'])->name('treasurer.funds_search');

});

//Donator Route
Route::middleware(['auth', 'verified', 'can:donator-only'])->group(function () {

    Route::get('/donator/dashboard', [HomeController::class, 'donator_dashboard'])->name('donator.dashboard');
    Route::get('/donator/donate_general/{fund}', [FundController::class, 'donator_donate_general'])->name('donator.donate_general');
    Route::get('/donator/donate_main/{fund}', [FundController::class, 'donator_donate_main'])->name('donator.donate_main');
    Route::get('/donator/donate_section/{fund}/{section}', [FundController::class, 'donator_donate_section'])->name('donator.donate_section');
    Route::post('/donator/submit_payment', [InvoiceController::class, 'donator_submit_payment'])->name('donator.submit_payment');
    Route::get('/donator/transaction', [InvoiceController::class, 'donator_transaction_list'])->name('donator.transaction');
    Route::get('/donator/scan_qr', [FundController::class, 'donator_scan_qr'])->name('donator.scan_qr');
    Route::get('/donator/funds/search', [FundController::class, 'donator_search_fund'])->name('donator.funds_search');

// QR Route
    Route::get('/funds/fund_details/{id}', [FundController::class, 'fund_details'])->name('funds.fund_details');
    Route::get('/general_funds/fund_details/{id}', [GeneralFundController::class, 'fund_details'])->name('general_funds.fund_details');
    Route::get('/general_funds/check_general_qrcode', [GeneralFundController::class, 'check_general_qr_code'])->name('general_funds.check_general_qrcode');

});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
