<?php

namespace App\Http\Controllers\Modules\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Exception;
use DB;
use App\GeneralModel;


class WebServiceController extends Controller
{
	/**
	 * @OA\Get(
	 * path="/api/monitoring-pejabat/{id_angka}",
	 * summary="List Monitoring Pejabat",
	 * description="Get list Monitoring Pejabat by id_angka",
	 * operationId="id_angka",
	 * tags={"monitoring pejabat"},
	 * security={ {"api_key": {"test"} }},
	 * @OA\Parameter(
	 *    description="ID Angka Perusahaan",
	 *    in="path",
	 *    name="id_angka",
	 *    required=true,
	 *    example="0101",
	 *    @OA\Schema(
	 *       type="string",
	 *    )
	 * ),
	 * @OA\Response(
	 *    response=200,
	 *    description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="headers", type="object"),
	 *       @OA\Property(property="original", type="object", 
	 *       	@OA\Property(property="status", type="boolean"),
	 *        	@OA\Property(property="msg", type="string"),
	 *        	@OA\Property(property="data", type="object",
	 *        		@OA\Property(property="current_page", type="integer"),
	 *        		@OA\Property(property="data", type="object",
	 *        		    @OA\Property(property="0", type="object",
	 *              		@OA\Property(property="id", type="integer"),
	 *        			@OA\Property(property="pejabat", type="string"),
	 *        			@OA\Property(property="bumns", type="string"),
	 *        			@OA\Property(property="nama", type="string"),
	 *        			@OA\Property(property="nomor", type="string"),
	 *        			@OA\Property(property="tanggal_awal", type="string"),
	 *        			@OA\Property(property="tanggal_akhir", type="string"),
	 *        			@OA\Property(property="instansi", type="string"),
	 *        			@OA\Property(property="asal_instansi", type="string"),
	 *        			@OA\Property(property="periode", type="integer"),
	 *        			@OA\Property(property="tanggal_sk", type="string"),
	 *        			@OA\Property(property="grup_jabat_nama", type="string"),
	 *        			@OA\Property(property="plt", type="string"),
	 *        			@OA\Property(property="komisaris_independen", type="string"),
	 *        			@OA\Property(property="aktifpejabat", type="string"),
	 *        			@OA\Property(property="expire", type="boolean"),
	 *        			@OA\Property(property="kurang3", type="boolean"),
	 *        			@OA\Property(property="kurang6", type="boolean"),
	 *        			@OA\Property(property="id_talenta", type="integer"),
	 * 					)
	 *        			
	 *        		),
	 *        	)
	 *       ),
	 *       @OA\Property(property="exception", type="string"),
	 *    ),
	 *       
	 * ),
	 * @OA\Response(
	 *    response=401,
	 *    description="Returns when user is not authenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="message", type="string", example="Not authorized"),
	 *    )
	 * ),
	 * )
	 */
	public function DataMonitoringPejabat($id_angka)
	{
		return response()->json((new GeneralModel())->monitoringpejabat($id_angka));	
	}

