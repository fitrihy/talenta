var datatable;

$(document).ready(function(){
	$('body').on('click','.cls-generate',function(){
		onbtngenerate();
	});

    $('.kt-select2').select2({
		placeholder: "Pilih"
	});
});
