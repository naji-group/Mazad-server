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
        <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="text-center">
                        <i class="fa fa-user-plus fa-2x mb-2"></i>
                        <h4 class="mb-0">تسجيل مسوق جديد</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('marketer.register') }}">
                        @csrf

                        <!-- حقل الاسم -->
                        <div class="row mb-3">
                            <label for="username" class="col-md-4 col-form-label text-md-end">
                                <i class="fa fa-user me-2"></i>اسم المستخدم
                            </label>
                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" 
                                       name="username" value="{{ old('username') }}" required autocomplete="username" autofocus
                                       placeholder="أدخل الاسم الكامل">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- حقل البريد الإلكتروني -->
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">
                                <i class="fa fa-envelope me-2"></i>البريد الإلكتروني
                            </label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required autocomplete="email"
                                       placeholder="example@email.com">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    

                        <!-- حقل كلمة المرور -->
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">
                                <i class="fa fa-lock me-2"></i>كلمة المرور
                            </label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="new-password"
                                       placeholder="أدخل كلمة المرور">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- حقل تأكيد كلمة المرور -->
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">
                                <i class="fa fa-lock me-2"></i>تأكيد كلمة المرور
                            </label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" 
                                       name="password_confirmation" required autocomplete="new-password"
                                       placeholder="أعد إدخال كلمة المرور">
                            </div>
                        </div>

                        <!-- زر التسجيل -->
                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-user-plus me-2"></i>
                                    {{ __('تسجيل حساب جديد') }}
                                </button>
                            </div>
                        </div>

                        <!-- رابط تسجيل الدخول -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <p class="mb-0">
                                    لديك حساب بالفعل؟
                                    <a href="{{ route('marketer.login') }}" class="text-decoration-none fw-bold">
                                        سجل الدخول من هنا
                                    </a>
                                </p>
                            </div>
                        </div>
                    </form>
                <div>
                    <a href="{{ url('/auth/redirect') }}">انشاء حساب باستخدام Google</a>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

	
	 



 @endsection
 