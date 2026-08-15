<?php

$item = null;
$value = null;
$order = "id";

$products = ControllerProducts::ctrShowProducts($item, $value, $order);

 ?>


<div class="box box-default">

  <div class="box-header with-border">

    <h3 class="box-title">Recently Added Products</h3>

    <div class="box-tools pull-right">

      <button type="button" class="btn btn-box-tool" data-widget="collapse">

        <i class="fa fa-minus"></i>

      </button>

      <button type="button" class="btn btn-box-tool" data-widget="remove">

        <i class="fa fa-times"></i>

      </button>

    </div>

  </div>

  <div class="box-body">

    <ul class="products-list product-list-in-box">

    <?php

    // Get the total count of available products
    $totalProducts = count($products);

    // Determine how many items to display (maximum 7, or fewer if less exist)
    $limit = min(7, $totalProducts);

    for ($i = 0; $i < $limit; $i++) {

      // Set default image if product image is empty or file doesn't exist
      $candidateImage = ltrim((string) ($products[$i]["image"] ?? ''), '/');
      $imagePath = $candidateImage !== '' && file_exists(dirname(__DIR__, 3).'/'.$candidateImage)
        ? $candidateImage
        : "views/img/products/default/anonymous.png";

      echo '<li class="item">

        <div class="product-img">

          <img src="'.e($imagePath).'" alt="Product Image">

        </div>

        <div class="product-info">

          <a href="products" class="product-title">

            '.e($products[$i]["description"]).'

            <span class="label label-warning pull-right">K'.number_format((float) $products[$i]["sellingPrice"], 2).'</span>

          </a>

      </div>

      </li>';

    }

    ?>

    </ul>

  </div>

  <div class="box-footer text-center">

    <a href="products" class="uppercase">View All Products</a>

  </div>

</div>
