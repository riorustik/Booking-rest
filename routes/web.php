<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/wel', function () {
    return view('welcome');
});


Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});



Route::middleware(['auth', 'verified'])->group(function () {

    Route::group(['middleware' => 'role:admin'], function () {
        //add
        Route::get('admin/tables', [TableController::class, 'index'])->name('tables');
        Route::get('admin/create/table', [TableController::class, 'createTable'])->name('add-table');
        Route::post('admin/create-table', [TableController::class, 'create']);
        //show
        Route::get('admin/table/all/{id}', [TableController::class, 'showOneTable'])->name('table-data-one');
        //update
        Route::get('admin/table/all/{id}/update', [TableController::class, 'updateTable'])->name('table-update');
        Route::post('admin/table/all/{id}/update', [TableController::class, 'updateTableSubmit'])->name('table-update-submit');
        //delete
        Route::get('admin/table/all/{id}/delete', [TableController::class, 'deleteTable'])->name('table-delete');

        Route::get('admin/', function (){
            return view('tableSearchAdmin');
        });
        Route::post('/admin/search', [OrderController::class, 'dateTimeSelectionAdmin']);
        Route::get('/admin/details/{id}', [OrderController::class, 'showNumberSelectedTableAdmin'])->name('table-data-number-admin');
        Route::post('/admin/create-order/{id}', [OrderController::class, 'createOrderAdmin'])->name('create-order-admin');

        Route::get('admin/success', [OrderController::class, 'successBookingAdmin'])->name('success-booking-admin');
        Route::get('admin/error', [OrderController::class, 'errorBookingAdmin'])->name('error-booking-admin');

        Route::get('admin/users', [OrderController::class, 'allUsersdata'])->name('users-admin');
        Route::get('admin/users/delete/{id}', [TableController::class, 'deleteUsers'])->name('users-delete');

        Route::get('/admin/booking', function () {
            return view('admin.allTables11');
        });
        Route::get('admin/all/booking', function (){
            return view('admin.allBookingTables');
        });
        Route::post('/admin/all/booking/date', [OrderController::class, 'dateOrderSelectionAdmin']);
        Route::get('/admin/booking/all/{id}/{day}/delete', [OrderController::class, 'deleteOrderAdmin'])->name('order-delete-admin');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::get('/info/tables', [TableController::class, 'allTablesRest'])->name('infoOfAllTables');
Route::get('/bookings', [OrderController::class, 'allMyBooking'])->name('myBookingTables');
Route::get('/booking/all/{id}/delete', [OrderController::class, 'deleteOrder'])->name('order-delete');

Route::get('/info/contacts', function () {
    return view('contacts');
})->name('infoContacts');







Auth::routes();
Route::get('/logout',[AuthenticatedSessionController::class,'destroy'])->name('logoutt');

Route::get('/pro', function () {
    return view('profile');
});

Route::get('/profile', [OrderController::class, 'profileUser'])->name('profile');


//Route::get('/test', [\App\Http\Controllers\SchedulesController::class, 'test']);

//Route::get('/data', [OrderController::class, 'allTable'])->name('select-booking-table');

Route::post('/search', [OrderController::class, 'dateTimeSelection']);


Route::get('/', function (){
  return view('tableSearch');
});

Route::get('/about', function (){
    return view('about');
});

Route::get('/details', function (){
    return view('bookingDetails');
});


Route::get('/details/{id}', [OrderController::class, 'showNumberSelectedTable'])->name('table-data-number');



Route::get('/success', [OrderController::class, 'successBooking'])->name('success-booking');
Route::get('/error', [OrderController::class, 'errorBooking'])->name('error-booking');

Route::post('/create-order/{id}', [OrderController::class, 'createOrder'])->name('create-order');

require __DIR__.'/auth.php';
