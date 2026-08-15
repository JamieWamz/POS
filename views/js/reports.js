/*=============================================
LOCAL STORAGE VARIABLE
=============================================*/

if(localStorage.getItem("captureRange2") != null){

  $("#daterange-btn2 span").html(localStorage.getItem("captureRange2"));


}else{

  $("#daterange-btn2 span").html('<i class="fa fa-calendar"></i> Date Range')

}

/*=============================================
DATES RANGE
=============================================*/

$('#daterange-btn2').daterangepicker(
  {
    ranges   : {
      'Today'       : [moment(), moment()],
      'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 days' : [moment().subtract(6, 'days'), moment()],
      'Last 30 days': [moment().subtract(29, 'days'), moment()],
      'this month'  : [moment().startOf('month'), moment().endOf('month')],
      'Last month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment(),
    endDate  : moment()
  },
  function (start, end) {
    $('#daterange-btn2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    var initialDate = start.format('YYYY-MM-DD');

    var finalDate = end.format('YYYY-MM-DD');

    var captureRange = $("#daterange-btn2 span").html();

     localStorage.setItem("captureRange2", captureRange);
  window.location = "index.php?route=reports&initialDate="+initialDate+"&finalDate="+finalDate;

  }

)

/*=============================================
CANCEL DATES RANGE
=============================================*/

$(".daterangepicker.opensright .range_inputs .cancelBtn").on("click", function(){

  localStorage.removeItem("captureRange2");
  window.location = "reports";
})

/*=============================================
CAPTURE TODAY'S BUTTON
=============================================*/

$(".daterangepicker.opensright .ranges li").on("click", function(){

  var todayButton = $(this).attr("data-range-key");

  if(todayButton == "Today"){

    var d = new Date();

    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();

    // Pad single digits with leading zero (01, 02, etc.)
    var dayString = day < 10 ? "0" + day : day;
    var monthString = month < 10 ? "0" + month : month;

    var initialDate = year + "-" + monthString + "-" + dayString;
    var finalDate = year + "-" + monthString + "-" + dayString;

    localStorage.setItem("captureRange2", "Today");

    // FIX: Changed 'route=sales' to 'route=reports'
    window.location = "index.php?route=reports&initialDate=" + initialDate + "&finalDate=" + finalDate;

  }

})
