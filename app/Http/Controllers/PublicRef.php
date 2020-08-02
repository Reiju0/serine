<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PublicFunction;
use DB;

class PublicRef extends Controller
{
    
    public static function role($selected = '0', $all = false, $permission = array('Users.admin')){
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="role" id="role" required>';
        if($all != false){
            $return .= '<option value="'.$all.'">All</option>';
        }
        $return .= PublicFunction::getRole($selected, $permission);
        $return .= '</select>';

        return $return;
    }

    public static function role_group($selected = '0', $all = false){
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="role_group" id="role_group" required>';
        if($all != false){
            $return .= '<option value="'.$all.'">All</option>';
        }

        $query = DB::table('roles')
            ->whereIn('id', ['2', '3', '4', '5'])
            ->orderBy('id', 'asc');

        $query = $query->get();
        
        foreach ($query as $key) {
            $return .= "<option value='".$key->id."' ";
            if($key->id == $selected ){
                $return .= "selected";
            }
            $return .= ">".$key->name."</option>";
        }

        
        $return .= '</select>';

        return $return;
    }

    public static function kode($groups, $id, $selected = 0, $kddept = 'z', $filter = true){
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="kode" id="kode">';
            $all = "<option value='0'>All</option>";
            $user_group = DB::table('user_group')
                            ->where('id', '=', $id);
            if($user_group->count() < 1){
                return '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="kode" id="kode"></select>';
            }

            $user_group = $user_group->first();

            $sql = $user_group->ref_table;

            if($user_group->kolom == 'kdsatker'){
                $sql = " select a.*, b.kdkanwil 
                    from $user_group->ref_table a 
                    left join t_kppn b on a.kdkppn = b.kdkppn
                     ";

                if($kddept != 'z'){
                    $sql .= " where a.kddept = ? and a.status = '1'";
                }
            }

            $ref_table = DB::table(DB::raw("($sql) a"))
                            ->orderBy($user_group->kolom, 'asc');
            if($user_group->kolom == 'kdsatker' and $kddept != 'z'){
                $ref_table = $ref_table->setBindings([$kddept]);
            }

            //$ref_table = app(PublicF::class)->WhereGroup($ref_table)->get();
            if($filter){
                $ref_table = PublicFunction::WhereGroup($groups, $ref_table);
            }

            if($ref_table->count() <= 1){
                $all = "";
            }

            $ref_table = $ref_table->get();

            foreach ($ref_table as $key) {
                $array =  (array) $key;
                $all .= "<option value='".$array[$user_group->kolom]."' ";
                if($array[$user_group->kolom] == $selected ){
                    $all .= "selected";
                }
                $all .= ">".$array[$user_group->kolom].'. '.$array[$user_group->nama]."</option>";
            }
        $return .= $all;
        $return .= '</select>';

        return $return;
    }

    public static function getRef($kode, $selected = 0, $required = false, $array = null){

        $user_group = DB::table('user_group')
                            ->where('kolom', '=', $kode)
                            ->first();
        $tabel = DB::table($user_group->ref_table)->where('status', '1')->orderBy($kode, 'asc')->get();

        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="'.$kode.'" id="'.$kode.'" ';
        if($required){
            $return .= 'required';
        }
        $return .= '>';
            $all = "<option value='0'>All</option>";
        
        $nmtabel = $user_group->nama;
            foreach ($tabel as $key) {
                $all .= "<option value='".$key->$kode."' ";
                if($key->$kode == $selected ){
                    $all .= "selected";
                }
                $all .= ">".$key->$kode.'. '.$key->$nmtabel."</option>";
            }
        $return .= $all;
        $return .= '</select>';

        return $return;
    }

    public static function getRefTable ($table, $kode, $nmtabel, $selected = 0, $required = false, $array = null){
        $query = DB::table($table)->where('status', '1')->orderBy($kode, 'asc');

        if($array != null){
            $array = json_decode(json_encode($array));
            if(isset($array->query)){
                foreach ($array->query as $key) {
                    //$key = (object) $key;
                    if($key->method == 'where'){
                        if(isset($key->param)){
                            $query = $query->where($key->kolom, $key->param, $key->value);
                        }else{
                            $query = $query->where($key->kolom, $key->value);
                        }
                    }

                    if($key->method == 'wherein'){
                        $query = $query->whereIn($key->kolom, $key->value);
                    }

                    if($key->method == 'order'){
                        if(isset($key->param)){
                            $query = $query->orderBy($key->kolom, $key->param);
                        }else{
                            $query = $query->orderBy($key->kolom, 'asc');
                        }
                    }
                }
            }
        }

        //return $array->query;

        $nama = $kode;
        if(isset($array->name)){
            $nama = $array->name;
        }

        $query = $query->get();
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="'.$nama.'" id="'.$nama.'" ';
        if($required){
            $return .= 'required';
        }
        $return .= '>';
            $all = '<option value="">Select one</option>';
            if(isset($array->select)){
                $all = '';
                foreach ($array->select as $key) {
                    # code...
                    $all .= "<option value='$key->key'>$key->text</option>";
                }
            }
            
            foreach ($query as $key) {
                $all .= "<option value='".$key->$kode."' ";
                if($key->$kode == $selected ){
                    $all .= "selected";
                }
                $all .= ">".$key->$kode.'. '.$key->$nmtabel."</option>";
            }
        $return .= $all;
        $return .= '</select>';

        return $return;
    }
	
