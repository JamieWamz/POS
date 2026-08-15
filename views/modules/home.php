<?php

if($_SESSION["profile"] == "Seller"){

  echo '<script>
    window.location = "create-sale";
  </script>';

  return;

}

?>
<div class="content-wrapper">

  <section class="content-header">
    <div>
      <span class="pos-section-label">Today</span>
      <h1>Golden Tap overview</h1>
      <p class="pos-page-description">Sales, expenses and stock status for <?php echo e(date('l, j F Y')); ?>.</p>
    </div>
    <?php if (user_has_role(['Administrator', 'Seller'])): ?>
      <a class="btn btn-primary" href="create-sale"><i class="fa fa-plus" aria-hidden="true"></i> New sale</a>
    <?php endif; ?>

  </section>

  <section class="content">

    <div class="row g-0 pos-daily-tally">

      <?php

        if($_SESSION["profile"] =="Administrator"){

          include "home/top-boxes.php";

        }

      ?>

    </div>

    <?php if($_SESSION["profile"] === "Administrator") { include "home/admin-operations.php"; } ?>

    <div class="row">

      <div class="col-lg-12">

      <?php

        if($_SESSION["profile"] =="Administrator"){

          include "reports/sales-graph.php";

        }

      ?>

      </div>

      <div class="col-lg-6">

        <?php

          if($_SESSION["profile"] =="Administrator"){

            include "reports/bestseller-products.php";

          }

        ?>

      </div>

       <div class="col-lg-6">

        <?php

          if($_SESSION["profile"] =="Administrator"){

            include "home/recent-products.php";

          }

        ?>

      </div>

      <div class="col-lg-12">

        <?php

        if($_SESSION["profile"] =="Special" || $_SESSION["profile"] =="Seller"){

           echo '<div class="box box-default">

           <div class="box-header">

           <h1>Welcome ' .e($_SESSION["name"]).'</h1>

           </div>

           </div>';

        }

        ?>

      </div>

    </div>

  </section>

</div>
