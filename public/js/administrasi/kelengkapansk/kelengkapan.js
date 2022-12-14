var datatablekelengkapan;
var datatablesumkelengkapan;

$(document).ready(function(){
	$('body').on('click','.cls-add-kelengkapan',function(){

		winform(urlcreatekelengkapan, {'id_talenta':$('input[name=id_talenta]').val(), 'id_perusahaan':$('input[name=id_perusahaan]').val(), 'grup_jabatan_id':$('input[name=grup_jabatan_id]').val()}, 'Tambah kelengkapan');
	});

	$('body').on('click','.cls-button-edit-kelengkapan',function(){
		winform(urleditkelengkapan, {'id':$(this).data('id'),'id_talenta':$('input[name=id_talenta]').val(), 'id_perusahaan':$('input[name=id_perusahaan]').val(), 'grup_jabatan_id':$('input[name=grup_jabatan_id]').val()}, 'Ubah kelengkapan');
	});

	$('body').on('click','.cls-button-delete-kelengkapan',function(){
		onbtndeletekelengkapan(this);
	});

  $('body').on('click', '.cls-urlpendukung', function(){
     popupwindow($(this).data('url'), $(this).data('keterangan'), 500, 500);
  });
	
	setDatatableKelengkapan();
  setDatatableSumKelengkapan();
});

function setDatatableKelengkapan(){
  datatablekelengkapan = $('#datatable-kelengkapan').on( 'preXhr.dt', function ( e, settings, processing ) {
            KTApp.block('.cls-content-data', {
                overlayColor: '#000000',
                type: 'v2',
                state: 'primary',
                message: 'Sedang proses, silahkan tunggu ...'
            });
  })
  .on('xhr.dt', function ( e, settings, json, xhr ) {
      KTApp.unblock('.cls-content-data');
  }).DataTable({
      serverSide: true,
      processing: true,
      search: {
          caseInsensitive: true
      },
      searchHighlight: true,
      ajax: {
	        url: urldatatablekelengkapan,
	        type: 'GET',
	        data: function (d) {
	            d.id_talenta = $('input[name=id_talenta]').val();
	        }
	  },
      columns: [
        { data: null, orderable: false, searchable: false},
        { data: 'nama_lengkap', name: 'nama_lengkap', searchable: true},
        { data: 'nama', name: 'nama', searchable: true},
        { data: 'filename', name: 'filename', searchable: true},
        { data: 'action', orderable: false, searchable: false}
      ],
      drawCallback: function( settings ) {
        var info = datatablekelengkapan.page.info();
          $('[data-toggle="tooltip"]').tooltip();
          datatablekelengkapan.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
              cell.innerHTML = info.start + i + 1;
          } );
      }
  });	
}

function setDatatableSumKelengkapan(){
  datatablesumkelengkapan = $('#datatable-sum-kelengkapan').on( 'preXhr.dt', function ( e, settings, processing ) {
            KTApp.block('.cls-content-data', {
                overlayColor: '#000000',
                type: 'v2',
                state: 'primary',
                message: 'Sedang proses, silahkan tunggu ...'
            });
  })
  .on('xhr.dt', function ( e, settings, json, xhr ) {
      KTApp.unblock('.cls-content-data');
  }).DataTable({
      serverSide: true,
      processing: true,
      info: false,
      searching: false,
      lengthChange: false,
      ajax: {
          url: urldatatablesumkelengkapan,
          type: 'GET',
          data: function (d) {
              d.id_talenta = $('input[name=id_talenta]').val();
          }
    },
      columns: [
        { data: null, orderable: false, searchable: false},
        { data: 'nama_lengkap', name: 'nama_lengkap', searchable: true},
        { data: 'nama', name: 'nama', searchable: true},
        { data: 'filename', name: 'filename', searchable: true},
      ],
      drawCallback: function( settings ) {
        var info = datatablesumkelengkapan.page.info();
          $('[data-toggle="tooltip"]').tooltip();
          datatablesumkelengkapan.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
              cell.innerHTML = info.start + i + 1;
          } );
      }
  }); 
}

function onbtndeletekelengkapan(element){
	swal.fire({
        title: "Pemberitahuan",
        text: "Yakin hapus data kelengkapan ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, hapus data",
        cancelButtonText: "Tidak"
    }).then(function(result) {
        if (result.value) {
            $.ajax({
               url: urldeletekelengkapan,
               data:{id:$(element).data('id')},
               type:'post',
               dataType:'json',
               beforeSend: function(){
		            KTApp.block('.cls-content-data', {
		                overlayColor: '#000000',
		                type: 'v2',
		                state: 'primary',
		                message: 'Sedang proses, silahkan tunggu ...'
		            });
               },
               success: function(data){
                   KTApp.unblock('.cls-content-data');

		           swal.fire({
		                title: data.title,
		                html: data.msg,
		                type: data.flag,

		                buttonsStyling: false,

		                confirmButtonText: "<i class='flaticon2-checkmark'></i> OK",
		                confirmButtonClass: "btn btn-default"
		           });

                   if(data.flag == 'success') {
                   	  datatablekelengkapan.ajax.reload( null, false );
                      datatablesumkelengkapan.ajax.reload( null, false );
                   }
               },
                error: function(jqXHR, exception) {
                  KTApp.unblock('.cls-content-data');
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
        }
    });	
}