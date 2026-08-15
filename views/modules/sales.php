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

    <h1>
	<!-- Log on to codeastro.com for more projects! -->
      Golden Pub Sales Management

    </h1>

    <ol class="breadcrumb">

      <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>

      <li class="active">Dashboard</li>

    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <a href="create-sale">
          <button class="btn btn-success" >

          <i class="fa fa-plus"></i> Add Sale

          </button>
        </a>

        <button type="button" class="btn btn-primary pull-right" id="daterange-btn">

            <span>
              <i class="fa fa-calendar"></i> Date Range
            </span>

            <i class="fa fa-caret-down"></i>

        </button>

      </div>

      <div class="box-body">
		<!-- Log on to codeastro.com for more projects! -->
        <table class="table table-bordered table-hover table-striped dt-responsive tables" width="100%">

          <thead>

           <tr>

             <th style="width:10px">#</th>
             <th>Bill</th>
             <th>Customer</th>
             <th>Seller</th>
             <th>Payment Method</th>
             <th>Net Cost</th>
             <th>Total Cost</th>
             <th>Date</th>
             <th>Actions</th>

           </tr>

          </thead>

          <tbody>

            <?php

          $initialDate = valid_date(isset($_GET["initialDate"]) ? (string) $_GET["initialDate"] : null);
          $finalDate = valid_date(isset($_GET["finalDate"]) ? (string) $_GET["finalDate"] : null);
          if (!$initialDate || !$finalDate || $initialDate > $finalDate) {
            $initialDate = null;
            $finalDate = null;
          }

          $answer = ControllerSales::ctrSalesDatesRange($initialDate, $finalDate);

          foreach ($answer as $key => $value) {


           echo '<tr><td>'.($key+1).'</td>

                  <td>'.(int) $value["code"].'</td>';

                  $itemCustomer = "id";
                  $valueCustomer = $value["idCustomer"];

                  $customerAnswer = ControllerCustomers::ctrShowCustomers($itemCustomer, $valueCustomer);

                  echo '<td>'.e($customerAnswer["name"] ?? 'Unknown').'</td>';

                  $itemUser = "id";
                  $valueUser = $value["idSeller"];

                  $userAnswer = ControllerUsers::ctrShowUsers($itemUser, $valueUser);

                  echo '<td>'.e($userAnswer["name"] ?? 'Unknown').'</td>

                  <td>'.e($value["paymentMethod"]).'</td>

                  <td>K '.number_format($value["netPrice"],2).'</td>

                  <td>K '.number_format($value["totalPrice"],2).'</td>

                  <td>'.e($value["saledate"]).'</td>

                  <td>

                    <div class="btn-group">

                      <div class="btn-group">

                      <a class="btn btn-success" href="views/modules/download-invoice-xml.php?code='.(int)$value["code"].'" aria-label="Download invoice XML">XML</a>

                      <button class="btn btn-warning btnPrintBill" saleCode="'.(int) $value["code"].'">

                        <i class="fa fa-print"></i>

                      </button>';

                       if($_SESSION["profile"] == "Administrator"){

                         echo '<button class="btn btn-primary btnEditSale" idSale="'.(int) $value["id"].'"><i class="fa fa-pencil"></i></button>

                          <button class="btn btn-danger btnDeleteSale" idSale="'.(int) $value["id"].'"><i class="fa fa-trash"></i></button>';
                       }

                   echo '</div>

                  </td>

                </tr>';
            }

        ?>


          </tbody>

        </table>

         <?php

          $deleteSale = new ControllerSales();
          $deleteSale -> ctrDeleteSale();

          ?>

      </div>

    </div>
	<!-- Log on to codeastro.com for more projects! -->
  </section>

</div>
<script>
  $('.tables').DataTable({
    // Column index 7 is "Date". "desc" sorts newest to oldest.
    "order": [[ 7, "desc" ]],
    "deferRender": true,
    "retrieve": true,
    "processing": true
});
</script>
