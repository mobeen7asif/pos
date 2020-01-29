

$(document).ready(function(){ 
    $(".select2").select2(); 
});   

$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.fn.dataTable.ext.errMode = 'none';    

$(document).ajaxError(function (event, jqxhr, settings, thrownError) {
   if (jqxhr.status === 401) {
      document.location.href = panel_url+'/login';
   }
});

//$(document).ajaxStart(function () {
//    Pace.restart();
//});

function create_datatables(url,columns,ordering = []){
    $('#datatable').DataTable({
      oLanguage: { sProcessing: '<img src="'+ base_url +'/images/bx_loader.gif">' },
      processing: true,
      serverSide: true,
      ordering: true,
      responsive: true,
      ajax: url,
      columns: columns,
      order: ordering
  });
}

function remove_record(url,reload_datatable,method){
    // confirm then
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this record!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        closeOnConfirm: false,
        closeOnCancel: false
      },
      function(isConfirm){
        if (isConfirm) {
            $.ajax({
              type: method,
              url: url,
              dataType: "json",
              complete:function (res) {
                  swal.close();
                  var j = JSON.parse(res.responseText);
                  var result = j.result;
                  if(res.status == 200){
                      if(reload_datatable != ""){
                         reload_datatable.fnDraw(); 
                      }   
                      
                      toastr.success(result.success);
                                            
                  }else{
                      toastr.error(result.error);
                  }
              },
                error: function (request, status, error) {
                    swal.close();
                    var result = request.responseJSON.result;
                    var err = JSON.parse(request.responseText);
                    if(status == 401){
                      toastr.error(result.error);                  
                  }else{
                      toastr.error(err.message);
                  }                    
                } 
          });
        } else {
            swal.close();
            toastr.info("Your record is safe :)");
        }
      });
}

function generateRandomNumber(length) {
    if(!length) { length = 16; }
    //var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var chars = "1234567890";
    var result="";

    for (var i = length; i > 0; --i)
        result += chars[Math.round(Math.random() * (chars.length - 1))]
    return result
}

$(document).ready(function(){  
    $('form').validator().on('submit', function (e) {
        if (e.isDefaultPrevented()) {
            $("input[type=submit]").removeProp("disabled");
        }else{
            $("input[type=submit]").prop("disabled", "disabled");            
        }
    });
});