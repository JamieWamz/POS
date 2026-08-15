<?php
  require_once __DIR__ . '/../core/bootstrap.php';

  /*=============================================
  CONTROLLERS
  =============================================*/
  require_once "controllers/template.controller.php";
  require_once "controllers/users.controller.php";
  require_once "controllers/categories.controller.php";
  require_once "controllers/products.controller.php";
  require_once "controllers/customers.controller.php";
  require_once "controllers/sales.controller.php";
  require_once "controllers/expenses.controller.php";

  /*=============================================
  MODELS
  =============================================*/
  require_once "models/users.model.php";
  require_once "models/categories.model.php";
  require_once "models/products.model.php";
  require_once "models/customers.model.php";
  require_once "models/sales.model.php";
  require_once "models/expenses.model.php";
?>

<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="theme-color" content="#111827">

  <title><?php echo e(app_config('name', 'Golden Tap POS')); ?></title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="icon" href="views/img/template/icono-negro.png">

  <!--=================================
  =            Plugins CSS            =
  ==================================-->
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="views/bower_components/bootstrap/dist/css/bootstrap.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="views/bower_components/font-awesome/css/font-awesome.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="views/dist/css/AdminLTE.min.css">

  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="views/dist/css/skins/skin-red-light.min.css">
  <link rel="stylesheet" href="views/css/modern-pos.css">

   <!-- DataTables -->
  <link rel="stylesheet" href="views/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="views/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">

  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="views/plugins/iCheck/minimal/blue.css">

  <!-- Daterange picker -->
  <link rel="stylesheet" href="views/bower_components/bootstrap-daterangepicker/daterangepicker.css">

  <!-- Morris chart -->
  <link rel="stylesheet" href="views/bower_components/morris.js/morris.css">

  <!--====  End of Plugins CSS  ====-->

  <!--========================================
  =            plugins javascript            =
  =========================================-->
  <!-- jQuery 3 -->
  <script src="views/bower_components/jquery/dist/jquery.min.js"></script>
  <script>
    window.POS = Object.freeze({
      csrfToken: <?php echo json_encode(csrf_token(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
    });

    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function (form) {
        if (!form.querySelector('input[name="csrf_token"]')) {
          var input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'csrf_token';
          input.value = window.POS.csrfToken;
          form.appendChild(input);
        }
      });
    });

    window.submitSecurePost = function (action, values) {
      var form = document.createElement('form');
      form.method = 'post';
      form.action = action;
      var payload = Object.assign({csrf_token: window.POS.csrfToken}, values || {});
      Object.keys(payload).forEach(function (key) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = payload[key];
        form.appendChild(input);
      });
      document.body.appendChild(form);
      form.submit();
    };

    if (window.jQuery) {
      jQuery.ajaxSetup({headers: {'X-CSRF-Token': window.POS.csrfToken}});
    }
  </script>

  <!-- Bootstrap 3.3.7 -->
  <script src="views/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

  <!-- FastClick -->
  <script src="views/bower_components/fastclick/lib/fastclick.js"></script>

  <!-- AdminLTE App -->
  <script src="views/dist/js/adminlte.min.js"></script>

   <!-- DataTables -->
  <script src="views/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="views/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="views/bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
  <script src="views/bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>

  <!-- sweet alert -->
  <script src="views/plugins/sweetalert2/sweetalert2.all.js"></script>

  <!-- iCheck 1.0.1 -->
  <script src="views/plugins/iCheck/icheck.min.js"></script>
  <!-- InputMask -->
  <script src="views/plugins/input-mask/jquery.inputmask.js"></script>
  <script src="views/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
  <script src="views/plugins/input-mask/jquery.inputmask.extensions.js"></script>
  <!-- jQuery Number -->
  <script src="views/plugins/jqueryNumber/jquerynumber.min.js"></script>

  <!-- daterangepicker http://www.daterangepicker.com/-->
  <script src="views/bower_components/moment/min/moment.min.js"></script>
  <script src="views/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

  <!-- Morris.js charts http://morrisjs.github.io/morris.js/-->
  <script src="views/bower_components/raphael/raphael.min.js"></script>
  <script src="views/bower_components/morris.js/morris.min.js"></script>

  <!-- ChartJS http://www.chartjs.org/-->
  <script src="views/bower_components/chart.js/Chart.min.js"></script>

</head>

<body class="hold-transition skin-red-light sidebar-mini login-page pos-shell">

<!-- Site wrapper -->

  <?php

    if(is_authenticated()){

      echo '<div class="wrapper">';

      /*=============================================
      =            header          =
      =============================================*/

      include "modules/header.php";

      /*=============================================
      =            sidebar          =
      =============================================*/

      include "modules/sidebar.php";

      /*=============================================
      =            Content          =
      =============================================*/

      $route = isset($_GET["route"]) ? (string) $_GET["route"] : "home";
      $routeRoles = [
        "home" => [],
        "users" => ["Administrator"],
        "activity" => ["Administrator"],
        "categories" => ["Administrator", "Special"],
        "products" => ["Administrator", "Special"],
        "customers" => ["Administrator", "Seller"],
        "sales" => ["Administrator", "Seller"],
        "create-sale" => ["Administrator", "Seller"],
        "edit-sale" => ["Administrator"],
        "reports" => ["Administrator", "Seller"],
        "expenses" => ["Administrator", "Seller"],
        "expenses-report" => ["Administrator", "Seller"],
        "logout" => []
      ];

      if (!array_key_exists($route, $routeRoles)) {
        include "modules/404.php";
      } elseif (!user_has_role($routeRoles[$route])) {
        http_response_code(403);
        include "modules/403.php";
      } else {
        include "modules/".$route.".php";
      }

      /*=============================================
      =            Footer          =
      =============================================*/

      include "modules/footer.php";

      echo '</div>';

    }else{
       /*=============================================
      =            login          =
      =============================================*/

      include "modules/login.php";
    }

  ?>

<!-- ./wrapper -->

<script src="views/js/template.js"></script>
<script src="views/js/users.js"></script>
<script src="views/js/categories.js"></script>
<script src="views/js/products.js"></script>
<script src="views/js/customers.js"></script>
<script src="views/js/sales.js"></script>
<script src="views/js/reports.js"></script>
<script src="views/js/expenses.js"></script> <!-- ADD THIS LINE -->

</body>
</html>
