
<!DOCTYPE html>
 
 
<html lang="{{ config('app.locale') }}"   >
  @php
  $homeCtrlr=new App\Http\Controllers\Web\HomeController();
    $pages=$homeCtrlr->pages_menu();
 @endphp
 
  @include('site.layouts.head') 
  @include('site.layouts.header') 
  @yield('content')
  @include('site.layouts.footer')
 
 
</html>
