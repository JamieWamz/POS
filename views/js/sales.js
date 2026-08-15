/*=============================================
LOAD DYNAMIC PRODUCTS TABLE
=============================================*/

$('.salesTable').DataTable({
  "ajax": "ajax/datatable-sales.ajax.php",
  "deferRender": true,
  "retrieve": true,
  "processing": true
});

/*=============================================
CATEGORY & PRODUCT SELECTION LOGIC
=============================================*/
window.activeProductCategory = 'all';

function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>'"]/g, function(character) {
    return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[character];
  });
}

function filterCategory(categorySelected) {
  window.activeProductCategory = categorySelected;
  var grid = $("#product-grid");
  grid.empty();
  $(".cat-btn").removeClass("btn-primary").addClass("btn-default");
  $(".cat-btn").filter(function () {
    return String($(this).data("category")) === String(categorySelected);
  }).removeClass("btn-default").addClass("btn-primary");

  // Ensure catalog is array across all browsers
  var catalog = window.catalog || [];
  var categories = window.categories || [];

  if (!catalog || catalog.length === 0) {
    grid.append('<div class="col-xs-12"><p class="text-muted">No products found in database.</p></div>');
    return;
  }

  var targetId = categorySelected;
  if (categorySelected !== 'all') {
    var matchedCat = categories.find(function(c) {
      return (c.category && c.category.toLowerCase() === String(categorySelected).toLowerCase()) ||
             (c.Category && c.Category.toLowerCase() === String(categorySelected).toLowerCase()) ||
             (c.id && String(c.id) === String(categorySelected));
    });
    if (matchedCat) {
      targetId = matchedCat.id;
    }
  }

  var filtered = (categorySelected === 'all')
    ? catalog
    : catalog.filter(function(product) {
        var prodCatId = product.idCategory || product.id_category || product.category_id;
        var prodCatName = product.category || product.Category;

        return (
          String(prodCatId) === String(targetId) ||
          (prodCatName && String(prodCatName).toLowerCase() === String(categorySelected).toLowerCase())
        );
      });

  var query = String($("#productSearch").val() || '').trim().toLowerCase();
  if (query) {
    filtered = filtered.filter(function(product) {
      return String(product.description || product.Description || product.name || '').toLowerCase().includes(query)
        || String(product.code || '').toLowerCase().includes(query);
    });
  }

  if (filtered.length === 0) {
    grid.append('<div class="col-xs-12"><p class="text-muted">No products in this category.</p></div>');
    return;
  }

  filtered.forEach(function(product) {
    var desc = product.description || product.Description || product.name || "Product";
    var rawPrice = product.selling_price ?? product.sellingPrice ?? product.price ?? product.sales_price ?? 0;
    var price = parseFloat(rawPrice).toFixed(2);
    var id = Number(product.id || product.idProduct);
    var imagePath = product.image || product.Image || 'views/img/products/default/anonymous.png';
    var stock = Number(product.stock || 0);

    var btnHtml =
      '<button type="button" class="pos-product-card addProductBtn" onclick="addProductToSale(' + id + ')" ' + (stock <= 0 ? 'disabled' : '') + '>' +
        '<img src="' + escapeHtml(imagePath) + '" alt="">' +
        '<strong>' + escapeHtml(desc) + '</strong>' +
        '<span class="pos-product-card__meta"><span class="pos-product-card__price">K' + price + '</span>' +
        '<span class="pos-product-card__stock">' + stock + ' in stock</span></span>' +
      '</button>';

    grid.append(btnHtml);
  });
}

