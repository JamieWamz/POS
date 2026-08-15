<?php
$saleId = filter_input(INPUT_GET, 'idSale', FILTER_VALIDATE_INT);
if (!$saleId) {
  http_response_code(404);
  include __DIR__ . '/404.php';
  return;
}
?>
<div class="content-wrapper">

  <section class="content-header">

    <h1>

      Edit Sale

    </h1>

    <ol class="breadcrumb">

      <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>

      <li class="active">Edit Sale</li>
		<!-- Log on to codeastro.com for more projects! -->
    </ol>

  </section>

  <section class="content">

    <div class="row">

      <!--=============================================
      THE FORM
      =============================================-->
      <div class="col-lg-5 col-xs-12">

        <div class="box box-default">

          <div class="box-header with-border"></div>

          <form role="form" method="post" class="saleForm">

            <div class="box-body">

                <div class="box">

                  <?php

                    $item = "id";
                    $value = $saleId;

                    $sale = ControllerSales::ctrShowSales($item, $value);
                    if (!$sale) { http_response_code(404); include __DIR__ . '/404.php'; return; }

                    $itemUser = "id";
                    $valueUser = $sale["idSeller"];

                    $seller = ControllerUsers::ctrShowUsers($itemUser, $valueUser);

                    $itemCustomers = "id";
                    $valueCustomers = $sale["idCustomer"];

                    $customers = ControllerCustomers::ctrShowCustomers($itemCustomers, $valueCustomers);

                    $taxPercentage = isset($sale['taxRate'])
                      ? (float) $sale['taxRate']
                      : ((float) $sale["netPrice"] > 0 ? round($sale["tax"] * 100 / $sale["netPrice"], 2) : 0);
                    $savedPaymentMethod = explode('-', (string)$sale['paymentMethod'], 2)[0];
                ?>

                    <!--=====================================
                    =            SELLER INPUT           =
                    ======================================-->


                    <div class="form-group">

                      <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-user"></i></span>

                        <input type="text" class="form-control" name="newSeller" id="newSeller" value="<?php echo e($seller["name"] ?? 'Former user'); ?>" readonly>

                        <input type="hidden" name="idSeller" value="<?php echo (int) ($seller["id"] ?? 0); ?>">

                      </div>
					<!-- Log on to codeastro.com for more projects! -->
                    </div>


                    <!--=====================================
                    CODE INPUT
                    ======================================-->


                    <div class="form-group">

                      <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-key"></i></span>

                        <input type="text" class="form-control" id="newSale" name="editSale" value="<?php echo (int) $sale["code"]; ?>" readonly>

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

                            <option value="<?php echo (int)$customers["id"]; ?>"><?php echo e($customers["name"]); ?></option>

                            <?php

                            $item = null;
                            $value = null;

                            $customers = ControllerCustomers::ctrShowCustomers($item, $value);

                            foreach ($customers as $key => $value) {
                              echo '<option value="'.(int)$value["id"].'">'.e($value["name"]).'</option>';
                            }


                            ?>

                        </select>

                        <span class="input-group-addon"><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modalAddCustomer" data-dismiss="modal">Add Customer</button></span>

                      </div>
					<!-- Log on to codeastro.com for more projects! -->
                    </div>

                    <!--=====================================
                    =            PRODUCT INPUT           =
                    ======================================-->


                    <div class="form-group row newProduct">
                      <?php

                        $productList = json_decode($sale["products"], true);

                        foreach ($productList as $key => $value) {

                          $item = "id";
                          $valueProduct = $value["id"];
                          $order = "id";

                          $answer = ControllerProducts::ctrShowproducts($item, $valueProduct, $order);

                          $lastStock = $answer["stock"] + $value["quantity"];

                          echo '<div class="row" style="padding:5px 15px">

                                <div class="col-xs-6" style="padding-right:0px">

                                  <div class="input-group">

                                    <span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs removeProduct" idProduct="'.$value["id"].'"><i class="fa fa-trash"></i></button></span>

                                    <input type="text" class="form-control newProductDescription" idProduct="'.(int)$value["id"].'" name="addProduct" value="'.e($value["description"]).'" readonly required>

                                  </div>

                                </div>

                                <div class="col-xs-3">

                                  <input type="number" class="form-control newProductQuantity" name="newProductQuantity" min="1" value="'.$value["quantity"].'" stock="'.$lastStock.'" newStock="'.$value["stock"].'" required>

                                </div>

                                <div class="col-xs-3 enterPrice" style="padding-left:0px">

                                  <div class="input-group">

                                    <span class="input-group-addon"><i class="fa fa-money"></i></span>

                                    <input type="text" class="form-control newProductPrice" realPrice="'.$answer["sellingPrice"].'" name="newProductPrice" value="'.$value["totalPrice"].'" readonly required>

                                  </div>

                                </div>

                              </div>';
                        }


                        ?>

                    </div>

                    <input type="hidden" name="productsList" id="productsList">

                    <!--=====================================
                    =            ADD PRODUCT BUTTON          =
                    ======================================-->

                    <button type="button" class="btn btn-default hidden-lg btnAddProduct">Add Product</button>

                    <hr>
					<!-- Log on to codeastro.com for more projects! -->
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

                                  <input type="number" class="form-control" name="newTaxSale" id="newTaxSale" value="<?php echo e($taxPercentage); ?>" min="0" max="100" step="0.01" required>

                                  <input type="hidden" name="newTaxPrice" id="newTaxPrice" value="<?php echo number_format((float) $sale["tax"], 2, '.', ''); ?>" required>

                                  <input type="hidden" name="newNetPrice" id="newNetPrice" value="<?php echo number_format((float) $sale["netPrice"], 2, '.', ''); ?>" required>

                                  <span class="input-group-addon"><i class="fa fa-percent"></i></span>

                                </div>
                              </td>
								<!-- Log on to codeastro.com for more projects! -->
                              <td style="width: 50%">

                                <div class="input-group">

                                  <span class="input-group-addon"><i class="fa fa-money"></i></span>

                                  <input type="number" class="form-control" name="newSaleTotal" id="newSaleTotal" placeholder="00000" totalSale="<?php echo number_format((float) $sale["netPrice"], 2, '.', ''); ?>" value="<?php echo number_format((float) $sale["totalPrice"], 2, '.', ''); ?>" readonly required>

                                  <input type="hidden" name="saleTotal" id="saleTotal" value="<?php echo number_format((float) $sale["totalPrice"], 2, '.', ''); ?>" required>

                                </div>

                              </td>

                            </tr>

                          </tbody>

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

                          <?php $savedPaymentParts = explode('-', (string) $sale['paymentMethod'], 2); ?>
                          <select class="form-control" name="newPaymentMethod" id="newPaymentMethod"
                                  data-tendered="<?php echo number_format((float) ($sale['amountTendered'] ?? $sale['totalPrice']), 2, '.', ''); ?>"
                                  data-reference="<?php echo e($savedPaymentParts[1] ?? ''); ?>" required>

                              <option value="">-Select Payment Method-</option>
                              <option value="cash" <?php echo $savedPaymentMethod === 'cash' ? 'selected' : ''; ?>>Cash</option>
                              <option value="CC" <?php echo $savedPaymentMethod === 'CC' ? 'selected' : ''; ?>>Mobile Money</option>
                              <option value="DC" <?php echo $savedPaymentMethod === 'DC' ? 'selected' : ''; ?>>Debit Card</option>

                          </select>

                        </div>

                      </div>

                      <div class="paymentMethodBoxes"></div>

                      <input type="hidden" name="listPaymentMethod" id="listPaymentMethod" required>

                    </div>

                    <br>

                </div>

            </div>
			<!-- Log on to codeastro.com for more projects! -->
            <div class="box-footer">
              <button type="submit" class="btn btn-success pull-right">Save Changes</button>
            </div>
          </form>

          <?php

            $editSale = new ControllerSales();
            $editSale -> ctrEditSale();

          ?>

        </div>

      </div>


      <!--=============================================
      =            PRODUCTS TABLE                   =
      =============================================-->


      <div class="col-lg-7 hidden-md hidden-sm hidden-xs">

          <div class="box box-default">

            <div class="box-header with-border"></div>

            <div class="box-body">

              <table class="table table-bordered table-hover table-striped dt-responsive salesTable">

                <thead>

                   <tr>

                     <th style="width:10px">#</th>
                     <th>Image</th>
                     <th style="width:30px">Code</th>
                     <th>Description</th>
                     <th>Stock</th>
                     <th>Actions</th>
					<!-- Log on to codeastro.com for more projects! -->
                   </tr>

                </thead>

              </table>

            </div>

          </div>


      </div>

    </div>

  </section>

</div>


<!--=====================================
=            module add Customer            =
======================================-->

<!-- Modal -->
<div id="modalAddCustomer" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form role="form" method="POST">
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

            <!--Input id document -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input class="form-control input-lg" type="number" min="0" name="newIdDocument" placeholder="Write your ID" required>
              </div>
            </div>

            <!--Input email -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input class="form-control input-lg" type="text" name="newEmail" placeholder="Email" required>
              </div>
            </div>

            <!--Input phone -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                <input class="form-control input-lg" type="text" name="newPhone" placeholder="phone" data-inputmask="'mask':'(999) 999-9999'" data-mask required>
              </div>
            </div>

            <!--Input address -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                <input class="form-control input-lg" type="text" name="newAddress" placeholder="Address" required>
              </div>
            </div>


            <!--Input phone -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input class="form-control input-lg" type="text" name="newBirthdate" placeholder="Birth Date" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask required>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Save Customer</button>
        </div>
      </form><!-- Log on to codeastro.com for more projects! -->

      <?php

        $createCustomer = new ControllerCustomers();
        $createCustomer -> ctrCreateCustomer();

      ?>
    </div>

  </div>
</div><!-- Log on to codeastro.com for more projects! -->

<!--====  End of module add Customer  ====-->
