<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Exception;
use App\User;
use App\Talenta;
use Route;
use Mail;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Collection;
use App\CVInterest;
use App\CVPajak;
use App\CVSummary;
use App\DataKeluarga;
use App\DataKaryaIlmiah;
use App\DataPenghargaan;
use App\TransactionTalentaKeahlian;
use App\PengalamanLain;
use App\RiwayatPendidikan;
use App\RiwayatPelatihan;
use App\RiwayatJabatanLain;
use App\RiwayatOrganisasi;
use App\LembagaAssessment;

class GeneralModel extends Model
{
	
	public function getparentmenu($search)
	{
		return Menu::where('label','ilike','%'.$search.'%')->orderBy('parent_id','asc')->orderBy('order','asc')->get();
	}

	public function getassidemenu()
	{
		
		try{
			$html = '<div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">';
			$html .= '<div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">';
			$html .= '<ul class="kt-menu__nav ">';
			$html .= $this->getrecursivemenu(0, Menu::where('status', true)->orderBy('order','ASC')->get(), User::find((int)Auth::user()->id)->getmenuaccess());
			$html .= '</ul>';		
			$html .= '</div>';		
			$html .= '</div>';		

			return $html;
		}catch(Exception $e){}
	}

	protected function setsvgproperty($icon)
	{
		if(empty($icon)){
			$iconsvg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <circle fill="#000000" cx="5" cy="12" r="2"/>
        <circle fill="#000000" cx="12" cy="12" r="2"/>
        <circle fill="#000000" cx="19" cy="12" r="2"/>
    </g>
</svg>';
		}
		else {
			$iconsvg = $icon;
		}
		return '<span class="kt-menu__link-icon">'.$iconsvg.'</span>';
	}

	protected function getrecursivemenu($parent_id, $menu, $data)
	{
		$html = '';
		$result = $menu->where('parent_id', (int)$parent_id)->sortBy('order');
		foreach ($result as $value) {
			$child = $menu->where('parent_id', (int)$value->id)->sortBy('order');
			$childData = $data->where('parent_id', (int)$value->id)->sortBy('order');

			$routing = $value->route_name != '#'? (Route::has($value->route_name)? route($value->route_name) : 'javascript:;') : '#';

			if((bool)$child->count() && (bool)$childData->count()){
				//jika ada child
				$class = (bool)$menu->where('parent_id', (int)$value->id)->where('route_name',Route::currentRouteName())->count()? 'kt-menu__item  kt-menu__item--submenu kt-menu__item--open kt-menu__item--here' : 'kt-menu__item  kt-menu__item--submenu';
				if(strpos(Route::currentRouteName(), 'referensi') !== false && (int)$value->id == 8){
					$class = 'kt-menu__item  kt-menu__item--submenu kt-menu__item--open kt-menu__item--here';
				} 
				$html .= '<li class="'.$class.'" aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a href="'.$routing.'" class="kt-menu__link kt-menu__toggle">'.$this->setsvgproperty($value->icon).'<span class="kt-menu__link-text">'.$value->label.'</span><i class="kt-menu__ver-arrow la la-angle-right"></i></a>';
				$html .= '<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>';
				$html .= '<ul class="kt-menu__subnav">';
				$html .= '<li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true"><span class="kt-menu__link"><span class="kt-menu__link-text">'.$value->label.'</span></span></li>';

				$html .= $this->getrecursivemenu((int)$value->id, $menu, $data);
				$html .= '</ul>';
				$html .= '</div>';
				$html .= '</li>';

			}else{
				//jika tidak ada child
				if((bool)$data->where('id', (int)$value->id)->count()){
					$class = Route::currentRouteName() === $value->route_name? 'kt-menu__item--active' : '';
					$html .= '<li class="kt-menu__item '.$class.'" aria-haspopup="true"><a href="'.$routing.'" class="kt-menu__link ">'.$this->setsvgproperty($value->icon).'<span class="kt-menu__link-text">'.$value->label.'</span></a></li>';
				}
			}
		}
		return $html;
	}

	public function getkategoriuser($search)
	{
		return KategoriUser::where('kategori','ilike','%'.$search.'%')->get();
	}

	public function getlembagaassessment($search)
	{
		return DB::table('lembaga_assessment')
		       ->select([
		       	'lembaga_assessment.id',
		       	'lembaga_assessment.nama'
		       ])
		       ->get();
	}

	public function getbumnactive($search)
	{
		return DB::table('perusahaan_status_history')
		       ->select([
		       	'perusahaan_status_history.id', 
		       	'perusahaan_status_history.perusahaan_id', 
		       	'perusahaan_status_history.tmt_awal', 
		       	'perusahaan_status_history.tmt_akhir', 
		       	'perusahaan.id_angka', 
		       	'perusahaan.id_huruf', 
		       	'perusahaan.nama_lengkap', 
		       	'perusahaan.nama_singkat'
		       ])
		       ->join('perusahaan','perusahaan.id','=','perusahaan_status_history.perusahaan_id')
		       ->where('status_perusahaan_id', 1)
		       ->where(function($query) use($search) {
		       	   $query->where('perusahaan.nama_lengkap','ilike','%'.$search.'%')
		       	         ->orWhere('perusahaan.nama_singkat','ilike','%'.$search.'%');
		       })
		       ->whereRaw('tmt_awal <= NOW()::DATE')
		       ->whereRaw('(CASE WHEN tmt_akhir IS NOT NULL THEN tmt_akhir >= NOW()::DATE ELSE NOW()::DATE = NOW()::DATE END)')
		       ->get();
	}

	public function monitoringpejabat($id_angka)
	{
		try{

			$where = " ";

            if($id_angka){
               $where .= " and perusahaan.id_angka = '".$id_angka."' ";
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
                      CASE
                          
                          WHEN view_organ_perusahaan.aktif = 't' THEN
                          talenta.nama_lengkap ELSE talenta.nama_lengkap 
                        END AS pejabat,
                      talenta.id as id_talenta,
                      CASE
                          
                          WHEN view_organ_perusahaan.aktif = 't' THEN
                          'AKTIF' ELSE'TIDAK AKTIF' 
                        END AS aktifpejabat,
                        perusahaan.nama_lengkap AS bumns,
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
                        surat_keputusan.save = 't' and struktur_organ.aktif = 't' $where
                      ORDER BY
                        perusahaan.ID ASC,
                        grup_jabatan.ID ASC,
                        struktur_organ.urut ASC";

            $isiadmin  = DB::select(DB::raw($id_sql));
            $collections = new Collection;
            foreach($isiadmin as $val){

                $collections->push([

                    'id' => $val->id,
                    'pejabat' => $val->pejabat,
                    'bumns' => $val->bumns,
                    'nama' => $val->nama_jabatan,
                    'nomor' => $val->nomor,
                    'tanggal_awal' => \Carbon\Carbon::createFromFormat('Y-m-d', $val->tanggal_awal)->format('d-m-Y'),
                    'tanggal_akhir' => \Carbon\Carbon::createFromFormat('Y-m-d', $val->tanggal_akhir)->format('d-m-Y'),
                    'instansi' => $val->instansi,
                    'asal_instansi' => $val->asal_instansi,
                    'periode' => $val->periode,
                    'tanggal_sk' => \Carbon\Carbon::createFromFormat('Y-m-d', $val->tanggal_sk)->format('d-m-Y'),
                    'grup_jabat_nama' => $val->grup_jabat_nama,
                    'plt' => $val->plt,
                    'komisaris_independen' => $val->komisaris_independen,
                    'aktifpejabat' => $val->aktifpejabat,
                    'expire' => $val->expire,
                    'kurang3' => $val->kurang3,
                    'kurang6' => $val->kurang6,
                    'id_talenta' => $val->id_talenta
                ]);
            }

			return response()->json([
				'status' => (bool)$collections->count(),
				'msg' => null,
				'data' => $collections
			]);			
		}catch(Exception $e){
			return response()->json([
				'status' => false,
				'msg' => 'Data tidak ditemukan',
				'data' => []
			]);			
		}
	}

	public function biodatatalent()
	{
		try{

	        $data = Talenta::select([
	        	'id',
	        	'nama_lengkap',
	        	'jenis_kelamin',
	        	'nik',
	        	'npwp',
	        	'email',
	        	'nomor_hp',
	        	'alamat',
	        	'suku',
	        	'gol_darah',
	        	'tanggal_lahir',
	        	'tempat_lahir',
	        	'gelar'

	        ])
	        ->orderBy('nama_lengkap', 'ASC')
	        ->paginate();

	        return response()->json([
	          'status' => (bool)$data->count(),
	          'msg' => null,
	          'data' => $data
	        ]);
		}catch(Exception $e){
			return response()->json([
				'status' => false,
				'msg' => 'Data tidak ditemukan',
				'data' => []
			]);				
		}
	}

	public function cvpejabat($id_talenta)
	{
		try{

			$where = " ";

            if($id_talenta){
               $where .= " talenta.id = ".$id_talenta." ";
            }

			$id_sql = "SELECT
						  talenta.*,
						  instansi_baru.nama as instansi,
						  jenis_asal_instansi.nama as asalinstansi,
						  status_kawin.nama as statuskawin,
						  status_talenta.nama as statustalenta,
						  kategori_jabatan_talent.nama as kategoritalent,
						  kategori_non_talent.nama as kategorinontalent,
						  perusahaan.nama_lengkap as talentaasal	
						FROM
							talenta
							LEFT JOIN instansi_baru ON instansi_baru.ID = talenta.id_asal_instansi
							LEFT JOIN jenis_asal_instansi ON jenis_asal_instansi.ID = talenta.id_jenis_asal_instansi
							LEFT JOIN status_kawin ON status_kawin.ID = talenta.id_status_kawin
							LEFT JOIN status_talenta on status_talenta.id = talenta.id_status_talenta
							LEFT JOIN kategori_jabatan_talent on kategori_jabatan_talent.id = talenta.id_kategori_jabatan_talent
							LEFT JOIN kategori_non_talent on kategori_non_talent.id = talenta.id_kategori_non_talent
							LEFT JOIN perusahaan on perusahaan.id = talenta.id_perusahaan
					    WHERE
						$where
						ORDER BY
						  talenta.nama_lengkap asc";

            $isicv  = DB::select(DB::raw($id_sql));
            $collections = new Collection;
            foreach($isicv as $val){

            	$cvinterest = CVInterest::where("id_talenta", $val->id)->get();
            	$cvpajak = CVPajak::where("id_talenta", $val->id)->get();
            	$cvsummary = CVSummary::where("id_talenta", $val->id)->get();
            	$datakeluarga = DataKeluarga::where("id_talenta", $val->id)->get();
            	$datakaryailmiah = DataKaryaIlmiah::where("id_talenta", $val->id)->get();
            	$datapenghargaan = DataPenghargaan::where("id_talenta", $val->id)->get();

            	//$datakeahlian = TransactionTalentaKeahlian::where("id_talenta", $val->id)->get();

            	$datakeahlian = DB::table('transaction_talenta_keahlian')
                       ->leftJoin('keahlian', 'keahlian.id', '=', 'transaction_talenta_keahlian.id_keahlian')
                       ->select(DB::raw("transaction_talenta_keahlian.*,
										  keahlian.deskripsi,
										  keahlian.jenis_keahlian"))
                       ->where('transaction_talenta_keahlian.id_talenta', '=', $val->id)
                       ->orderBy('transaction_talenta_keahlian.id_keahlian', 'ASC')
                       ->get();

            	$datapengalamanlain = PengalamanLain::where("id_talenta", $val->id)->get();
            	//$datariwayatpendidikan = RiwayatPendidikan::where("id_talenta", $val->id)->get();


            	$datariwayatpendidikan = DB::table('riwayat_pendidikan')
                       ->leftJoin('jenjang_pendidikan', 'jenjang_pendidikan.id', '=', 'riwayat_pendidikan.id_jenjang_pendidikan')
                       ->select(DB::raw("riwayat_pendidikan.*,
										  jenjang_pendidikan.nama"))
                       ->where('riwayat_pendidikan.id_talenta', '=', $val->id)
                       ->orderBy('riwayat_pendidikan.id_jenjang_pendidikan', 'ASC')
                       ->get();

            	$datariwayatpelatihan = RiwayatPelatihan::where("id_talenta", $val->id)->get();
            	$datariwayatjabatanlain = RiwayatJabatanLain::where("id_talenta", $val->id)->get();
            	$datariwayatorganisasi = RiwayatOrganisasi::where("id_talenta", $val->id)->get();


                $collections->push([

                    'id' => $val->id,
                    'pejabat' => $val->nama_lengkap,
                    'status_talenta' => $val->statustalenta,
                    'kategori_talenta' => $val->kategoritalent,
                    'kategori_non_talenta' => $val->kategorinontalent,
                    'talenta_asal' => $val->talentaasal,
                    'instansi' => $val->instansi,
                    'asalinstansi' => $val->asalinstansi,
                    'cvinterest' => $cvinterest,
                    'cvpajak' => $cvpajak,
                    'cvsummary' => $cvsummary,
                    'datakeluarga' => $datakeluarga,
                    'datakaryailmiah' => $datakaryailmiah,
                    'datapenghargaan' => $datapenghargaan,
                    'datakeahlian' => $datakeahlian,
                    'datapengalamanlain' => $datapengalamanlain,
                    'datariwayatpendidikan' => $datariwayatpendidikan,
                    'datariwayatpelatihan' => $datariwayatpelatihan,
                    'datariwayatjabatanlain' => $datariwayatjabatanlain,
                    'datariwayatorganisasi' => $datariwayatorganisasi,
                ]);
            }

			return response()->json([
				'status' => (bool)$collections->count(),
				'msg' => null,
				'data' => $collections
			]);			
		}catch(Exception $e){
			return response()->json([
				'status' => false,
				'msg' => 'Data tidak ditemukan',
				'data' => []
			]);			
		}
	}
	
}
