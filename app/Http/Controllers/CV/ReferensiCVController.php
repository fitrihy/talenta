<?php

namespace App\Http\Controllers\CV;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\CVHelper;
use App\ReferensiCV;
use App\Talenta;
use App\JenisAsalInstansi;
use App\TidakMemilikiReferensi;
use Carbon\Carbon;
use DB;
use Illuminate\Validation\Rule;

class ReferensiCVController extends Controller
{
    protected $__route;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    function __construct()
    {
         $this->__route = 'cv.referensi_cv';;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request, $id)
    {
      activity()->log('Menu CV Referensi');
      $talenta = Talenta::find($id);
      $eksternal = JenisAsalInstansi::where('nama', 'Eksternal')->first();
      $tidak_memiliki = TidakMemilikiReferensi::where('id_talenta', (int)$id)->first();
      return view($this->__route.'.index',[
          'pagetitle' => 'Curicullum Vitae - '.$talenta->nama_lengkap,
          'talenta' => $talenta,
          'eksternal' => $eksternal,
          'tidak_memiliki' => $tidak_memiliki,
          'active' => "referensi_cv",
          'breadcrumb' => [
              [
                  'url' => '/',
                  'menu' => 'Homes'
              ],
              [
                  'url' => route('cv.board.index'),
                  'menu' => 'CV'
              ] ,
              [
                  'url' => route('cv.referensi_cv.index', ['id_talenta' => $id]),
                  'menu' => 'Referensi CV'
              ]               
          ]
      ]);

    }

    public function datatable(Request $request, $id)
    {
        $data = ReferensiCV::select('*')->where('id_talenta', $id);

        try{
            return datatables()->of($data)
            ->addColumn('action', function ($row){
                $id = (int)$row->id;
                $button = '<div align="center">';

                $button .= '<button type="button" class="btn btn-icon btn-sm  btn-light-primary mr-2 cls-button-edit first_table-edit" data-id="'.$id.'" data-toggle="tooltip" data-original-title="Ubah data Referensi"><i class="flaticon-edit"></i></button>';

                $button .= '&nbsp;';

                $button .= '<button type="button" class="btn  btn-light-danger mr-2 btn-icon cls-button-delete btn-sm first_table-delete" data-id="'.$id.'" data-periode="'.$row->nama.'" data-toggle="tooltip" data-original-title="Hapus data Referensi"><i class="flaticon-delete"></i></button>'; 

                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['action'])
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

    public function create(Request $request, $id)
    {
      try{
          return view($this->__route.'.form',[
                  'actionform' => 'insert',
                  'id_talenta' => $id
          ]);
      }catch(Exception $e){}
    }

    public function edit(Request $request, $id)
    {
        try{
          return view($this->__route.'.form',[
                  'actionform' => 'update',
                  'id_talenta' => $id,
                  'data' => ReferensiCV::find((int)$request->input('id')),
          ]);
      }catch(Exception $e){}

    }

    public function store(Request $request, $id)
    {
      $result = [
          'flag' => 'error',
          'msg' => 'Error System',
          'title' => 'Error'
      ];      

      $validator = $this->validateform($request);   

      if (!$validator->fails()) {          
          $param = $request->except(['id', '_token', '_method', 'actionform']);
          $param['id_talenta'] = $id;


          switch ($request->input('actionform')) {
              case 'insert': DB::beginTransaction();
                             try{
                                $data = ReferensiCV::create($param);
                                $tidakmemiliki = TidakMemilikiReferensi::where('id_talenta', (int)$id)->first();
                                if(@$tidakmemiliki->id_talenta){
                                    $tidakmemiliki->delete();
                                }
                                $param_talenta['fill_referensi_cv'] = CVHelper::referensiCVFillCheck($id);
                                $param_talenta['persentase'] = CVHelper::fillPercentage($id);
                                $status = Talenta::find($id)->update($param_talenta);

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
                                $data = ReferensiCV::find((int)$request->input('id'));
                                $data->update($param);

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
                                  'msg' => $e->getMessage(),
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

    protected function validateform($request)
    {
        $required['nama'] = 'required';
        $required['perusahaan'] = 'required';
        $required['jabatan'] = 'required';
        $required['nomor_handphone'] = 'required';

        $message['nama.required'] = 'Nama wajib Diisi';
        $message['perusahaan.required'] = 'Perusahaan wajib Diisi';
        $message['jabatan.required'] = 'Jabatan wajib Diisi';
        $message['nomor_handphone.required'] = 'Nomor Handphone wajib Diisi';

        return Validator::make($request->all(), $required, $message);       
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = ReferensiCV::find((int)$request->input('id'));
            $id = $data->id_talenta;
            $data->delete();
            $param_talenta['fill_referensi_cv'] = CVHelper::referensiCVFillCheck($id);
            $param_talenta['persentase'] = CVHelper::fillPercentage($id);
            $status = Talenta::find($id)->update($param_talenta);

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

    public function tidak_memiliki(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $data = TidakMemilikiReferensi::where('id_talenta', $id)->first();
            if($data){
                $data->delete();
            }else{
                $param['id_talenta'] = $id;
                TidakMemilikiReferensi::create($param);
            }
            $param_talenta['fill_referensi'] = CVHelper::referensiCVFillCheck($id);
            $param_talenta['persentase'] = CVHelper::fillPercentage($id);
            $status = Talenta::find($id)->update($param_talenta);

            DB::commit();
            $result = [
                'flag'  => 'success',
                'msg' => 'Sukses Ubah data',
                'title' => 'Sukses'
            ];
        }catch(\Exception $e){
            DB::rollback();
            $result = [
                'flag'  => 'warning',
                'msg' => 'Gagal Ubah data',
                'title' => 'Gagal'
            ];
        }
        return response()->json($result);       
    }
}