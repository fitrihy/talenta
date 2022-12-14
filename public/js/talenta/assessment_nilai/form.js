$(document).ready(function(){
  $('.modal').on('shown.bs.modal', function () {
    $('.kt-select2').select2({
        placeholder: "Pilih"
    });

    $("input[name='hasil']").on('change', function() {
      if($(this).val()=='Qualified'){
        $(".div-qualified").show();
      }
    });

    $("#tanggal").datepicker({
        orientation: "left",
        autoclose: true,
        format: 'dd/mm/yyyy'
    }).on('changeDate', function(ev){
      var startDate = new Date(ev.date);
      startDate.setFullYear(startDate.getFullYear() + 2);
      $('#tanggal_expired').datepicker('setDate', startDate);
    });
    
    $("#tanggal_expired").datepicker({
      orientation: "left",
      autoclose: true,
      format: 'dd/mm/yyyy'
    });

    $('#form-first').validate({
       submitHandler: function(form){
               var typesubmit = $("input[type=submit][clicked=true]").val();
               $(form).ajaxSubmit({
                   type: 'post',
                   url: urlstore,
                   data: {source : typesubmit},
                   dataType : 'json',
                   beforeSend: function(){
                  KTApp.block('.kt-form', {
                      overlayColor: '#000000',
                      type: 'v2',
                      state: 'primary',
                      message: 'Sedang proses, silahkan tunggu ...'
                  });
                   },
                   success: function(data){
                     KTApp.unblock('.kt-form');

                 swal.fire({
                      title: data.title,
                      html: data.msg,
                      type: data.flag,

                      buttonsStyling: false,

                      confirmButtonText: "<i class='flaticon2-checkmark'></i> OK",
                      confirmButtonClass: "btn btn-success"
                 });                     

                     if(data.flag == 'success') {
                       $('#winform').modal('hide');
                       datatable.ajax.reload( null, false );
                     }
                   },
                   error: function(jqXHR, exception){
                     KTApp.unblock('.kt-form');
                       var msgerror = '';
                       if (jqXHR.status === 0) {
                           msgerror = 'jaringan tidak terkoneksi.';
                       } else if (jqXHR.status == 404) {
                           msgerror = 'Halaman tidak ditemukan. [404]';
                       } else if (jqXHR.status == 500) {
                           msgerror = 'Internal Server Error [500].';
                       } else if (exception === 'parsererror') {
                           msgerror = 'Requested JSON parse gagal.';
                       } else if (exception === 'timeout') {
                           msgerror = 'RTO.';
                       } else if (exception === 'abort') {
                           msgerror = 'Gagal request ajax.';
                       } else {
                           msgerror = 'Error.\n' + jqXHR.responseText;
                       }
                 swal.fire({
                      title: "Error System",
                      html: msgerror+', coba ulangi kembali !!!',
                      type: 'error',

                      buttonsStyling: false,

                      confirmButtonText: "<i class='flaticon2-checkmark'></i> OK",
                      confirmButtonClass: "btn btn-default"
                 });                                 
                   }
               });
               return false;
       }
    });   
  });
});

