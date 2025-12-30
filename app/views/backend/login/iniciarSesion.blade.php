<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="Seguro de Gastos médicos">
        <meta name="author" content="li_s.javier@hotmail.com">

        <link rel="shortcut icon" href="{{asset('assets/images/icon/favicon.ico')}}">

        <title>Inicar sesión - Seguro de Gastos médicos</title>

        {{HTML::style('backend/css/style.default.css')}}
        {{HTML::style('backend/css/jquery.gritter.css')}}
        {{HTML::style('backend/css/logincesiztel.css')}}

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="signin">
    <!--
    you can substitue the span of reauth email for a input with the email and
    include the remember me checkbox
    -->
    <div class="container">
        <div class="card card-container">
            <div class="logo text-center">
                {{HTML::image('protectodiez/logos/gastosmedicosmayores200.jpg')}}
            </div>
            <p id="profile-name" class="profile-name-card"></p>
            <form class="form-signin" id="iniciarSesionForm" name="iniciarSesionForm" method="post" >
              <div class="input-group mb15 form-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" id="username" name="username" class="form-control" placeholder="Email - usuario">
              </div><!-- input-group -->
              <div class="input-group mb15 form-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña">
              </div><!-- input-group -->
              <br>
              <div class="clearfix">
                <div class="pull-right">
                  <button type="button" class="btn btn-block btn-success iniciarSesion" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Iniciar sesión <i class="fa fa-angle-right ml5"></i></button>
                </div>
              </div>
            </form><!-- /form -->
            <div id="divError" class="hidden" style='color:#f00;'>
            	Tu usuario ha sido bloqueado, te enviamos un correo eletrónico con instrucciones para desbloquearlo
            </div>
            <br>
            <a href="#" class="forgot-password">
                ¿Olvidaste tu contraseña?
            </a>
        </div><!-- /card-container -->
    </div><!-- /container -->
        <script type="text/javascript">
            var _root_ = "{{ URL::to("/admingm/"); }}/";
        </script>
        {{HTML::script('backend/js/jquery-1.11.1.min.js')}}
        {{HTML::script('backend/js/jquery-migrate-1.2.1.min.js')}}
        {{HTML::script('backend/js/bootstrap.min.js')}}
        {{HTML::script('backend/js/jquery.validate.min.js')}}
        {{HTML::script('backend/js/jquery.gritter.min.js')}}
        {{HTML::script('backend/js/helpers/login.js?20251009')}}
    </body>
</html>
