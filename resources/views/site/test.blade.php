@extends('site.layouts.layout')

 
@section('page-title')
الاشعارات
@endsection
@section('css')
@endsection

@section('content')
<div id="breadcrumb" class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<div class="row">
			<div class="col-md-12">
				<h3 class="breadcrumb-header"></h3>
				<ul class="breadcrumb-tree">
					<li><a href="#">الاشعارات</a></li>
				  
				</ul>
			</div>
		</div>
		<!-- /row -->
	</div>
	<!-- /container -->
</div>

<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<div class="row">
			@php
		//	date_default_timezone_set('Asia/Dubai');
			echo  ini_get('date.timezone');
			$time='2025-11-18 01:26:28';
			$timeDubai = Carbon\Carbon::parse($time)->timezone('Asia/Dubai')->toIso8601String();;
//$timeUtc = $timeDubai->copy()->timezone('UTC');

//$newdate = $timeUtc->toIso8601String();
echo"New date:";
echo $timeDubai ;

			 @endphp
			  <br/>
			 now:
			  {{ now() }}
			  <br/>
			  now timezone:
			  {{ now()->timezone('Asia/Dubai') }}
			  <br/>
			 @foreach ($pages as  $page)
			 {{ $page->created_at }}
			 <br/>
			 {{ $page->created_at->timezone('Asia/Dubai') }}
			 <br/>
			 @endforeach
		</div>
		<div class="row">
			<button class="btn  btn-primary" id="btn_sendToken"  >Save token</button>
			<br/>
			<div >
				<textarea id="tokenVal"></textarea>
			</div>
			<div id="msg"></div>
			<form action="{{ url('sendNotification') }}" name="send-notify-form" id="send-notify-form" method="POST">
				@csrf
				<div class="form-group">
					<label>Message Title</label>
					<input type="text" class="form-control" name="title">
				</div>
				<div class="form-group">
					<label>Message Body</label>
					<textarea class="form-control" name="body"></textarea>
				</div>
				<button type="submit" id="btn-send-notify" name="btn-send-notify" class="btn btn-success btn-block">Send Notification</button>
			</form>
			 
					<div class="row row-sm">
						<div class="col">
							<div class="card  box-shadow-0">
								<div class="card-header">
									<h4 class="card-title mb-1">Send By Token</h4>
									<p class="mb-2 text-danger" id="msgtoken"></p>
								</div>
								<div class="card-body row pt-0">
									<div class="col-lg-8">
										<form action="{{ url('sendbytoken')}}" name="send-withtoken-form" id="send-withtoken-form" method="POST">
											@csrf
											<div class="form-group">
												<label>Token</label>
											 
												 
													<textarea class="form-control"   rows="3" name="input_token"></textarea>
												 
											</div>
											<div class="form-group">
												<label>marketer id</label>
												<input type="text" class="form-control" name="marketer_id">
											</div>
											<div class="form-group">
												<label>Message Title</label>
												<input type="text" class="form-control" name="title">
											</div>
											<div class="form-group">
												<label>customer name</label>
												<input type="text" class="form-control" name="customer_name">
											</div>
											<div class="form-group">
												<label>Message Body</label>
												<textarea class="form-control" name="body"></textarea>
											</div>
											<div class="form-group">
												<label>social</label>
												<textarea class="form-control" name="social"></textarea>
											</div>
											<div class="form-group">
												<label>social id</label>
												<input type="text" class="form-control" name="social_id">
											</div>
											<button type="submit" id="btn-send-withtoken" name="btn-send-withtoken" class="btn btn-success btn-block">Send Notification</button>
										</form>
								</div> 
			
								</div>
							</div>
						</div>
					</div>
			

		</div>
		<!-- /row -->
	</div>
	<!-- /container -->
</div>


@endsection
@section('js')


<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>
<script >
	// Import the functions you need from the SDKs you need 8.10.1  12.3.0
	//import { initializeApp } from "https://www.gstatic.com/firebasejs/12.3.0/firebase-app.js";
	// TODO: Add SDKs for Firebase products that you want to use
	// https://firebase.google.com/docs/web/setup#available-libraries
  
	// Your web app's Firebase configuration
	const firebaseConfig = {
	  apiKey: "AIzaSyCQlGu4IJQsHeX57ebTtGcnGgJgF5Enur0",
	  authDomain: "zawed-app.firebaseapp.com",
	  projectId: "zawed-app",
	  storageBucket: "zawed-app.firebasestorage.app",
	  messagingSenderId: "186501874471",
	  appId: "1:186501874471:web:dfddcd329d5c18772a5f02"
	};
  
	// Initialize Firebase
	//const app = initializeApp(firebaseConfig);

	firebase.initializeApp(firebaseConfig);

  </script>

<script >
	$(document).ready(function () {
	$('#btn_sendToken').on('click', function () {
		sendToken();
	});
});
  
	 
    const messaging = firebase.messaging();
 function sendToken(){
	messaging
            .requestPermission()
            .then(function() {
                return messaging.getToken()
 }) .then(function(value) {
	alert(value);
$("#tokenVal").val(value);

$.ajax({
                    url: '{{ url("saveToken") }}',
                    type: 'POST',
					headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {token: value },
                   
                    success: function(res) {
                        alert(res);
                    },
                    error: function(error) {
 
                        alert(error);
                    },
                });
			}).catch(function(error) {
                alert(error);
            });
 }
 
    
 
</script>
 
<script src="{{URL::asset('site/js/notify.js')}}"></script>
 
@endsection