// Add clicked product into the POS sale table
function addProductToSale(productId) {
  const product = (window.catalog || []).find(p => String(p.id || p.idProduct) === String(productId));
  if (!product) return;

  const id = product.id || product.idProduct;
  const desc = product.description || product.Description || product.name || "Product";
  const rawPrice = product.selling_price ?? product.sellingPrice ?? product.price ?? product.sales_price ?? 0;
  const price = parseFloat(rawPrice).toFixed(2);
  const stock = Number(product.stock ?? 0);

  if (stock <= 0) {
    swal({
      title: "There's no stock available",
      type: "error",
      confirmButtonText: "Close!"
    });
    return;
  }

  let existingInput = $(`#idProduct${id}`);

  if (existingInput.length > 0) {
    let qtyInput = existingInput.closest('.row').find('.newProductQuantity');
    let currentQty = parseInt(qtyInput.val()) || 0;

    if (currentQty + 1 > stock) {
      swal({
        title: "The quantity is more than your stock",
        text: "There's only " + stock + " units!",
        type: "error",
        confirmButtonText: "Close!"
      });
      return;
    }

    qtyInput.val(currentQty + 1).trigger('change');
  } else {
    const rowHtml = `
      <div class="row" style="padding:5px 15px">
        <div class="col-xs-6" style="padding-right:0px">
          <div class="input-group">
            <span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs removeProduct" idProduct="${id}"><i class="fa fa-times"></i></button></span>
            <input type="text" class="form-control newProductDescription" idProduct="${id}" id="idProduct${id}" name="addProductSale" value="${escapeHtml(desc)}" readonly required>
          </div>
        </div>
        <div class="col-xs-3 enterQuantity">
          <input type="number" class="form-control newProductQuantity" name="newProductQuantity" min="1" value="1" stock="${stock}" newStock="${stock - 1}" required>
        </div>
        <div class="col-xs-3 enterPrice" style="padding-left:0px">
          <div class="input-group">
            <span class="input-group-addon"><b>K</b></span>
            <input type="text" class="form-control newProductPrice" realPrice="${price}" name="newProductPrice" value="${price}" readonly required>
          </div>
        </div>
      </div>
    `;

    $(".newProduct").append(rowHtml);
  }

  addingTotalPrices();
  addTax();
  listProducts();
  $(".newProductPrice").number(true, 2);
}

/*=============================================
UPDATE JSON PRODUCT LIST
=============================================*/

function listProducts() {
  var productsList = [];
  var description = $(".newProductDescription");
  var quantity = $(".newProductQuantity");
  var price = $(".newProductPrice");

  for (var i = 0; i < description.length; i++) {
    productsList.push({
      "id": $(description[i]).attr("idProduct"),
      "description": $(description[i]).val(),
      "quantity": $(quantity[i]).val(),
      "stock": $(quantity[i]).attr("newStock"),
      "price": $(price[i]).attr("realPrice"),
      "totalPrice": $(price[i]).val()
    });
  }

  $("#productsList").val(JSON.stringify(productsList));
}

/*=============================================
ADDING PRODUCTS TO THE SALE FROM THE TABLE (OLD DATATABLE METHOD)
=============================================*/

$(".salesTable tbody").on("click", "button.addProductSale", function(){
  var idProduct = $(this).attr("idProduct");
  $(this).removeClass("btn-primary addProductSale");
  $(this).addClass("btn-default");

  var datum = new FormData();
    datum.append("idProduct", idProduct);

     $.ajax({
       url:"ajax/products.ajax.php",
        method: "POST",
        data: datum,
        cache: false,
        contentType: false,
        processData: false,
        dataType:"json",
        success:function(answer){
            var description = answer["description"];
            var stock = answer["stock"];
            var price = answer["sellingPrice"];

            if(stock == 0){
            swal({
            title: "There's no stock available",
            type: "error",
            confirmButtonText: "Close!"
          });
          $("button[idProduct='"+idProduct+"']").addClass("btn-primary addProductSale");
          return;
            }

            $(".newProduct").append(
            '<div class="row" style="padding:5px 15px">'+
            '<div class="col-xs-6" style="padding-right:0px">'+
              '<div class="input-group">'+
                '<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs removeProduct" idProduct="'+idProduct+'"><i class="fa fa-times"></i></button></span>'+
                '<input type="text" class="form-control newProductDescription" idProduct="'+idProduct+'" id="idProduct'+idProduct+'" name="addProductSale" value="'+description+'" readonly required>'+
              '</div>'+
            '</div>'+
            '<div class="col-xs-3 enterQuantity">'+
               '<input type="number" class="form-control newProductQuantity" name="newProductQuantity" min="1" value="1" stock="'+stock+'" newStock="'+Number(stock-1)+'" required>'+
            '</div>' +
            '<div class="col-xs-3 enterPrice" style="padding-left:0px">'+
              '<div class="input-group">'+
                '<span class="input-group-addon"><b>K</b></span>'+
                '<input type="text" class="form-control newProductPrice" realPrice="'+price+'" name="newProductPrice" value="'+price+'" readonly required>'+
              '</div>'+
            '</div>'+
          '</div>')

        addingTotalPrices();
          addTax();
          listProducts();
          $(".newProductPrice").number(true, 2);
        }
     })
});

/*=============================================
REMOVE PRODUCTS FROM THE SALE
=============================================*/

var idRemoveProduct = [];
localStorage.removeItem("removeProduct");

