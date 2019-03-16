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
     * Jest to data złożenia zamówienia
     * Kolumna Data
     */
    public function getDateOfOrder($id)
    {
      $all_status = $this->getAllStatusOrders($id);
      $first_status = array_shift($all_status);

      return $first_status;
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
     * Pobiera dowolny status o podanym $idStatus dla podanego zamówienia $id
     * Pobiera tylko jedno, pierwsze wystąpienie tego statusu
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
     * Zwraca pierwsze wystąpienie statusu spośród podanych kilku w tablicy.
     * Sprawdza czy są statusy dla podanego zamówienia. Ważna jest kolejnosć
     * statusów w tablicy. Jeśli znajdzie pierwszy, przerywa działanie i zwraca ten status.
     *
     * @param  array  $statuses - tablica ze statusami do sprawdzenia
     * @param  int $id  - indentyfikator zamówienia
     * @return array lub null
     */
    private function getFirstOccurrenceStatus($statuses = [], $id)
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
     * Sumuje czas pomiędzyy dwoma znanymi statusami.
     * Statusy mogą występować w bazie po kilka razy.
     * @param  int $first_status  [description]
     * @param  int $second_status [description]
     * @param  int $id            [description]
     * @return int $time          - liczba minut
     */
    private function getSumOfRepeatedKnowStatuses($first_status, $second_status, $id)
    {
      $found_statuses = [];
      $time = 0;

      $all_status = $this->getAllStatusOrders($id);

      // wyodrębniam statusy do tablicy
      foreach ($all_status as $status) {
        if($status->id_order_state == $first_status || $status->id_order_state == $second_status)
        {
          array_push($found_statuses, $status);
        }
      }

      $time = $this->getCountSum($found_statuses);
      //jak pobrać stąd pracownika i przekazać dalej
      return $time;
    }

    /**
     * Liczy sumę czasu pomiędzy dwoma sąsiednimi statusami w tablicy
     * @param  array $array_of_statuses - tablica ze statusami
     * @return int $time - suma minut
     */
    private function getCountSum($array_of_statuses)
    {
      $time = 0;
      $size = count($array_of_statuses);

      // rozmiar musi być parzysty
      if ($size % 2 != 0) {
        $size = $size - 1 ;
      }

      for ($i = 0; $i < $size; $i += 2) {
        $time_first_status = \Carbon\Carbon::parse($array_of_statuses[$i]->date_add);
        $time_second_status = \Carbon\Carbon::parse($array_of_statuses[$i+1]->date_add);
        $time += $time_first_status->diffInminutes($time_second_status);
      }

      return $time;
    }

    /**
     * Zwraca tablicę ze znalezionym statusem i następnym za nim
     * może być takich par kilka
     * @param  int $status [description]
     * @param  int $id     [description]
     * @return [type]         [description]
     */
    public function getFirstStatusBehind($know_status, $id)
    {
      $found_statuses = [];
      $all_status = $this->getAllStatusOrders($id);


      $length = count($all_status);
      for($i = 0; $i < $length; $i++)
      {
        if($all_status[$i]->id_order_state == $know_status)
        {
          array_push($found_statuses, $all_status[$i]);
          if($i+1 < $length)
            array_push($found_statuses, $all_status[$i+1]);
        }
      }

      return $this->getCountSum($found_statuses);
    }

    /**
    * Przygotowuje pojedyńczy wiersz do wyświetlenia w tabeli
    */
    public function prepareOneRow($id)
    {
      $temp_order = [];
      $pracownicy = [];

      /* KOLUMNA DATA ------------------------------------------------------- */

      $first_status = $this->getDateOfOrder($id);

      $temp_order['zamowienie'] = $first_status->id_order;
      // $temp_order['pracownik'] = $first_status->firstname." ".$first_status->lastname;
      array_push($pracownicy, $first_status->firstname." ".$first_status->lastname);
      $temp_order['data'] = $first_status->date_add;

      // String to time
      // Carbon $time_of_order - czas złożenia zamówienia
      $time_of_order = \Carbon\Carbon::parse($first_status->date_add);


      /* KOLUMNA START ------------------------------------------------------ */

      // array/null $start_status - pierwszy status ustawiony przez pracownika sklepu
      // będzie to zawsze status W_TRAKCIE_PRZETWARZANIA
      $start_status = $this->getFirstOccurrenceStatus([
                              //config('statusy.OCZEKIWANIE_NA_PLATNOSC_BM'),
                              //config('statusy.NOWE_NIEZATWIERDZONE'),
                              config('statusy.W_TRAKCIE_PRZETWARZANIA')
                    ], $id);

      if ($start_status != null) {
        $temp_order['start'] = $start_status->date_add;
        // $temp_order['pracownik_2'] = $start_status->firstname." ".$start_status->lastname;
        array_push($pracownicy, $start_status->firstname." ".$start_status->lastname);

        // String to time
        $start = \Carbon\Carbon::parse($start_status->date_add);
        // Różnica w minutach
        $temp_order['opoznienie'] = $time_of_order->diffInminutes($start);
      }
      else {
        $temp_order['start'] = "brak";
        $temp_order['pracownik_2'] = null;
        $temp_order['opoznienie'] = null;
      }

      /* PRZYGOTOWANIE DO WYDANIA ------------------------------------------- */

      // Przygotowanie do wydania = obliczane według statusów Kompletowanie na magazynie - W trakcie przetwarzania
      $kompketowanie_status = $this->getAnyStatus($id, config('statusy.KOMPLETOWANIE_NA_MAGAZYNIE'));

      if ($kompketowanie_status != null) {
        $kompketowanie = \Carbon\Carbon::parse($kompketowanie_status->date_add);

        $start = isset($start) ? $start : 0;
        // przygotowanie_do_wydania = Kompletowanie na magazynie - W trakcie przetwarzania
        $temp_order['przygotowanie_do_wydania'] = $kompketowanie->diffInminutes($start);
        // $temp_order['pracownik_3'] = $kompketowanie_status->firstname." ".$kompketowanie_status->lastname;
        array_push($pracownicy, $kompketowanie_status->firstname." ".$kompketowanie_status->lastname);
      }
      else {
        $temp_order['przygotowanie_do_wydania'] = null;
        $temp_order['pracownik_3'] = null;
      }

      /* WYDANIE ------------------------------------------------------------ */
      $temp_order['wydanie'] =  $this->getSumOfRepeatedKnowStatuses(config('statusy.KOMPLETOWANIE_NA_MAGAZYNIE'), config('statusy.WYDANIE'), $id);


      /* PRZETWARZANIE PO WYDANIU ------------------------------------------- */
      $temp_order['przetwarzanie_po_wydaniu'] = $this->getFirstStatusBehind(config('statusy.PRZETWARZANIE_PO_WYDANIU'), $id);
      //pobrać pracownika

      /* CAŁKOWITY CZAS REALIZACJI ------------------------------------------ */
      $temp_order['calkowity_czas'] = $temp_order['przygotowanie_do_wydania'] + $temp_order['wydanie'] + $temp_order['przetwarzanie_po_wydaniu'];

      $temp_order['pracownicy'] = array_unique($pracownicy);

      return $temp_order;
    }

}
