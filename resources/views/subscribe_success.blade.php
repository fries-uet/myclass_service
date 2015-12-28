@extends('layouts.wrapper')

@section('head')
    <title>Đăng ký nhận kết quả thi học kì</title>
@endsection

@section('head.style')
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/style.css"/>
@endsection

@section('body')
    <div class="subscriber" style="padding-top: 50px;">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Chúc mừng bạn đã đăng ký thành công!</strong>

                    <div style="padding-top: 20px;"><a href="{{ route('subscribe') }}">Quay lại</a></div>
                </div>
            </div>
        </div>
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
    <?php enqueueScript(url('/') . '/assets/js/main.js', time()); ?>
@endsection
