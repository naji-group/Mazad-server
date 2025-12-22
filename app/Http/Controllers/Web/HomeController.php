<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $breadcrumb='Home';
        $pages=Setting::where('category','home')->orderBy('sequence')->get();
        if($pages){
if($pages->first()){
    $breadcrumb=$pages->first()->name;
}
        }

 
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
    public function home_name()
    {
        $breadcrumb='';
    $page=Setting::where('category','home')->orderBy('sequence')->first();
    if($page){
        $breadcrumb=$page->name;
                }
                return   $breadcrumb;
}
}
