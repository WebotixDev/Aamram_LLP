@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/date-picker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/dropzone.css') }}">
@endsection


<div class="container">
    <div class="row">
        <form method="POST" action="{{ route('get-Districts') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="state">Select State</label>
                <select class="form-control" name="state_id" id="state">
                    <option value="">Select State</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="district_name">District</label>
                <input type="text" class="form-control" id="district_name" name="district_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
