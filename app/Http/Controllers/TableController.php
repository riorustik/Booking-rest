<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();

        return view('admin.allTables', ['tables'=>$tables]);
    }
    //
    public function create(Request $request)
    {
        $table = new Table();
        $table->number = $request->number;
        $table->type_hall = $request->type_hall;
        $table->deposit = $request->deposit;
        $table->guest_quantity = $request->guest_quantity;
        $path=$request->image->store('tables');
        $table->image = $path;

        $table->save();

        return redirect()->route("tables");
    }

    public function createTable()
    {
        return view('admin.addTable');
    }

    public function showOneTable($id)
    {
        $table = new Table();
        return view('admin.showTable', ['table'=>$table->find($id)]);
    }


    public function updateTable($id)
    {
        $table = new Table();
        return view('admin.updateTable', ['table'=>$table->find($id)]);
    }

    public function updateTableSubmit($id, Request $request)
    {

        $table = Table::find($id);
        $table->number = $request->number;
        $table->type_hall = $request->type_hall;
        $table->deposit = $request->deposit;
        $table->guest_quantity = $request->guest_quantity;
        $path=$request->image->store('tables');
        $table->image = $path;
        $table->save();

        return redirect()->route("tables");
    }
    public function  deleteTable($id){
        Table::where('id', '=',$id)->delete();
        return redirect()->route("tables");
    }

    public function allTablesRest()
    {
        $tables = Table::all();
        return view('index', ['tables'=>$tables]);
    }

    public function  deleteUsers($id){
        User::where('id', '=',$id)->delete();
        DB::table('model_has_roles')
            ->where('model_id','=', $id)
            ->delete();
        return redirect()->route("users-admin");
    }


}
