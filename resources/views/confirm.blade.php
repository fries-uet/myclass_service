@extends('layouts.wrapper')

@section('head')
    <title>Xác nhận đăng ký</title>
@endsection

@section('head.style')
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/style.css"/>
@endsection
<?php
?>

@section('body')
    <div class="subscriber" style="padding-top: 50px;">
    </div>
    <div class="container">
        @if(count($errors) > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger errors">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Đã có lỗi xảy ra!</strong>
                        <ul>
                            @foreach($errors->all() as $key => $error)
                                <li class="{{ $key }}">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Thành công!</strong> Bạn đã đăng ký thành công.<br>
                        Enjoy it!
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('body.script')
    <?php enqueueScript(url('/') . '/assets/js/jquery.min.js', '1.11.3'); ?>
    <?php enqueueScript(url('/') . '/assets/js/bootstrap.min.js', '3.3.6'); ?>
@endsection
