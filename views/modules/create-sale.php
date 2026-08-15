<?php
if ($_SESSION['profile'] === 'Special') {
    echo '<script>window.location = "home";</script>';
    return;
}

$sales = ControllerSales::ctrShowSales(null, null) ?: [];
$nextSaleCode = 10001;
foreach ($sales as $sale) {
    $nextSaleCode = max($nextSaleCode, (int) $sale['code'] + 1);
}
$customers = ControllerCustomers::ctrShowCustomers(null, null) ?: [];
$categories = ControllerCategories::ctrShowCategories(null, null) ?: [];
$allProducts = ControllerProducts::ctrShowProducts(null, null, 'id') ?: [];
$defaultVatRate = max(0, min(100, (float) app_config('business.default_tax_rate', 16)));
?>
<div class="content-wrapper">
  <section class="content-header">
    <div>
      <span class="pos-section-label">Register</span>
      <h1>New sale</h1>
      <p class="pos-page-description">Select products, confirm payment and print the receipt after checkout.</p>
    </div>
    <a class="btn btn-default" href="sales"><i class="fa fa-history" aria-hidden="true"></i> Sales history</a>
  </section>

  <section class="content">
    <div class="row g-4">
      <div class="col-xl-5 order-xl-2 pos-checkout-column">
        <form role="form" method="post" class="saleForm">
          <input type="hidden" name="newSale" id="newSale" value="<?php echo $nextSaleCode; ?>">
          <input type="hidden" name="idSeller" value="<?php echo (int) $_SESSION['id']; ?>">
          <input type="hidden" name="productsList" id="productsList">
          <input type="hidden" name="newTaxPrice" id="newTaxPrice">
          <input type="hidden" name="newNetPrice" id="newNetPrice" required>
          <input type="hidden" name="saleTotal" id="saleTotal" required>
          <input type="hidden" name="listPaymentMethod" id="listPaymentMethod" required>

          <section class="box pos-order-card">
            <header class="box-header">
              <div class="pos-order-number">Current order<strong>#<?php echo $nextSaleCode; ?></strong></div>
              <div class="pos-order-meta">Cashier<br><strong><?php echo e($_SESSION['name']); ?></strong></div>
            </header>

            <div class="box-body">
              <div class="form-group">
                <label class="pos-field-label" for="selectCustomer">Customer</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fa fa-user" aria-hidden="true"></i></span>
                  <select class="form-select" name="selectCustomer" id="selectCustomer" required>
                    <option value="">Select a customer</option>
                    <?php foreach ($customers as $customer): ?>
                      <option value="<?php echo (int) $customer['id']; ?>"><?php echo e($customer['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#modalAddCustomer">New</button>
                </div>
              </div>

              <div class="pos-cart-heading">
                <strong>Order items</strong>
                <span>Quantity and line total</span>
              </div>
              <div class="newProduct"></div>
              <div class="pos-cart-empty"><i class="fa fa-shopping-basket" aria-hidden="true"></i><br>Select a product from the catalogue.</div>

              <div class="pos-order-totals">
                <div class="pos-order-totals__row">
                  <label for="newTaxSale"><?php echo e(app_config('business.tax_label', 'VAT')); ?> rate</label>
                  <div class="input-group" style="max-width: 130px;">
                    <input type="number" class="form-control" name="newTaxSale" id="newTaxSale" value="<?php echo e(number_format($defaultVatRate, 2, '.', '')); ?>" min="0" max="100" step="0.01" required>
                    <span class="input-group-text">%</span>
                  </div>
                </div>
                <div class="pos-order-totals__row">
                  <label for="newSaleTotal">Total due</label>
                  <div class="pos-order-total">
                    <div class="input-group">
                      <span class="input-group-addon"><?php echo e(app_config('business.currency', 'K')); ?></span>
                      <input type="number" class="form-control" name="newSaleTotal" id="newSaleTotal" value="0.00" totalSale="0" readonly required>
                    </div>
                  </div>
                </div>
              </div>

              <div class="pos-payment-section">
                <label class="pos-field-label" for="newPaymentMethod">Payment</label>
                <div class="pos-payment-grid">
                  <select class="form-select" name="newPaymentMethod" id="newPaymentMethod" required>
                    <option value="">Select payment method</option>
                    <option value="cash">Cash</option>
                    <option value="CC">Mobile money</option>
                    <option value="DC">Debit card</option>
                  </select>
                  <div class="paymentMethodBoxes"></div>
                </div>
              </div>
            </div>

            <footer class="box-footer">
              <button type="submit" class="btn btn-primary btn-lg w-100"><i class="fa fa-check" aria-hidden="true"></i> Complete sale and print receipt</button>
            </footer>
          </section>
        </form>

        <?php
        $saveSale = new ControllerSales();
        $saveSale->ctrCreateSale();
        ?>
      </div>

      <div class="col-xl-7 order-xl-1 pos-catalog-column">
        <section class="box">
          <header class="box-header">
            <div>
              <span class="pos-eyebrow">Catalogue</span>
              <h2 class="box-title">Products</h2>
            </div>
            <div class="pos-product-search">
              <i class="fa fa-search" aria-hidden="true"></i>
              <input type="search" id="productSearch" class="form-control" placeholder="Search by product or code" autocomplete="off">
            </div>
          </header>
          <div class="box-body">
            <div class="categories-wrapper" aria-label="Product categories">
              <button type="button" class="btn btn-primary cat-btn" data-category="all" onclick="filterCategory('all')">All products</button>
              <?php foreach ($categories as $category): ?>
                <?php
                $categoryName = $category['category'] ?? $category['Category'] ?? $category['name'] ?? '';
                $categoryId = $category['id'] ?? $category['idCategory'] ?? '';
                ?>
                <?php if ($categoryName !== ''): ?>
                  <button type="button" class="btn btn-default cat-btn" data-category="<?php echo e($categoryId); ?>" onclick="filterCategory(<?php echo (int) $categoryId; ?>)"><?php echo e($categoryName); ?></button>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
            <div id="product-grid" class="pos-product-grid" aria-live="polite"></div>
          </div>
        </section>
      </div>
    </div>
  </section>
</div>

<div id="modalAddCustomer" class="modal fade" tabindex="-1" aria-labelledby="modalAddCustomerTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="returnTo" value="create-sale">
        <div class="modal-header">
          <h2 class="modal-title" id="modalAddCustomerTitle">Add customer</h2>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="newCustomer">Full name</label>
              <input id="newCustomer" class="form-control" type="text" name="newCustomer" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="newEmail">Email</label>
              <input id="newEmail" class="form-control" type="email" name="newEmail">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="newPhone">Phone</label>
              <input id="newPhone" class="form-control" type="tel" name="newPhone" required>
            </div>
            <div class="col-12">
              <label class="form-label" for="newAddress">Address</label>
              <input id="newAddress" class="form-control" type="text" name="newAddress" required>
            </div>
            <div class="col-12">
              <label class="form-label" for="newBirthdate">Date of birth</label>
              <input id="newBirthdate" class="form-control" type="date" name="newBirthdate" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save customer</button>
        </div>
      </form>
      <?php
      $createCustomer = new ControllerCustomers();
      $createCustomer->ctrCreateCustomer();
      ?>
    </div>
  </div>
</div>

<script>
  window.catalog = <?php echo json_encode($allProducts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  window.categories = <?php echo json_encode($categories, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
