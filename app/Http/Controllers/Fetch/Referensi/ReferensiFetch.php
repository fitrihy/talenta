<?php

namespace App\Http\Controllers\Fetch\Referensi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Agama;
use App\Kota;
use App\Provinsi;
use App\Universitas;


class ReferensiFetchController extends Controller
{
    public function GetAllAgama(Request $request) 
    {
        $return = [];
        $data = Agama::get();
        foreach($data as $id => $text){
            $return[] = ['id' => $id, 'text' => $text ];
        }
        return response()->json(['results' => $return]);
    }

    public function getKota(Request $request)
    {
    	$id_provinsi = $request->id_provinsi;
        $return = [];
        $data = Kota::where('provinsi_id', $id_provinsi)->get();

        foreach($data as $item){
            $return[] = ['id' => $item->id, 'nama' => $item->nama];
        }
        return response()->json($return);
    }

    public function getAllProvinsi(Request $request)
    {
        $return = [];
        $data = Provinsi::where('is_luar_negeri', false)->get();
        foreach($data as $item){
            $return[] = ['id' => $item->id, 'nama' => $item->nama];
        }
        return response()->json($return);
    }
    
    public function getDataUniversitas(Request $request)
    {
        $return = [];
        $data = Universitas::where('id', $request->id_universitas)->first();
        if(@$data->refNegara->is_luar_negeri == false){
            $return['negara'] = 'INDONESIA';
            $return['id_negara'] = 'INDONESIA';
            $return['provinsi'] = (@$data->refNegara->nama ? @$data->refNegara->nama:'' );
            $return['id_provinsi'] = (@$data->refNegara->id ? @$data->refNegara->id:'' );
        }else{
            $return['negara'] = (@$data->refNegara->nama ? @$data->refNegara->nama: '' );
            $return['id_negara'] = (@$data->refNegara->id ? @$data->refNegara->id:'' );
            $return['provinsi'] = '';
            $return['id_provinsi'] = '';
        }
        $return['kota'] = (@$data->refKota->nama ? @$data->refKota->nama:'' );
        $return['id_kota'] = (@$data->refKota->id ? @$data->refKota->id:'' );

        return response()->json($return);
    }
}
