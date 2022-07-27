@extends('layouts.app')

@section('addbeforecss')
<link href="{{asset('assets/vendors/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
@endsection

<!-- Konten -->
@section('content')
<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				{{$namaperusahaan->nama_lengkap}} ({{$nomor_sk}}) <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">{{$namagrupjabat->nama}}</span>
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
		<div class="container py-3 my-3 border rounded shadow-sm">
		    <div class="row">
		        <div class="col-12">
		            <ul class="nav nav-tabs flex-nowrap" role="tablist">
		            	@foreach($tabjenisks as $key => $value)

		            	<li role="presentation" class="nav-item">
		                    <a href="#kt_portlet_base_demo_1_{{$value->id}}_tab_content_{{$value->id}}" class="nav-link {{$key === 0 ? 'active' : 'disabled'}}" data-toggle="tab" aria-controls="step{{$value->id}}" role="tab" title="{{$value->nama}}"> {{$value->nama}} </a>
		                </li>
		            	
		                @endforeach
		                <li role="presentation" class="nav-item">
		                    <a href="#summary" class="nav-link disabled" data-toggle="tab" aria-controls="summary" role="tab" title="Summary"> Summary </a>
		                </li>
		            </ul>
		            <form role="form">
		            	<input type="hidden" name="id_surat_keputusan" id="id_surat_keputusan" readonly="readonly" value="{{$id_surat_keputusan}}" />
						<input type="hidden" name="id_perusahaan" id="id_perusahaan" readonly="readonly" value="{{$id_perusahaan}}" />
						<input type="hidden" name="grup_jabatan_id" id="grup_jabatan_id" readonly="readonly" value="{{$grup_jabatan_id}}" />
		                <div class="tab-content py-2">
		                	@foreach($jenis_sk_id as $key => $value)
		                	@if($value == 1)
		                    <div class="tab-pane {{$key === 0 ? 'active' : ''}}" role="tabpanel" id="kt_portlet_base_demo_1_{{$value}}_tab_content_{{$value}}">

		                    	@if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    	<br>
		                    	<br>
		                    	<br>

		                        <div class="kt-portlet__head">
									<div class="kt-portlet__head-label">
										<h3 class="kt-portlet__head-title">
											{{$namaperusahaan->nama_lengkap}} ({{$nomor_sk}}) <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">{{$namagrupjabat->nama}}</span>
										</h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="dropdown dropdown-inline">
											<a href="#" class="btn btn-default btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="flaticon-add"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right" style="">
												<ul class="kt-nav">
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link cls-add-angkat">
															<i class="kt-nav__link-icon flaticon2-line-chart "></i>
															<span class="kt-nav__link-text">Pengangkatan</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link cls-add-angkatlagi">
															<i class="kt-nav__link-icon flaticon2-send" ></i>
															<span class="kt-nav__link-text"> Perpanjangan</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="kt-nav__link ">
															<i class="kt-nav__link-icon flaticon2-pie-chart-1"></i>
															<span class="kt-nav__link-text">Data Pokok</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
							            
							        </div>
								</div>
								<div class="kt-portlet__body">
									<!--begin: Datatable -->
									<div class="table-responsive">
										<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-angkat">
								            <thead>
								                <tr>
								                    <th width="5%"><div align="center">No.</div></th>
								                    <th width="30%"><div align="center">Jabatan</div></th>
								                    <th width="15%"><div align="center">Nama Pejabat</div></th>
								                    <th width="5%"><div align="center">Periode</div></th>
								                    <th width="10%"><div align="center">Tanggal Awal Menjabat</div></th>
								                    <th width="10%"><div align="center">Tanggal Akhir Menjabat</div></th>
								                    <th width="15%"><div align="center">Aksi</div></th>
								                </tr>
								            </thead>
								            <tbody></tbody>
								        </table>
									</div>
							        

							        <!--end: Datatable -->
								</div>
		                        @if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    </div>
		                    @elseif($value == 2)
		                    <div class="tab-pane {{$key === 0 ? 'active' : ''}}" role="tabpanel" id="kt_portlet_base_demo_1_{{$value}}_tab_content_{{$value}}">

		                    	@if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    	<br>
		                    	<br>
		                    	<br>

		                        <div class="kt-portlet__head">
									<div class="kt-portlet__head-label">
										<h3 class="kt-portlet__head-title">
											{{$namaperusahaan->nama_lengkap}} ({{$nomor_sk}}) <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">{{$namagrupjabat->nama}}</span>
										</h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="dropdown dropdown-inline">
											<a href="#" class="btn btn-default btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="flaticon-add"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right" style="">
												<ul class="kt-nav">
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link cls-add-berhenti">
															<i class="kt-nav__link-icon flaticon2-line-chart "></i>
															<span class="kt-nav__link-text">Pemberhentian</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="kt-nav__link ">
															<i class="kt-nav__link-icon flaticon2-pie-chart-1"></i>
															<span class="kt-nav__link-text">Data Pokok</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
							        </div>
								</div>
								<div class="kt-portlet__body">
									<!--begin: Datatable -->
									<div class="table-responsive">
										<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-berhenti">
								            <thead>
								                <tr>
								                    <th width="5%"><div align="center">No.</div></th>
								                    <th width="10%"><div align="center">Jabatan</div></th>
								                    <th width="25%"><div align="center">Nama Pejabat</div></th>
								                    <th width="25%"><div align="center">Keterangan</div></th>
								                    <th width="10%"><div align="center">Tanggal Akhir Menjabat</div></th>
								                    <th width="5%"><div align="center">Aksi</div></th>
								                </tr>
								            </thead>
								            <tbody></tbody>
								        </table>
									</div>
							        

							        <!--end: Datatable -->
								</div>
		                        @if($key === 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    </div>
		                    @elseif($value == 3)
		                    <div class="tab-pane {{$key === 0 ? 'active' : ''}}" role="tabpanel" id="kt_portlet_base_demo_1_{{$value}}_tab_content_{{$value}}">

		                    	@if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    	<br>
		                    	<br>
		                    	<br>

		                        <div class="kt-portlet__head">
									<div class="kt-portlet__head-label">
										<h3 class="kt-portlet__head-title">
											{{$namaperusahaan->nama_lengkap}} ({{$nomor_sk}}) <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">{{$namagrupjabat->nama}}</span>
										</h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="dropdown dropdown-inline">
											<a href="#" class="btn btn-default btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="flaticon-add"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right" style="">
												<ul class="kt-nav">
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link cls-add-plt">
															<i class="kt-nav__link-icon flaticon2-line-chart "></i>
															<span class="kt-nav__link-text">Penetapan PLT</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="kt-nav__link ">
															<i class="kt-nav__link-icon flaticon2-pie-chart-1"></i>
															<span class="kt-nav__link-text">Data Pokok</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
							        </div>
								</div>
								<div class="kt-portlet__body">
									<!--begin: Datatable -->
									<div class="table-responsive">
										<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-plt">
								            <thead>
								                <tr>
								                    <th><div align="center">No.</div></th>
								                    <th><div align="center">Jabatan PLT</div></th>
								                    <th><div align="center">Nama Pejabat</div></th>
								                    <th><div align="center">Tanggal Awal Menjabat</div></th>
								                    <th><div align="center">Tanggal Akhir Menjabat</div></th>
								                    <th><div align="center">Aksi</div></th>
								                </tr>
								            </thead>
								            <tbody></tbody>
								        </table>
									</div>
							        

							        <!--end: Datatable -->
								</div>
		                        @if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    </div>
		                    @elseif($value == 4)
		                    <div class="tab-pane {{$key === 0 ? 'active' : ''}}" role="tabpanel" id="kt_portlet_base_demo_1_{{$value}}_tab_content_{{$value}}">

		                    	@if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    	<br>
		                    	<br>
		                    	<br>

		                        <div class="kt-portlet__head">
									<div class="kt-portlet__head-label">
										<h3 class="kt-portlet__head-title">
											{{$namaperusahaan->nama_lengkap}} ({{$nomor_sk}}) <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">{{$namagrupjabat->nama}}</span>
										</h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="dropdown dropdown-inline">
											<a href="#" class="btn btn-default btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="flaticon-add"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right" style="">
												<ul class="kt-nav">
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link cls-add-klatur">
															<i class="kt-nav__link-icon flaticon2-line-chart "></i>
															<span class="kt-nav__link-text">Nomenklatur</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="kt-nav__link ">
															<i class="kt-nav__link-icon flaticon2-pie-chart-1"></i>
															<span class="kt-nav__link-text">Data Pokok</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
							        </div>
								</div>
								<div class="kt-portlet__body">
									<!--begin: Datatable -->
									<div class="table-responsive">
										<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-klatur">
								            <thead>
								                <tr>
								                    <th><div align="center">No.</div></th>
								                    <th><div align="center">Jabatan</div></th>
								                    <th><div align="center">Nomenklatur</div></th>
								                    <th><div align="center">Aksi</div></th>
								                </tr>
								            </thead>
								            <tbody></tbody>
								        </table>
									</div>
							        

							        <!--end: Datatable -->
								</div>
		                        @if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    </div>
		                    @elseif($value == 5)
		                    <div class="tab-pane {{$key === 0 ? 'active' : ''}}" role="tabpanel" id="kt_portlet_base_demo_1_{{$value}}_tab_content_{{$value}}">

		                    	@if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    	<br>
		                    	<br>
		                    	<br>

		                        <div class="kt-portlet__head">
									<div class="kt-portlet__head-label">
										<h3 class="kt-portlet__head-title">
											{{$namaperusahaan->nama_lengkap}} ({{$nomor_sk}}) <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">{{$namagrupjabat->nama}}</span>
										</h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="dropdown dropdown-inline">
											<a href="#" class="btn btn-default btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="flaticon-add"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right" style="">
												<ul class="kt-nav">
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link cls-add-alt">
															<i class="kt-nav__link-icon flaticon2-line-chart "></i>
															<span class="kt-nav__link-text">Alih Tugas</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="kt-nav__link ">
															<i class="kt-nav__link-icon flaticon2-pie-chart-1"></i>
															<span class="kt-nav__link-text">Data Pokok</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
							        </div>
								</div>
								<div class="kt-portlet__body">
									<!--begin: Datatable -->
									<div class="table-responsive">
										<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-alt">
								            <thead>
								                <tr>
								                    <th><div align="center">No.</div></th>
								                    <th><div align="center">Nama Pejabat</div></th>
								                    <th><div align="center">Jabatan Alih Tugas</div></th>
								                    <th><div align="center">Tanggal Awal Alih Tugas</div></th>
								                    <th><div align="center">Tanggal Akhir Alih Tugas</div></th>
								                    <th><div align="center">Aksi</div></th>
								                </tr>
								            </thead>
								            <tbody></tbody>
								        </table>
									</div>
							        

							        <!--end: Datatable -->
								</div>
		                        @if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    </div>
		                    @elseif($value == 7)
		                    <div class="tab-pane {{$key === 0 ? 'active' : ''}}" role="tabpanel" id="kt_portlet_base_demo_1_{{$value}}_tab_content_{{$value}}">


		                    	@if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    	<br>
		                    	<br>
		                    	<br>

		                        <div class="kt-portlet__head">
									<div class="kt-portlet__head-label">
										<h3 class="kt-portlet__head-title">
											{{$namaperusahaan->nama_lengkap}} ({{$nomor_sk}}) <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">{{$namagrupjabat->nama}}</span>
										</h3>
									</div>
									<div class="kt-portlet__head-toolbar">
										<div class="dropdown dropdown-inline">
											<a href="#" class="btn btn-default btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="flaticon-add"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right" style="">
												<ul class="kt-nav">
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link cls-add-independen">
															<i class="kt-nav__link-icon flaticon2-line-chart "></i>
															<span class="kt-nav__link-text">Komisaris Independen</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="kt-nav__link ">
															<i class="kt-nav__link-icon flaticon2-pie-chart-1"></i>
															<span class="kt-nav__link-text">Data Pokok</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
							        </div>
								</div>
								<div class="kt-portlet__body">
									<!--begin: Datatable -->
									<div class="table-responsive">
										<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-independen">
								            <thead>
								                <tr>
								                    <th><div align="center">No.</div></th>
								                    <th><div align="center">Nama Pejabat</div></th>
								                    <th><div align="center">Jabatan</div></th>
								                    <th><div align="center">Tanggal Awal Jabatan</div></th>
								                    <th><div align="center">Tanggal Akhir Jabatan</div></th>
								                    <th><div align="center">Aksi</div></th>
								                </tr>
								            </thead>
								            <tbody></tbody>
								        </table>
									</div>
							        

							        <!--end: Datatable -->
								</div>
		                        @if($key == 0)
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @else
		                        <ul class="float-right">
		                            <li class="list-inline-item">
		                                <a type="button" href="/administrasi/bumn/{{$id_surat_keputusan}}/edittambah" class="btn btn-primary">
											<span><<</span> Data Pokok
										</a>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
		                            </li>
		                            <li class="list-inline-item">
		                                <button type="button" class="btn btn-warning next-step">Next <span>>></span></button>
		                            </li>
		                        </ul>
		                        @endif
		                    </div>
		                    @endif
		                    @endforeach
		        <div class="tab-pane" role="tabpanel" id="summary">

		        <ul class="float-right">
                    <li class="list-inline-item">
                        <button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
                    </li>
                    <li class="list-inline-item">
                        <button type="button" class="btn btn-brand save-tambah2">Submit</button>
                    </li>
                    <li class="list-inline-item">
                        <span class="kt-margin-left-10">or <a href="/administrasi/bumn/index" class="kt-link kt-font-bold">Cancel</a></span>
                    </li>


                </ul>
            	<br>
            	<br>
            	<br>
                <div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Summary Input Data {{$namaperusahaan->nama_lengkap}} ({{$nomor_sk}}) <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">{{$namagrupjabat->nama}}</span>
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					@foreach($jenis_sk_id as $jenis_sk)
						@if($jenis_sk == 1)

						<h5>Pengangkatan</h5>
						<div class="table-responsive">
							<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-sum-angkat">
					            <thead>
					                <tr>
					                    <th><div align="center">No.</div></th>
					                    <th><div align="center">Jabatan</div></th>
					                    <th><div align="center">Nama Pejabat</div></th>
					                    <th><div align="center">Periode</div></th>
					                    <th><div align="center">Tanggal Awal Menjabat</div></th>
					                    <th><div align="center">Tanggal Akhir Menjabat</div></th>
					                </tr>
					            </thead>
					            <tbody></tbody>
					        </table>
						</div>
				        
				        <!--end: Datatable -->
				        <br>
				        @elseif($jenis_sk == 2)
				        <!--begin: Datatable -->
					
				        <h5>Pemberhentian</h5>
				        <div class="table-responsive">
				        	<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-sum-berhenti">
					            <thead>
					                <tr>
					                    <th><div align="center">No.</div></th>
					                    <th><div align="center">Jabatan</div></th>
					                    <th><div align="center">Nama Pejabat</div></th>
					                    <th><div align="center">Keterangan</div></th>
					                    <th><div align="center">Tanggal Akhir Menjabat</div></th>
					                </tr>
					            </thead>
					            <tbody></tbody>
					        </table>
				        </div>
				        
				        <!--end: Datatable -->
				        <br>
				        @elseif($jenis_sk == 3)
				        <h5>Pelaksana Tugas</h5>
				        <div class="table-responsive">
				        	<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-sum-plt">
					            <thead>
					                <tr>
					                    <th><div align="center">No.</div></th>
					                    <th><div align="center">Jabatan PLT</div></th>
					                    <th><div align="center">Nama Pejabat</div></th>
					                    <th><div align="center">Tanggal Awal Menjabat</div></th>
					                    <th><div align="center">Tanggal Akhir Menjabat</div></th>
					                </tr>
					            </thead>
					            <tbody></tbody>
					        </table>
				        </div>
				        
				        <!--end: Datatable -->
				        <br>
				        @elseif($jenis_sk == 4)
				        <h5>Perubahan Nomenklatur</h5>
				        <div class="table-responsive">
				        	<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-sum-klatur">
					            <thead>
					                <tr>
					                    <th><div align="center">No.</div></th>
					                    <th><div align="center">Jabatan</div></th>
					                    <th><div align="center">Nomenklatur</div></th>
					                </tr>
					            </thead>
					            <tbody></tbody>
					        </table>
				        </div>
				        
				        <!--end: Datatable -->
				        <br>
				        @elseif($jenis_sk == 5)
				        <h5>Alih Tugas</h5>
				        <div class="table-responsive">
				        	<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-sum-alt">
					            <thead>
					                <tr>
					                    <th><div align="center">No.</div></th>
					                    <th><div align="center">Nama Pejabat</div></th>
					                    <th><div align="center">Jabatan Alih Tugas</div></th>
					                    <th><div align="center">Tanggal Awal Alih Tugas</div></th>
					                    <th><div align="center">Tanggal Akhir Alih Tugas</div></th>
					                </tr>
					            </thead>
					            <tbody></tbody>
					        </table>
				        </div>
				        
				        @elseif($jenis_sk == 7)
				        <h5>Komisaris Independen</h5>
				        <div class="table-responsive">
				        	<table class="table table-striped- table-bordered table-hover table-checkable" id="datatable-sum-independen">
					            <thead>
					                <tr>
					                    <th><div align="center">No.</div></th>
					                    <th><div align="center">Nama Pejabat</div></th>
					                    <th><div align="center">Jabatan</div></th>
					                    <th><div align="center">Tanggal Awal Jabatan</div></th>
					                    <th><div align="center">Tanggal Akhir Jabatan</div></th>
					                </tr>
					            </thead>
					            <tbody></tbody>
					        </table>
				        </div>
				        
				        @endif
					@endforeach
			    </div>
			    <div class="kt-portlet__foot">
					<div class="row align-items-center">
						<div class="col-lg-6 m--valign-middle">
							Pesan Konfirmasi: Tahap Input Sudah Selesai, Apakah anda yakin untuk submit data tersebut?
						</div>
						<div class="col-lg-6 kt-align-right">
							<button type="button" class="btn btn-warning prev-step"><span><<</span> Previous</button>
							<button type="button" class="btn btn-brand save-tambah2">Submit</button>
							<span class="kt-margin-left-10">or <a href="/administrasi/bumn/index" class="kt-link kt-font-bold">Cancel</a></span>
						</div>
					</div>
				</div>
		                    </div>
		                    <div class="clearfix"></div>
		                </div>
		            </form>
		        </div>
		    </div>
		</div>
	</div>
</div>


@endsection

@section('addafterjs')
<script type="text/javascript">
	//pengangkatan
    var urlcreateangkat = "{{route('administrasi.bumn.createangkat')}}";
    var urlcreateangkatlagi = "{{route('administrasi.bumn.createangkatlagi')}}";
    var urleditangkat = "{{route('administrasi.bumn.editangkat')}}";
    var urleditangkatlagi = "{{route('administrasi.bumn.editangkatlagi')}}";
    var urlstoreangkat = "{{route('administrasi.bumn.storeangkat')}}";
    var urlstoreangkatlagi = "{{route('administrasi.bumn.storeangkatlagi')}}";
    var urldeleteangkat = "{{route('administrasi.bumn.deleteangkat')}}";
    var urldatatableangkat = "{{route('administrasi.bumn.datatableangkat')}}";
    var urldatatablesumangkat = "{{route('administrasi.bumn.datatablesumangkat')}}";
    //pemberhentian
    var urlcreatehenti = "{{route('administrasi.bumn.createhenti')}}";
    var urledithenti = "{{route('administrasi.bumn.edithenti')}}";
    var urlstorehenti = "{{route('administrasi.bumn.storehenti')}}";
    var urldeletehenti = "{{route('administrasi.bumn.deletehenti')}}";
    var urldatatablehenti = "{{route('administrasi.bumn.datatablehenti')}}";
    var urldatatablesumhenti = "{{route('administrasi.bumn.datatablesumhenti')}}";
    //nomenklatur
    var urlcreateklatur = "{{route('administrasi.bumn.createklatur')}}";
    var urleditklatur = "{{route('administrasi.bumn.editklatur')}}";
    var urlstoreklatur = "{{route('administrasi.bumn.storeklatur')}}";
    var urldeleteklatur = "{{route('administrasi.bumn.deleteklatur')}}";
    var urldatatableklatur = "{{route('administrasi.bumn.datatableklatur')}}";
    var urldatatablesumklatur = "{{route('administrasi.bumn.datatablesumklatur')}}";
    //plt
    var urlcreateplt = "{{route('administrasi.bumn.createplt')}}";
    var urleditplt = "{{route('administrasi.bumn.editplt')}}";
    var urlstoreplt = "{{route('administrasi.bumn.storeplt')}}";
    var urldeleteplt = "{{route('administrasi.bumn.deleteplt')}}";
    var urldatatableplt = "{{route('administrasi.bumn.datatableplt')}}";
    var urldatatablesumplt = "{{route('administrasi.bumn.datatablesumplt')}}";
    //alt
    var urlcreatealt = "{{route('administrasi.bumn.createalt')}}";
    var urleditalt = "{{route('administrasi.bumn.editalt')}}";
    var urlstorealt = "{{route('administrasi.bumn.storealt')}}";
    var urldeletealt = "{{route('administrasi.bumn.deletealt')}}";
    var urldatatablealt = "{{route('administrasi.bumn.datatablealt')}}";
    var urldatatablesumalt = "{{route('administrasi.bumn.datatablesumalt')}}";
    //independen
    var urlcreateindependen = "{{route('administrasi.bumn.createindependen')}}";
    var urleditindependen = "{{route('administrasi.bumn.editindependen')}}";
    var urlstoreindependen = "{{route('administrasi.bumn.storeindependen')}}";
    var urldeleteindependen = "{{route('administrasi.bumn.deleteindependen')}}";
    var urldatatableindependen = "{{route('administrasi.bumn.datatableindependen')}}";
    var urldatatablesumindependen = "{{route('administrasi.bumn.datatablesumindependen')}}";

    var urlsavetambah2 = "{{route('administrasi.bumn.savetambah2')}}";
</script>

<script type="text/javascript" src="{{asset('js/administrasi/bumn/tambah3.js')}}"></script>
<script src="{{asset('assets/vendors/custom/datatables/datatables.bundle.js')}}" type="text/javascript"></script>
<script type="text/javascript" src="{{asset('js/administrasi/bumn/henti.js')}}"></script>
<script type="text/javascript" src="{{asset('js/administrasi/bumn/klatur.js')}}"></script>
<script type="text/javascript" src="{{asset('js/administrasi/bumn/plt.js')}}"></script>
<script type="text/javascript" src="{{asset('js/administrasi/bumn/alt.js')}}"></script>
<script type="text/javascript" src="{{asset('js/administrasi/bumn/independen.js')}}"></script>
@endsection