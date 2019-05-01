<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Http\Request;
use App\Order;
use App\BusinessDaysCalculator;
use App\BusinessHoursCalc;

class HomeController extends Controller
{
    //Home page
    public function home()
    {
      date_default_timezone_set('Europe/Warsaw');

      // $start = '2019-04-24 16:00:00';
      // $end = '2019-04-25 10:01:00';

      // $start = DateTime::createFromFormat('Y-m-d H:i:s', $start);
      // $t = $start->format('Y-m-d');
      // dd($t);

      // $start = DateTime::createFromFormat('Y-m-d H:i:s', $start);
      // $end = DateTime::createFromFormat('Y-m-d H:i:s', $end);
      // $s = BusinessDaysCalculator::getWorkingHoursInSeconds($start, $end);
      // $t = $date->format('Y-m-d H:i:s');
      // dd($s/60);

      // dd(BusinessDaysCalculator::isThatDateWorkingDay('2019-04-23'));
      // dd(date("d-M-Y",easter_date(2019)));

      // Pobieram id wszystkich zamówień
      // $query = DB::select('SELECT id_order FROM ps_orders ORDER BY id_order;');

      // $query = DB::select('SELECT o.id_order, e.firstname, e.lastname, oh.id_order_state, oh.date_add
      //                       FROM ps_orders o
      //                       LEFT JOIN ps_order_history oh ON o.id_order = oh.id_order
      //                       LEFT JOIN ps_employee e ON oh.id_employee = e.id_employee
      //                       ORDER BY o.id_order, oh.date_add;
      //                      '
      // );

      // $query = DB::select('SELECT o.id_order, o.date_add, e.firstname, e.lastname, oh.id_order_state, oh.date_add
      //                       FROM ps_orders o
      //                       JOIN ps_order_history oh ON o.id_order = oh.id_order AND o.date_add > "2019-01-01 00:00:00"
      //                       LEFT JOIN ps_employee e ON oh.id_employee = e.id_employee
      //
      //                       ORDER BY o.id_order, oh.date_add;
      //                      '
      // );

      /* -- ostatnie np. 20 zamówień -- */
      // $query = DB::select('SELECT o.id_order, e.firstname, e.lastname, oh.id_order_state, oh.date_add
      //                       FROM (SELECT id_order FROM ps_orders ORDER BY id_order DESC LIMIT 20) o
      //                       JOIN ps_order_history oh ON o.id_order = oh.id_order
      //                       LEFT JOIN ps_employee e ON oh.id_employee = e.id_employee
      //                       ORDER BY o.id_order DESC, oh.date_add DESC;
      //                      '
      // );

      $query = DB::select('SELECT o.id_order, e.firstname, e.lastname, oh.id_order_state, oh.date_add
                            FROM (SELECT id_order FROM ps_orders ORDER BY id_order DESC LIMIT 20) o
                            JOIN ps_order_history oh ON o.id_order = oh.id_order
                            LEFT JOIN ps_employee e ON oh.id_employee = e.id_employee
                            ORDER BY o.id_order DESC, oh.date_add;
                           '
      );

      // $query_pag = DB::table('ps_orders')
      //              ->leftJoin('ps_order_history', 'ps_orders.id_order', '=', 'ps_order_history.id_order')
      //              ->leftJoin('ps_employee', 'ps_order_history.id_employee', '=', 'ps_employee.id_employee')
      //              ->select('ps_orders.id_order', 'ps_employee.firstname', 'ps_employee.lastname', 'ps_order_history.id_order_state', 'ps_order_history.date_add')
      //              ->orderBy('ps_orders.id_order', 'ASC')
      //              ->orderBy('ps_order_history.date_add', 'ASC')
      //              ->paginate(20);

      $ord = new Order($query);

      // dd($ord->getAllStatusOrders(17));

      $id_all_orders = $ord->getIdAllOrders();

      $orders = [];
      $temp_order = [];

      foreach ($id_all_orders as $id) {
        $temp_order = $ord->prepareOneRow($id);
        array_push($orders, $temp_order);
      }

      return view('home', ['orders' => $orders]);
    }

    public function zakres(Request $request)
    {
      //  $someName = $request->someName;

      //  $validation = $request->validate([
      //    'from_date' => 'nullable|date',
      //    'to_date' => 'nullable|date',
      //  ]);

       $query = DB::select('SELECT o.id_order, e.firstname, e.lastname, oh.id_order_state, oh.date_add
                             FROM (SELECT id_order FROM ps_orders WHERE date_add >= ? AND date_add <= ? ORDER BY id_order) o
                             JOIN ps_order_history oh ON o.id_order = oh.id_order
                             LEFT JOIN ps_employee e ON oh.id_employee = e.id_employee
                             ORDER BY o.id_order DESC, oh.date_add DESC;
                            ', array($request->from_date, $request->to_date)
       );


       $ord = new Order($query);

       $id_all_orders = $ord->getIdAllOrders();

       $orders = [];
       $temp_order = [];

       foreach ($id_all_orders as $id) {
         $temp_order = $ord->prepareOneRow($id);
         array_push($orders, $temp_order);
       }

       return view('home', ['orders' => $orders]);
    }
}
