<form class="kt-form kt-form--label-right" method="POST" id="form-dynamicsearch">
	@csrf
	<input type="hidden" name="id" id="id" readonly="readonly" value="{{$actionform == 'update'? (int)$dynamicsearch->id : null}}" />
	<input type="hidden" name="actionform" id="actionform" readonly="readonly" value="{{$actionform}}" />
	<div class="kt-portlet__body">
		<div class="form-group row">
			<div class="col-lg-6">
				<label>Menu</label>
				<input type="text" class="form-control" name="menu" id="menu" value="{{!empty(old('menu'))? old('menu') : ($actionform == 'update' && $dynamicsearch->menu != ''? $dynamicsearch->menu : old('menu'))}}" />
			</div>
			<div class="col-lg-6">
				<label>Sub Menu</label>
				<input type="text" class="form-control" name="submenu" id="submenu" value="{{!empty(old('submenu'))? old('submenu') : ($actionform == 'update' && $dynamicsearch->submenu != ''? $dynamicsearch->submenu : old('submenu'))}}" />
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-6">
				<label>Tipe</label>
				<input type="text" class="form-control" name="tipe" id="tipe" value="{{!empty(old('tipe'))? old('tipe') : ($actionform == 'update' && $dynamicsearch->tipe != ''? $dynamicsearch->tipe : old('tipe'))}}" />
			</div>
			<div class="col-lg-6">
				<label>Tabel Referensi</label>
				<input type="text" class="form-control" name="tabel_referensi" id="tabel_referensi" value="{{!empty(old('tabel_referensi'))? old('tipe') : ($actionform == 'update' && $dynamicsearch->tabel_referensi != ''? $dynamicsearch->tabel_referensi : old('tabel_referensi'))}}" />
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-12">
				<label>Keterangan</label>
				<input type="text" class="form-control" name="keterangan" id="keterangan" value="{{!empty(old('keterangan'))? old('keterangan') : ($actionform == 'update' && $dynamicsearch->keterangan != ''? $dynamicsearch->keterangan : old('keterangan'))}}" />
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

<script type="text/javascript" src="{{asset('js/referensi/dynamicsearch/form.js')}}"></script>