    /**
	 * @OA\Get(
	 * path="/api/all-talent",
	 * summary="All Biodata Talent",
	 * description="Get All Biodata Talent",
	 * operationId="biodataShow",
	 * tags={"biodata"},
	 * security={ {"bearer": {} }},
	 * @OA\Response(
	 *    response=200,
	 *    description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="headers", type="object"),
	 *       @OA\Property(property="original", type="object", 
	 *       	@OA\Property(property="status", type="boolean"),
	 *        	@OA\Property(property="msg", type="string"),
	 *        	@OA\Property(property="data", type="object",
	 *        		@OA\Property(property="current_page", type="integer"),
	 *        		@OA\Property(property="data", type="object",
	 *        		    @OA\Property(property="0", type="object",
	 *              		@OA\Property(property="id", type="integer"),
	 *        			@OA\Property(property="nama_lengkap", type="string"),
	 *        			@OA\Property(property="jenis_kelamin", type="string"),
	 *        			@OA\Property(property="nik", type="string"),
	 *        			@OA\Property(property="npwp", type="string"),
	 *        			@OA\Property(property="email", type="string"),
	 *        			@OA\Property(property="nomor_hp", type="string"),
	 *        			@OA\Property(property="alamat", type="string"),
	 *        			@OA\Property(property="suku", type="string"),
	 *        			@OA\Property(property="gol_darah", type="string"),
	 *        			@OA\Property(property="tanggal_lahir", type="string"),
	 *        			@OA\Property(property="tempat_lahir", type="string"),
	 *        			@OA\Property(property="gelar", type="string"),
	 * 					)
	 *        			
	 *        		),
	 *        		@OA\Property(property="first_page_url", type="string"),
	 *        		@OA\Property(property="from", type="integer"),
	 *        		@OA\Property(property="last_page", type="integer"),
	 *        		@OA\Property(property="last_page_url", type="string"),
	 *        		@OA\Property(property="next_page_url", type="string"),
	 *        		@OA\Property(property="path", type="string"),
	 *        		@OA\Property(property="per_page", type="integer"),
	 *        		@OA\Property(property="prev_page_url", type="string"),
	 *        		@OA\Property(property="to", type="integer"),
	 *        		@OA\Property(property="total", type="integer"),
	 *        	)
	 *       ),
	 *       @OA\Property(property="exception", type="string"),
	 *    ),
	 *       
	 * ),
	 * 
	 * )
	 * 
	 * 
	 */
	public function biodatatalent()
	{
		return response()->json((new GeneralModel())->biodatatalent());	
	}

	/**
	 * @OA\Get(
	 * path="/api/cv-pejabat/{id_talenta}",
	 * summary="Detail CV Pejabat",
	 * description="Get Detail CV Pejabat by id talenta",
	 * operationId="id_talenta",
	 * tags={"CV pejabat"},
	 * security={ {"bearer": {} }},
	 * @OA\Parameter(
	 *    description="ID Talenta",
	 *    in="path",
	 *    name="id_talenta",
	 *    required=true,
	 *    example="1",
	 *    @OA\Schema(
	 *       type="integer",
	 *       format="int64"
	 *    )
	 * ),
	 * @OA\Response(
	 *    response=200,
	 *    description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="headers", type="object"),
	 *       @OA\Property(property="original", type="object", 
	 *       	@OA\Property(property="status", type="boolean"),
	 *        	@OA\Property(property="msg", type="string"),
	 *        	@OA\Property(property="data", type="object",
	 *        		@OA\Property(property="current_page", type="integer"),
	 *        		@OA\Property(property="data", type="object",
	 *        		    @OA\Property(property="0", type="object",
	 *              		@OA\Property(property="id", type="integer"),
	 *        			@OA\Property(property="pejabat", type="string"),
	 *        			@OA\Property(property="status_talenta", type="string"),
	 *        			@OA\Property(property="kategori_talenta", type="string"),
	 *        			@OA\Property(property="kategori_non_talenta", type="string"),
	 *        			@OA\Property(property="talenta_asal", type="string"),
	 *        			@OA\Property(property="instansi", type="string"),
	 *        			@OA\Property(property="asalinstansi", type="string"),
	 *        			@OA\Property(property="cvinterest", type="object"),
	 *        			@OA\Property(property="cvpajak", type="object"),
	 *        			@OA\Property(property="cvsummary", type="object"),
	 *        			@OA\Property(property="datakeluarga", type="object"),
	 *        			@OA\Property(property="datakaryailmiah", type="object"),
	 *        			@OA\Property(property="datapenghargaan", type="object"),
	 *        			@OA\Property(property="datakeahlian", type="object"),
	 *        			@OA\Property(property="datapengalamanlain", type="object"),
	 *        			@OA\Property(property="datariwayatpendidikan", type="object"),
	 *        			@OA\Property(property="datariwayatpelatihan", type="object"),
	 *        			@OA\Property(property="datariwayatjabatanlain", type="object"),
	 *        			@OA\Property(property="datariwayatorganisasi", type="object"),
	 * 					)
	 *        			
	 *        		),
	 *        	)
	 *       ),
	 *       @OA\Property(property="exception", type="string"),
	 *    ),
	 *       
	 * ),
	 * )
	 */
	public function cvpejabat($id_talenta)
	{
		return response()->json((new GeneralModel())->cvpejabat($id_talenta));	
	}
}