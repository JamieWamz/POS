<?php

if($_SESSION["profile"] == "Special"){

  echo '<script>

    window.location = "home";

  </script>';

  return;

}

?>
<!-- Log on to codeastro.com for more projects! -->
<div class="content-wrapper">

  <section class="content-header">

    <h1>

      Golden Pub Sales Management

    </h1>

    <ol class="breadcrumb">

      <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>

      <li class="active">Create Sale</li>

    </ol>

  </section>

  <section class="content">

    <div class="row">

      <!--=============================================
      THE FORM
      =============================================-->
      <div class="col-lg-5 col-md-5 col-xs-12 pos-checkout-column">

        <div class="box box-default">

          <div class="box-header with-border"></div>

          <form role="form" method="post" class="saleForm">

            <div class="box-body">

                <div class="box">

                    <!--=====================================
                    =            SELLER INPUT           =
                    ======================================-->


                    <div class="form-group">

                      <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-user"></i></span>

                        <input type="text" class="form-control" name="newSeller" id="newSeller" value="<?php echo e($_SESSION["name"]); ?>" readonly>

                        <input type="hidden" name="idSeller" value="<?php echo (int) $_SESSION["id"]; ?>">

                      </div>

                    </div>


                    <!--=====================================
                    CODE INPUT
                    ======================================-->


                    <div class="form-group">

                      <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-key"></i></span>


                        <?php
                          $item = null;
                          $value = null;

                          $sales = ControllerSales::ctrShowSales($item, $value);

                          if(!$sales){

                            echo '<input type="text" class="form-control" name="newSale" id="newSale" value="10001" readonly>';
                          }
                          else{

                            foreach ($sales as $key => $value) {

                            }

                            $code = $value["code"] +1;

                            echo '<input type="text" class="form-control" name="newSale" id="newSale" value="'.$code.'" readonly>';

                          }

                        ?>

                      </div>


                    </div>


                    <!--=====================================
                    =            CUSTOMER INPUT           =
                    ======================================-->

                    <!-- Log on to codeastro.com for more projects! -->
                    <div class="form-group">

                      <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-users"></i></span>
                        <select class="form-control" name="selectCustomer" id="selectCustomer" required>

                            <option value="">Select Customer</option>

                            <?php

                            $item = null;
                            $value = null;

                            $customers = ControllerCustomers::ctrShowCustomers($item, $value);

                            foreach ($customers as $key => $value) {
                              echo '<option value="'.(int) $value["id"].'">'.e($value["name"]).'</option>';
                            }


                            ?>

                        </select>

                        <span class="input-group-addon"><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modalAddCustomer" data-dismiss="modal">Add Customer</button></span>

                      </div>

                    </div>
					<!-- Log on to codeastro.com for more projects! -->
                    <!--=====================================
                    =            PRODUCT INPUT           =
                    ======================================-->


                    <div class="form-group row newProduct">


                    </div>

                    <input type="hidden" name="productsList" id="productsList">

                    <!--=====================================
                    =            ADD PRODUCT BUTTON          =
                    ======================================-->

                    <button type="button" class="btn btn-default hidden-lg btnAddProduct">Add Product</button>

                    <hr>

                    <div class="row">

                      <!--=====================================
                        TAXES AND TOTAL INPUT
                      ======================================-->

                      <div class="col-xs-8 pull-right">

                        <table class="table">

                          <thead>

                            <th>VAT</th>
                            <th>Total</th>

                          </thead>


                          <tbody>

                            <tr>

                              <td style="width: 50%">

                                <div class="input-group">

                                  <?php $defaultVatRate = max(0, min(100, (float) app_config('business.default_tax_rate', 16))); ?>
                                  <input type="number" class="form-control" name="newTaxSale" id="newTaxSale" value="<?php echo e(number_format($defaultVatRate, 2, '.', '')); ?>" min="0" max="100" step="0.01" required>

                                  <input type="hidden" name="newTaxPrice" id="newTaxPrice">

                                  <input type="hidden" name="newNetPrice" id="newNetPrice" required>

                                  <span class="input-group-addon"><i class="fa fa-percent"></i></span>

                                </div>
                              </td>

                              <td style="width: 50%">

                                <div class="input-group">

                                  <span class="input-group-addon"><b>K</b></span>

                                  <input type="number" class="form-control" name="newSaleTotal" id="newSaleTotal" placeholder="00000" totalSale="" readonly required>

                                  <input type="hidden" name="saleTotal" id="saleTotal" required>

                                </div>

                              </td>

                            </tr>

                          </tbody>
						<!-- Log on to codeastro.com for more projects! -->
                        </table>

                      </div>

                      <hr>

                    </div>

                    <hr>

                    <!--=====================================
                      PAYMENT METHOD
                      ======================================-->

                    <div class="form-group row">

                      <div class="col-xs-6" style="padding-right: 0">

                        <div class="input-group">

                          <select class="form-control" name="newPaymentMethod" id="newPaymentMethod" required>

                              <option value="">-Select Payment Method-</option>
                              <option value="cash">Cash</option>
                              <option value="CC">Mobile Money</option>
                              <option value="DC">Debit Card</option>

                          </select>

                        </div>

                      </div>

                      <div class="paymentMethodBoxes"></div>

                      <input type="hidden" name="listPaymentMethod" id="listPaymentMethod" required>

                    </div>

                    <br>

                </div>

            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-success pull-right">Save Sale</button>
            </div>
          </form>

          <?php

            $saveSale = new ControllerSales();
            $saveSale -> ctrCreateSale();

          ?>

        </div>

      </div>


     <!--=============================================
      =            CATEGORY & PRODUCTS SELECTOR     =
      =============================================-->
      <div class="col-lg-7 col-md-7 col-xs-12 pos-catalog-column">

        <div class="box box-warning">
          <div class="box-header with-border">
            <div>
              <span class="pos-eyebrow">Catalog</span>
              <h3 class="box-title">Choose products</h3>
            </div>
            <div class="pos-product-search">
              <i class="fa fa-search" aria-hidden="true"></i>
              <input type="search" id="productSearch" class="form-control" placeholder="Search products" autocomplete="off">
            </div>
          </div>

          <div class="box-body">

            <!-- CATEGORY BUTTONS OUTPUT HERE -->
            <div class="categories-wrapper mb-3" style="margin-bottom: 15px;">
              <button type="button" class="btn btn-primary cat-btn" data-category="all" onclick="filterCategory('all')">All</button>

              <?php
                $item = null;
                $value = null;
                $categories = ControllerCategories::ctrShowCategories($item, $value);

                foreach ($categories as $key => $cat) {
                  $categoryName = $cat["category"] ?? $cat["Category"] ?? $cat["name"] ?? "";
                  $categoryId   = $cat["id"] ?? $cat["idCategory"] ?? "";

                  if (!empty($categoryName)) {
                    // Passes category ID to match products properly
                    echo '<button type="button" class="btn btn-default cat-btn" data-category="'.e($categoryId).'" onclick="filterCategory('.(int)$categoryId.')">'.e($categoryName).'</button> ';
                  }
                }
              ?>
            </div>

            <hr>

            <!-- PRODUCT GRID (Filtered products display here) -->
            <div id="product-grid" class="pos-product-grid">
            </div>

          </div>
        </div>

      </div>

