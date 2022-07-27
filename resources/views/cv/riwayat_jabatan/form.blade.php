<form action="{{route('cv.keluarga.store', ['id_talenta' => $id_talenta])}}" class="kt-form kt-form--label-right" method="POST" id="form-first">
	@csrf
	<input type="hidden" name="id" id="id" readonly="readonly" value="{{$actionform == 'update'? (int)$data->id : null}}" />
	<input type="hidden" name="actionform" id="actionform" readonly="readonly" value="{{$actionform}}"/>
	<input type="hidden" name="formal_flag" id="actionform" readonly="readonly" value="TRUE"/>
	<div class="kt-portlet__body">
		<div class="row">
			<div class="col-lg-12">				
				<div class="form-group">
					<label>Tupoksi</label>	
					@php
            	      $value = ($actionform == 'update'? $data->tupoksi : '')
            	    @endphp					
					<textarea id="" class="form-control" rows="3" name="tupoksi">{{ $value }}</textarea>
				</div>
			</div>	
			<div class="col-lg-12">				
				<div class="form-group">
					<label>Achievement</label>	
					@php
            	      $value = ($actionform == 'update'? $data->achievement : '')
            	    @endphp					
					<textarea id="" class="form-control" rows="3" name="achievement">{{ $value }}</textarea>
				</div>
			</div>	
		</div>
	</div>
	<div class="kt-portlet__foot">
		<div class="kt-form__actions">
			<div class="row">
				<div class="col-lg-6">
					<button type="submit" class="btn btn-primary">Simpan</button>
				</div>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript" src="{{asset('js/cv/datepicker.js')}}"></script>
<script type="text/javascript" src="{{asset('js/cv/riwayat_organisasi/form-formal.js')}}"></script>
<script type="text/javascript">
</script>