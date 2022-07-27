<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Perusahaan;
use App\JenisSk;
use App\KategoriMobility;
use App\MobilityJabatan;
use App\KategoriPemberhentian;
use App\AlasanPemberhentian;
use App\GrupJabatan;
use App\SuratKeputusan;
use App\StrukturOrgan;
use App\PeriodeJabatan;
use App\Periode;
use App\Rekomendasi;
use App\Talenta;
use App\JenisMobilityJabatan;
use App\RincianSK;
use App\JenisJabatan;
use App\OrganPerusahaan;
use App\SKPengangkatan;
use App\SKPemberhentian;
use App\SKNomenklatur;
use App\SKPenetapanplt;
use App\SKAlihtugas;
use App\SKKomIndependen;
use App\User;
use App\Instansi;
use App\AsalInstansi;
use App\JenisAsalInstansi;
use App\AsalInstansiBaru;
use App\JenisAsalInstansiBaru;
use Carbon\Carbon;
use App\Agama;
use App\Suku;
use DB;
use Config;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use App\Imports\RowImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Exports\ExcelSheet;
use App\Exports\MonitoringPejabat;

class MonitoringPejabatController extends Controller
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
         $this->__route = 'administrasi.monitoring.pejabat';
         $this->suratkeputusanfile = Config::get('folder.suratkeputusanfile');
         $this->suratkeputusanfile_url = Config::get('folder.suratkeputusanfile_url');
         //$this->middleware('permission:admonitoringpejabat-list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
      activity()->log('Menu Administrasi SK Monitoring Pejabat');

      $id_users = \Auth::user()->id;
      $id_users_bumn = \Auth::user()->id_bumn;
      $users = User::where('id', $id_users)->first();

      $cekbumns = Perusahaan::where('induk', $id_users_bumn)->get();
      $countbumns = $cekbumns->count();

      
        if ($users->kategori_user_id == 2) {
            if($countbumns > 0){

              $anakperush = DB::select( DB::raw("WITH RECURSIVE anak AS (
                                            SELECT
                                              perusahaan_id,
                                              perusahaan_induk_id,
                                              tmt_awal,
                                              tmt_akhir 
                                            FROM
                                              perusahaan_relasi 
                                            WHERE
                                              perusahaan_induk_id = ".$id_users_bumn." UNION
                                              SELECT
                                                pr.perusahaan_id,
                                                pr.perusahaan_induk_id,
                                                pr.tmt_awal,
                                                pr.tmt_akhir 
                                              FROM
                                                perusahaan_relasi pr
                                                INNER JOIN anak A ON A.perusahaan_id = pr.perusahaan_induk_id 
                                              ) SELECT DISTINCT
                                              ( P.urut ),
                                              P.ID,
                                              ak.perusahaan_id,
                                              ak.perusahaan_induk_id,
                                              P.nama_lengkap,
                                              ak.tmt_awal,
                                              ak.tmt_akhir 
                                            FROM
                                              anak ak
                                              LEFT JOIN perusahaan P ON ak.perusahaan_id = P.ID
                                              GROUP BY
                                                P.ID,
                                                P.nama_lengkap,
                                                ak.perusahaan_induk_id,
                                                ak.perusahaan_id,
                                                ak.tmt_awal,
                                                ak.tmt_akhir 
                                              ORDER BY
                                              P.urut ASC,
                                            P.ID ASC;"));
              
              $anaks = $anakperush;
            } else {
              $anaks = Perusahaan::where('id', $users->id_bumn)->get();
            }
            
        } else {
            $induks = Perusahaan::orderBy('id', 'asc')->get();
            $anaks = Perusahaan::orderBy('id', 'asc')->get();
        }

        $jenisasalinstansis = JenisAsalInstansi::whereIn('id',[2,3,4,7,8,9,11,12,13,15,16])->get();
        $asalinstansis = AsalInstansi::get();

      

        return view($this->__route.'.index',[
            'pagetitle' => 'Administrasi SK Monitoring',
            'grupjabats' => GrupJabatan::orderBy('id', 'asc')->get(),
            'pejabats' => Talenta::orderBy('id', 'asc')->get(),
            'jabatans' => JenisJabatan::orderBy('id', 'asc')->get(),
            'periodes' => Periode::orderBy('id', 'asc')->get(),
            'jenisasalinstansis' => $jenisasalinstansis,
            'asalinstansis' => $asalinstansis,
            'bumns' => $anaks,
            'agama' => Agama::orderBy('id', 'asc')->get(),
            'sukus' => Suku::get(),
            'users' => $users,
            'breadcrumb' => [
                [
                    'url' => '/',
                    'menu' => 'Home'
                ],
                [
                    'url' => '/',
                    'menu' => 'Administrasi SK'
                ],
                [
                    'url' => route('administrasi.monitoring.pejabat.index'),
                    'menu' => 'Monitoring Pejabat'
                ]               
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        try{

            $id_users = \Auth::user()->id;
            $id_users_bumn = \Auth::user()->id_bumn;
            $users = User::where('id', $id_users)->first();

            $cekbumns = Perusahaan::where('induk', $id_users_bumn)->get();
            $countbumns = $cekbumns->count();

            $where = " ";

            if($request->id_bumn){
               $where .= " and perusahaan.id = ".$request->id_bumn." ";
            } else {
               if ($users->kategori_user_id == 2) {
                   if($countbumns > 0){
                    $where .= " and perusahaan.induk = ".$users->id_bumn." ";
                   } else {
                    $where .= " and perusahaan.id = ".$users->id_bumn." ";
                   }
                   
               } else {
                 $where .= " "; 
               }
            }

            if($request->id_grup_jabat){
               $where .= " and surat_keputusan.id_grup_jabatan = ".$request->id_grup_jabat." ";
            } else {
               $where .= " ";
            }

            if($request->id_jabatan){
               $where .= " and jenis_jabatan.id = ".$request->id_jabatan." ";
            } else {
               $where .= " ";
            }

            if($request->id_periode){
               $where .= " and view_organ_perusahaan.id_periode_jabatan = ".$request->id_periode." ";
            } else {
               $where .= " ";
            }

            if($request->nomor_sk){
               $where .= " and lower(surat_keputusan.nomor) like lower('%".$request->nomor_sk."%') ";
            } else {
               $where .= " ";
            }

            if($request->pejabataktif){
               if ($request->pejabataktif == 'AKTIF') {
                 $aktif = 't';
               } else {
                 $aktif = 'f';
               }
               $where .= " and view_organ_perusahaan.aktif = '$aktif' ";
            } else {
               $where .= " ";
            }

            if($request->pejabat){
               $where .= " and lower(talenta.nama_lengkap) like lower('%".$request->pejabat."%') ";
            } else {
               $where .= " ";
            }

            if($request->id_asal_instansi){
               $where .= " and talenta.id_asal_instansi = ".$request->id_asal_instansi." ";
            } else {
               $where .= " ";
            }

            if($request->tgl_sk){
               $where .= " and surat_keputusan.tanggal_sk =  '".\Carbon\Carbon::createFromFormat('d/m/Y', $request->tgl_sk)->format('Y-m-d')."' ";
            } else {
               $where .= " ";
            }

            if($request->jenis_kelamin){
               $where .= " and talenta.jenis_kelamin = '".$request->jenis_kelamin."' ";
            } else {
               $where .= " ";
            }

            if($request->id_agama){
               $where .= " and talenta.id_agama = ".$request->id_agama." ";
            } else {
               $where .= " ";
            }

            if($request->masa_jabatan){
               if($request->masa_jabatan == 'kurang3'){
                 $where .= " AND ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE - INTERVAL '3 months ago' ) ";
               } elseif ($request->masa_jabatan == 'kurang6') {
                   $where .= " AND ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE - INTERVAL '6 months ago' ) ";
               } elseif ($request->masa_jabatan == 'expire') {
                    $where .= " AND ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE ) ";
               }
            } else {
               $where .= " ";
            }

            if($request->kewarganegaraan){
               $where .= " and talenta.kewarganegaraan = '".$request->kewarganegaraan."' ";
            } else {
               $where .= " ";
            }

            if($request->id_suku){
               $where .= " and talenta.id_suku = '".$request->id_suku."' ";
            } else {
               $where .= " ";
            }

            if($users->kategori_user_id == 1){
              $id_sql = "SELECT
                            perusahaan.ID,
                            talenta.ID as talenta_id,
                            surat_keputusan.ID as sk_id,
                          CASE
                              
                              WHEN ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE ) THEN
                            TRUE ELSE FALSE 
                            END AS expire,
                          CASE
                              
                              WHEN ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE - INTERVAL '3 months ago' ) THEN
                            TRUE ELSE FALSE 
                            END AS kurang3,
                          CASE
                              
                              WHEN ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE - INTERVAL '6 months ago' ) THEN
                            TRUE ELSE FALSE 
                            END AS kurang6,
                          CASE
                              
                              WHEN view_organ_perusahaan.aktif = 't' THEN
                              talenta.nama_lengkap ELSE talenta.nama_lengkap 
                            END AS pejabat,
                          CASE
                              
                              WHEN view_organ_perusahaan.aktif = 't' THEN
                              'AKTIF' ELSE'TIDAK AKTIF' 
                            END AS aktifpejabat,
                            perusahaan.nama_lengkap AS bumns,
                            grup_jabatan.ID AS grup_jabat_id,
                            grup_jabatan.nama AS grup_jabat_nama,
                          CASE
                              
                              WHEN sk_perubahan_nomenklatur.nomenklatur_baru IS NULL THEN
                              struktur_organ.nomenklatur_jabatan ELSE sk_perubahan_nomenklatur.nomenklatur_baru 
                            END AS nama_jabatan,
                            surat_keputusan.nomor,
                            surat_keputusan.tanggal_sk,
                            surat_keputusan.file_name,
                            view_organ_perusahaan.tanggal_awal,
                            view_organ_perusahaan.tanggal_akhir,
                            view_organ_perusahaan.plt,
                            view_organ_perusahaan.komisaris_independen,
                            instansi_baru.nama AS instansi,
                            jenis_asal_instansi.nama AS asal_instansi,
                            view_organ_perusahaan.id_periode_jabatan AS periode,
                            struktur_organ.ID AS struktur_id 
                          FROM
                            view_organ_perusahaan
                            LEFT JOIN talenta ON talenta.ID = view_organ_perusahaan.id_talenta
                            LEFT JOIN struktur_organ ON struktur_organ.ID = view_organ_perusahaan.id_struktur_organ
                            LEFT JOIN perusahaan ON perusahaan.ID = struktur_organ.id_perusahaan
                            LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                            LEFT JOIN surat_keputusan ON surat_keputusan.ID = view_organ_perusahaan.id_surat_keputusan
                            LEFT JOIN instansi_baru ON instansi_baru.ID = talenta.id_asal_instansi
                            LEFT JOIN jenis_asal_instansi ON jenis_asal_instansi.ID = instansi_baru.id_jenis_asal_instansi
                            LEFT JOIN grup_jabatan ON grup_jabatan.ID = jenis_jabatan.id_grup_jabatan
                            LEFT JOIN sk_perubahan_nomenklatur ON sk_perubahan_nomenklatur.id_struktur_organ = struktur_organ.
                            ID LEFT JOIN sk_kom_independen ON sk_kom_independen.id_struktur_organ = struktur_organ.ID 
                          WHERE
                            struktur_organ.aktif = 't' $where
                          ORDER BY
                            perusahaan.ID ASC,
                            grup_jabatan.ID ASC,
                            struktur_organ.urut ASC";
            } elseif ($users->kategori_user_id == 2) {

              $id_sql = "SELECT
                            perusahaan.ID,
                            talenta.ID as talenta_id,
                            surat_keputusan.ID as sk_id,
                          CASE
                              
                              WHEN ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE ) THEN
                            TRUE ELSE FALSE 
                            END AS expire,
                          CASE
                              
                              WHEN ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE - INTERVAL '3 months ago' ) THEN
                            TRUE ELSE FALSE 
                            END AS kurang3,
                          CASE
                              
                              WHEN ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE - INTERVAL '6 months ago' ) THEN
                            TRUE ELSE FALSE 
                            END AS kurang6,
                          CASE
                              
                              WHEN view_organ_perusahaan.aktif = 't' THEN
                              talenta.nama_lengkap ELSE talenta.nama_lengkap 
                            END AS pejabat,
                          CASE
                              
                              WHEN view_organ_perusahaan.aktif = 't' THEN
                              'AKTIF' ELSE'TIDAK AKTIF' 
                            END AS aktifpejabat,
                            perusahaan.nama_lengkap AS bumns,
                            grup_jabatan.ID AS grup_jabat_id,
                            grup_jabatan.nama AS grup_jabat_nama,
                          CASE
                              
                              WHEN sk_perubahan_nomenklatur.nomenklatur_baru IS NULL THEN
                              struktur_organ.nomenklatur_jabatan ELSE sk_perubahan_nomenklatur.nomenklatur_baru 
                            END AS nama_jabatan,
                            surat_keputusan.nomor,
                            surat_keputusan.tanggal_sk,
                            surat_keputusan.file_name,
                            view_organ_perusahaan.tanggal_awal,
                            view_organ_perusahaan.tanggal_akhir,
                            view_organ_perusahaan.plt,
                            view_organ_perusahaan.komisaris_independen,
                            instansi_baru.nama AS instansi,
                            jenis_asal_instansi.nama AS asal_instansi,
                            view_organ_perusahaan.id_periode_jabatan AS periode,
                            struktur_organ.ID AS struktur_id 
                          FROM
                            view_organ_perusahaan
                            LEFT JOIN talenta ON talenta.ID = view_organ_perusahaan.id_talenta
                            LEFT JOIN struktur_organ ON struktur_organ.ID = view_organ_perusahaan.id_struktur_organ
                            LEFT JOIN perusahaan ON perusahaan.ID = struktur_organ.id_perusahaan
                            LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                            LEFT JOIN surat_keputusan ON surat_keputusan.ID = view_organ_perusahaan.id_surat_keputusan
                            LEFT JOIN instansi_baru ON instansi_baru.ID = talenta.id_asal_instansi
                            LEFT JOIN jenis_asal_instansi ON jenis_asal_instansi.ID = instansi_baru.id_jenis_asal_instansi
                            LEFT JOIN grup_jabatan ON grup_jabatan.ID = jenis_jabatan.id_grup_jabatan
                            LEFT JOIN sk_perubahan_nomenklatur ON sk_perubahan_nomenklatur.id_struktur_organ = struktur_organ.
                            ID LEFT JOIN sk_kom_independen ON sk_kom_independen.id_struktur_organ = struktur_organ.ID 
                          WHERE
                            struktur_organ.aktif = 't' $where
                          ORDER BY
                            perusahaan.ID ASC,
                            grup_jabatan.ID ASC,
                            struktur_organ.urut ASC";
            } else {
              $id_sql = "SELECT
                            perusahaan.ID,
                            talenta.ID as talenta_id,
                            surat_keputusan.ID as sk_id,
                          CASE
                              
                              WHEN ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE ) THEN
                            TRUE ELSE FALSE 
                            END AS expire,
                          CASE
                              
                              WHEN ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE - INTERVAL '3 months ago' ) THEN
                            TRUE ELSE FALSE 
                            END AS kurang3,
                          CASE
                              
                              WHEN ( view_organ_perusahaan.tanggal_akhir < CURRENT_DATE - INTERVAL '6 months ago' ) THEN
                            TRUE ELSE FALSE 
                            END AS kurang6,
                          CASE
                              
                              WHEN view_organ_perusahaan.aktif = 't' THEN
                              talenta.nama_lengkap ELSE talenta.nama_lengkap 
                            END AS pejabat,
                          CASE
                              
                              WHEN view_organ_perusahaan.aktif = 't' THEN
                              'AKTIF' ELSE'TIDAK AKTIF' 
                            END AS aktifpejabat,
                            perusahaan.nama_lengkap AS bumns,
                            grup_jabatan.ID AS grup_jabat_id,
                            grup_jabatan.nama AS grup_jabat_nama,
                          CASE
                              
                              WHEN sk_perubahan_nomenklatur.nomenklatur_baru IS NULL THEN
                              struktur_organ.nomenklatur_jabatan ELSE sk_perubahan_nomenklatur.nomenklatur_baru 
                            END AS nama_jabatan,
                            surat_keputusan.nomor,
                            surat_keputusan.tanggal_sk,
                            surat_keputusan.file_name,
                            view_organ_perusahaan.tanggal_awal,
                            view_organ_perusahaan.tanggal_akhir,
                            view_organ_perusahaan.plt,
                            view_organ_perusahaan.komisaris_independen,
                            instansi_baru.nama AS instansi,
                            jenis_asal_instansi.nama AS asal_instansi,
                            view_organ_perusahaan.id_periode_jabatan AS periode,
                            struktur_organ.ID AS struktur_id 
                          FROM
                            view_organ_perusahaan
                            LEFT JOIN talenta ON talenta.ID = view_organ_perusahaan.id_talenta
                            LEFT JOIN struktur_organ ON struktur_organ.ID = view_organ_perusahaan.id_struktur_organ
                            LEFT JOIN perusahaan ON perusahaan.ID = struktur_organ.id_perusahaan
                            LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                            LEFT JOIN surat_keputusan ON surat_keputusan.ID = view_organ_perusahaan.id_surat_keputusan
                            LEFT JOIN instansi_baru ON instansi_baru.ID = talenta.id_asal_instansi
                            LEFT JOIN jenis_asal_instansi ON jenis_asal_instansi.ID = instansi_baru.id_jenis_asal_instansi
                            LEFT JOIN grup_jabatan ON grup_jabatan.ID = jenis_jabatan.id_grup_jabatan
                            LEFT JOIN sk_perubahan_nomenklatur ON sk_perubahan_nomenklatur.id_struktur_organ = struktur_organ.
                            ID LEFT JOIN sk_kom_independen ON sk_kom_independen.id_struktur_organ = struktur_organ.ID 
                          WHERE
                            struktur_organ.aktif = 't' $where
                          ORDER BY
                            perusahaan.ID ASC,
                            grup_jabatan.ID ASC,
                            struktur_organ.urut ASC";
            }

            /*$id_sql = "SELECT
                        bumns.ID,
                      CASE
                          
                          WHEN view_organ_perusahaan.aktif = 't' THEN
                          talenta.nama_lengkap ELSE talenta.nama_lengkap 
                        END AS pejabat,
                        CASE
                          
                          WHEN view_organ_perusahaan.aktif = 't' THEN
                          'AKTIF' ELSE 'TIDAK AKTIF' 
                        END AS aktifpejabat,
                        bumns.nama_lengkap AS bumns,
                        grup_jabatan.ID AS grup_jabat_id,
                        grup_jabatan.nama AS grup_jabat_nama,
                      CASE
                          
                          WHEN view_organ_perusahaan.nomenklatur IS NULL THEN
                          struktur_organ.nomenklatur_jabatan ELSE view_organ_perusahaan.nomenklatur 
                        END AS nama_jabatan,
                        surat_keputusan.nomor,
                        surat_keputusan.tanggal_sk,
                        view_organ_perusahaan.tanggal_awal,
                        view_organ_perusahaan.tanggal_akhir,
                        view_organ_perusahaan.plt,
                        view_organ_perusahaan.komisaris_independen,
                        instansi.nama AS instansi,
                        jenis_asal_instansi.nama AS asal_instansi,
                        view_organ_perusahaan.id_periode_jabatan AS periode,
                        struktur_organ.ID AS struktur_id 
                      FROM
                        view_organ_perusahaan
                        LEFT JOIN talenta ON talenta.ID = view_organ_perusahaan.id_talenta
                        LEFT JOIN struktur_organ ON struktur_organ.ID = view_organ_perusahaan.id_struktur_organ
                        LEFT JOIN bumns ON bumns.ID = struktur_organ.id_perusahaan
                        LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                        LEFT JOIN surat_keputusan ON surat_keputusan.ID = view_organ_perusahaan.id_surat_keputusan
                        LEFT JOIN instansi ON instansi.ID = talenta.id_asal_instansi
                        LEFT JOIN jenis_asal_instansi ON jenis_asal_instansi.ID = instansi.id_jenis_asal_instansi
                        LEFT JOIN grup_jabatan ON grup_jabatan.ID = jenis_jabatan.id_grup_jabatan
                        LEFT JOIN sk_perubahan_nomenklatur ON sk_perubahan_nomenklatur.id_struktur_organ = struktur_organ.
                        ID LEFT JOIN sk_kom_independen ON sk_kom_independen.id_struktur_organ = struktur_organ.ID 
                      WHERE
                         surat_keputusan.save = 't' $where
                      ORDER BY
                        bumns.ID ASC,
                        grup_jabatan.ID ASC,
                        struktur_organ.urut ASC";*/

            $isiadmin  = DB::select(DB::raw($id_sql));
            $collections = new Collection;
            foreach($isiadmin as $val){

                $collections->push([

                    'id' => $val->id,
                    'talenta_id' => $val->talenta_id,
                    'sk_id' => $val->sk_id,
                    'pejabat' => $val->pejabat,
                    'bumns' => $val->bumns,
                    'nama' => $val->nama_jabatan,
                    'nomor' => $val->nomor,
                    'tanggal_awal' => $val->tanggal_awal ? \Carbon\Carbon::createFromFormat('Y-m-d', $val->tanggal_awal)->format('d-m-Y') : '',
                    'tanggal_akhir' => $val->tanggal_akhir ? \Carbon\Carbon::createFromFormat('Y-m-d', $val->tanggal_akhir)->format('d-m-Y') : '',
                    'instansi' => $val->instansi,
                    'asal_instansi' => $val->asal_instansi,
                    'periode' => $val->periode,
                    'tanggal_sk' => $val->tanggal_sk ? \Carbon\Carbon::createFromFormat('Y-m-d', $val->tanggal_sk)->format('d-m-Y') : '',
                    'grup_jabat_nama' => $val->grup_jabat_nama,
                    'plt' => $val->plt,
                    'komisaris_independen' => $val->komisaris_independen,
                    'aktifpejabat' => $val->aktifpejabat,
                    'expire' => $val->expire,
                    'kurang3' => $val->kurang3,
                    'kurang6' => $val->kurang6,
                    'file_name' => $val->file_name,
                ]);
            }
            return datatables()->of($collections)
            ->editColumn('pejabat', function($row){
                      $html = '';

                      if($row['periode'] == 2){
                        $html_periode = 'kt-badge--danger';
                      } else {
                        $html_periode = 'kt-badge--success';
                      }

                      if($row['expire'] == 't'){
                        $html_expire = '<span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill kt-badge--rounded">E</span>';
                      } elseif ($row['kurang3'] == 't') {
                        $html_expire = '<span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded"><3</span>';
                      } elseif ($row['kurang6'] == 't') {
                        $html_expire = '<span class="kt-badge kt-badge--primary kt-badge--inline kt-badge--pill kt-badge--rounded"><6</span>';
                      } else {
                        $html_expire = '&nbsp;';
                      }

                      if($row['plt'] == true){
                        $html .= '<a href="javascript:;" class="cls-minicv" data-id="'.$row['talenta_id'].'" data-toggle="tooltip" data-original-title="CV"><b>'.$row['pejabat'].'</b>&nbsp;&nbsp;<span class="kt-badge '.$html_periode.' kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row['periode'].'</span>&nbsp;&nbsp;'.$html_expire.'&nbsp;&nbsp;<span class="kt-badge kt-badge--info kt-badge--inline kt-badge--pill kt-badge--rounded">PLT</span><br/>'.$row['nama'].'</a>';
                      } elseif ($row['komisaris_independen'] == true) {
                        $html .= '<a href="javascript:;" class="cls-minicv" data-id="'.$row['talenta_id'].'" data-toggle="tooltip" data-original-title="CV"><b>'.$row['pejabat'].'</b>&nbsp;&nbsp;<span class="kt-badge '.$html_periode.' kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row['periode'].'</span>&nbsp;&nbsp;&nbsp;&nbsp;'.$html_expire.'<span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">Komisaris Independen</span><br/>'.$row['nama'].'</a>';
                      } else {
                        $html .= '<a href="javascript:;" class="cls-minicv" data-id="'.$row['talenta_id'].'" data-toggle="tooltip" data-original-title="CV"><b>'.$row['pejabat'].'</b>&nbsp;&nbsp;<span class="kt-badge '.$html_periode.' kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row['periode'].'</span>&nbsp;&nbsp;'.$html_expire.'<br/>'.$row['nama'].'</a>';
                      }
                      
                      return $html;
            })
            ->editColumn('nomor', function($row){
                      $id = (int)$row['sk_id'];
                      $id_perusahaan = (int)$row['id'];
                      $FilePendukung = SuratKeputusan::where('id',(int)$row['sk_id'])->first();
                      $html = '';
                      
                      $isExists = $this->checkFile($row['file_name']);

                        if($isExists){
                            
                            $html .= '<a href="javascript:;" data-toggle="tooltip"><b data-id_keputusan="'.$id.'" data-id_perusahaan="'.$id_perusahaan.'" data-nomor_sk="'.$row['nomor'].'" class="cls-button-detail">'.$row['nomor'].'</b>&nbsp;<a style="cursor:pointer" class="cls-urlpendukung" data-url="'.asset($this->suratkeputusanfile_url.$row['file_name']).'" data-keterangan="'.$row['nomor'].'" ><i class="flaticon2-file" ></i>&nbsp;</a>
                            <br/>'.$row['tanggal_sk'].'</a>';
                        } else {
                            $html .= '<a href="javascript:;" data-toggle="tooltip"><b data-id_keputusan="'.$id.'" data-id_perusahaan="'.$id_perusahaan.'" data-nomor_sk="'.$row['nomor'].'" class="cls-button-detail">'.$row['nomor'].'</b>&nbsp;<span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill kt-badge--rounded">Belum Ada PDF</span>
                            <br/>'.$row['tanggal_sk'].'</a>';
                        }


                      return $html;
            })
            /*->editColumn('nomor', function($row){
                      $html = '';
                      $html .= '<b>'.$row['nomor'].'</b><br/>'.$row['tanggal_sk'];
                      
                      return $html;
            })*/
            ->editColumn('grup_jabat_nama', function($row){
                      $html = '';
                      if($row['grup_jabat_nama'] == 'Direksi'){
                        $html .= '<span class="kt-badge kt-badge--primary kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row['grup_jabat_nama'].'</span>';
                      } else {
                        $html .= '<span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row['grup_jabat_nama'].'</span>';
                      }
                      //$html .= '<b>'.$row['nomor'].'</b><br/>'.$row['tanggal_sk'];
                      
                      return $html;
            })
            ->editColumn('instansi', function($row){
                      $html = '';
                      $html .= '<b>'.$row['asal_instansi'].'</b><br/>'.$row['instansi'];
                      
                      return $html;
            })
            ->filter(function ($instance) use ($request) {
                if ($request->has('pejabataktif')) {
                    $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                        return ($row['aktifpejabat'] == $request->pejabataktif) ? true : false;
                    });
                }
            })
            ->rawColumns(['bumns','grup_jabat_nama','pejabat','nomor','tanggal_awal', 'tanggal_akhir', 'instansi'])
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

    public function export(Request $request) 
    {
        $id_bumn = $request->id_bumn;
        $pejabat = $request->pejabat;
        $pejabataktif = $request->pejabataktif;
        $id_grup_jabat = $request->id_grup_jabat;
        $tgl_sk = $request->tgl_sk;
        $id_jabatan = $request->id_jabatan;
        $id_periode = $request->id_periode;
        $id_asal_instansi = $request->id_asal_instansi;
        $nomor_sk = $request->nomor_sk;
        $jenis_kelamin = $request->jenis_kelamin;
        $id_agama = $request->id_agama;
        $masa_jabatan = $request->masa_jabatan;
        $kewarganegaraan = $request->kewarganegaraan;
        $id_suku = $request->id_suku;
        return Excel::download(new MonitoringPejabat($id_bumn,$pejabat,$pejabataktif,$id_grup_jabat,$tgl_sk,$id_jabatan,$id_periode,$id_asal_instansi,$nomor_sk,$jenis_kelamin,$id_agama,$masa_jabatan,$kewarganegaraan,$id_suku), 'monitoringpejabat.xlsx');
    }

    public function checkFile($filename)
    {

        $path = './uploads/suratkeputusanfile/'.$filename;

        $isExists = file_exists($path);

        return $isExists;

    }
}