<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PublicFunction;
use Cartalyst\Sentinel\Native\Facades\Sentinel;


class HomeController extends Controller
{
    //

    public function __construct(){
        //$this->middleware('permission:Core.Menu');
        //$this->middleware('ajax');
    }

    public function index(){
    		return redirect()->guest('login');
    }

    public function View(Request $request)
    {
        //dd($request->user);
        $menu = '
            <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">';

        $query = DB::table('menu')
                    ->where('is_parent', '=', '1')
                    ->where('parent_id', '0')
                    ->where('status', '=', '1')
                    ->orderBy('delta', 'asc')
                    ->get();


        foreach ($query as $item) {
            # code...
            $permission = PublicFunction::permission($request, explode(",", $item->permission));
            //dd($permission);
            if ($permission['response'] == true)
            {
                    if($item->have_link == '1'){

                        $menu .= '<li class=" nav-item"><a href="javascript:ajaxLoad(\''.url('/'.$item->path).'\')">
                                    <i class="'.$item->style.'"></i>
                                      <span data-i18n="" class="menu-title">'.$item->menu.'
                                        </span>
                                        </a>
                                        </li>';
                    }else{

                        $menu .= '<li class="has-sub nav-item"><a href="#">';

                        $menu .= '<i class="'.$item->style.'"></i><span data-i18n="" class="menu-title">'.$item->menu.'</span>
                            </a>
                            <ul class="menu-content">';
                        $child = DB::table('menu')
                                    ->where('parent_id', '=', $item->id)
                                    ->where('status', '=', '1')
                                    ->orderBy('delta', 'asc')
                                    ->get();
                        foreach ($child as $key) {
                            $permission = PublicFunction::permission($request, explode(",", $key->permission));
                            if ($permission['response'] == true)
                            {
                                if($key->have_link == '1'){
                                    $menu .= '<li><a href="javascript:ajaxLoad(\''.url('/'.$key->path).'\')"><i class="'.$key->style.'"></i>'.$key->menu.'</a></li>';
                                }else{
                                    //$menu .= '<li><i class="'.$key->style.'"></i>'.$key->menu.'</li>';
                                    $queryb = DB::table('menu')
                                            ->where('parent_id', $key->id)
                                            ->where('status', '=', '1')
                                            ->orderBy('delta', 'asc')
                                            ->get();

                                    $menu .= '<li class="treeview">';
                                    $menu .= '<a>';

                                    $menu .= '<i class="'.$key->style.'"></i><span>'.$key->menu.'</span> <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span></a>
                                    <ul class="treeview-menu">';

                                    foreach ($queryb as $itemb) {
                                        # code...
                                        $permission = PublicFunction::permission($request, explode(",", $itemb->permission));
                                        //dd($permission);
                                        if ($permission['response'] == true)
                                        {
                                            if($itemb->have_link == '1'){
                                                $menu .= '<li><a href="javascript:ajaxLoad(\''.url('/'.$itemb->path).'\')"><i class="'.$itemb->style.'"></i>'.$itemb->menu.'</a></li>';
                                            }else{
                                                $menu .= '<li><i class="'.$itemb->style.'"></i>'.$itemb->menu.'</li>';
                                            }


                                        }
                                    }
                                    $menu .= '</ul></li>';

                                }
                            }
                        }
                        $menu .= '</ul>';
                    }

                $menu .= '
                    </li>
            ';
            }

        }

        $menu .= '
                </ul>
        ';

        $permission = PublicFunction::permission($request, array('admin'));
        if ($permission['response'] == true)
        {
            $menu .= '
                    <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
                    <li class="has-sub nav-item"><a href="#"><i class="ft-home"></i><span data-i18n="" class="menu-title">Admin Menu</span></a>
                                <ul class="menu-content">

                                    <li><a href="javascript:ajaxLoad(\''.url('/role').'\')"><i class="fa fa-check-circle-o"></i> Role</a></li>
                                                <li><a href="javascript:ajaxLoad(\''.url('/permission').'\')"><i class="fa fa fa-check-square-o"></i> Permission</a></li>
                                                <li><a href="javascript:ajaxLoad(\''.url('/menu').'\')"><i class="fa fa-th-list"></i> Menu</a></li>
                                                
                                </ul>
                            </li>
                        </ul>

                        ';

                        //<li><a href="javascript:ajaxLoad(\''.url('/option').'\')"><i class="fa fa-cogs"></i> Option</a></li>
        }

        //$user = $request->user;
        $user   = Sentinel::check();
        $group_id = json_decode($user->group_id);
        $val = json_decode($user->group_value);
        $pic = $user->foto;

        $foto = "/images/avatar_noimage.png";

         if(isset($group_id)){
            if(($group_id==1)||($group_id==2)||($group_id==3)){
                $user_group = DB::table('user_group')->where('id', '=',$group_id)->first();
                $table = DB::table($user_group->ref_table)->where($user_group->kolom,'=', $val)->first();
                    if(isset($table->kddept)){
                    $foto = "/images/logo-kl/".$table->kddept.".jpg";
                    }
                }elseif($group_id==5){
                    $foto = "/images/logo-kemenkeu/kppn.jpg";
                }elseif($group_id==6){
                    $foto = "images/logo-kemenkeu/kanwildjpbn.jpg";
                }
            }
        else{
                $foto ="/images/logo-kl/015.jpg";
        }

        return view('home',['menu' => $menu, 'user' => $user, 'foto' => $foto]);
    }

}
