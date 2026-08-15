<?php

if($_SESSION["profile"] == "Special"){

  echo '<script>

    window.location = "home";

  </script>';

  return;

}

?>
<div class="content-wrapper">

  <section class="content-header">

    <div><span class="pos-section-label">Business</span><h1>Sales report</h1><p class="pos-page-description">Compare revenue, transactions and products across a selected period.</p></div>

    <ol class="breadcrumb">

      <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>

      <li class="active">Sales report</li>

    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <div class="input-group">

          <button type="button" class="btn btn-default" id="daterange-btn2">

            <span>
              <i class="fa fa-calendar"></i> Date range
            </span>

            <i class="fa fa-caret-down"></i>

          </button>

        </div>

        <div class="box-tools pull-right">



        <!-- Change "inicialDate" to "initialDate" on the lines below -->
        <?php

        if(valid_date(isset($_GET["initialDate"]) ? (string)$_GET["initialDate"] : null) && valid_date(isset($_GET["finalDate"]) ? (string)$_GET["finalDate"] : null)){

          echo '<a href="views/modules/download-report.php?report=report&amp;initialDate='.rawurlencode($_GET["initialDate"]).'&amp;finalDate='.rawurlencode($_GET["finalDate"]).'">';

        }else{

          echo '<a href="views/modules/download-report.php?report=report">';

        }

        ?>
           <button class="btn btn-success" style="margin-top:5px">Export CSV</button>

          </a>

        </div>

      </div>

      <div class="box-body">

        <div class="row">

          <div class="col-xs-12">

            <?php

            include "reports/sales-graph.php";

            ?>

          </div>

           <div class="col-md-6 col-xs-12">

            <?php

            include "reports/bestseller-products.php";

            ?>

          </div>

          <div class="col-md-6 col-xs-12">

            <?php

            include "reports/sellers.php";

            ?>

         </div>

         <div class="col-md-6 col-xs-12">

            <?php

            include "reports/buyers.php";

            ?>

         </div>

        </div>

      </div>

    </div>

  </section>

 </div>
