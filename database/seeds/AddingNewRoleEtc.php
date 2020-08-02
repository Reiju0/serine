<?php

use Illuminate\Database\Seeder;

class AddingNewRoleEtc extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
    	
		DB::table('roles')->updateOrInsert(
                ['slug' => 'OprKPPN'],
                [
					'name'	 => 'OprKPPN',
					'group_id'  => 0,
					'permissions' => '{"Monitoring.ReadKPPN":true,"Todo.Admin":true,"Todo.Read":true}',
					'admin_of'   	 => '{}'
				]
        );
		
		DB::table('permission')->updateOrInsert(['module' => 'Todo','permission'	 => 'Todo.Read']);
		DB::table('permission')->updateOrInsert(['module' => 'Todo','permission'	 => 'Todo.Admin']);
		DB::table('permission')->updateOrInsert(['module' => 'Monitoring','permission'	 => 'Monitoring.ReadKPPN']);
			
		DB::table('menu')->updateOrInsert(
		        ['menu' => 'To Do','is_parent'	 => 1,
				    'parent_id'  => 0,
					'permission' => 'Todo.Admin,Todo.Read,admin',
					'status'   	 => 1,
					'have_link'	 => 1,
					'path'	     => 'todo',
					'style'	     => 'fa fa-check',
					'delta'   	 => 1
				]
		);
		
		DB::table('menu')->updateOrInsert(
                [   'menu' => 'Monitoring',
					'is_parent'	 => 1,
					'parent_id'  => 0,
					'permission' => 'Monitoring.ReadKPPN,admin',
					'status'   	 => 1,
					'have_link'	 => 1,
					'path'	     => 'monitoring/per_satker',
					'style'	     => 'fa fa-eye',
					'delta'   	 => 1
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 1,
					'jenis'	     => 'GUP',
					'status'     => 1,
					'uid' 		 => 1
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 2,
					'jenis'	     => 'Laporan KKP',
					'status'     => 1,
					'uid' 		 => 2
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 3,
					'jenis'	     => 'SKPP',
					'status'     => 1,
					'uid' 		 => 3
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 4,
					'jenis'	     => 'Laporan Saldo',
					'status'     => 1,
					'uid' 		 => 4
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 5,
					'jenis'	     => 'BAR Rekening',
					'status'     => 1,
					'uid' 		 => 5
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 6,
					'jenis'	     => 'Retur',
					'status'     => 1,
					'uid' 		 => 6
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 7,
					'jenis'	     => 'Konfirmasi',
					'status'     => 1,
					'uid' 		 => 7
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 8,
					'jenis'	     => 'Capaian Output',
					'status'     => 1,
					'uid' 		 => 8
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 9,
					'jenis'	     => 'LPJ',
					'status'     => 1,
					'uid' 		 => 9
				]
        );
		
		DB::table('ref_jenis')->updateOrInsert(
                [   'kdjenis'    => 10,
					'jenis'	     => 'Survei Best FO',
					'status'     => 1,
					'uid' 		 => 10
				]
        );
		
		DB::commit();
    }
}
