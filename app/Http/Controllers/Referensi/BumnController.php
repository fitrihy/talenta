<?php

namespace App\Http\Controllers\Referensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Perusahaan;
use App\JenisPerusahaan;
use App\KelasBumn;
use Carbon\Carbon;
use DB;

class BumnController extends Controller
{
    protected $__route;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    function __construct()
    {
         ini_set( 'max_execution_time', 0);
         $this->__route = 'referensi.bumn';
         // $this->middleware('permission:bumn-list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)

    {
        activity()->log('Menu Referensi BUMN');
        $bumns = Perusahaan::orderBy('id');

        if($request->perusahaan)
          $bumns = $bumns->where('nama_lengkap', 'ilike', '%' . $request->perusahaan . '%');

        if($request->id_jenis_perusahaan)
          $bumns = $bumns->where('id_jenis_perusahaan', $request->id_jenis_perusahaan);

        $bumns = $bumns->get();
        $jenis_perusahaans = JenisPerusahaan::all();
        return view($this->__route.'.index',[
            'bumns' => $bumns,
            'jenis_perusahaans' => $jenis_perusahaans,
            'src_perusahaan' => $request->perusahaan,
            'src_jenis_perusahaan' => $request->id_jenis_perusahaan,
            'pagetitle' => 'Referensi BUMN, Anak, dan Cucu',
            'breadcrumb' => [
                [
                    'url' => '/',
                    'menu' => 'Homes'
                ],
                [
                    'url' => route('referensi.bumn.index'),
                    'menu' => 'BUMN'
                ]               
            ]
        ]);

    }

    public function datatable(Request $request)
    {
        try{
            return datatables()->of(Perusahaan::query())
            ->addColumn('action', function ($row){
                $id = (int)$row->id;
                $button = '<div align="center">';

                $button .= '<button type="button" class="btn btn-outline-brand btn-icon cls-button-edit" data-id="'.$id.'" data-toggle="tooltip" data-original-title="Ubah data BUMN '.$row->nama.'"><i class="flaticon-edit"></i></button>';

                $button .= '&nbsp;';

                $button .= '<button type="button" class="btn btn-outline-danger btn-icon cls-button-delete" data-id="'.$id.'" data-nama="'.$row->nama.'" data-toggle="tooltip" data-original-title="Hapus data BUMN '.$row->nama.'"><i class="flaticon-delete"></i></button>'; 

                $button .= '</div>';
                return $button;
            })
            ->editColumn('created_at', function($data) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('jS F Y');
            })
            ->rawColumns(['nama_lengkap','id_huruf', 'jenis_perusahaan','kepemilikan', 'kelas', 'action'])
            ->toJson();
        }catch(Exception $e){
            return response([
                'draw'            => 0,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => []
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $bumn = Perusahaan::get();
       
        return view($this->__route.'.form',[
            'actionform' => 'insert',
            'bumn' => $bumn
        ]);

    }

    public function store(Request $request)
    {
        $result = [
            'flag' => 'error',
            'msg' => 'Error System',
            'title' => 'Error'
        ];      

        $validator = $this->validateform($request);   

        if (!$validator->fails()) {
            $param['id_jenis_perusahaan'] = $request->input('id_jenis_perusahaan');
            $param['kelas'] = $request->input('kelas');

            switch ($request->input('actionform')) {
                case 'insert': DB::beginTransaction();
                               try{
                                  $bumn = Perusahaan::create((array)$param);

                                  DB::commit();
                                  $result = [
                                    'flag'  => 'success',
                                    'msg' => 'Sukses tambah data',
                                    'title' => 'Sukses'
                                  ];
                               }catch(\Exception $e){
                                  DB::rollback();
                                  $result = [
                                    'flag'  => 'warning',
                                    'msg' => $e->getMessage(),
                                    'title' => 'Gagal'
                                  ];
                               }

                break;
                
                case 'update': DB::beginTransaction();
                               try{
                                  $bumn = Perusahaan::find((int)$request->input('id'));
                                  $bumn->update((array)$param);

                                  DB::commit();
                                  $result = [
                                    'flag'  => 'success',
                                    'msg' => 'Sukses ubah data',
                                    'title' => 'Sukses'
                                  ];
                               }catch(\Exception $e){
                                  DB::rollback();
                                  $result = [
                                    'flag'  => 'warning',
                                    'msg' => 'Gagal ubah data',
                                    'title' => 'Gagal'
                                  ];
                               }

                break;
            }
        }else{
            $messages = $validator->errors()->all('<li>:message</li>');
            $result = [
                'flag'  => 'warning',
                'msg' => '<ul>'.implode('', $messages).'</ul>',
                'title' => 'Gagal proses data'
            ];                      
        }

        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request)
    {

        try{
            $jenis_perusahaans = JenisPerusahaan::all();
            $kelas_perusahaans = KelasBumn::all();
            $bumn = Perusahaan::find((int)$request->input('id'));

                return view($this->__route.'.form',[
                    'actionform' => 'update',
                    'bumn' => $bumn,
                    'jenis_perusahaans' => $jenis_perusahaans,
                    'kelas_perusahaans' => $kelas_perusahaans

                ]);
        }catch(Exception $e){}

    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = Perusahaan::find((int)$request->input('id'));
            $data->delete();

            DB::commit();
            $result = [
                'flag'  => 'success',
                'msg' => 'Sukses hapus data',
                'title' => 'Sukses'
            ];
        }catch(\Exception $e){
            DB::rollback();
            $result = [
                'flag'  => 'warning',
                'msg' => 'Gagal hapus data',
                'title' => 'Gagal'
            ];
        }
        return response()->json($result);       
    }

    protected function validateform($request)
    {
        $required['id_jenis_perusahaan'] = 'required';

        $message['id_jenis_perusahaan.required'] = 'Kategori Perusahaan wajib diinput';

        return Validator::make($request->all(), $required, $message);       
    }
}