	public static function getRefTableMulti ($table, $kode, $nmtabel, $selected = 0, $required = false, $array = null){
        $query = DB::table($table)->orderBy($kode, 'asc');

        if($array != null){
            $array = json_decode(json_encode($array));
            if(isset($array->query)){
                foreach ($array->query as $key) {
                    //$key = (object) $key;
                    if($key->method == 'where'){
                        if(isset($key->param)){
                            $query = $query->where($key->kolom, $key->param, $key->value);
                        }else{
                            $query = $query->where($key->kolom, $key->value);
                        }
                    }

                    if($key->method == 'wherein'){
                        $query = $query->whereIn($key->kolom, $key->value);
                    }

                    if($key->method == 'order'){
                        if(isset($key->param)){
                            $query = $query->orderBy($key->kolom, $key->param);
                        }else{
                            $query = $query->orderBy($key->kolom, 'asc');
                        }
                    }
                }
            }
        }

        //return $array->query;

        $nama = $kode;
        if(isset($array->name)){
            $nama = $array->name;
        }

        $query = $query->get();
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" multiple name="'.$nama.'[]" id="'.$nama.'" ';
        if($required){
            $return .= 'required';
        }
        $return .= '>';
            $all = '<option value="">Select one</option>';
            if(isset($array->select)){
                $all = '';
                foreach ($array->select as $key) {
                    # code...
                    $all .= "<option value='$key->key'>$key->text</option>";
                }
            }
            
            foreach ($query as $key) {
                $all .= "<option value='".$key->$kode."' ";
                if($key->$kode == $selected ){
                    $all .= "selected";
                }
                $all .= ">".$key->$kode.'. '.$key->$nmtabel."</option>";
            }
        $return .= $all;
        $return .= '</select>';

        return $return;
    }

    public static function getRefTableAdvance ($sql, $name, $kode, $nmtabel, $selected = 0, $required = false, $array = null){
        
                                    
        $query = DB::table(DB::raw("($sql) a"))
                 ->selectRaw('a.*')
				 ->orderByRaw('abs('.$kode.')')
                 ->get();
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="'.$name.'" id="'.$name.'" ';
        if($required){
            $return .= 'required';
        }
        $return .= '>';
            $all = '<option value="">Select one</option>';
            if(isset($array->select)){
                $all = '';
                foreach ($array->select as $key) {
                    # code...
                    $all .= "<option value='$key->key'>$key->text</option>";
                }
            }
            
            foreach ($query as $key) {
                $all .= "<option value='".$key->$kode."' ";
                if($key->$kode == $selected ){
                    $all .= "selected";
                }
                $all .= ">".$key->$kode." - ".$key->$nmtabel."</option>";
            }
        $return .= $all;
        $return .= '</select>';

        return $return;
    }

	public static function getRefTableAdvanceMulti ($sql, $name, $kode, $nmtabel, $selected = 0, $required = false, $array = null){
        
                                    
        $query = DB::table(DB::raw("($sql) a"))
                 ->selectRaw('a.*')
                 ->get();
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" Multiple name="'.$name.'[]" id="'.$name.'" ';
        if($required){
            $return .= 'required';
        }
        $return .= '>';
            $all = '<option value="xx">--Semua--</option>';
            if(isset($array->select)){
                $all = '';
                foreach ($array->select as $key) {
                    # code...
                    $all .= "<option value='$key->key'>$key->text</option>";
                }
            }
            
            foreach ($query as $key) {
                $all .= "<option value='".$key->$kode."' ";
                if($key->$kode == $selected ){
                    $all .= "selected";
                }
                $all .= ">".$key->$kode." - ".$key->$nmtabel."</option>";
            }
        $return .= $all;
        $return .= '</select>';

        return $return;
    }
	
