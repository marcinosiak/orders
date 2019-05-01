@extends('master')

@section('content')
  <h4 class="my-3">Wydajność zamówień</h4>

  <form action="{{url('zakres')}}" method="post" target="_self">
    <div class="input-append date form_date">
        <label for="from_date">data od:</label>
        <input size="13" type="text" value="" class="dtp" name="from_date" id="from_date" data-date-format="yyyy-mm-dd hh:ii:ss">
        <span class="add-on dtp"><i class="icon-th"></i></span>
    </div>

    <div class="input-append date to_date">
        <label for="data1">data do:</label>
        <input size="13" type="text" value="" class="dtp" name="to_date" id="to_date" data-date-format="yyyy-mm-dd hh:ii:ss">
        <span class="add-on dtp"><i class="icon-th"></i></span>
    </div>

    <input type="submit" class="btn btn-primary btn-sm btn-szukaj" value="Szukaj zamówień">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
  </form>

  <script type="text/javascript">
      $(function () {
          //https://www.malot.fr/bootstrap-datetimepicker/index.php
          $('.form_date').datetimepicker({
              language:  'pl',
              format: 'yyyy-mm-dd hh:ii:ss',
              weekStart: 1,
              todayBtn:  1,
              autoclose: 1,
              todayHighlight: 2,
              startView: 2,
              minView: 0,
              forceParse: 0,
              pickerPosition: "bottom-left"
            });

            $('.to_date').datetimepicker({
                language:  'pl',
                format: 'yyyy-mm-dd hh:ii:ss',
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 2,
                startView: 2,
                minView: 0,
                forceParse: 0,
                pickerPosition: "bottom-left"
              });

              var last_x_days = moment().subtract(7, 'days').format('YYYY-MM-DD HH:mm:ss');
              var now = moment().format('YYYY-MM-DD HH:mm:ss');

              $('#from_date').val(last_x_days);
              $('#to_date').val(now);

              // https://momentjs.com/
              // console.log(now);
              // console.log(last_x_days);
      });

      // console.log(moment().format());
  </script>


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
      <th scope="row">{{$order['zamowienie']}}</th>
      <td>
        {{-- {{var_dump($order['pracownicy'])}} --}}
        {{--
        @if ($order['pracownik'] != " ") {{ $order['pracownik'] }} <br> @endif
        @if ($order['pracownik_2']) {{ $order['pracownik_2'] }} <br> @endif
        @if ($order['pracownik_3']) {{ $order['pracownik_3'] }} <br> @endif
         --}}
        @foreach ($order['pracownicy'] as $pracownik)
          @if ($pracownik != " ") {{ $pracownik }} <br> @endif
        @endforeach
      </td>
      <td>{{$order['data']}}</td>
      <td>
        {{$order['start']}}
        @if ($order['opoznienie']) ({{$order['opoznienie']}} min) @endif
      </td>
      <td>
        @if ($order['przygotowanie_do_wydania']) {{$order['przygotowanie_do_wydania']}} min @else - @endif
      </td>
      <td>
        @if ($order['wydanie']) {{$order['wydanie']}} min @else - @endif
      </td>
      <td>
        @if ($order['przetwarzanie_po_wydaniu']) {{$order['przetwarzanie_po_wydaniu']}} min @else - @endif
      </td>
      <td>
        @if ($order['calkowity_czas']) {{$order['calkowity_czas']}} min @else - @endif
      </td>
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
