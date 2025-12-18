<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $pages=Setting::where('category','home')->get();
$breadcrumb='Home';
        return view('site.home',compact(['pages','breadcrumb']));
    }
    public function static_page($slug)
    {
        $page=Setting::where('category','pages')->where('code',$slug)->first();
        if($page){
            return view('site.static-page',compact('page'));
        }else{
            return response("",404);
        }
       
    }
    public function pages_menu()
    {
        $pages=Setting::where('category','pages')->select('id','name',
 
'category',
'sequence',
'code')->get();
      return $pages;
       
    }
}