$(".saleForm").on("click", "button.removeProduct", function(){
  $(this).closest(".row").remove();
  var idProduct = $(this).attr("idProduct");

  if(localStorage.getItem("removeProduct") == null){
    idRemoveProduct = [];
  }else{
    idRemoveProduct.concat(localStorage.getItem("removeProduct"));
  }

  idRemoveProduct.push({"idProduct":idProduct});
  localStorage.setItem("removeProduct", JSON.stringify(idRemoveProduct));

  $("button.recoverButton[idProduct='"+idProduct+"']").removeClass('btn-default');
  $("button.recoverButton[idProduct='"+idProduct+"']").addClass('btn-primary addProductSale');

  if($(".newProduct").children().length == 0){
    $("#newTaxSale").val(0);
    $("#newSaleTotal").val(0);
    $("#saleTotal").val(0);
    $("#newSaleTotal").attr("totalSale",0);
    $("#productsList").val("");
    calculateChange();
  }else{
      addingTotalPrices();
        addTax();
        listProducts();
  }
});

/*=============================================
MODIFY QUANTITY & RECALCULATE
=============================================*/

$(".saleForm").on("change keyup", "input.newProductQuantity", function(){
  var price = $(this).parent().parent().children(".enterPrice").children().children(".newProductPrice");
  var finalPrice = ($(this).val() * price.attr("realPrice")).toFixed(2);

  price.val(finalPrice);

  var newStock = Number($(this).attr("stock")) - $(this).val();
  $(this).attr("newStock", newStock);

  if(Number($(this).val()) > Number($(this).attr("stock"))){
    $(this).val(1);
    var finalPrice = $(this).val() * price.attr("realPrice");
    price.val(finalPrice);

    addingTotalPrices();
    addTax();
    listProducts();

    swal({
        title: "The quantity is more than your stock",
        text: "There's only "+$(this).attr("stock")+" units!",
        type: "error",
        confirmButtonText: "Close!"
      });
      return;
  }

  addingTotalPrices();
    addTax();
    listProducts();
});

/*=============================================
PRICES ADDITION
=============================================*/

function addingTotalPrices(){
  var priceItem = $(".newProductPrice");
  var arrayAdditionPrice = [];

  for(var i = 0; i < priceItem.length; i++){
     arrayAdditionPrice.push(Number($(priceItem[i]).val()));
  }

  if (arrayAdditionPrice.length === 0) {
    $("#newSaleTotal").val(0);
    $("#saleTotal").val(0);
    $("#newSaleTotal").attr("totalSale", 0);
    calculateChange();
    return;
  }

  function additionArrayPrices(totalSale, numberArray){
    return totalSale + numberArray;
  }

  var addingTotalPrice = arrayAdditionPrice.reduce(additionArrayPrices);

  $("#newSaleTotal").val(addingTotalPrice);
  $("#saleTotal").val(addingTotalPrice);
  $("#newSaleTotal").attr("totalSale", addingTotalPrice);

  calculateChange();
}

/*=============================================
ADD TAX
=============================================*/

function addTax(){
  var tax = $("#newTaxSale").val();
  var totalPrice = $("#newSaleTotal").attr("totalSale");

  var netPrice = Number(totalPrice) || 0;
  var taxRate = Number(tax) || 0;
  var taxPrice = Math.round((netPrice * taxRate / 100) * 100) / 100;
  var totalwithTax = Math.round((netPrice + taxPrice) * 100) / 100;

  $("#newSaleTotal").val(totalwithTax.toFixed(2));
  $("#saleTotal").val(totalwithTax.toFixed(2));
  $("#newTaxPrice").val(taxPrice.toFixed(2));
  $("#newNetPrice").val(netPrice.toFixed(2));

  calculateChange();
}

$("#newTaxSale").change(function(){
  addTax();
});

$("#newSaleTotal").number(true, 2);

/*=============================================
SELECT PAYMENT METHOD & CASH CHANGE
=============================================*/

