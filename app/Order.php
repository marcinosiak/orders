<?php

namespace App;

class Order
{
  // Wszystkie zamówienia z bazy ze wszystkimi statusami
  private $orders = [];

    public function __construct($orders_from_db)
    {
      $this->orders = $orders_from_db;
    }

    public function getOrders()
    {
      return $this->orders;
    }

    public function getFirtsElement()
    {
      return $this->orders[0];
    }

    /**
     * Pobiera pierwszy status dla podanego zamówienia
     */
    public function getFirstStatus($id)
    {
      $all_status = $this->getAllStatusOrders($id);
      $first_status = array_shift($all_status);

      return $first_status;
    }

    /**
     * Pobiera dowolny status o podanym $idStatus dla podanego zamówienia $id
     */
    public function getAnyStatus($id, $id_status)
    {
      $all_status = $this->getAllStatusOrders($id);

      foreach ($all_status as $status) {
        if($status->id_order_state == $id_status) {
          return $status;
        }
      }
      return null;
    }

    /**
     * Pobieram id wszystkich zamówień
     */
    public function getIdAllOrders()
    {
      $temp_order = [];
      $id_all_orders = [];

      foreach ($this->orders as $order) {
        array_push($temp_order, $order->id_order);
      }

      $id_all_orders = array_unique($temp_order);

      return $id_all_orders;
    }

    /**
     * Pobiera wszystkie statusy dla podanego zamówienia
     */
    public function getAllStatusOrders($id)
    {
      $all_status = [];

      foreach ($this->orders as $order) {
        if($order->id_order == $id){
          array_push($all_status, $order);
        }
      }

      return $all_status;
    }

    /**
     * Sprawdza czy sa statusy dla podanego zamówienia. Ważna jest kolejnosć
     * statusów w tablicy. Jeśli znajdzie pierwszy, przerywa działanie i zwraca ten status.
     *
     * @param  array  $statuses - tablica ze statusami do sprawdzenia
     * @param  int $id  - indentyfikator zamówienia
     * @return array lub null
     */
    private function statusesExist($statuses = [], $id)
    {
      foreach ($statuses as $status) {
        $any_status = $this->getAnyStatus($id, $status);
        if ($any_status != null) {
          return $any_status;
        }
      }

      return null;
    }

    /**
    * Przygotowuje pojedyńczy wiersz do wyświetlenia w tabeli
    */
    public function prepareOneRow($id)
    {
      $temp_order = [];
      $first_status = $this->getFirstStatus($id);

      $temp_order['zamowienie'] = $first_status->id_order;
      $temp_order['pracownik'] = $first_status->firstname." ".$first_status->lastname;
      $temp_order['data'] = $first_status->date_add;
      // String to time
      $data = \Carbon\Carbon::parse($first_status->date_add);

      $any_status = $this->statusesExist([
                              config('statusy.W_TRAKCIE_PRZETWARZANIA_OCZEKIWANIE_NA_PLATNOSC_BM'),
                              config('statusy.W_TRAKCIE_PRZETWARZANIA_NOWE_NIEZATWIERDZONE'),
                              config('statusy.W_TRAKCIE_PRZETWARZANIA')
                    ], $id);

      if ($any_status != null) {
        $temp_order['start'] = $any_status->date_add;
        $temp_order['pracownik_2'] = $any_status->firstname." ".$any_status->lastname;

        // String to time
        $start = \Carbon\Carbon::parse($any_status->date_add);
        // Różnica w minutach
        $temp_order['opoznienie'] = $data->diffInminutes($start);
      }
      else {
        $temp_order['start'] = "brak";
        $temp_order['pracownik_2'] = null;
        $temp_order['opoznienie'] = null;
      }

      // Przygotowanie do wydania = obliczane według statusów Kompletowanie na magazynie - W trakcie przetwarzania
      $kompketowanie_status = $this->getAnyStatus($id, config('statusy.KOMPLETOWANIE_NA_MAGAZYNIE'));

      if ($kompketowanie_status != null) {
        $kompketowanie = \Carbon\Carbon::parse($kompketowanie_status->date_add);

        $start = isset($start) ? $start : 0;
        // przygotowanie_do_wydania = Kompletowanie na magazynie - W trakcie przetwarzania
        $temp_order['przygotowanie_do_wydania'] = $kompketowanie->diffInminutes($start);
        $temp_order['pracownik_3'] = $kompketowanie_status->firstname." ".$kompketowanie_status->lastname;
      }
      else {
        $temp_order['przygotowanie_do_wydania'] = null;
        $temp_order['pracownik_3'] = null;
      }

      // Wydanie = obliczane według statusów "Wydane - Kompletowanie na magazynie"
      $wydanie_status = $this->getAnyStatus($id, config('statusy.WYDANIE'));

      if ($wydanie_status != null) {
        $wydanie = \Carbon\Carbon::parse($wydanie_status->date_add);

        $kompketowanie = isset($kompketowanie) ? $kompketowanie : 0;
        $temp_order['wydanie'] = $wydanie->diffInminutes($kompketowanie);
      }
      else {
        $temp_order['wydanie'] = null;
      }

      return $temp_order;
    }

}
