var datatable;

$(document).ready(function(){
	$('body').on('click','.first_table', function(){
		winform(urlcreate, {}, 'Tambah Data Jabatan');
	});

  $('body').on('click','.second_table', function(){
    winform(urlcreate2, {}, 'Tambah Data Jabatan');
  });

	$('body').on('click','.first_table-edit',function(){
    winform(urledit, {'id':$(this).data('id')}, 'Ubah Data Jabatan');
  });

  $('body').on('click','.second_table-edit',function(){
    winform(urledit2, {'id':$(this).data('id')}, 'Ubah Data Jabatan');
  });

	$('body').on('click','.first_table-delete',function(){
		onbtndelete(this);
	});

  $('body').on('click','.second_table-delete',function(){
    onbtndelete2(this);
  });
	
  $('.kt-select2').select2({
        placeholder: "Pilih"
    });

  $('body').on('change', '#form_tahun', function() {
    datatable.ajax.reload( null, false );
  });
  
  $('body').on('change', '#form_perusahaan', function() {
    datatable.ajax.reload( null, false );
  });

	setDatatable();
  setDatatable2();
});

function setDatatable(){
  datatable = $('#datatable').on( 'preXhr.dt', function ( e, settings, processing ) {
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
      searching: false,
      search: {
          caseInsensitive: true
      },
      pageLength: 5,
      lengthMenu: [[5, 10, 20, -1], [5, 10, 20, "All"]],
      searchHighlight: true,
      ajax: urldatatable,
      ajax: {
          url: urldatatable,
          type: 'GET'
      },
      columns: [
        { data: null, orderable: false, searchable: false},
        { data: 'jabatan', name: 'jabatan', searchable: true},
        { data: 'tupoksi', name: 'tupoksi', searchable: true},
        { data: 'tanggal_awal', name: 'tanggal_awal', searchable: true},
        { data: 'tanggal_akhir', name: 'tanggal_akhir', searchable: true},
        { data: 'achievement', name: 'achievement', searchable: true},
        { data: 'action', orderable: false, searchable: false}
      ],
      drawCallback: function( settings ) {
        var info = datatable.page.info();
          $('[data-toggle="tooltip"]').tooltip();
          datatable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
              cell.innerHTML = info.start + i + 1;
          } );
      }
  });	
}

function setDatatable2(){
  datatable2 = $('#datatable2').on( 'preXhr.dt', function ( e, settings, processing ) {
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
      searching: false,
      search: {
          caseInsensitive: true
      },
      pageLength: 5,
      lengthMenu: [[5, 10, 20, -1], [5, 10, 20, "All"]],
      searchHighlight: true,
      ajax: urldatatable2,
      ajax: {
          url: urldatatable2,
          type: 'GET'
      },
      columns: [
        { data: null, orderable: false, searchable: false},
        { data: 'penugasan', name: 'penugasan', searchable: true},
        { data: 'tupoksi', name: 'tupoksi', searchable: true},
        { data: 'tanggal_awal', name: 'tanggal_awal', searchable: true},
        { data: 'tanggal_akhir', name: 'tanggal_akhir', searchable: true},
        { data: 'action', orderable: false, searchable: false}
      ],
      drawCallback: function( settings ) {
        var info = datatable2.page.info();
          $('[data-toggle="tooltip"]').tooltip();
          datatable2.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
              cell.innerHTML = info.start + i + 1;
          } );
      }
  }); 
}

function onbtndelete(element){
	swal.fire({
        title: "Pemberitahuan",
        text: "Yakin hapus data Jabatan "+$(element).data('periode')+" ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, hapus data",
        cancelButtonText: "Tidak"
    }).then(function(result) {
        if (result.value) {
            $.ajax({
               url: urldelete,
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
                   	  datatable.ajax.reload( null, false );
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

function onbtndelete2(element){
  swal.fire({
        title: "Pemberitahuan",
        text: "Yakin hapus data Jabatan "+$(element).data('periode')+" ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, hapus data",
        cancelButtonText: "Tidak"
    }).then(function(result) {
        if (result.value) {
            $.ajax({
               url: urldelete2,
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
                      datatable2.ajax.reload( null, false );
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