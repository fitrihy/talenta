<style>
  .foto-talenta {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
    height: 100px;
  }
  .detail-talenta {
    font-size: 10px;
  }
  .detail-talenta td {
    vertical-align: text-top;
    padding-bottom:5px;
  }


</style>

<div class="kt-portlet__body" >
    <div class="form-group row btn-footer text-center">
        <div class="col-lg-12">
            <a href="#" target="_blank" style="float:right;margin-top:-90px;margin-right:20px;" class="btn btn-primary btn-elevate btn-icon-sm cls-compare-pdf">
                <i class="far fa-file-pdf"></i>
                Download PDF
            </a>
        </div>
    </div>
    <div class="form-group row" style="margin-top: -20px;">
        @foreach ($talentas as $talenta)
        <div class="col-lg-4">
            <div class="kt-portlet__body" style="border-color: red;border: 2px solid #9b9b9b;">
                <div class="form-group row">
                    <div class="col-lg-12 text-center"> 
                        <img class="foto-talenta" src="{{ \App\Helpers\CVHelper::getFoto($talenta->foto, ($talenta->jenis_kelamin == 'L' ? 'img/male.png': 'img/female.png')) }}" alt=""> 
                    </div> 
                    <div class="col-lg-12 kt-font-dark detail-talenta">
                        <div class="form-group row text-center">
                            <div class="col-lg-12">
                                <span style="font-size:12px;"><b>{{$talenta->nama_lengkap}}</b></span><br>
                                {{$talenta->nik}}
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="form-group row" style="background-color:#9b9b9b;height:1px;margin-top:-10px;margin-bottom:10px;"> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <table>
                                <tr>
                                    <td><b>Jabatan</b></td>
                                    <td>:</td>
                                    <td>
                                        {{$talenta->jabatan}} 
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>BUMN</b></td>
                                    <td>:</td>
                                    <td>
                                        {{$talenta->nama_perusahaan}}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Jenis Kelamin</b></td>
                                    <td>:</td>
                                    <td>
                                        {{$talenta->jenis_kelamin}}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Tempat Lahir</b></td>
                                    <td>:</td>
                                    <td>
                                        {{$talenta->tempat_lahir}}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Tanggal Lahir</b></td>
                                    <td>:</td>
                                    <td>
                                        {{\App\Helpers\CVHelper::tglformat(@$talenta->tanggal_lahir)}}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Suku</b></td>
                                    <td>:</td>
                                    <td>
                                        {{$talenta->suku}}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Golongan Darah</b></td>
                                    <td>:</td>
                                    <td>
                                        {{$talenta->gol_darah}}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Pendidikan</b></td>
                                    <td>:</td>
                                    <td>
                                        {{$talenta->pendidikan}}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Gelar Akademik</b></td>
                                    <td>:</td>
                                    <td>
                                        {{$talenta->gelar}}
                                    </td>
                                </tr>
                            </table>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>



<script type="text/javascript">
</script>

<script src="{{asset('assets/vendors/custom/datatables/datatables.bundle.js')}}" type="text/javascript"></script>