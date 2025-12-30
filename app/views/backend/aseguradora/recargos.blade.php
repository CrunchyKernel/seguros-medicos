@section('contenido')
    <div class="container-fluid">
        <table class="table table-condensed table-bordered">
            <thead>
                <tr class="info">
                    <th>
                       <h4>Aseguradora</h4>
                    </th>
                    <th>
                      <h4>Mensual</h4>
                    </th>
                    <th>
                        <h4>Trimestral</h4>
                    </th>
                    <th>
                        <h4>Semestral</h4>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($recargos as $recargo)
                    <tr>
                        <td>
                            {{$recargo->logo}}
                        </td>
                        <td>
                            <a href="#"  class="campo editable editable-click" data-type="text" data-ciclo="interes_mensual" data-aseguradora="{{$recargo->logo}}" data-original-title="Interes mensual" >
                                {{$recargo->interes_mensual}} </a>
                        </td>
                        <td>
                            <a href="#"  class="campo editable editable-click" data-type="text" data-ciclo="interes_trimestral" data-aseguradora="{{$recargo->logo}}" data-original-title="Interes trimestral" >
                                {{$recargo->interes_trimestral}} </a>
                        </td>
                        <td>
                            <a href="#"  class="campo editable editable-click" data-type="text" data-ciclo="interes_semestral" data-aseguradora="{{$recargo->logo}}" data-original-title="Interes semestral">
                                {{$recargo->interes_semestral}} </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop