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
use DB;
use Config;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Exports\ExcelSheet;
use App\Exports\MonitoringSK;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

class PengisianskController extends Controller
{
  protected $__route;
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  function __construct()
  {
    $this->__route = 'administrasi.monitoring.pengisiansk';
    //$this->middleware('permission:admonitoringpejabat-list');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  public function index(Request $request)
  {
    activity()->log('Menu Administrasi SK Monitoring Pengisian SK');

    $id_users = \Auth::user()->id;
    $id_users_bumn = \Auth::user()->id_bumn;
    $users = User::where('id', $id_users)->first();
    $cekbumns = Perusahaan::where('induk', $id_users_bumn)->get();
    if ($users->kategori_user_id == 1) {
      $bumn = Perusahaan::where('induk', 0)->where('level', 0)->where('kepemilikan', 'BUMN')->orderBy('id', 'asc')->get();
    } else {
      $bumn = Perusahaan::where('induk', 0)->where('level', 0)->where('kepemilikan', 'BUMN')->where('id', $users->id_bumn)->first();
    }
    $countbumns = $cekbumns->count();

    return view($this->__route . '.index', [
      'pagetitle' => 'Administrasi SK Monitoring',
      'grupjabats' => GrupJabatan::orderBy('id', 'asc')->get(),
      'pejabats' => Talenta::orderBy('id', 'asc')->get(),
      'jabatans' => JenisJabatan::orderBy('id', 'asc')->get(),
      'periodes' => Periode::orderBy('id', 'asc')->get(),
      'bumns' => $bumn,
      'users' => $users,
      'countbumns' => $countbumns,
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
          'url' => route('administrasi.monitoring.pengisiansk.index'),
          'menu' => 'Monitoring Pengisian SK'
        ]
      ]
    ]);
  }

  public function datatable(Request $request)
  {
    try {

      $id_users = \Auth::user()->id;
      $id_users_bumn = \Auth::user()->id_bumn;
      $users = User::where('id', $id_users)->first();

      $cekbumns = Perusahaan::where('induk', $id_users_bumn)->get();
      $countbumns = $cekbumns->count();

    
      $where1=$where2 = " ";
      if ($request->id_bumn) {
        $where1 .= " and perusahaan.id = " . $request->id_bumn . " ";
        $where2 .= " and perusahaan.induk = " . $request->id_bumn . " ";
      } else {
        $where1 .= " ";
      }

      // if ($request->id_grup_jabat) {
      //   $where .= " and surat_keputusan.id_grup_jabatan = " . $request->id_grup_jabat . " ";
      // } else {
      //   $where .= " ";
      // }

      // if ($request->id_jabatan) {
      //   $where .= " and jenis_jabatan.id = " . $request->id_jabatan . " ";
      // } else {
      //   $where .= " ";
      // }

      // if ($request->nomor) {
      //   $where .= " and lower(surat_keputusan.nomor) like lower('%" . $request->nomor . "%') ";
      // } else {
      //   $where .= " ";
      // }

      // if ($request->pejabat) {
      //   $where .= " and lower(talenta.nama_lengkap) like lower('%" . $request->pejabat . "%') ";
      // } else {
      //   $where .= " ";
      // }

      // if ($request->tanggal_sk) {
      //   $where .= " and surat_keputusan.tanggal_sk =  '" . \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_sk)->format('Y-m-d') . "' ";
      // } else {
      //   $where .= " ";
      // }

      if ($users->kategori_user_id == 1) {
        $id_sql = "SELECT
        perusahaan.ID,
        perusahaan.bumn_nama,
        perusahaan.jumlah_direksi,
        perusahaan.jumlah_dirkomwas,
        perusahaan.jumlah_organ_isi,
        anak_perusahaan.jumlah_direksi_anak,
        anak_perusahaan.jumlah_dirkomwas_anak,
        anak_perusahaan.jumlah_organ_isi_anak,
        trunc(sum((perusahaan.presentase_isi+anak_perusahaan.presentase_isi_anak)/2)::NUMERIC) as presentase_isi
        from 
        (SELECT
        perusahaan.ID AS ID,
        perusahaan.nama_lengkap AS bumn_nama,
        COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) AS jumlah_direksi,
        COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) AS jumlah_dirkomwas,
        COUNT ( CASE organ_perusahaan.aktif WHEN FALSE THEN 1 END ) AS jumlah_organ_isi,
        TRUNC(
        ( COUNT ( CASE organ_perusahaan.aktif WHEN TRUE THEN 1 END ) ) :: NUMERIC / ( COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) + COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) ) * 100,
        0 
        ) AS presentase_isi 
      FROM
        perusahaan
        LEFT JOIN struktur_organ ON struktur_organ.id_perusahaan = perusahaan.ID 
        LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
        LEFT JOIN organ_perusahaan ON organ_perusahaan.id_struktur_organ = struktur_organ.ID 
      WHERE
        struktur_organ.aktif = 't' 
        and organ_perusahaan.aktif='t'
        AND perusahaan.induk = 0 
        AND perusahaan.LEVEL = 0
        ".$where1."
      GROUP BY
        perusahaan.ID,
        perusahaan.nama_lengkap 
      ORDER BY
        perusahaan.ID ASC) as perusahaan,
        (SELECT
        perusahaan.induk,
        COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) AS jumlah_direksi_anak,
        COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) AS jumlah_dirkomwas_anak,
        COUNT ( CASE organ_perusahaan.aktif WHEN FALSE THEN 1 END ) AS jumlah_organ_isi_anak,
        TRUNC(
        ( COUNT ( CASE organ_perusahaan.aktif WHEN TRUE THEN 1 END ) ) :: NUMERIC / ( COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) + COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) ) * 100,
        0 
        ) AS presentase_isi_anak
      FROM
        perusahaan
        LEFT JOIN struktur_organ ON struktur_organ.id_perusahaan = perusahaan.
        ID LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
        LEFT JOIN organ_perusahaan ON organ_perusahaan.id_struktur_organ = struktur_organ.ID 
      WHERE
        struktur_organ.aktif = 't'
        and organ_perusahaan.aktif = 't'
        AND perusahaan.induk != 0 
        ".$where2."
        GROUP BY
        perusahaan.induk 
      ORDER BY
        perusahaan.induk ASC) as anak_perusahaan
        where perusahaan.id=anak_perusahaan.induk
        GROUP BY
        perusahaan.id,
        perusahaan.bumn_nama,
        perusahaan.jumlah_direksi,
        perusahaan.jumlah_dirkomwas,
        perusahaan.jumlah_organ_isi,
        anak_perusahaan.jumlah_direksi_anak,
        anak_perusahaan.jumlah_dirkomwas_anak,
        anak_perusahaan.jumlah_organ_isi_anak";

        $isiadmin  = DB::select(DB::raw($id_sql));
        $collections = new Collection;
        foreach ($isiadmin as $val) {
          $collections->push([
            'id' => $val->id,
            'bumn_nama' => $val->bumn_nama,
            'jumlah_direksi' => $val->jumlah_direksi,
            'jumlah_dirkomwas' => $val->jumlah_dirkomwas,
            'jumlah_organ_isi' => $val->jumlah_organ_isi,
            'jumlah_direksi_anak' => $val->jumlah_direksi_anak,
            'jumlah_dirkomwas_anak' => $val->jumlah_dirkomwas_anak,
            'jumlah_organ_isi_anak' => $val->jumlah_organ_isi_anak,
            'presentase_isi' => $val->presentase_isi
          ]);
        }
      } elseif ($users->kategori_user_id == 2) {

        if ($countbumns > 0) {
          $id_sql = "SELECT
                  perusahaan.ID AS ID,
                  perusahaan.nama_lengkap AS bumn_nama,
                  COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) AS jumlah_direksi,
                  COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) AS jumlah_dirkomwas,
                  COUNT ( CASE organ_perusahaan.aktif WHEN FALSE THEN 1 END ) AS jumlah_organ_isi,
                  TRUNC(
                  ( COUNT ( CASE organ_perusahaan.aktif WHEN TRUE THEN 1 END ) ) :: NUMERIC / ( COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) + COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) ) * 100,
                  0 
                  ) AS presentase_isi 
                FROM
                  perusahaan
                  LEFT JOIN struktur_organ ON struktur_organ.id_perusahaan = perusahaan.
                  ID LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                  LEFT JOIN organ_perusahaan ON organ_perusahaan.id_struktur_organ = struktur_organ.ID 
                WHERE
                  struktur_organ.aktif = 't' 
                  and organ_perusahaan.aktif = 't'
                  AND perusahaan.induk = $id_users_bumn
                GROUP BY
                  perusahaan.ID,
                  perusahaan.nama_lengkap 
                ORDER BY
                  perusahaan.ID ASC";
        } else {
          $id_sql = "SELECT
                  perusahaan.ID AS ID,
                  perusahaan.nama_lengkap AS bumn_nama,
                  COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) AS jumlah_direksi,
                  COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) AS jumlah_dirkomwas,
                  COUNT ( CASE organ_perusahaan.aktif WHEN FALSE THEN 1 END ) AS jumlah_organ_isi,
                  TRUNC(
                  ( COUNT ( CASE organ_perusahaan.aktif WHEN TRUE THEN 1 END ) ) :: NUMERIC / ( COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) + COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) ) * 100,
                  0 
                  ) AS presentase_isi 
                FROM
                  perusahaan
                  LEFT JOIN struktur_organ ON struktur_organ.id_perusahaan = perusahaan.
                  ID LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                  LEFT JOIN organ_perusahaan ON organ_perusahaan.id_struktur_organ = struktur_organ.ID 
                WHERE
                  struktur_organ.aktif = 't' 
                  and organ_perusahaan.aktif = 't'
                  AND perusahaan.id = $id_users_bumn 
                GROUP BY
                  perusahaan.ID,
                  perusahaan.nama_lengkap 
                ORDER BY
                  perusahaan.ID ASC";
        }

        $isiadmin  = DB::select(DB::raw($id_sql));
        $collections = new Collection;
        foreach ($isiadmin as $val) {
          $collections->push([
            'id' => $val->id,
            'bumn_nama' => $val->bumn_nama,
            'jumlah_direksi' => $val->jumlah_direksi,
            'jumlah_dirkomwas' => $val->jumlah_dirkomwas,
            'jumlah_organ_isi' => $val->jumlah_organ_isi,
            'presentase_isi' => $val->presentase_isi
          ]);
        }
      } else {
        $id_sql = "SELECT
        perusahaan.ID,
        perusahaan.bumn_nama,
        perusahaan.jumlah_direksi,
        perusahaan.jumlah_dirkomwas,
        perusahaan.jumlah_organ_isi,
        anak_perusahaan.jumlah_direksi_anak,
        anak_perusahaan.jumlah_dirkomwas_anak,
        anak_perusahaan.jumlah_organ_isi_anak,
        trunc(sum((perusahaan.presentase_isi+anak_perusahaan.presentase_isi_anak)/2)::NUMERIC) as presentase_isi
        from 
        (SELECT
        perusahaan.ID AS ID,
        perusahaan.nama_lengkap AS bumn_nama,
        COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) AS jumlah_direksi,
        COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) AS jumlah_dirkomwas,
        COUNT ( CASE organ_perusahaan.aktif WHEN FALSE THEN 1 END ) AS jumlah_organ_isi,
        TRUNC(
        ( COUNT ( CASE organ_perusahaan.aktif WHEN TRUE THEN 1 END ) ) :: NUMERIC / ( COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) + COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) ) * 100,
        0 
        ) AS presentase_isi 
      FROM
        perusahaan
        LEFT JOIN struktur_organ ON struktur_organ.id_perusahaan = perusahaan.ID 
        LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
        LEFT JOIN organ_perusahaan ON organ_perusahaan.id_struktur_organ = struktur_organ.ID 
      WHERE
        struktur_organ.aktif = 't' 
        and organ_perusahaan.aktif = 't'
        AND perusahaan.induk = 0 
        AND perusahaan.LEVEL = 0 
        ".$where1."
      GROUP BY
        perusahaan.ID,
        perusahaan.nama_lengkap 
      ORDER BY
        perusahaan.ID ASC) as perusahaan,
        (SELECT
        perusahaan.induk,
        COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) AS jumlah_direksi_anak,
        COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) AS jumlah_dirkomwas_anak,
        COUNT ( CASE organ_perusahaan.aktif WHEN FALSE THEN 1 END ) AS jumlah_organ_isi_anak,
        TRUNC(
        ( COUNT ( CASE organ_perusahaan.aktif WHEN TRUE THEN 1 END ) ) :: NUMERIC / ( COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) + COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) ) * 100,
        0 
        ) AS presentase_isi_anak
      FROM
        perusahaan
        LEFT JOIN struktur_organ ON struktur_organ.id_perusahaan = perusahaan.
        ID LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
        LEFT JOIN organ_perusahaan ON organ_perusahaan.id_struktur_organ = struktur_organ.ID 
      WHERE
        struktur_organ.aktif = 't'
        and organ_perusahaan.aktif = 't'
        AND perusahaan.induk != 0
        ".$where2."
        GROUP BY
        perusahaan.induk 
      ORDER BY
        perusahaan.induk ASC) as anak_perusahaan
        where perusahaan.id=anak_perusahaan.induk
        GROUP BY
        perusahaan.id,
        perusahaan.bumn_nama,
        perusahaan.jumlah_direksi,
        perusahaan.jumlah_dirkomwas,
        perusahaan.jumlah_organ_isi,
        anak_perusahaan.jumlah_direksi_anak,
        anak_perusahaan.jumlah_dirkomwas_anak,
        anak_perusahaan.jumlah_organ_isi_anak";

        $isiadmin  = DB::select(DB::raw($id_sql));
        $collections = new Collection;
        foreach ($isiadmin as $val) {
          $collections->push([
            'id' => $val->id,
            'bumn_nama' => $val->bumn_nama,
            'jumlah_direksi' => $val->jumlah_direksi,
            'jumlah_dirkomwas' => $val->jumlah_dirkomwas,
            'jumlah_organ_isi' => $val->jumlah_organ_isi,
            'jumlah_direksi_anak' => $val->jumlah_direksi_anak,
            'jumlah_dirkomwas_anak' => $val->jumlah_dirkomwas_anak,
            'jumlah_organ_isi_anak' => $val->jumlah_organ_isi_anak,
            'presentase_isi' => $val->presentase_isi
          ]);
        }
      }




      /*->editColumn('jumlah_direksi', function($row){
                      $html = '';
                      if($row['id_grup_jabat'] == 1){
                        $html .= $row['nama_lengkap'].'<br/><span class="kt-badge kt-badge--primary kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row['nama_grup_jabatan'].'</span>';
                      } else {
                        if($row['nama_grup_jabatan'] == 'Dekom/Dewas'){
                            $html .= $row['nama_lengkap'].'<br/><span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">Dekom</span>';
                        } else {
                            $html .= $row['nama_lengkap'].'<br/><span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row['nama_grup_jabatan'].'</span>';
                        }
                        
                      }
                      
                      return $html;
            })
            ->editColumn('jumlah_dirkomwas', function($row){
                      $html = '';
                      if($row['id_grup_jabat'] == 1){
                        $html .= $row['nama_lengkap'].'<br/><span class="kt-badge kt-badge--primary kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row['nama_grup_jabatan'].'</span>';
                      } else {
                        if($row['nama_grup_jabatan'] == 'Dekom/Dewas'){
                            $html .= $row['nama_lengkap'].'<br/><span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">Dekom</span>';
                        } else {
                            $html .= $row['nama_lengkap'].'<br/><span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">'.$row['nama_grup_jabatan'].'</span>';
                        }
                        
                      }
                      
                      return $html;
            })*/
      return datatables()->of($collections)
        ->rawColumns(['bumn_nama', 'jumlah_direksi', 'jumlah_dirkomwas', 'jumlah_organ_isi', 'presentase_isi'])
        ->toJson();
    } catch (Exception $e) {
      return response([
        'draw'            => 0,
        'recordsTotal'    => 0,
        'recordsFiltered' => 0,
        'data'            => []
      ]);
    }
  }

  public function datatablegrup(Request $request)
  {
    try {

      $where = " ";

      if ($request->id_bumn) {
        $where .= " and bumns.induk = " . $request->id_bumn . " ";
      } else {
        $where .= " ";
      }

      $id_users = \Auth::user()->id;
      $id_users_bumn = \Auth::user()->id_bumn;

      $users = User::where('id', $id_users)->first();

      if ($users->kategori_user_id == 1) {
        $id_sql = "SELECT
                          bumns.ID AS ID,
                          bumns.nama_lengkap AS bumn_nama,
                          COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) AS jumlah_direksi,
                          COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) AS jumlah_dirkomwas,
                          COUNT ( CASE organ_perusahaan.aktif WHEN false THEN 1 END ) AS jumlah_organ_isi,
                          TRUNC(
                          ( COUNT ( CASE organ_perusahaan.aktif WHEN TRUE THEN 1 END ) ) :: NUMERIC / ( COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) + COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) ) * 100,
                          0 
                          ) AS presentase_isi 
                        FROM
                          bumns
                          LEFT JOIN struktur_organ ON struktur_organ.id_perusahaan = bumns.
                          ID LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                          LEFT JOIN organ_perusahaan ON organ_perusahaan.id_struktur_organ = struktur_organ.ID 
                        WHERE
                          struktur_organ.aktif = 't'
                          AND bumns.LEVEL <> 0
                          $where
                        GROUP BY
                          bumns.ID,
                          bumns.nama_lengkap 
                        ORDER BY
                          bumns.ID ASC";
      } elseif ($users->kategori_user_id == 2) {
        $perusahaan_id = "WITH RECURSIVE anak AS (
          SELECT
            perusahaan_id,
            perusahaan_induk_id,
            tmt_awal,
            tmt_akhir 
          FROM
            perusahaan_relasi 
          WHERE
            perusahaan_induk_id = " . $id_users_bumn . " 
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
            P.induk
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
          P.ID ASC";

        $test = DB::select(DB::raw($perusahaan_id));
        foreach ($test as $val) {
          $pegId[] = $val->induk;
        }
        $filter_id = implode(",", $pegId);
        $id_sql = "SELECT
                          perusahaan.ID AS ID,
                          perusahaan.nama_lengkap AS bumn_nama,
                          COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) AS jumlah_direksi,
                          COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) AS jumlah_dirkomwas,
                          COUNT ( CASE organ_perusahaan.aktif WHEN false THEN 1 END ) AS jumlah_organ_isi,
                          TRUNC(
                          ( COUNT ( CASE organ_perusahaan.aktif WHEN TRUE THEN 1 END ) ) :: NUMERIC / ( COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) + COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) ) * 100,
                          0 
                          ) AS presentase_isi 
                        FROM
                        perusahaan
                          LEFT JOIN struktur_organ ON struktur_organ.id_perusahaan = perusahaan.
                          ID LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                          LEFT JOIN organ_perusahaan ON organ_perusahaan.id_struktur_organ = struktur_organ.ID 
                        WHERE
                          struktur_organ.aktif = 't'
                          and perusahaan.induk in ($filter_id)
                        GROUP BY
                        perusahaan.ID,
                        perusahaan.nama_lengkap 
                        ORDER BY
                        perusahaan.ID ASC";
      } else {
        $id_sql = "SELECT
                          perusahaan.ID AS ID,
                          perusahaan.nama_lengkap AS bumn_nama,
                          COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) AS jumlah_direksi,
                          COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) AS jumlah_dirkomwas,
                          COUNT ( CASE organ_perusahaan.aktif WHEN false THEN 1 END ) AS jumlah_organ_isi,
                          TRUNC(
                          ( COUNT ( CASE organ_perusahaan.aktif WHEN TRUE THEN 1 END ) ) :: NUMERIC / ( COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 1 THEN 1 END ) + COUNT ( CASE jenis_jabatan.id_grup_jabatan WHEN 4 THEN 1 END ) ) * 100,
                          0 
                          ) AS presentase_isi 
                        FROM
                        perusahaan
                          LEFT JOIN struktur_organ ON struktur_organ.id_perusahaan = perusahaan.
                          ID LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                          LEFT JOIN organ_perusahaan ON organ_perusahaan.id_struktur_organ = struktur_organ.ID 
                        WHERE
                          struktur_organ.aktif = 't'
                          AND perusahaan.LEVEL <> 0
                        GROUP BY
                        perusahaan.ID,
                        perusahaan.nama_lengkap 
                        ORDER BY
                        perusahaan.ID ASC";
      }



      $isiadmin  = DB::select(DB::raw($id_sql));
      $collections = new Collection;
      foreach ($isiadmin as $val) {
        $collections->push([
          'id' => $val->id,
          'bumn_nama' => $val->bumn_nama,
          'jumlah_direksi' => $val->jumlah_direksi,
          'jumlah_dirkomwas' => $val->jumlah_dirkomwas,
          'jumlah_organ_isi' => $val->jumlah_organ_isi,
          'presentase_isi' => $val->presentase_isi
        ]);
      }
      return datatables()->of($collections)
        ->rawColumns(['bumn_nama', 'jumlah_direksi', 'jumlah_dirkomwas', 'jumlah_organ_isi', 'presentase_isi'])
        ->toJson();
    } catch (Exception $e) {
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
      return Excel::download(new MonitoringSK, 'monitoringsk.xlsx');
    }
}
