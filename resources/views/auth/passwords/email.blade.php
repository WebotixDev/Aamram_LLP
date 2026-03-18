@extends('layouts.authentication.master')

@section('css')
@endsection

@section('main_content')
    <!-- loader starts-->
    <div class="loader-wrapper">
        <div class="loader">
            <div class="loader4"></div>
        </div>
    </div>
    <!-- loader ends-->
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="login-card login-dark">
                        <div>
                            <div><a class="logo" href="{{ route('admin.dashboard') }}"> <img class="img-fluid for-dark"
                                        src="{{ asset('assets/images/logo/logo.png') }}" alt="looginpage"><img
                                        class="img-fluid for-light" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                                        alt="looginpage"></a></div>
                            <div class="login-main">
                                @if (session('status'))
                                    <div class="alert alert-primary p-2" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                <form class="theme-form" method="POST" action="{{ route('password.email') }}">
                                    @csrf
                                    <h4 class="mb-3">Reset Your Password</h4>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-12">
                                                <input class="form-control mb-1 @error('email') is-invalid @enderror" type="email" name="email"  value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Test@gmail.com">
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <div class="text-end mt-2">
                                                    <button class="btn btn-primary" type="submit" style="inline-size: -webkit-fill-available;">Send Password Reset Link</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <a href="{{ route('login') }}" class="text-center">
                                    <p><i class="fa fa-long-arrow-left"></i>
                                    Back to Login Page</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function ($) {
        "use strict";
        $(document).ready(function() {
            $('#resetForm').validate({
                rules: {
                    email: {
                        required: true,
                    },
                    password: {
                        required: true,
                    },
                    confirm_password:{
                        required: true,
                        equalTo: "#password"
                    }
                }
            });
        });
    })(jQuery);
</script>
@endpush
