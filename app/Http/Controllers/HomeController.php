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

      // $first = $ord->getFirtsElement();
      // dd($first->firstname);
      // dd($first);

      // $orders = $ord->getAllStatusOrders(10);
      // dd($orders);

      // $orders = $ord->getAnyStatus(10, 4);
      // dd($orders);

      // $orders = $ord->getFirstStatus(10);
      // dd($orders);

      $id_all_orders = $ord->getIdAllOrders();

      $orders = [];
      $temp_order = [];
      // foreach ($all_id_orders as $id) {
      //   $first_status = $ord->getFirstStatus($id);
      //   array_push($orders, $first_status);
      //
      //   $any_status = $ord->getAnyStatus($id, 4);
      //   array_push($orders, $any_status);
      // }

      foreach ($id_all_orders as $id) {
/*        $first_status = $ord->getFirstStatus($id);
        // var_dump($first_status);
        // dd($first_status);

        $temp_order['zamowienie'] = $first_status->id_order;
        $temp_order['pracownik'] = $first_status->firstname." ".$first_status->lastname;
        $temp_order['data'] = $first_status->date_add;

        $any_status = $ord->getAnyStatus($id, 4);

        if ($any_status != null) {
          # code...
          $temp_order['start'] = $any_status->date_add;
          $temp_order['pracownik_2'] = $any_status->firstname." ".$any_status->lastname;
        }
        else {
          $temp_order['start'] = "brak";
          $temp_order['pracownik_2'] = "";
        }
*/
        // var_dump($any_status['date_add']);
        // dd($any_status['date_add']);
        // $orders['start'] = $any_status['date_add'];
        // $orders['start'] = $any_status->date_add;
        // var_dump($temp_order);
        $temp_order = $ord->prepareOneRow($id);
        array_push($orders, $temp_order);
        // var_dump($temp_order['date_add']);
        // var_dump($temp_order);
      }

      // var_dump($orders);
      // dd($orders);
      // Pobieram pierwszy status dla zamówienia a później następny dowolny status i muszę je połączyć
      // do tej samej tablicy
      // Jak dodać $ord->getFirstStatus i $ord->getAnyStatus do jednej tablicy $orders ???

      return view('home', ['orders' => $orders]);
    }
}
