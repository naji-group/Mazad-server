<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
// use App\Models\Marketer;
 
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
// use File;
// use Illuminate\Support\Carbon;
// use Intervention\Image\ImageManager;
// use Intervention\Image\Drivers\Gd\Driver;
// use PhpParser\Node\Expr\Cast\Object_;
// use Ramsey\Uuid\Type\Decimal;

class HelpController extends Controller
{


  public $path = [];
  // public $iconpath = []; 
  // private $defaultimage = "default.png";
  // private $defaultsvg = "default.svg";
  
 
  public function __construct()
  {  
    //experts
    $this->path['marketers'] = 'images/marketers'; 
      
  }
  /**
   * Display a listing of the resource.
   */

   
   public function getlocalpath($subpath)
   {  
    
     $url=config('filesystems.disks.public.url').'/'.$subpath. '/';      
         return $url;
   }

   public function getdefaultbyCode($code)
   {  
   $setting= Setting::where('code',$code)->select('id','code','image')->first();

     $url=config('filesystems.disks.public.url').'/'.$setting?->image;      
         return $url;
   }
   public function getpublicurl($subpath)
   {  
    
     $url=config('filesystems.disks.public.url').'/'.$subpath;      
         return $url;
   }
   
  public function MarketerPath()
  { //image record
      $url =  $this->getlocalpath($this->path['marketers']);
          return $url;

  } 
  //
    
  public  function changemod($filePath)
  { 
  chmod($filePath, 0755);  
  chmod(dirname($filePath), 0755);  
  }
  public function offset_timezone($time,$newtimezone)
  {      

      $timezone = new CarbonTimeZone($newtimezone);
      $utcNow = Carbon::now('UTC');
      $localNow = $utcNow->copy()->setTimezone($timezone);
      
      // فرق التوقيت بالدقائق
      $diffInMinutes = (int)($localNow->utcOffset());
      
      $comment_time = Carbon::parse($time);
      
      // إضافة فرق التوقيت
      $adjustedCommentTime = $comment_time->addMinutes($diffInMinutes);
      
      // اختبار النتيجة
      return $adjustedCommentTime;
  }

  public function date_diff($start_date=null,$end_date=null)
  {
      // إذا كان لدينا تاريخ البدء والانتهاء
      if ($start_date && $end_date) {
          $start = Carbon::parse($start_date );
          $end = Carbon::parse($end_date);
          
          // حساب المدة بالثواني
          $durationSeconds = $start->diffInSeconds($end);
          
          // تحويل إلى HH:mm:ss
          $hours = floor($durationSeconds / 3600);
          $minutes = floor(($durationSeconds % 3600) / 60);
          $seconds = $durationSeconds % 60;
          $durationFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
          
          return [
            'duration_seconds'=>   $durationSeconds ,
            'duration_str'=>$durationFormatted,
          ];
      }
      return [
        'duration_seconds'=>  0,
        'duration_str'=>"0",
      ];
     
  }

}
