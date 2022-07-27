@extends('layouts.app')


@section('addbeforecss')
<link href="{{asset('assets/global/plugins/jquery-treegrid-master/css/jquery.treegrid.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__head kt-portlet__head--lg">
        <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
                <i class="kt-font-brand flaticon-web"></i>
            </span>
            <h3 class="kt-portlet__head-title">
                {{$pagetitle}}
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions">
                    <a href="{{url('referensi/bumn/silababumnsync')}}" class="btn btn-brand btn-elevate btn-icon-sm">
                        <i class="la la-refresh"></i>
                        Sync Data
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="kt-portlet__body">
        <!-- start pencarian -->
        <div class="kt-portlet kt-portlet--collapsed kt-shape-bg-color-2" data-ktportlet="true" id="kt_portlet_tools_6">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Pencarian
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-group">
                        <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-warning btn-icon-md" aria-describedby="tooltip_m3bv968wwi"><i class="la la-angle-down"></i></a>
                    </div>
                </div>
            </div>

            <!--begin::Form-->
            <form class="kt-form kt-form--label-right">
                <div class="kt-portlet__body">
                    <div class="form-group row">
                          <div class="col-lg-6">
                            <label><span class="kt-font-dark">Perusahaan:</span></label>
                            <input type="text" class="form-control" name="perusahaan" id="perusahaan" value="{{$src_perusahaan}}" />
                          </div>
                          <div class="col-lg-6">
                            <label><span class="kt-font-dark">Kategori Perusahaan:</span></label>
                            <select class="form-control kt-select2" id="id_jenis_perusahaan" name="id_jenis_perusahaan">
                              <option></option>  
                              @foreach($jenis_perusahaans as $jenis_perusahaan)
                                <option value="{{ $jenis_perusahaan->id }}" @if($src_jenis_perusahaan == $jenis_perusahaan->id) selected="selected" @endif>{{ $jenis_perusahaan->nama }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                </div>
                <div class="kt-portlet__foot">
                    <div class="">
                        <a id="cari" type="button" class="btn btn-danger" href="javascript:;" >
                                        cari
                                    </a>
                        <a id="reset" type="button" class="btn btn-warning" href="javascript:;" >
                                        reset
                                    </a>
                    </div>
                </div>
            </form>

            <!--end::Form-->
        </div>
            
        @php $induks = $bumns->where('induk', 0)->sortBy('kepemilikan'); $no=1; @endphp
        @if (count($induks) > 0)
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover tree">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Nama</th>
                        <th style="width: 25%;">Nama Singkat</th>
                        <th style="width: 15%;">Jenis Perusahaan</th>
                        <th style="width: 5%;">Kepemilikan</th>
                        <th style="width: 5%;">Kelas</th>
                        <th style="width: 10%;">Created At</th>
                        <th style="width:10%;">Kategori Perusahaan</th>
                        <th style="width: 5%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                 @foreach ($induks as $induk)

                    <tr class="treegrid-{{$induk->id}}" data-count="2">
                        <td>{{$no}}</td>
                        <td class="nama{{$induk->id}}">{{$induk->nama_lengkap}}</td>
                        <td>{{$induk->nama_singkat}}</td>
                        <td>{{$induk->jenis_perusahaan}}</td>
                        <td>{{$induk->kepemilikan}}</td>
                        <td>{{$induk->kelas}}</td>
                        <td>{{$induk->created_at}}</td>
                        <td>@if($induk->kategori_perusahaan) {{$induk->kategori_perusahaan->nama}} @endif</td>
                        <td><button type="button" class="btn btn-outline-brand btn-icon cls-button-edit" data-id="{{$induk->id}}" data-toggle="tooltip" data-original-title="Ubah data BUMN {{$induk->nama_lengkap}}"><i class="flaticon-edit"></i></button></td>
                    </tr>

                    @php $anaks = $bumns->where('induk', $induk->id); @endphp

                    @foreach ($anaks as $anak)

                        <tr class="treegrid-{{$anak->id}} treegrid-parent-{{$anak->induk}} item{{$anak->id}}">
                            <td></td>
                            <td class="nama{{$anak->id}}">{{$anak->nama_lengkap}}</td>
                            <td>{{$anak->nama_singkat}}</td>
                            <td>{{$anak->jenis_perusahaan}}</td>
                            <td>{{$anak->kepemilikan}}</td>
                            <td>{{$anak->kelas}}</td>
                            <td>{{$anak->created_at}}</td>
                            <td>@if($anak->kategori_perusahaan)  {{$anak->kategori_perusahaan->nama}} @endif</td>
                            <td>edit</td>
                        </tr>

                        @php $cucus = $bumns->where('induk', $anak->id); @endphp

                        @foreach ($cucus as $cucu)

                            <tr class="treegrid-{{$cucu->id}} treegrid-parent-{{$cucu->induk}} item{{$cucu->id}}">
                                <td></td>
                                <td class="nama{{$cucu->id}}">{{$cucu->nama_lengkap}}</td>
                                <td>{{$cucu->nama_singkat}}</td>
                                <td>{{$cucu->jenis_perusahaan}}</td>
                                <td>{{$cucu->kepemilikan}}</td>
                                <td>{{$cucu->kelas}}</td>
                                <td>{{$cucu->created_at}}</td>
                                <td>@if($cucu->kategori_perusahaan) {{$cucu->kategori_perusahaan->nama}} @endif</td>
                                <td>edit</td>
                            </tr>

                            <!-- @php $cicits = $bumns->where('induk', $cucu->id); @endphp

                            @foreach ($cicits as $cicit)

                                <tr class="treegrid-{{$cicit->id}} treegrid-parent-{{$cicit->induk}} item{{$cicit->id}}">
                                    <td class="nama{{$cicit->id}}">{{$cicit->nama_lengkap}}</td>
                                    <td>{{$cicit->nama_singkat}}</td>
                                    <td>{{$cicit->jenis_perusahaan}}</td>
                                    <td>{{$cicit->kepemilikan}}</td>
                                    <td>{{$cicit->created_at}}</td>
                                </tr>

                            @endforeach -->

                        @endforeach

                    @endforeach
                    @php $no++; @endphp
                 @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

@section('addafterjs')
  <script type="text/javascript">
      var urlcreate = "{{route('referensi.bumn.create')}}";
      var urledit = "{{route('referensi.bumn.edit')}}";
      var urlstore = "{{route('referensi.bumn.store')}}";
      var urldatatable = "{{route('referensi.bumn.datatable')}}";
      var urldelete = "{{route('referensi.bumn.delete')}}";
  </script>
  <script type="text/javascript" src="{{asset('assets/global/plugins/jquery-treegrid-master/js/jquery.treegrid.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/referensi/bumn/index.js')}}"></script>
  <script type="text/javascript">
      $('.tree').treegrid({
        initialState : 'collapsed',
        treeColumn : 1,
        indentTemplate : '<span style="width: 32px; height: 16px; display: inline-block; position: relative;"></span>'
      });
    </script>
@endsection