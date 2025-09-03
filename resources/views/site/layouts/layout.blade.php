
<!DOCTYPE html>
 
 
<html lang="{{ config('app.locale') }}"   >
 
 
 
  @include('site.layouts.head') 
  @include('site.layouts.header') 
  @yield('content')
  @include('site.layouts.footer')
 
 
</html>