<!-- Log on to codeastro.com for more projects! -->
<!--=====================================
=            module add Customer            =
======================================-->

<!-- Modal -->
<div id="modalAddCustomer" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form role="form" method="POST">
        <input type="hidden" name="returnTo" value="create-sale">
        <div class="modal-header" style="background: #DD4B39; color: #fff">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Customer</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">

            <!--Input name -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input class="form-control input-lg" type="text" name="newCustomer" placeholder="Write name" required>
              </div>
            </div>

            <!--Input id document
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input class="form-control input-lg" type="number" min="0" name="newIdDocument" placeholder="Write your ID" required>
              </div>
            </div>-->

            <!--Input email -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input class="form-control input-lg" type="email" name="newEmail" placeholder="Email" required>
              </div>
            </div>

            <!--Input phone -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                <input class="form-control input-lg" type="text" name="newPhone" placeholder="phone" data-inputmask="'mask':'(999) 999-999999'" data-mask required>
              </div>
            </div>

            <!--Input address -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                <input class="form-control input-lg" type="text" name="newAddress" placeholder="Address" required>
              </div>
            </div>


            <!--Input Birthday -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input class="form-control input-lg" type="date" name="newBirthdate" required>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Save Customer</button>
        </div>
      </form>

      <?php

        $createCustomer = new ControllerCustomers();
        $createCustomer -> ctrCreateCustomer();

      ?>
    </div>

  </div>
</div>

<!-- Pass Database Data to JS Global Variables -->
<script>
  window.catalog = <?php
    $item = null;
    $value = null;
    $order = "id";
    $allProducts = ControllerProducts::ctrShowProducts($item, $value, $order);
    echo json_encode($allProducts ? $allProducts : [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
  ?>;

  window.categories = <?php
    $item = null;
    $value = null;
    $allCategories = ControllerCategories::ctrShowCategories($item, $value);
    echo json_encode($allCategories ? $allCategories : [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
  ?>;
</script>

<!-- Log on to codeastro.com for more projects! -->
<!--====  End of module add Customer  ====-->
