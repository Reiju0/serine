<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PublicFunction;

class EtcController extends Controller
{
    //
    public function index()
    {
    	/*$sql = DB::table('activations')->where('id', '<', '30')->get();
    	dd($sql);*/
    	# code...
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf->WriteHTML('<h1>Hello world!</h1>');
        $mpdf->Output();
    }

    public function indikator(){
        /*
    	$sql = DB::connection('mysql')->table('bios')->get();
    	foreach ($sql as $key ) {
    		# code...
    		DB::table('ref_indikator')->where('indikator', $key->INDIKATOR)->where('id', $key->ID)->update([
    			'kdindikator' => $key->KD_INDIKATOR,
    			'kdrumpun' => $key->KDRUMPUN_NEW
    		]);
    		echo "$key->KD_INDIKATOR done <br>";
    	}*/
        $slug = '58';
        $kode = '400977';
        $subject = 'percobaan';
        $isi = '<p>align peringatan</p>';


        $role = DB::table('roles')->where('slug', $slug)->orWhere('id', $slug)->first();

        $sql = "SELECT * FROM users a LEFT JOIN (
                    SELECT USER_ID, ROLE_ID, GID, kode
                    FROM ROLE_USERS, json_table(USER_GROUPS, '$' columns(
                        GID VARCHAR2(20) PATH '$.all.id',
                        NESTED PATH '$.all.val[*]' columns (
                            kode PATH '$'
                        )
                    ))
                    WHERE ROLE_ID = ?
                    ) b ON a.id = b.user_id
                WHERE kode = ?
                    AND aktif = '1'";

        $query = DB::table(DB::raw("($sql) a"))
                ->setBindings([$role->id, $kode]);

        

        //dd($query->get());
        PublicFunction::SendGeneralEmail($slug, $kode, $subject, $isi);
    }
}
