@extends('layouts.authentication.master')

@section('css')
@endsection

@section('main_content')
    <!-- login page start-->
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card login-dark">
                    <div>
                        <div>
                        @php
                            $logo = DB::table('company')->value('logo');
                        
                        @endphp
                            <a class="logo" href="{{ route('admin.dashboard') }}">
                            <img src="{{ asset('public/' . $logo) }}" style="height: 80px; width: 200px;" alt="Company Logo">
                            </a>
                        </div>
                        <div class="login-main">
                            <form class="theme-form" method="POST" action="{{ route('login') }}">
                                @csrf
                                <!-- <h4>Sign in to account </h4>
                                <p>Enter your email & password to login</p> -->
                                <div class="form-group">

                                    <label class="col-form-label">Mobile Number</label>
                                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="" placeholder="Enter Mobile Number" required autocomplete="phone" autofocus>
                                    
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-form-label">Password </label>
                                    <div class="form-input position-relative">
                                            <input id="password" type="password"  class="form-control @error('password') is-invalid @enderror" name="password" value="123456789"  placeholder="Enter password" required autocomplete="current-password">
                                        <div class="show-hide"> <span class="show"></span></div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group mb-0 text-end">
                                    @if (Route::has('password.request'))
                                        <a class="checkbox1" href="{{ route('password.request') }}">Forgot password?</a>
                                    @endif
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-block w-100" type="submit">Sign in</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection