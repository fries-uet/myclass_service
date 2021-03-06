@extends('layouts.wrapper')

@section('head')
    <title>Đăng ký nhận kết quả thi học kì</title>
@endsection

@section('head.style')
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/style.css?v=1.2.0"/>
@endsection
<?php
$email = $data['data']['email'];
$msv = $data['data']['msv'];
$count_subject = $data['count_subject'];
$count_user = $data['count_user'];
?>

@section('body')
    <div class="container text-center" style="padding-top: 50px;">
        <h1>Công cụ hóng điểm thi chính xác và nhanh nhất!</h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Đăng ký</h2>

                <form action="{{ route('subscribe') }}" data-toggle="validator" method="POST">
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="form_msv">
                                <label for="msv">Mã số Sinh Viên:</label>

                                <div class="input-group">
                                <span class="input-group-addon"><span
                                            class="glyphicon glyphicon-education"></span></span>
                                    <input type="number" name="msv" id="msv" class="form-control"
                                           data-remote="{{ route('getInfo') }}"
                                           data-error="Mã số sinh viên không đúng! Hoặc đã có vấn đề gì đó đã xảy ra."
                                           data-minlength="8" maxlength="8" value="{{ $msv }}">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Họ và tên:</label>
                                <input type="text" name="name" id="name" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>

                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                            <input type="email" name="email" class="form-control" id="email"
                                   data-error="Email chưa hợp lệ."
                                   required value="{{ $email }}">
                        </div>
                        <div class="help-block with-errors"></div>
                    </div>

                    @include('api.recapcha')

                    <div class="form-group">
                        <button class="btn btn-success btn-block" type="submit">Đăng ký</button>
                        <div class="reconfirm text-center" style="padding-top: 20px">
                            <a href="{{ route('reconfirm') }}">Xác nhận lại</a>
                        </div>
                    </div>

                </form>
            </div>
            <div class="col-md-6">
                <h2>Danh sách môn thi</h2>
                <table class="table" id="timetable">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã LMH</th>
                        <th>Tên môn học</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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

        @include('partials.stats')
    </div>
@endsection

@section('body.script')
    <script>
        var url_ajax = "{{ base64_encode(route('getInfo')) }}";
    </script>
    <?php enqueueScript('//www.google.com/recaptcha/api.js'); ?>
    <?php enqueueScript(url('/') . '/assets/js/jquery.min.js', '1.11.3'); ?>
    <?php enqueueScript(url('/') . '/assets/js/bootstrap.min.js', '3.3.6'); ?>
    <?php enqueueScript(url('/') . '/assets/js/validator.min.js', '0.9.0'); ?>
    <?php enqueueScript(url('/') . '/assets/js/main.min.js', '1.0.1'); ?>
@endsection
