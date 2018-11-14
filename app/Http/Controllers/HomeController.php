<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    //Home page
    public function home()
    {
      $orders = DB::select('SELECT oh.id_order, oh.id_employee, e.firstname, e.lastname 
                            FROM ps_order_history oh, ps_employee e
                            WHERE oh.id_employee = e.id_employee'
      );

      return view('home', ['orders' => $orders]);
    }
}
