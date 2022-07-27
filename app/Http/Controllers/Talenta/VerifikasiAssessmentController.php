<?php
namespace App\Http\Controllers\Talenta;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\KelasBumn;
use App\ClusterBumn;
use App\Keahlian;
use App\Talenta;
use App\TransactionTalentaKeahlian;
use App\TransactionTalentaCluster;
use App\TransactionTalentaKelas;
use App\TransactionTalentaSocialMedia;
use App\Perusahaan;
use App\SocialMedia;
use DB;
use Carbon\Carbon;
use App\User;
use Illuminate\Validation\Rule;

class VerifikasiAssessmentController extends Controller
{
    protected $__route;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
         $this->__route = 'talenta.verifikasi_assessment';
         $this->__title = "Verifikasi Assessment";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
      activity()->log('Menu Talenta Verifikasi Assessment');
      $data['jumlah_nominated'] = VerifikasiKbumnController::query_talenta(5)->get()->count();
      $data['jumlah_eligible1'] = VerifikasiKbumnController::query_talenta(6)->get()->count();
      $data['jumlah_eligible2'] = VerifikasiKbumnController::query_talenta(7)->get()->count();
      $data['jumlah_qualified'] = VerifikasiKbumnController::query_talenta(8)->get()->count();

      return view($this->__route.'.index',[
          'pagetitle' => $this->__title,
          'route' => $this->__route,
            'talenta' => Talenta::orderBy('id', 'asc')->get(),
            'perusahaan' => Perusahaan::orderBy('id', 'asc')->get(),
            'data' => $data,
            'breadcrumb' => [
              [
                  'url' => '/',
                  'menu' => 'Homes'
              ],
              [
                  'url' => '/',
                  'menu' => 'Talent Management'
              ],
              [
                  'url' => route('talenta.verifikasi_assessment.index'),
                  'menu' => 'Verifikasi Assessment'
              ]
          ]
      ]);

    }


    public function datatable(Request $request)
    {
        $id_users = \Auth::user()->id;
        $id_users_bumn = \Auth::user()->id_bumn;
        $users = User::where('id', $id_users)->first();

        $talenta = DB::table('talenta')
                      ->where('talenta.id_status_talenta', 7) // status eligble 2
                      ->leftJoin('status_talenta', 'status_talenta.id', '=', 'talenta.id_status_talenta')
                      ->leftJoin('lembaga_assessment', 'lembaga_assessment.id', '=', 'talenta.id_lembaga_assessment')
                      ->leftJoin(DB::raw("lateral (SELECT id_talenta, hasil, full_report, short_report, tanggal_expired FROM assessment_nilai where assessment_nilai.id_talenta = talenta.id order by assessment_nilai.tanggal_expired desc limit 1) assessment_nilai"), 'assessment_nilai.id_talenta', '=', 'talenta.id')
                      ->leftJoin(DB::raw("lateral (select s.nomenklatur_jabatan, p.nama_lengkap, p.id, skp.nomenklatur_baru, s.id_perusahaan, sk.id_grup_jabatan
                                      from view_organ_perusahaan v
                                      left join struktur_organ s on v.id_struktur_organ = s.id
                                      LEFT JOIN organ_perusahaan op ON op.id_struktur_organ = s.id
                                      LEFT JOIN surat_keputusan sk ON sk.id = op.id_surat_keputusan
                                      left join perusahaan p on p.id = s.id_perusahaan
                                      LEFT JOIN sk_perubahan_nomenklatur skp ON skp.id_struktur_organ = s.ID
                                      where v.id_talenta = talenta.id 
                                      and v.aktif = 't'
                                      and sk.id_grup_jabatan = 1
                                      and (v.tanggal_akhir >= now( ) 
                                        or v.tanggal_akhir is null)
                                      order by v.id_struktur_organ ASC, s.urut ASC 
                                      limit 1) jabatan"), 'talenta.id', '=', 'talenta.id')
                      ->leftJoin('perusahaan', 'perusahaan.id', '=', 'jabatan.id_perusahaan')
                      ->select(DB::raw("talenta.id,
                                    talenta.nama_lengkap, 
                                    talenta.nik, 
                                    talenta.persentase, 
                                    talenta.id_status_talenta, 
                                    jabatan.nama_lengkap as nama_perusahaan, 
                                    case when jabatan.nomenklatur_baru is NULL then 
                                    jabatan.nomenklatur_jabatan else jabatan.nomenklatur_baru END as jabatan,
                                    status_talenta.nama as status_talenta,
                                    lembaga_assessment.nama as lembaga_assessment,
                                    assessment_nilai.hasil,
                                    assessment_nilai.short_report,
                                    assessment_nilai.full_report,
                                    talenta.id_jenis_asal_instansi"))
                      ->GroupBy('talenta.id', 'jabatan.nomenklatur_jabatan', 'jabatan.nomenklatur_baru', 'jabatan.nama_lengkap', 'status_talenta.nama', 'lembaga_assessment.nama', 'assessment_nilai.hasil', 'assessment_nilai.short_report', 'assessment_nilai.full_report')
                      ->orderBy(DB::raw("case when talenta.id_status_talenta = 4 then 1 else 2 end"))
                      ->orderBy('talenta.nama_lengkap', 'ASC');

       if($users->kategori_user_id != 1){
        $talenta = $talenta->whereRaw("((( (perusahaan.id in (
                                    WITH RECURSIVE anak AS (
                                    SELECT
                                      perusahaan_id,
                                      perusahaan_induk_id,
                                      tmt_awal,
                                      tmt_akhir 
                                    FROM
                                      perusahaan_relasi 
                                    WHERE
                                      perusahaan_induk_id = ".$id_users_bumn." 
                                      AND (
                                        tmt_awal <= NOW( ) :: DATE 
                                        AND (
                                        CASE
                                            
                                            WHEN tmt_akhir IS NOT NULL THEN
                                            tmt_akhir >= NOW( ) :: DATE ELSE NOW( ) :: DATE = NOW( ) :: DATE 
                                          END 
                                          ) 
                                        ) UNION
                                      SELECT
                                        pr.perusahaan_id,
                                        pr.perusahaan_induk_id,
                                        pr.tmt_awal,
                                        pr.tmt_akhir 
                                      FROM
                                        perusahaan_relasi pr
                                        INNER JOIN anak A ON A.perusahaan_id = pr.perusahaan_induk_id 
                                      ) SELECT
                                      P.ID
                                    FROM
                                      anak ak
                                      LEFT JOIN perusahaan P ON ak.perusahaan_id = P.ID 
                                    WHERE
                                      (
                                        ak.tmt_awal <= NOW( ) :: DATE 
                                        AND (
                                        CASE
                                            
                                            WHEN ak.tmt_akhir IS NOT NULL THEN
                                            ak.tmt_akhir >= NOW( ) :: DATE ELSE NOW( ) :: DATE = NOW( ) :: DATE 
                                          END 
                                          ) 
                                        ) 
                                      GROUP BY
                                        P.urut,
                                        P.ID,
                                        P.nama_lengkap,
                                        ak.perusahaan_induk_id,
                                        ak.perusahaan_id,
                                        ak.tmt_awal,
                                        ak.tmt_akhir 
                                      ORDER BY
                                      P.urut ASC,
                                    P.ID ASC 
                                    )
                                  ) OR perusahaan.id = ".$id_users_bumn.")
                                  AND talenta.id_perusahaan IS NULL)
                                  OR 
                                  talenta.id_perusahaan = ".$id_users_bumn.") ");
        }

        if($request->nama_lengkap){
          $talenta->where("talenta.nama_lengkap",'ilike', '%'.$request->nama_lengkap.'%');
        }

        if($request->instansi){
          $instansi = Perusahaan::find($request->instansi);
          $talenta->where("jabatan.nama_lengkap", $instansi->nama_lengkap);
        }

        try{
            return datatables()->of($talenta)
            ->editColumn('nama_lengkap', function ($row){
                if($row->nik != '')  {
                  $return = '<a href="javascript:;" class="cls-minicv" data-id="'.(int)$row->id.'" data-toggle="tooltip" data-original-title="CV">
                              <b>'.$row->nama_lengkap."</b></br><span>".$row->nik.'
                          </span></a>';
                }else{
                  $return = '<a href="javascript:;" class="cls-minicv" data-id="'.(int)$row->id.'" data-toggle="tooltip" data-original-title="CV">
                                          <b>'.$row->nama_lengkap."</b></br><span style='color:red'><i>NIK belum ada</i>
                                      </span></a>";
                }
                
                return $return;
            })
            ->editColumn('jabatan', function ($row){
                if($row->jabatan){
                  return "<b>".$row->jabatan."</b></br><span>".$row->nama_perusahaan."</span>";
                }else{
                  return "Tidak Sedang Menjabat";
                }
            })
            ->editColumn('hasil', function ($row){
                $return = '';
                if($row->hasil=='Qualified'){
                    $return = '<span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row->hasil.'</span>';
                }else if($row->hasil=='Not Qualified'){
                    $return = '<span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row->hasil.'</span>';
                }
                if($row->short_report!=''){
                  $return .= '<a class="tooltips" title="Download Short Report" href="'.\URL::to('uploads/talenta/assessment/'.$row->short_report).'" download="'.$row->short_report.'" ><span class="btn-icon-only btn btn-sm yellow-gold sbold"><i class="flaticon2-document"></i>Short Report</a><br/>';
                }
                if($row->full_report!=''){
                  $return .= '<a class="tooltips" title="Download Full Report" href="'.\URL::to('uploads/talenta/assessment/'.$row->full_report).'" download="'.$row->full_report.'" ><span class="btn-icon-only btn btn-sm yellow-gold sbold"><i class="flaticon2-document"></i>Full Report</a><br/>';
                }
                return $return;
            })
            ->editColumn('lembaga_assessment', function ($row){
                return $row->lembaga_assessment;
            })
            ->editColumn('log_status', function ($row){
                $color = '';
                if($row->id_status_talenta==1){
                  $color = '#c5ff6f';
                }else if($row->id_status_talenta==2){
                  $color = '#b8e0f4';
                }else if($row->id_status_talenta==3){
                  $color = '#d4d4d4';
                }else if($row->id_status_talenta==4){
                  $color = '#efb3f5';
                }else if($row->id_status_talenta==5){
                  $color = '#b5f0ed';
                }else if($row->id_status_talenta==6){
                  $color = '#a7a0ff';
                }else if($row->id_status_talenta==7){
                  $color = '#a7a0ff';
                }else if($row->id_status_talenta==8){
                  $color = '#6ebbff';
                }
                $return = '<a href="javascript:;" style="color:#4f4f4f;background-color:'.$color.';" class="cls-logstatus kt-badge kt-badge--inline kt-badge--pill kt-badge--rounded" data-id="'.(int)$row->id.'" data-toggle="tooltip" data-original-title="Log Status">'.$row->status_talenta.'</a>';
                return $return;
            })
            ->addColumn('approve', function ($row){
                $id = (int)$row->id;
                
                $button = '<label class="mt-checkbox mt-checkbox-outline">
                <input type="checkbox" data-id="'.(int)$row->id.'" data-nama_lengkap="'.$row->nama_lengkap.'" value="'.$id.'" form="form_group" class="checked_item"/>
                <span></span>
                </label>';
                
                if(empty($row->hasil)){
                  $button = '<label class="mt-checkbox mt-checkbox-outline">
                  <input type="checkbox" value="'.$id.'" form="form_group" disabled/>
                  <span></span>
                  </label>';
                }

                return $button;
            })
            ->addColumn('reject', function ($row){
                $id = (int)$row->id;
                
                $button = '<label class="mt-checkbox mt-checkbox-outline">
                <input class="reject_item" type="checkbox" data-id="'.(int)$row->id.'" data-nama_lengkap="'.$row->nama_lengkap.'" value="'.$id.'" form="form_group"/>
                <span></span>
                </label>';
                
                if(empty($row->hasil)){
                  $button = '<label class="mt-checkbox mt-checkbox-outline">
                  <input type="checkbox" value="'.$id.'" form="form_group" disabled/>
                  <span></span>
                  </label>';
                }

                return $button;
            })
            ->rawColumns(['approve','reject','hasil','nama_lengkap','jabatan', 'log_status'])
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
    
    public function update_talenta(Request $request)
    {
        DB::beginTransaction();
        try{
            if(!empty($request->input('checked_list'))){
              $checked_list = explode(",", $request->input('checked_list'));
              $param['id_status_talenta'] = 8; //status qualified
              $status = Talenta::whereIn('id', $checked_list)->update($param);

              if($status){
                foreach ($checked_list as $key => $value) {
                  RegisterController::store_log($value, $param['id_status_talenta']);
                }
              }
            }
            
            if(!empty($request->input('reject_list'))){
              $reject_list = explode(",", $request->input('reject_list'));
              $param['id_status_talenta'] = 3; //status non talent
              $status = Talenta::whereIn('id', $reject_list)->update($param);

              if($status){
                foreach ($reject_list as $key => $value) {
                  RegisterController::store_log($value, $param['id_status_talenta']);
                }
              }
            }
            DB::commit();

            $data['jumlah_nominated'] = VerifikasiKbumnController::query_talenta(5)->get()->count();
            $data['jumlah_eligible1'] = VerifikasiKbumnController::query_talenta(6)->get()->count();
            $data['jumlah_eligible2'] = VerifikasiKbumnController::query_talenta(7)->get()->count();
            $data['jumlah_qualified'] = VerifikasiKbumnController::query_talenta(8)->get()->count();
            $result = [
                'flag'  => 'success',
                'msg' => 'Sukses submit talenta',
                'title' => 'Sukses',
                'data' => $data,
            ];
        }catch(\Exception $e){
            DB::rollback();
            $result = [
                'flag'  => 'warning',
                'msg' => 'Gagal submit talenta',
                'title' => 'Gagal'
            ];
        }
        return response()->json($result);
    }

}
