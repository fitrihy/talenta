@extends('layouts.app')


@section('addbeforecss')
<link href="{{asset('assets/vendors/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
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
        <!-- <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions">            
                    @can('talent-create')
                    <a href="javascript:;" class="btn btn-primary btn-elevate btn-icon-sm cls-add">
                        <i class="la la-plus"></i>
                        Tambah Talenta
                    </a>
                    @endcan
                </div>
            </div>
        </div> -->
    </div>
    
    <div class="kt-portlet__body">

        <div class="alert alert-custom fade show mb-5" style="background-color:yellow;" role="alert">
            <div class="alert-text"><b>Keterangan :</b><br>
                <b>Approval Nominated -></b> Persetujuan atas usulan talent dari BUMN, status talent Nominated<br>
                <b>Assessment Assignment -></b> Assign talent ke lembaga assessment<br>
                <b>Verifikasi Assessment -></b> Memverifikasi hasil assessment talent<br>
                <b>View All -></b> Melihat semua data talent termasuk statusnya
            </div>
            <div class="alert-close">
                <button style="margin-top:-50px;" type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="flaticon2-delete" style="font-size:x-small;"></i></span>
                </button>
            </div>
        </div>

      <!-- end pencarian -->

        <ul class="nav nav-tabs nav-tabs mb-5">
            <li class="nav-item">
                <a href="/talenta/verifikasi_kbumn/index" class="nav-link active btn-approval"> Approval Nominated <label id="jumlah_nominated" class="kt-badge kt-badge--danger kt-badge--pill kt-badge--rounded">{{$data['jumlah_nominated']}}</label></a>
            </li>
            <li class="nav-item">
                <a href="/talenta/assessment/index" class="nav-link btn-assessment"> Assessment Assignment 
                    <label id="jumlah_eligible1" class="kt-badge kt-badge--danger kt-badge--pill kt-badge--rounded">{{$data['jumlah_eligible1']}}</label>
                </a>
            </li>
            <li class="nav-item">
                <a href="/talenta/verifikasi_assessment/index" class="nav-link btn-verifikasi"> Verifikasi Assessment
                    <label id="jumlah_eligible2" class="kt-badge kt-badge--danger kt-badge--pill kt-badge--rounded">{{$data['jumlah_eligible2']}}</label>
                </a>
            </li>
        </ul>

        <div class="kt-portlet kt-shape-bg-color-2">
            <form class="kt-form kt-form--label-right" >
                <div class="kt-portlet__body">
                <div class="col-lg-12">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label><span class="kt-font-dark">Search Talent:</span></label>
                            
                            <select class="form-control kt-select2" id="nama_lengkap" name="nama_lengkap">
                                <option></option>
                                @foreach($talenta as $data)
                                <option value="{{ $data->nama_lengkap }}">{{ $data->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label><span class="kt-font-dark">Perusahaan:</span></label>
                            <select class="form-control kt-select2" id="instansi" name="instansi">
                                <option></option>
                                @foreach($perusahaan as $data)
                                <option value="{{ $data->id }}">{{ $data->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 pull-right">
                            <div class="">
                                <a id="cari" type="button" class="btn btn-danger" href="javascript:;" >cari</a>
                                <a id="reset" type="button" class="btn btn-warning" href="javascript:;" >reset</a>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </form>
        </div>

        {{-- <div class="form-group row">
            <div class="col-lg-4">
                <label><span class="kt-font-dark">BUMN:</span></label>
                
                <select class="form-control kt-select2" id="instansi" name="instansi">
                    <option></option>
                    @foreach($perusahaan as $data)
                    <option value="{{ $data->id }}">{{ $data->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-4">
            </div>
        </div> --}}

        <div class="tab-content">
            <!-- Data Talent -->
            <div class="tab-pane active" role="tabpanel" id="datatalent">
                <div class="col-lg-12">
                    <label class="mt-checkbox mt-checkbox-outline pull-right">
                        <input type="checkbox" class="reject_all" />
                        <span>Reject All</span>
                    </label>
                    <label class="mt-checkbox mt-checkbox-outline pull-right">
                        <input type="checkbox" class="checked_all" />
                        <span>Approve All &ensp;</span>
                    </label>
                </div>
                <!--begin: Datatable -->
                <div class="table-responsive">
                    <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                        <thead>
                            <tr>
                                <th width="5%" rowspan="2">No.</th>
                                <th rowspan="2" >Nama Lengkap</th>
                                <th rowspan="2" >Status Talent</th>
                                <th rowspan="2" >Jabatan</th>
                                <th rowspan="2" >Status Pengisian</th>
                                <th colspan="2"><div align="center">Aksi</div></th>
                            </tr>
                            <tr>
                                <th> Approve </th>
                                <th> Reject </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!--end: Datatable -->
                
                <div class="col-lg-12">
                    <input type="hidden" name="checked_list" class="checked_list">
                    <input type="hidden" name="reject_list" class="reject_list">
                    <button class="btn btn-success pull-right btn-submit" style="margin-top:40px;">Submit Talent</button>
                </div>
            </div>
            <!-- end Data Talent -->
        </div>

    </div>
</div>
@endsection

@section('addafterjs')
  <script type="text/javascript">
      var urlminicv = "{{route('talenta.register.minicv')}}";
      var urllogstatus = "{{route('talenta.register.log_status')}}";
      var urldatatable = "{{route('talenta.verifikasi_kbumn.datatable')}}";
      var urlupdate_talenta = "{{route('talenta.verifikasi_kbumn.update_talenta')}}";
  </script>
  <script src="{{asset('assets/vendors/custom/datatables/datatables.bundle.js')}}" type="text/javascript"></script>
  <script type="text/javascript" src="{{asset('js/talenta/verifikasi_kbumn/index.js')}}"></script>
@endsection
