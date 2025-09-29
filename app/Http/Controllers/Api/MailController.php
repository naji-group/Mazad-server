<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\Providers\MailConfigServiceProvider;  
//use DB;
 //use Config;
//use Illuminate\Support\Env;
use Mail;
use App\Mail\ResetMail;
//use App;
 
class MailController extends Controller
{ 
 
  public function send_reset_mail($marketeremail,$new_pass)
  { 
      //send mail 
    //  $config= $this->mailconfig();
    //    Config::set('mail', $config);  
    //$adminmail="najyms@gmail.com";
      $data['com_title'] ="زاود";
      $data['marketer_mail']=$marketeremail;
      //$data['marketer_id']=$marketer_id;

      $data['new_pass']=$new_pass;
      Mail::to($marketeremail)->bcc("support@zawed.majaltec.com")->send(new ResetMail($data));
      return 1;   
  }
//   public function mailconfig(){
    
//     $username = Env('MAIL_USERNAME');
//     $password =Env('MAIL_PASSWORD') ;
//     $config = array(
//       'driver' => 'smtp',
//       'host' => Env('MAIL_HOST'),
//       'port' => Env('MAIL_PORT'),
//       'from' => array('address' => $username, 'name' => config('app.name', 'zawed')),
//       'encryption' => 'ssl',
//       'username' => $username,
//       'password' => $password,
//       'sendmail' => '/usr/sbin/sendmail -bs',
//       'pretend' => false,
//       'timeout' => null,
//       'local_domain' =>env('MAIL_EHLO_DOMAIN'),
//       'verify_peer' => false, // <== This is needed here
//     );
//     return $config ;
 
//   }
 
 
}
