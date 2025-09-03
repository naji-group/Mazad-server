@extends('site.layouts.layout')
 @section('content')
 
	

	<!-- BREADCRUMB -->
    <div id="breadcrumb" class="section">
        <!-- container -->
       
        <!-- /container -->
    </div>
    <!-- /BREADCRUMB -->

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="text-center">
        @if (Auth::guard('web_marketers')->check()) 
        {{ Auth::guard('web_marketers')->user()->username }} 
     {{ Auth::guard('web_marketers')->user()->id }}
     {{ Auth::guard('web_marketers')->user()->image }}
     
@endif
</div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

	
	 



 @endsection
 