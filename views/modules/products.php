<?php
if ($_SESSION['profile'] === 'Seller') {
    echo '<script>window.location = "home";</script>';
    return;
}
$productCategories = ControllerCategories::ctrShowCategories(null, null) ?: [];
?>
<div class="content-wrapper">
  <section class="content-header">
    <div>
      <span class="pos-section-label">Catalogue</span>
      <h1>Products</h1>
      <p class="pos-page-description">Set the selling price, purchasing cost, available stock and exact product image.</p>
    </div>
    <ol class="breadcrumb"><li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li><li class="active">Products</li></ol>
  </section>

  <section class="content">
    <div class="box">
      <div class="box-header">
        <div><span class="pos-eyebrow">Inventory</span><h2 class="box-title">Product list</h2></div>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addProduct"><i class="fa fa-plus" aria-hidden="true"></i> Add product</button>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-hover table-striped dt-responsive productsTable" width="100%">
          <thead><tr><th>#</th><th>Image</th><th>Code</th><th>Product</th><th>Category</th><th>Stock</th><th>Buying price</th><th>Selling price</th><th>Date added</th><th>Actions</th></tr></thead>
        </table>
        <input type="hidden" value="<?php echo e($_SESSION['profile']); ?>" id="hiddenProfile">
      </div>
    </div>
  </section>
</div>

<div id="addProduct" class="modal fade" tabindex="-1" aria-labelledby="addProductTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="products" enctype="multipart/form-data">
        <div class="modal-header">
          <h2 class="modal-title" id="addProductTitle">Add product</h2>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="newCategory">Category</label>
              <select class="form-select" id="newCategory" name="newCategory" required>
                <option value="">Select a category</option>
                <?php foreach ($productCategories as $category): ?>
                  <option value="<?php echo (int) $category['id']; ?>"><?php echo e($category['Category']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="newCode">Product code</label>
              <input class="form-control" type="text" id="newCode" name="newCode" maxlength="40" placeholder="e.g. HEINEKEN330" required>
            </div>
            <div class="col-12">
              <label class="form-label" for="newDescription">Product name</label>
              <input class="form-control" type="text" id="newDescription" name="newDescription" maxlength="160" placeholder="Use the exact brand and pack size" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="newStock">Opening stock</label>
              <input class="form-control" type="number" id="newStock" name="newStock" min="0" step="1" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="newBuyingPrice">Buying price</label>
              <div class="input-group"><span class="input-group-text">K</span><input type="number" class="form-control" id="newBuyingPrice" name="newBuyingPrice" step="0.01" min="0" required></div>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="newSellingPrice">Selling price</label>
              <div class="input-group"><span class="input-group-text">K</span><input type="number" class="form-control" id="newSellingPrice" name="newSellingPrice" step="0.01" min="0.01" required></div>
            </div>
            <div class="col-12"><hr class="my-1"></div>
            <div class="col-md-8 pos-product-image-control">
              <label class="form-label" for="newProdPhoto">Product image</label>
              <input id="newProdPhoto" type="file" class="form-control newImage" name="newProdPhoto" accept="image/jpeg,image/png,image/webp">
              <div class="pos-image-choice"><span>or find the branded pack</span></div>
              <button class="btn btn-default findProductImages" type="button" data-name-target="newDescription" data-url-target="newImageUrl" data-preview-target="newImagePreview" data-results-target="newImageResults"><i class="fa fa-search" aria-hidden="true"></i> Search product catalogue</button>
              <input id="newImageUrl" type="hidden" name="newImageUrl">
              <p class="help-block">The closest catalogue match is selected automatically. Check the brand and pack size before saving; an uploaded file takes precedence.</p>
              <div id="newImageResults" class="pos-image-results" aria-live="polite"></div>
            </div>
            <div class="col-md-4 pos-product-preview">
              <span class="form-label">Preview</span>
              <img id="newImagePreview" src="views/img/products/default/anonymous.png" class="img-thumbnail" alt="Product image preview">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save product</button>
        </div>
      </form>
      <?php (new ControllerProducts())->ctrCreateProducts(); ?>
    </div>
  </div>
</div>

<div id="modalEditProduct" class="modal fade" tabindex="-1" aria-labelledby="editProductTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="products" enctype="multipart/form-data">
        <div class="modal-header">
          <h2 class="modal-title" id="editProductTitle">Edit product</h2>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="editCategory">Category</label>
              <select class="form-select" id="editCategory" name="editCategory" required>
                <?php foreach ($productCategories as $category): ?>
                  <option value="<?php echo (int) $category['id']; ?>"><?php echo e($category['Category']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="editCode">Product code</label>
              <input type="text" class="form-control" id="editCode" name="editCode" readonly required>
            </div>
            <div class="col-12">
              <label class="form-label" for="editDescription">Product name</label>
              <input type="text" class="form-control" id="editDescription" name="editDescription" maxlength="160" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="editStock">Available stock</label>
              <input type="number" class="form-control" id="editStock" name="editStock" min="0" step="1" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="editBuyingPrice">Buying price</label>
              <div class="input-group"><span class="input-group-text">K</span><input type="number" class="form-control" id="editBuyingPrice" name="editBuyingPrice" step="0.01" min="0" required></div>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="editSellingPrice">Selling price</label>
              <div class="input-group"><span class="input-group-text">K</span><input type="number" class="form-control" id="editSellingPrice" name="editSellingPrice" step="0.01" min="0.01" required></div>
            </div>
            <div class="col-12"><hr class="my-1"></div>
            <div class="col-md-8 pos-product-image-control">
              <label class="form-label" for="editImage">Replace with uploaded image</label>
              <input id="editImage" type="file" class="form-control newImage" name="editImage" accept="image/jpeg,image/png,image/webp">
              <div class="pos-image-choice"><span>or find the branded pack</span></div>
              <button class="btn btn-default findProductImages" type="button" data-name-target="editDescription" data-url-target="editImageUrl" data-preview-target="editImagePreview" data-results-target="editImageResults"><i class="fa fa-search" aria-hidden="true"></i> Search product catalogue</button>
              <input id="editImageUrl" type="hidden" name="editImageUrl">
              <p class="help-block">The closest catalogue match is selected automatically. Leave the image controls untouched to keep the current image.</p>
              <div id="editImageResults" class="pos-image-results" aria-live="polite"></div>
            </div>
            <div class="col-md-4 pos-product-preview">
              <span class="form-label">Current image</span>
              <img id="editImagePreview" src="views/img/products/default/anonymous.png" class="img-thumbnail" alt="Product image preview">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
      <?php (new ControllerProducts())->ctrEditProduct(); ?>
    </div>
  </div>
</div>

<?php (new ControllerProducts())->ctrDeleteProduct(); ?>
