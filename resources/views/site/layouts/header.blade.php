<body>
    <!-- HEADER -->
    <header>
    

     
    </header>
    <!-- /HEADER -->

    <!-- NAVIGATION -->
    <nav id="navigation">
        <!-- container -->
        <div class="container">
            <!-- responsive-nav -->
            <div id="responsive-nav">
                <!-- NAV -->
                <ul class="main-nav nav navbar-nav">
                    <li class="active"><a href="#">Home</a></li>
                    @foreach ($pages as $page )
                    <li  ><a href="{{ route('site.pages',$page->code) }}">{{ $page->name }}</a></li>
                    @endforeach
                    
                </ul>
                <!-- /NAV -->
            </div>

            <!-- /responsive-nav -->
        </div>
        <!-- /container -->
    </nav>
    <!-- /NAVIGATION -->