    public static function Validation_Message(){
        $messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'numeric'  => 'Kolom :attribute hanya boleh diisi angka.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai',
            'email'     => 'Kolom :attribute harus diisi email',
            'alpha_dash' => 'Kolom :attribute hanya bisa diisi huruf/angka'
        ];

        return $messages;
    }

    public static function getPeriode($selected = 0, $required = false){
        $array  = [
            ['val' => '01', 'text' => 'Januari'],
            ['val' => '02', 'text' => 'Februari'],
            ['val' => '03', 'text' => 'Maret'],
            ['val' => '04', 'text' => 'April'],
            ['val' => '05', 'text' => 'Mei'],
            ['val' => '06', 'text' => 'Juni'],
            ['val' => '07', 'text' => 'Juli'],
            ['val' => '08', 'text' => 'Agustus'],
            ['val' => '09', 'text' => 'September'],
            ['val' => '10', 'text' => 'Oktober'],
            ['val' => '11', 'text' => 'November'],
            ['val' => '12', 'text' => 'Desember']
        ];
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="periode" id="periode" ';
        if($required){
            $return .= 'required';
        }
        $return .= '>';
        $all = '<option value="">Select one</option>';
        foreach ($array as $key) {
            # code...
            $all .= "<option value='".$key['val']."' ";
            if($key['val'] == $selected ){
                $all .= "selected";
            }
            $all .= " >".$key['val'].". $key[text]</option>";
        }

        $return .= $all;
        $return .= '</select>';

        return $return;
    }

    public static function getSatkerBlu($group, $selected = 0, $required = false){
        $satker     = "SELECT a.*, b.kdkanwil from t_satker_blu a 
                        LEFT JOIN t_kppn b on a.kdkppn = b.kdkppn
                        ";

        $query = DB::table(DB::raw("($satker) a"))
                    ->where('status', '1')
                    ->orderBy('kdsatker', 'asc');

        $query = PublicFunction::WhereGroup($group,$query);

        $query = $query->get();
        //dd($query);
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="kdsatker" id="kdsatker" ';
        if($required){
            $return .= 'required ';             
        }
        $return .= '>';
            $all = '';
            if($query->count() > 1){
                $all = '<option value="">Select one</option>';
            }
            foreach ($query as $key) {
                $all .= "<option value='".$key->kdsatker."' ";
                if($key->kdsatker == $selected){
                    $all .= 'selected';
                }
                $all .= ">".$key->kdsatker.'. '.$key->nmsatker."</option>";
            }
        $return .= $all;
        $return .= '</select>';

        return $return;
    }


    public static function getKk($kode1,$kode){
        		
		if($kode1=='kanwil'){
			$sql= "select * from ref_pembinaan_kk_template where is_for='1' and status='1'";
		}else{
			$sql="select * from ref_pembinaan_kk_template where is_for='0' and status='1'";
		}
		

        $query = DB::table(DB::raw("($sql) a"))
                    ->orderBy('template_name', 'asc');
		
		$query = $query->get();
		
        $group = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="id_template" id="id_template">';
		foreach ($query as $key) {
			  $group .= "<option value='".$key->id."' ";
			  if($key->id == $kode ){
				  $group .= "selected";
			  }
			  $group .= ">".$key->template_name."</option>";
		}
		$group .= '</select>';
		return $group;
    }

	
	public static function getSatkerBluPenetapan($group, $selected = 0, $required = false){
        $satker     = "select kddept, kdsatker, nmsatker,stsdipa from t_satker_16 where kdsatker not in (select kdsatker from t_satker_blu where status=1)
                        ";

        $query = DB::table(DB::raw("($satker) a"))
                    ->where('stsdipa', '1')
                    ->orderBy('kdsatker', 'asc');

        $query = PublicFunction::WhereGroup($group,$query);

        $query = $query->get();
        //dd($query);
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="kdsatker" id="kdsatker" ';
        if($required){
            $return .= 'required ';             
        }
        $return .= '>';
            $all = '';
            if($query->count() > 1){
                $all = '<option value="">Select one</option>';
            }
            foreach ($query as $key) {
                $all .= "<option value='".$key->kdsatker."' ";
                if($key->kdsatker == $selected){
                    $all .= 'selected';
                }
                $all .= ">".$key->kdsatker.'. '.$key->nmsatker."</option>";
            }
        $return .= $all;
        $return .= '</select>';

        return $return;
    }
	
	
	
    public function PeriodeLk($d){

        if ($d=='01'){
            return 'JANUARI';
        }elseif($d=='02'){
            return 'FEBRUARI';
        }elseif($d=='03'){
            return 'MARET';
        }elseif($d=='04'){
            return 'APRIL';
        }elseif($d=='05'){
            return 'MEI';
        }elseif($d=='06'){
            return 'JUNI';
        }elseif($d=='07'){
            return 'JULI';
        }elseif($d=='08'){
            return 'AGUSTUS';
        }elseif($d=='09'){
            return 'SEPTEMBER';
        }elseif($d=='10'){
            return 'OKTOBER';
        }elseif($d=='11'){
            return 'NOVEMBER';
        }elseif($d=='12'){
            return 'DESEMBER';
        }else{
            return 'ERROR!';
        }
    }
	
	public function PeriodeLkPer($d){

        if ($d=='01'){
            return '31 JANUARI';
        }elseif($d=='02'){
            return '28 FEBRUARI';
        }elseif($d=='03'){
            return '31 MARET';
        }elseif($d=='04'){
            return '30 APRIL';
        }elseif($d=='05'){
            return '31 MEI';
        }elseif($d=='06'){
            return '30 JUNI';
        }elseif($d=='07'){
            return '31 JULI';
        }elseif($d=='08'){
            return '31 AGUSTUS';
        }elseif($d=='09'){
            return '30 SEPTEMBER';
        }elseif($d=='10'){
            return '31 OKTOBER';
        }elseif($d=='11'){
            return '30 NOVEMBER';
        }elseif($d=='12'){
            return '31 DESEMBER';
        }else{
            return 'ERROR!';
        }
    }


    public static function getStatusPengajuan($group, $selected = 0, $required = false){
        $satker     = "SELECT * FROM t_status_peserta WHERE status IN ('14','21','22','23')
                        ";

        $query = DB::table(DB::raw("($satker) a"))
                    //->where('status', '1')
                    ->orderBy('status', 'asc');

        $query = PublicFunction::WhereGroup($group,$query);

        $query = $query->get();
        //dd($query);
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="status" id="status" ';
        if($required){
            $return .= 'required ';             
        }
        $return .= '>';
            $all = '';
            if($query->count() > 1){
                $all = '<option value="xx">Select one</option>';
            }
            foreach ($query as $key) {
                $all .= "<option value='".$key->status."' ";
                if($key->status == $selected){
                    $all .= 'selected';
                }
                $all .= ">".$key->status.'. '.$key->nmstatus."</option>";
            }
        $return .= $all;
        $return .= '</select>';

        return $return;
    }
	
	public static function pegPbnOpen($groups, $id, $selected = 0, $kddept = 'z', $filter = true){
        $return = '<select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="nip1" id="nip1">';
		$all = "<option value='0'>All</option>";
		$user_group = DB::table('user_group')
						->where('id', '=', $id);

		$user_group = $user_group->first();

		$sql = $user_group->ref_table;

		if($user_group->kolom == 'kdsatker'){
			$sql = " select a.*, b.kdkanwil 
				from $user_group->ref_table a 
				left join t_kppn b on a.kdkppn = b.kdkppn
				 ";

			if($kddept != 'z'){
				$sql .= " where a.kddept = ? and a.status = '1'";
			}
		}

		$ref_table = DB::table(DB::raw("($sql) a"))
						->orderBy($user_group->kolom, 'asc');
		if($user_group->kolom == 'kdsatker' and $kddept != 'z'){
			$ref_table = $ref_table->setBindings([$kddept]);
		}
		
		if($filter){
			$ref_table = PublicFunction::WhereGroup($groups, $ref_table);
		}

		if($ref_table->count() <= 1){
			$all = "";
		}

		$ref_table = $ref_table->get();
		
		foreach ($ref_table as $key) {
			$array =  (array) $key;
			$all .= "<option value='".$array[$user_group->kolom]."' ";
			
			$rows = DB::select("
				select	*
				from t_kantor
				where kdkantor=?
			",[
				$array[$user_group->kolom]
			]);
			
			if(count($rows)>0){
				
				foreach($rows as $row){
					
					$punit = strlen($row->idunit);
					
					$rows1 = DB::connection('mysql')->select("
						SELECT	a.nip,
								a.nama
						FROM pbn_emp.dt_emp a
						WHERE a.active='y' AND a.status < 600 AND substr(a.unit,1,".$punit.")=?
						ORDER BY a.nama ASC
					",[
						$row->idunit
					]);
					
					foreach($rows1 as $row1){
						$return .= '<option value="'.$row1->nip.'">'.$row1->nip.' - '.$row1->nama.'</option>';
					}
					
				}
				
			}
			
		}
		
		$return .= '</select>';
		
		return $return;
		
    }
	
}
