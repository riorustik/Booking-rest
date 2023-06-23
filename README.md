# WEB-Сайт бронирования столиков в ресторане

Учебный проект, реализован в 2020 году на языке **`PHP`**

WEB-Сайт бронирования столов в ресторане. Имеет кабинет пользователя. Панель администратора. Систему бронирования с хранением в базе данных `MySQL`

Проект написан с использованием фреймворка **`Laravel`**

Функция бронирования стола
```
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
```

Пути следования и зависимые конроллеры пользователя
```
  Route::post('/create-order/{id}', [OrderController::class, 'createOrder'])->name('create-order');
  Route::get('/info/tables', [TableController::class, 'allTablesRest'])->name('infoOfAllTables');
  Route::get('/booking/all/{id}/delete', [OrderController::class, 'deleteOrder'])->name('order-delete');
  Route::get('/success', [OrderController::class, 'successBooking'])->name('success-booking');
  Route::get('/error', [OrderController::class, 'errorBooking'])->name('error-booking');
```

Схема таблицы пользоватлей в базе данных
```
 public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
```

