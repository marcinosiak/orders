@extends('master')

@section('content')
  <h4 class="my-3">Wydajność zamówień</h4>
  <pre>
  {{-- {{dd($orders)}} --}}
  {{-- {{ var_dump($orders) }} --}}
</pre>

  <table class="table">
  <thead class="thead-light">
    <tr>
      <th scope="col">Zamówienie</th>
      <th scope="col">Pracownik</th>
      <th scope="col">Data</th>
      <th scope="col">Start (opóźnienie)</th>
      <th scope="col">Przygotowanie do wydania</th>
      <th scope="col">Wydanie</th>
      <th scope="col">Przetwarzanie po wydaniu</th>
      <th scope="col">Całkowity czas realizacji</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($orders as $order)
    <tr>
      <th scope="row">{{$order->id_order}}</th>
      <td>{{$order->firstname}} {{$order->lastname}}</td>
      <td>{{$order->date_add}}</td>
      <td>@mdo</td>
      <td>Mark</td>
      <td>Otto</td>
      <td>@mdo</td>
      <td>Otto</td>
    </tr>
    @endforeach
    {{-- <tr>
      <th scope="row">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
    </tr>
    <tr>
      <th scope="row">3</th>
      <td>Larry</td>
      <td>the Bird</td>
      <td>@twitter</td>
    </tr> --}}
  </tbody>
</table>

@endsection