$("#newPaymentMethod").change(function(){
  var method = $(this).val();

  if(method == "cash"){
    // Adjust column width for payment dropdown container
    $(this).closest('.col-xs-6, .col-xs-4, .col-xs-12').removeClass("col-xs-6 col-xs-12").addClass("col-xs-4");

    // Target paymentMethodBoxes directly with labels embedded inside addons
    $(".paymentMethodBoxes").html(
       '<!-- Container aligned under Taxes and Total -->' +
       '<div class="col-xs-8 pull-right" style="padding-left: 0; padding-right: 0;">' +
         '<div class="row">' +
           '<!-- Cash Received -->' +
           '<div class="col-xs-6" style="padding-right: 5px;">' +
             '<div class="form-group" style="margin-bottom: 0;">' +
               '<div class="input-group">' +
                 '<span class="input-group-addon"><b>Received K</b></span>' +
                 '<input type="text" class="form-control" id="newCashValue" name="newCashValue" placeholder="000000" required>' +
               '</div>' +
             '</div>' +
           '</div>' +

           '<!-- Change -->' +
           '<div class="col-xs-6" id="getCashChange" style="padding-left: 5px;">' +
             '<div class="form-group" style="margin-bottom: 0;">' +
               '<div class="input-group">' +
                 '<span class="input-group-addon"><b>Change K</b></span>' +
                 '<input type="text" class="form-control" id="newCashChange" name="newCashChange" placeholder="000000" readonly required>' +
               '</div>' +
             '</div>' +
           '</div>' +
         '</div>' +
       '</div>'
    );

    $('#newCashValue').number(true, 2);
    $('#newCashChange').number(true, 2);
    var savedTendered = $(this).attr('data-tendered');
    if (savedTendered) {
      $('#newCashValue').val(savedTendered);
      calculateChange();
    }

    if (typeof listMethods === "function") {
      listMethods();
    }
  } else {
    $(this).closest('.col-xs-4, .col-xs-12').removeClass('col-xs-4 col-xs-12').addClass('col-xs-6');
    $(".paymentMethodBoxes").html(
       '<div class="col-xs-6" style="padding-left: 0px;">' +
                '<div class="input-group">' +
          '<input type="text" maxlength="60" class="form-control" id="newTransactionCode" name="newTransactionCode" placeholder="Transaction reference" required>' +
                  '<span class="input-group-addon"><i class="fa fa-lock"></i></span>' +
                '</div>' +
            '</div>'
    );
    var savedReference = $(this).attr('data-reference');
    if (savedReference) {
      $('#newTransactionCode').val(savedReference);
    }
  }
});

/*=============================================
CASH CHANGE CALCULATION
=============================================*/

$(".saleForm").on("change keyup input", "#newCashValue", function(){
  calculateChange();
});

function calculateChange(){
  var cash = $("#newCashValue").val();
  var total = $("#newSaleTotal").val();

  // Remove potential thousands separators (commas) before calculations
  if (typeof cash === "string") {
    cash = cash.replace(/,/g, '');
  }
  if (typeof total === "string") {
    total = total.replace(/,/g, '');
  }

  cash = Number(cash) || 0;
  total = Number(total) || 0;

  var change = cash - total;

  if(change < 0){
    $("#newCashChange").val("0.00");
  } else {
    $("#newCashChange").val(change.toFixed(2));
  }

  if (typeof listMethods === "function") {
    listMethods();
  }
}

/*=============================================
OTHER ACTIONS & INITIALIZATION
=============================================*/

$(".tables").on("click", ".btnEditSale", function(){
  var idSale = $(this).attr("idSale");
  window.location = "index.php?route=edit-sale&idSale="+idSale;
});

function removeAddProductSale(){
  var idProducts = $(".removeProduct");
  var tableButtons = $(".salesTable tbody button.addProductSale");

  for(var i = 0; i < idProducts.length; i++){
    var button = $(idProducts[i]).attr("idProduct");
    for(var j = 0; j < tableButtons.length; j ++){
      if($(tableButtons[j]).attr("idProduct") == button){
        $(tableButtons[j]).removeClass("btn-primary addProductSale").addClass("btn-default");
      }
    }
  }
}

$('.salesTable').on('draw.dt', function(){
  removeAddProductSale();
});

$(".tables").on("click", ".btnDeleteSale", function(){
  var idSale = $(this).attr("idSale");

  swal({
        title: 'Are you sure you want to delete this?',
        text: "If you're not, you can cancel!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Yes, delete it!'
      }).then(function(result){
        if (result.value) {
              submitSecurePost("sales", {deleteSaleId: idSale});
        }
  });
});

$(".tables").on("click", ".btnPrintBill", function(){
  var saleCode = $(this).attr("saleCode");
  window.open("views/modules/print-receipt.php?code=" + encodeURIComponent(saleCode), "_blank");
});

/* Initialize product grid */
$(document).ready(function() {
  filterCategory('all');
  $("#productSearch").on("input", function() {
    filterCategory(window.activeProductCategory || 'all');
  });
  if ($("#newPaymentMethod").val()) {
    $("#newPaymentMethod").trigger("change");
  }
});
