<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    //Home page
    public function home()
    {
      // $orders = DB::select('SELECT o.id_order, e.firstname, e.lastname
      //                       FROM ps_orders o, ps_order_history oh, ps_employee e
      //                       WHERE oh.id_employee = e.id_employee
      //                      '
      // );

      $orders = DB::select('SELECT o.id_order, e.firstname, e.lastname, oh.date_add
                            FROM ps_orders o
                            LEFT JOIN ps_order_history oh ON o.id_order = oh.id_order
                            LEFT JOIN ps_employee e ON oh.id_employee = e.id_employee
                            ORDER BY o.id_order
                           '
      );

      // $orders = DB::select('SELECT oh.id_order, oh.id_employee, e.firstname, e.lastname
      //                       FROM ps_order_history oh, ps_employee e
      //                       WHERE oh.id_employee = e.id_employee'
      // );

      return view('home', ['orders' => $orders]);
    }
}
