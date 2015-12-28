@extends('layouts.wrapper')

@section('head')
    <title>Gửi lại email xác nhận đăng ký</title>
@endsection

@section('head.style')
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/style.css"/>
@endsection
<?php
$email = $data['email'];
?>

@section('body')
    <div class="subscriber" style="padding-top: 50px;">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <form action="{{ route('reconfirm') }}" data-toggle="validator" method="POST">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="email">Email:</label>

                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                            <input type="email" name="email" class="form-control" id="email"
                                   data-error="Email chưa hợp lệ."
                                   required value="{{ $email or '' }}">
                        </div>
                        <div class="help-block with-errors"></div>
                    </div>

                    @include('api.recapcha')

                    <div class="form-group">
                        <button class="btn btn-success btn-block" type="submit">Gửi lại</button>

                        <div style="padding-top: 20px">
                            <a href="{{ route('subscribe') }}">Quay lại</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @if(count($errors) > 0)
                    <div class="alert alert-danger errors">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Đã có lỗi xảy ra!</strong>
                        <ul>
                            @foreach($errors->all() as $key => $error)
                                <li class="{{ $key }}">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@section('body.script')
    <?php enqueueScript('//www.google.com/recaptcha/api.js'); ?>
    <?php enqueueScript(url('/') . '/assets/js/jquery.min.js', '1.11.3'); ?>
    <?php enqueueScript(url('/') . '/assets/js/bootstrap.min.js', '3.3.6'); ?>
@endsection
