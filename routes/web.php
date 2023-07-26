<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\InvoicesAchiveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Invoices_Reports;
use App\Http\Controllers\Customers_report;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});


Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::resource('invoices', InvoicesController::class);
Route::resource('sections', SectionsController::class);
Route::resource('products', ProductsController::class);
Route::get('section/{id}',[InvoicesController::class,'getProducts']);
Route::get('InvoicesDetails/{id}',[InvoicesDetailsController::class,'edit']);
Route::get('View_file/{invoice_number}/{file_name}',[InvoicesDetailsController::class,'open_file']);
Route::get('/edit_invoice/{id}',[InvoicesController::class,'edit']);
Route::get('/status_show/{id}',[InvoicesController::class,'show'])->name('Status_show');
Route::post('/status_update/{id}',[InvoicesController::class,'status_update'])->name('Status_update');
Route::get('/Invoices_paid',[InvoicesController::class,'Invoices_paid']);
Route::get('/Invoice_UnPaid',[InvoicesController::class,'Invoice_UnPaid']);
Route::get('/Invoice_Partial',[InvoicesController::class,'Invoice_Partial']);
Route::resource('Archive', InvoicesAchiveController::class);
Route::get('print_invoice/{id}',[InvoicesController::class,'print_invoice']);
Route::get('invoices_report',[Invoices_Reports::class,'index']);
Route::post('Search_invoices',[Invoices_Reports::class,'Search_invoices']);
Route::get('customers_report',[Customers_report::class,'index'])->name('customers_report');
Route::post('Search_customers',[Customers_report::class,'Search_customers']);

Route::group(['middleware' => ['auth']], function() {

    Route::resource('roles',RoleController::class);

    Route::resource('users',UserController::class);

    });

Route::get('/{page}', [AdminController::class,'index']);
