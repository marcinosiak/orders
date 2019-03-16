<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Order;

class HomeController extends Controller
{
    //Home page
    public function home()
    {
      // Pobieram id wszystkich zamówień
      // $query = DB::select('SELECT id_order FROM ps_orders ORDER BY id_order;');


      $query = DB::select('SELECT o.id_order, e.firstname, e.lastname, oh.id_order_state, oh.date_add
                            FROM ps_orders o
                            LEFT JOIN ps_order_history oh ON o.id_order = oh.id_order
                            LEFT JOIN ps_employee e ON oh.id_employee = e.id_employee
                            ORDER BY o.id_order, oh.date_add;
                           '
      );

      $ord = new Order($query);

      $id_all_orders = $ord->getIdAllOrders();
// dd($ord->getSumOfRepeatedStatuses(config('statusy.KOMPLETOWANIE_NA_MAGAZYNIE'), config('statusy.WYDANIE'), 15));
// dd($ord->getFirstStatusBehind(config('statusy.PRZETWARZANIE_PO_WYDANIU'), 15));
      $orders = [];
      $temp_order = [];

      foreach ($id_all_orders as $id) {
        $temp_order = $ord->prepareOneRow($id);
        array_push($orders, $temp_order);
      }

      return view('home', ['orders' => $orders]);
    }
}
