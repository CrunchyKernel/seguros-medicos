ID: {{$cotizacionDatos->id_cotizacion}}<br>
Paquete: {{$planes}}<br>
Nombre: {{$cotizacionDatos->nombre}}<br>
Telefono: {{$cotizacionDatos->telefono}}<br>
Ciudad: {{$cotizacionDatos->ciudad}}, {{$cotizacionDatos->estado}}<br>
Cuenta con poliza: {{$cotizacionDatos->poliza_actual}}<br>
Integrantes: {{$integrantes}}<br>
Whatsapp: <a href="https://wa/me/521{{$cotizacionDatos->telefono}}">{{$cotizacionDatos->telefono}}</a><br>
Mapfre: <a href="https://zonaliados.mapfre.com.mx/Zonaliados.Multiplataforma/AYESalud?Cotizacion={{$cotizacionDatos->mapfre_numero}}">{{$cotizacionDatos->mapfre_numero}}</a><br>
Editar cotizacion: <a href="https://www.segurodegastosmedicosmayores.mx/cotizacion-nuevo/{{$cotizacionDatos->id_cotizacion}}/{{$cotizacionDatos->secret}}">aqui</a><br>
Cotizacion Admin: <a href="https://www.segurodegastosmedicosmayores.mx/admingm/cotizacion/verCotizacion/{{$cotizacionDatos->id_cotizacion}}/{{$cotizacionDatos->secret}}">ver cotizacion</a><br>
Origen: {{$cotizacionDatos->ruta}}<br>
