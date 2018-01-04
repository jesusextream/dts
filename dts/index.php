<?php
    session_start();
    session_destroy();
    if(isset($_GET['salida'])){
        $salida=$_GET['salida'];
        if($salida=='valida'){ $salida = 'Ha Salido Correctamente del Sistema.';}
        else if($salida=='fallida'){ $salida = 'Usuario y/o Clave Invalida';}
        else if($salida=='invalida'){ $salida = 'Por Medidas de Seguridad <br>Ha Salido del Sistema.'; }
        else if($salida=='inactivo'){ $salida = 'Su Usuario Se Encuentra Inactivo.'; }
        else if($salida=='no_registrado'){ $salida = 'Su Usuario No Se Encuentra Registrado.'; }
        else if($salida=='error_emp'){ $salida = 'Empresa No Encontrada.'; }
    }else{ $salida='Bienvenidos...'; }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>DTS Autoparts</title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form id="fromLogin" action="common/start_sesion.php" method="POST">
              <h1>Login</h1>
              <div>
                <input id="Username" name="Username" type="text" class="form-control" placeholder="Username" required="" />
              </div>
              <div>
                <input id="Password" name="Password" type="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <button class="btn btn-default submit" type="submit">Log in</button>
                <a class="reset_pass" href="#">Lost your password?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">New to site?
                  <a href="#signup" class="to_register"> Create Account </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-paw"></i> DTS Autoparts</h1>
                  <p>©2017 All Rights Reserved. DTS Autoparts. Privacy and Terms</p>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="register" class="animate form registration_form">
          <section class="login_content">
            <form>
              <h1>Create Account</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" required="" />
              </div>
              <div>
                <input type="email" class="form-control" placeholder="Email" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <a class="btn btn-default submit" href="index.html">Submit</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Already a member ?
                  <a href="#signin" class="to_register"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-paw"></i> DTS Autoparts</h1>
                  <p>©2017 All Rights Reserved. DTS Autoparts. Privacy and Terms</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
    <script>
      //script
    </script>
  </body>
</html>
