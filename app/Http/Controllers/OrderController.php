<?php

namespace App\Http\Controllers;


use App\Models\BusyTable;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function createOrder($id, Request $request)
    {
        $table = Table::find($id);
        $order = new Order();
        if (optional(Auth::user())->id == null) {
            $order->user_id = null;
        } else {
            $order->user_id = Auth::user()->id;
        }

        $order->table_id = $table->id;
        $order->phone = $request->phone;
        $order->name = $request->name;
        $order->email = $request->email;
        $order->guest_quantity = $_COOKIE["GuestQuantity"];
        $order->data_booking = $_COOKIE["DataBooking"];
        $order->time_start = $_COOKIE["TimeBooking"];
        $order->time_duration = 2;
        $order->time_end = Carbon::parse($_COOKIE["TimeBooking"])->addHours($order->time_duration)->subSeconds(1);
        $order->save();


        $busyTable = new BusyTable();
        $busyTable->order_id = $order->id;
        $busyTable->table_id = $table->id;
        $busyTable->date = $order->data_booking;
        $busyTable->time_start = $order->time_start;
        $busyTable->time_duration = $order->time_duration;
        $busyTable->time_end = $order->time_end;
        $busyTable->save();

        return redirect()->route("success-booking");


    }

    // показать номер стола при брони
    public function showNumberSelectedTable($id)
    {
        $table = new Table();
        return view('bookingDetails', ['table' => $table->find($id)]);
    }

    public function dateTimeSelection(Request $request)
    {

        setcookie("GuestQuantity", $request->guest_quantity, time() + 1000 * 3600);
        setcookie("TimeBooking", Carbon::parse($request->time_booking)->addSeconds(1), time() + 1000 * 3600);
        setcookie("DataBooking", date("Y-m-d", strtotime($request->data_booking)), time() + 1000 * 3600);
        //SELECT `table_id`, `number`,`time_end` from `busy_tables` left join `tables` on `busy_tables`.`table_id` = `tables`.`id` where (`time_end` <='15:00:00' or `time_start`>='17:00:00') and date = '2022-11-10' and `tables`.`guest_quantity`>=6;


//        if (count($busyTable) == 0) {
//            $table = new Table();
//            return view('bookingDetails', [
//                'table' => $table->find($id),
//                'date' => $request->date,
//                'time' => $request->time,
//                'guest_quantity' => $request->guest_quantity,
//            ]);
//        } else {
//            return redirect()->route("error-booking");
        //        dd($busyTable);
//        }

        $tables = (Table::freeTablesBusy(date("Y-m-d", strtotime($request->data_booking)),
            $request->guest_quantity,
            Carbon::parse($request->time_booking)->addSeconds(1),
            Carbon::parse($request->time_booking)->addHours(2)))->orderBy('number', 'asc')->get();

//        $str = '
//    <a href="/images/virtuemart/product/resized/img-01_1200x0.jpg" class="cropped">1</a>
//    <a href="/images/virtuemart/product/resized/img-02_1200x0.jpg" class="foo">2</a>
//    <a href="/images/virtuemart/product/resized/img-03_1200x0.jpg" class="cropped">3</a>
//';
//
//        $str = preg_replace_callback(
//            '~<a href="[^"]+" class="cropped"~',
//            function($m){
//                return preg_replace(['~resized/~', '~_[^.]+~'], '', $m[0]);
//            },
//            $str
//        );
//
//        dd($str);
        return view("availableTables", ['dateOfDay' => Carbon::parse(date("d-m-Y", strtotime($request->data_booking)))->day,
            'dateOfMonth' => Carbon::parse(date("d-m-Y", strtotime($request->data_booking)))->month,
            'dateOfYear' => Carbon::parse(date("d-m-Y", strtotime($request->data_booking)))->year,
            'guest_quantity' => $request->guest_quantity,
            'time_booking' => $request->time_booking,
            'tables' => $tables]);
    }


    public function allTable()
    {
//         'SELECT `tables`.`id`, `number` from `tables`
//left join `busy_tables` on `tables`.`id` = `busy_tables`.`table_id` and `busy_tables`.`date` = 25
//where `busy_tables`.`id` IS NULL and `tables`.`guest_quantity`>=6
//UNION
//SELECT `table_id`, `number`
//from `busy_tables` left join `tables`
//on `busy_tables`.`table_id` = `tables`.`id`
//where (`time_start` + `time_duration` <=15 or 15+2<=`time_start`) and date = 25 and `tables`.`guest_quantity`>=6';


        //  $tables = Table::all();

        $tables = DB::table('tables')->select('id', 'number', 'type_hall', 'deposit', 'guest_quantity')->get();

        return view('availableTables', ['tables' => $tables]);
    }


    public function successBooking()
    {
        return view('reaction.successBooking');
    }

    public function errorBooking()
    {
        return view('reaction.errorBooking');
    }


    public function profileUser()
    {
        if (optional(Auth::user())->id == null) {
            return view('auth.register');
        } else {
            return view('profile');
        }
    }

    public function allMyBooking()
    {
        $dateNow = Carbon::now()->year . '-' . Carbon::now()->month . '-' . Carbon::now()->day;


        if (optional(Auth::user())->id == null) {
            $title = 'Вы не авторизованы!';
            $titleActive = '';
            $titleHistory = '';
        } else {
            $title = 'Мои бронирования:';
            $titleActive = 'Активные бронирования:';
            $titleHistory = 'История бронирований:';
        }

        $user = optional(Auth::user())->id;
        //dd($user);
        if ($user == null) {
            $booking = [];
            $historyBooking = [];

        } else {
            $booking = DB::table('orders')->
            select('*', 'orders.guest_quantity', 'orders.id')->
            where('data_booking', '>=', $dateNow)->
            where('deleted_at', '=', NULL)->
            leftJoin('tables', 'orders.table_id', '=', 'tables.id')
                ->where('user_id', '=', $user)
                ->get();

            // dd($booking);
            $historyBooking = DB::table('orders')->
            select('*', 'orders.guest_quantity')->
            where('data_booking', '<>', $dateNow)->
            where('deleted_at', '!=', NULL)->
            leftJoin('tables', 'orders.table_id', '=', 'tables.id')
                ->where('user_id', '=', $user)
                ->get();

        }

        // dd($historyBooking);
        return view('myBooking', ['booking' => $booking, 'title' => $title, 'historyBooking' => $historyBooking,
            'titleActive' => $titleActive, 'titleHistory' => $titleHistory]);
    }

    public function deleteOrder($id)
    {

        Order::where('id', '=', $id)->delete();
        return redirect()->route("myBookingTables");


    }
    public function deleteOrderAdmin($id, $day)
    {
        Order::where('id', '=', $id)->delete();

        $date = date("Y-m-d", strtotime($day));

        $booking = DB::table('orders')
            ->select('*')
            ->where('data_booking', '=', $date)
//            ->where('deleted_at', '=', NULL)
//        $booking = DB::table('orders')->
//        select('*', 'orders.id')->
//        where('data_booking','=', $date)->
//        leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->get();
//        //dd($booking);
        if ($booking == []) {
            $title = 'Бронирований на этот день нет';
        } else {
            $title = '';
        }

//        $del = DB::table('orders')
//            ->select('deleted_at')
//            ->where('data_booking', '=', $date)
//            ->where('deleted_at', '!=', NULL)
//            ->get();
//


        $d = Carbon::parse($date)->day . '.'
            . Carbon::parse($date)->month . '.'
            . Carbon::parse($date)->year;
        // dd($day);

        $tables = DB::table('users')->get();


//        return redirect()->route("/admin/all/booking/date");
        // dd($dt);
        return view("allBookingDateAdmin", ['booking' => $booking, 'day' => $d, 'tables' => $tables, 'title' => $title]);


//        Order::where('id', '=', $id)->delete();
//        return view("admin.allBookingTables" );
//        return redirect()->route("myBookingTables");


    }

    //Функции для администратора
    //
    //
    //
    //
    //

    public function dateTimeSelectionAdmin(Request $request)
    {

        setcookie("GuestQuantity", $request->guest_quantity, time() + 1000 * 3600);
        setcookie("TimeBooking", Carbon::parse($request->time_booking)->addSeconds(1), time() + 1000 * 3600);
        setcookie("DataBooking", date("Y-m-d", strtotime($request->data_booking)), time() + 1000 * 3600);
        //SELECT `table_id`, `number`,`time_end` from `busy_tables` left join `tables` on `busy_tables`.`table_id` = `tables`.`id` where (`time_end` <='15:00:00' or `time_start`>='17:00:00') and date = '2022-11-10' and `tables`.`guest_quantity`>=6;
        $tables = (Table::freeTablesBusy(date("Y-m-d", strtotime($request->data_booking)),
            $request->guest_quantity,
            Carbon::parse($request->time_booking)->addSeconds(1),
            Carbon::parse($request->time_booking)->addHours(2)))->orderBy('number', 'asc')->get();
//        dd($tables);
        return view("availableTablesAdmin", ['dateOfDay' => Carbon::parse(date("d-m-Y", strtotime($request->data_booking)))->day,
            'dateOfMonth' => Carbon::parse(date("d-m-Y", strtotime($request->data_booking)))->month,
            'dateOfYear' => Carbon::parse(date("d-m-Y", strtotime($request->data_booking)))->year,
            'guest_quantity' => $request->guest_quantity, 'time_booking' => $request->time_booking,
            'tables' => $tables]);
    }

    public function showNumberSelectedTableAdmin($id)
    {
        $table = new Table();
        return view('bookingDetailsAdmin', ['table' => $table->find($id)]);
    }

    public function createOrderAdmin($id, Request $request)
    {
        $table = Table::find($id);
        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->table_id = $table->id;
        $order->phone = $request->phone;
        $order->name = $request->name;
        $order->email = $request->email;
        $order->guest_quantity = $_COOKIE["GuestQuantity"];
        $order->data_booking = $_COOKIE["DataBooking"];
        $order->time_start = $_COOKIE["TimeBooking"];
        $order->time_duration = 2;
        $order->time_end = Carbon::parse($_COOKIE["TimeBooking"])->addHours($order->time_duration)->subSeconds(1);
        $order->save();

        $busyTable = new BusyTable();
        $busyTable->order_id = $order->id;
        $busyTable->table_id = $table->id;
        $busyTable->date = $order->data_booking;
        $busyTable->time_start = $order->time_start;
        $busyTable->time_duration = $order->time_duration;
        $busyTable->time_end = $order->time_end;
        $busyTable->save();

        return redirect()->route("success-booking-admin");


    }

    public function successBookingAdmin()
    {
        return view('../reaction.successBookingAdmin');
    }

    public function errorBookingAdmin()
    {
        return view('../reaction.errorBookingAdmin');
    }

    public function allUsersdata()
    {
        $users = DB::table('model_has_roles')->
        select('*')->
        where('role_id', '=', 1)->
        leftJoin('users', 'model_has_roles.model_id', '=', 'users.id')
            ->get();


        return view('admin.allUsers', ['users' => $users]);
    }


    public function dateOrderSelectionAdmin(Request $request)
    {

        $date = date("Y-m-d", strtotime($request->data_booking));

        $booking = DB::table('orders')
            ->select('*')
            ->where('data_booking', '=', $date)
//            ->where('deleted_at', '=', NULL)
//        $booking = DB::table('orders')->
//        select('*', 'orders.id')->
//        where('data_booking','=', $date)->
//        leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->get();
//        //dd($booking);
        if ($booking == []) {
            $title = 'Бронирований на этот день нет';
        } else {
            $title = '';
        }

//        $del = DB::table('orders')
//            ->select('deleted_at')
//            ->where('data_booking', '=', $date)
//            ->where('deleted_at', '!=', NULL)
//            ->get();
//


        $day = Carbon::parse($date)->day . '.'
            . Carbon::parse($date)->month . '.'
            . Carbon::parse($date)->year;
        // dd($day);

        $tables = DB::table('users')->get();
        // dd($dt);
        return view("allBookingDateAdmin", ['booking' => $booking, 'day' => $day, 'tables' => $tables, 'title' => $title]);
    }

}
