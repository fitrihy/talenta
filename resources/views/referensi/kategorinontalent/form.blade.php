<form class="kt-form kt-form--label-right" method="POST" id="form-kategorinontalent">
	@csrf
	<input type="hidden" name="id" id="id" readonly="readonly" value="{{$actionform == 'update'? (int)$kategorinon->id : null}}" />
	<input type="hidden" name="actionform" id="actionform" readonly="readonly" value="{{$actionform}}" />
	<div class="kt-portlet__body">
		<div class="form-group row">
			<div class="col-lg-6">
				<label>Jenis Jabatan Talent</label>
				<select class="form-control kt-select2" name="id_kategori_jabatan_talent">
                    <option value=""></option>
                    @foreach($kategorijabatan as $kategori)
                    @php
            	      $select = !empty(old('id_kategori_jabatan_talent')) && in_array($kategori->id, old('id_kategori_jabatan_talent'))? 'selected="selected"' : ($actionform == 'update' && ($kategori->id==$kategorinon->id_kategori_jabatan_talent)? 'selected="selected"' : '')
            	   @endphp
	                <option value="{{ $kategori->id }}" {!! $select !!}>{{ $kategori->nama }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-6">
				<label>nama</label>
				<input type="text" class="form-control" name="nama" id="nama" value="{{!empty(old('nama'))? old('nama') : ($actionform == 'update' && $kategorinon->nama != ''? $kategorinon->nama : old('nama'))}}" />
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-6">
				<label>Keterangan</label>
				<input type="text" class="form-control" name="keterangan" id="keterangan" value="{{!empty(old('keterangan'))? old('keterangan') : ($actionform == 'update' && $kategorinon->keterangan != ''? $kategorinon->keterangan : old('keterangan'))}}" />
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

<script type="text/javascript" src="{{asset('js/referensi/kategorinontalent/form.js')}}"></script>