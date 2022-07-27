<?php

namespace App\Exports;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use DB;

class ReportTalenta implements FromView , WithTitle
{
    public function __construct($filter = "" ){
        $this->filter = $filter ;
    }


    public function view(): View
    {  
      $where = '';
      if($this->filter=='all'){
        $where = '';
      }else if($this->filter=='bumn'){
        $where = 'perusahaan.level = 0 AND';
      }else if($this->filter=='anak'){
        $where = 'perusahaan.level = 1 AND';
      }else if($this->filter=='cucu'){
        $where = 'perusahaan.level > 1 AND';
      }

      $id_sql = "SELECT
                    perusahaan.ID,
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
                    talenta.nama_lengkap AS pejabat,
                    grup_jabatan.ID AS grup_jabat_id,
                    grup_jabatan.nama AS grup_jabat_nama,
                    CASE
                      WHEN view_organ_perusahaan.nomenklatur IS NULL THEN
                      struktur_organ.nomenklatur_jabatan ELSE view_organ_perusahaan.nomenklatur 
                    END AS nama_jabatan,
                    surat_keputusan.nomor as surat_keputusan,
                    surat_keputusan.tanggal_sk,
                    surat_keputusan.tanggal_serah_terima,
                    surat_keputusan.id_grup_jabatan,
                    view_organ_perusahaan.tanggal_awal,
                    view_organ_perusahaan.tanggal_akhir,
                    age (view_organ_perusahaan.tanggal_akhir, view_organ_perusahaan.tanggal_awal) as lama_menjabat,
                    CASE
                      WHEN date_part('year', age(view_organ_perusahaan.tanggal_akhir, view_organ_perusahaan.tanggal_awal)) < 1 THEN '< 1 tahun'
                      WHEN date_part('year', age(view_organ_perusahaan.tanggal_akhir, view_organ_perusahaan.tanggal_awal)) < 2 THEN '1 - 2 tahun'
                      WHEN date_part('year', age(view_organ_perusahaan.tanggal_akhir, view_organ_perusahaan.tanggal_awal)) < 3 THEN '2 - 3 tahun'
                      WHEN date_part('year', age(view_organ_perusahaan.tanggal_akhir, view_organ_perusahaan.tanggal_awal)) < 4 THEN '3 - 4 tahun'
                      WHEN date_part('year', age(view_organ_perusahaan.tanggal_akhir, view_organ_perusahaan.tanggal_awal)) < 5 THEN '4 - 5 tahun'
                    END AS kelompok_masa_jabatan,
                    view_organ_perusahaan.plt,
                    view_organ_perusahaan.komisaris_independen,
                    instansi.nama AS instansi,
                    jenis_asal_instansi.nama AS asal_instansi,
                    talenta.jabatan_asal_instansi,
                    view_organ_perusahaan.id_periode_jabatan AS periode,
                    struktur_organ.ID AS struktur_id,
                    talenta.*,
                    agamas.nama as talenta_agama, 
                    status_kawin.nama as talenta_status_kawin, 
                    CASE 
                      WHEN perusahaan.level = 0 THEN bumn_0.nama_lengkap 
                      WHEN perusahaan.level = 1 THEN bumn_1.nama_lengkap 
                      WHEN perusahaan.level = 2 THEN bumn_2.nama_lengkap 
                      WHEN perusahaan.level = 3 THEN bumn_3.nama_lengkap 
                      WHEN perusahaan.level = 4 THEN bumn_4.nama_lengkap 
                    END AS bumn_induk,
                    CASE 
                      WHEN perusahaan.level = 1 THEN bumn_0.nama_lengkap 
                      WHEN perusahaan.level = 2 THEN bumn_1.nama_lengkap
                      WHEN perusahaan.level = 3 THEN bumn_2.nama_lengkap
                      WHEN perusahaan.level = 4 THEN bumn_3.nama_lengkap
                    END AS bumn_anak,
                    CASE 
                      WHEN perusahaan.level >= 2 THEN bumn_0.nama_lengkap 
                    END AS bumn_cucu,
                    date_part('year', age(talenta.tanggal_lahir)) as usia,
                    CASE
                      WHEN date_part('year', age(talenta.tanggal_lahir)) < 41 THEN '< 40 tahun'
                      WHEN date_part('year', age(talenta.tanggal_lahir)) > 40 
                      AND date_part('year', age(talenta.tanggal_lahir)) < 51 THEN '41 - 50 tahun'
                      WHEN date_part('year', age(talenta.tanggal_lahir)) > 50 
                      AND date_part('year', age(talenta.tanggal_lahir)) < 61 THEN '51 - 60 tahun'
                      WHEN date_part('year', age(talenta.tanggal_lahir)) > 60 THEN '> 60 tahun'
                    END AS kelompok_usia,
                    pendidikan_slta.perguruan_tinggi as pendidikan_slta,
                    pendidikan_s1.perguruan_tinggi as pendidikan_s1,
                    pendidikan_s1.penjurusan as pendidikan_s1_jurusan,
                    pendidikan_s2.perguruan_tinggi as pendidikan_s2,
                    pendidikan_s2.penjurusan as pendidikan_s2_jurusan,
                    pendidikan_s3.perguruan_tinggi as pendidikan_s3,
                    pendidikan_s3.penjurusan as pendidikan_s3_jurusan,
                    array_to_string( array_agg(keahlian.jenis_keahlian) , ',') as keahlian,
                    concat_ws(' - ',jabatan_2.penugasan, jabatan_2.instansi) as jabatan_2,
                    concat_ws(' - ',jabatan_1.penugasan, jabatan_1.instansi) as jabatan_1,
                    CASE 
                      WHEN surat_keputusan.id_grup_jabatan = 1 THEN 'Dekom/Dewas'
                      WHEN surat_keputusan.id_grup_jabatan = 4 THEN 'Komisaris'
                    END AS grup_jabatan, 
                    jenis_jabatan.nama as jenis_jabatan,
                    assesmen_direksi.nilai_asesmen_global as nilai_assesmen,
                    penghasilan.gaji_pokok, 
                    penghasilan.tantiem, 
                    penghasilan.tunjangan
                  FROM
                    view_organ_perusahaan
                    LEFT JOIN talenta ON talenta.ID = view_organ_perusahaan.id_talenta
                    LEFT JOIN struktur_organ ON struktur_organ.ID = view_organ_perusahaan.id_struktur_organ
                    LEFT JOIN perusahaan ON perusahaan.ID = struktur_organ.id_perusahaan
                    LEFT JOIN perusahaan AS bumn_0 ON bumn_0.ID = struktur_organ.id_perusahaan
                    LEFT JOIN perusahaan AS bumn_1 ON bumn_1.ID = bumn_0.induk
                    LEFT JOIN perusahaan AS bumn_2 ON bumn_2.ID = bumn_1.induk
                    LEFT JOIN perusahaan AS bumn_3 ON bumn_3.ID = bumn_2.induk
                    LEFT JOIN perusahaan AS bumn_4 ON bumn_4.ID = bumn_3.induk
                    LEFT JOIN jenis_jabatan ON jenis_jabatan.ID = struktur_organ.id_jenis_jabatan
                    LEFT JOIN surat_keputusan ON surat_keputusan.ID = view_organ_perusahaan.id_surat_keputusan
                    LEFT JOIN instansi ON instansi.ID = talenta.id_asal_instansi
                    LEFT JOIN jenis_asal_instansi ON jenis_asal_instansi.ID = instansi.id_jenis_asal_instansi
                    LEFT JOIN grup_jabatan ON grup_jabatan.ID = jenis_jabatan.id_grup_jabatan
                    LEFT JOIN agamas on agamas.id = talenta.id_agama 
                    LEFT JOIN status_kawin on status_kawin.id = talenta.id_status_kawin
                    LEFT JOIN riwayat_pendidikan AS pendidikan_slta on pendidikan_slta.id_talenta = talenta.id AND pendidikan_slta.id_jenjang_pendidikan = 5
                    LEFT JOIN riwayat_pendidikan AS pendidikan_s1 on pendidikan_s1.id_talenta = talenta.id AND (pendidikan_s1.id_jenjang_pendidikan = 2 OR pendidikan_s1.id_jenjang_pendidikan = 10)
                    LEFT JOIN riwayat_pendidikan AS pendidikan_s2 on pendidikan_s2.id_talenta = talenta.id AND (pendidikan_s2.id_jenjang_pendidikan = 3)
                    LEFT JOIN riwayat_pendidikan AS pendidikan_s3 on pendidikan_s3.id_talenta = talenta.id AND (pendidikan_s3.id_jenjang_pendidikan = 4)
                    LEFT JOIN transaction_talenta_keahlian on transaction_talenta_keahlian.id_talenta = talenta.id
                    LEFT JOIN keahlian on keahlian.id = transaction_talenta_keahlian.id_keahlian
                    LEFT JOIN lateral (SELECT riwayat_jabatan_lain.* FROM riwayat_jabatan_lain
                        WHERE riwayat_jabatan_lain.id_talenta = talenta.id order by tanggal_awal limit 1)  AS
                        jabatan_2 on TRUE
                    LEFT JOIN lateral (SELECT riwayat_jabatan_lain.* FROM riwayat_jabatan_lain
                        WHERE riwayat_jabatan_lain.id_talenta = talenta.id order by tanggal_awal limit 1 OFFSET 1)  AS
                        jabatan_1 on TRUE
                    LEFT JOIN lateral (SELECT assesmen_direksi.* FROM assesmen_direksi
                        WHERE assesmen_direksi.id_talenta = talenta.id order by assesmen_direksi.updated_at desc limit 1)  AS
                        assesmen_direksi on TRUE
                    LEFT JOIN lateral (SELECT penghasilan.* FROM penghasilan
                        WHERE penghasilan.id_talenta = talenta.id AND penghasilan.id_struktur_organ = view_organ_perusahaan.id_struktur_organ
                        ORDER BY penghasilan.tahun desc limit 1) AS penghasilan ON TRUE
                  WHERE
                    $where
                    surat_keputusan.save = 't' 
                    AND struktur_organ.aktif = 't'
                    AND view_organ_perusahaan.aktif = 't'
                  GROUP BY
                    perusahaan.ID,
                    view_organ_perusahaan.tanggal_akhir,
                    view_organ_perusahaan.aktif,
                    talenta.nama_lengkap,
                    talenta.ID,
                    grup_jabatan.ID,
                    view_organ_perusahaan.nomenklatur,
                    struktur_organ.nomenklatur_jabatan,
                    surat_keputusan.nomor,
                    surat_keputusan.tanggal_sk,
                    view_organ_perusahaan.tanggal_awal,
                    view_organ_perusahaan.plt,
                    view_organ_perusahaan.komisaris_independen,
                    instansi.nama,
                    jenis_asal_instansi.nama,
                    view_organ_perusahaan.id_periode_jabatan,
                    struktur_organ.ID,
                    talenta.jenis_kelamin,
                    talenta.nik,
                    talenta.npwp,
                    talenta.email,
                    talenta.nomor_hp,
                    talenta.kewarganegaraan,
                    talenta.gol_darah,
                    talenta.suku,
                    agamas.nama,
                    talenta.tanggal_lahir,
                    talenta.tempat_lahir, 
                    status_kawin.nama,
                    bumn_1.nama_lengkap,
                    bumn_2.nama_lengkap,
                    bumn_3.nama_lengkap,
                    bumn_4.nama_lengkap,
                    bumn_0.nama_lengkap,
                    pendidikan_slta.perguruan_tinggi,
                    pendidikan_s1.perguruan_tinggi,
                    pendidikan_s1.penjurusan,
                    pendidikan_s2.perguruan_tinggi,
                    pendidikan_s2.penjurusan,
                    pendidikan_s3.perguruan_tinggi,
                    pendidikan_s3.penjurusan,
                    jabatan_2.penugasan,
                    jabatan_2.instansi,
                    jabatan_1.penugasan,
                    jabatan_1.instansi,
                    surat_keputusan.id_grup_jabatan,
                    surat_keputusan.tanggal_serah_terima,
                    jenis_jabatan.nama,
                    assesmen_direksi.nilai_asesmen_global,
                    penghasilan.gaji_pokok, 
                    penghasilan.tantiem, 
                    penghasilan.tunjangan
                  ORDER BY
                    perusahaan.ID ASC,
                    grup_jabatan.ID ASC,
                    struktur_organ.urut ASC";
         $data  = DB::select(DB::raw($id_sql));
      

      return view('report.report_talenta', [
          'talenta' => $data, 
          'filter' => $this->filter
      ]);
    }

    public function title(): string
    {
        return 'Rekap pengisian CV Talenta' ;
    }
}
