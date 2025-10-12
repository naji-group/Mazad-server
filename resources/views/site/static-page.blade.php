@extends('site.layouts.layout')
 @section('content')
 
	

	<!-- BREADCRUMB -->
    <div id="breadcrumb" class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <h3 class="breadcrumb-header"></h3>
                    <ul class="breadcrumb-tree">
                        <li><a href="#">{{$page->name}}</a></li>
                      
                    </ul>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /BREADCRUMB -->

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container" style="direction: rtl">
            <!-- row -->
            <div class="row">
                {{ Str::of($page->value )->toHtmlString() }}  
               
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

	
	 



 @endsection
 