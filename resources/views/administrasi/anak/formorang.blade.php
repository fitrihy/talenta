<form class="kt-form kt-form--label-right" method="POST" id="form-orang-anak">
	@csrf
	<input type="hidden" name="actionform" id="actionform" readonly="readonly" value="{{$actionform}}" />
	
	<div class="kt-portlet__body">
		<div class="form-group row">
			<div class="col-lg-6">
				<label>Nama</label>
				<input type="text" required="required" class="form-control" name="nama_lengkap" id="nama_lengkap" />
			</div>
			<div class="col-lg-6">
				<label>Jenis Kelamin</label>
				<select class="form-control kt-select2" name="jenis_kelamin">
					<option value="L">Laki-laki</option>
					<option value="P">Perempuan</option>
				</select>
			</div>
		</div>
		<div class="form-group">
          <label>Kewarganegaraan</label>         
          <div class="radio-inline">
              <label class="radio">
                  <input type="radio" id="wni" checked="checked" name="kewarganegaraan" value="WNI">
                  <span></span>
                  WNI
              </label>
              <label class="radio">
                  <input type="radio" id="wna" name="kewarganegaraan" value="WNA">
                  <span></span>
                  WNA
              </label>
          </div>
        </div>
		<div class="form-group row">
			<div class="col-lg-6">
				<label>NIK/PASSPORT</label>
				<input type="text" required="required" class="form-control" name="nik" id="nik" />
			</div>
			<div class="col-lg-6">
				<label>NPWP</label>
				<input type="text"  class="form-control" name="npwp" id="npwp" />
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-6">
				<label>Email</label>
				<input type="text"  class="form-control" name="email" id="email" />
			</div>
			<div class="col-lg-6">
				<label>Nomor Telp</label>
				<input type="text"  class="form-control" name="nomor_hp" id="nomor_hp" />
			</div>
		</div>
	</div>
	<div class="kt-portlet__foot">
		<div class="kt-form__actions">
			<div class="row">
				<div class="col-lg-12">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
					<button type="submit" class="btn btn-primary">Simpan</button>
				</div>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript" src="{{asset('js/administrasi/anak/formorang.js')}}"></script>