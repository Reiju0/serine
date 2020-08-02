<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use PublicFunction;
use DataTables;
use DB;

class DashboardController extends Controller
{
    //switcher
	public function switcher(Request $request)
    {
        $permission = PublicFunction::permission($request, array('Todo.Admin'));
        if ($permission['response'] == true)
        {
			$s_rekap_today="SELECT ifnull(COUNT(*),0) total, ifnull(SUM(STATUS),0) selesai, ifnull(COUNT(*)- SUM(STATUS),0) progress
									FROM todo
							WHERE due_date= CURDATE() AND deleted_at=0";
			$q_rekap_today = DB::table(DB::raw("($s_rekap_today) a"))
			                 ->first();
					 
			$s_today="SELECT   jumlah,
						       selesai,
						       jumlah-selesai progress,
						       description,
						       jenis,
							   date_format(due_date,'%d-%m-%Y') deadline 
								   FROM (
										SELECT ifnull(COUNT(*),0) jumlah,
											   if(is_mass=0,kdsatker,0) kdsatker,
												 kdjenis,
												 priority,
												 title,
												 description,
												 due_date,
												 is_mass,
												 mass_id,
												 id,
												 ifnull(SUM(STATUS),0) selesai
										FROM todo where deleted_at=0 AND due_date=CURDATE() GROUP BY mass_id) a
										LEFT JOIN ref_jenis b ON a.kdjenis=b.kdjenis
										LEFT JOIN ref_satker c ON a.kdsatker=c.kdsatker";
			$q_today = DB::table(DB::raw("($s_today) a"))
			         ->get();
			
			$s_rank="SELECT *
                        FROM (
                               SELECT COUNT(*) jumlah,
 							          DATE_FORMAT(due_date,'%d-%m-%Y') tanggal
                                FROM todo
                                    WHERE deleted_at=0
                                GROUP BY due_date) a
                        ORDER BY jumlah DESC LIMIT 7";
						
			$q_rank = DB::table(DB::raw("($s_rank) a"))
			         ->get();
					 
			$data=['today' => $q_today,'rank' => $q_rank,'rekap' => $q_rekap_today];
			
			return view('dashboard::kppn',$data);
		}else{
			return redirect('dashboard/satker');
		}
	}
	
	
    public function index()
    {
        return view('dashboard::index');
    }
}

?>
