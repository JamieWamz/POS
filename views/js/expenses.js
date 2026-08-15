/*=============================================
EDIT EXPENSE
=============================================*/
$(".dtTables").on("click", ".btnEditExpense", function(){

  var idExpense = $(this).attr("idExpense");

  var datum = new FormData();
  datum.append("idExpense", idExpense);

  $.ajax({
    url: "ajax/expenses.ajax.php",
    method: "POST",
    data: datum,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(answer){

      $("#idExpense").val(answer["id"]);
      $("#editDescription").val(answer["description"]);
      $("#editCategory").val(answer["category"]);
      $("#editAmount").val(answer["amount"]);

    }
  });

});

/*=============================================
DELETE EXPENSE
=============================================*/
$(".dtTables").on("click", ".btnDeleteExpense", function(){

  var idExpense = $(this).attr("idExpense");

  swal({
    title: 'Are you sure you want to delete this expense?',
    text: "If you're not sure you can cancel the action!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancel',
    confirmButtonText: 'Yes, delete expense!'
  }).then(function(result){

    if(result.value){

        submitSecurePost("expenses", {deleteExpenseId: idExpense});

    }

  });

});
