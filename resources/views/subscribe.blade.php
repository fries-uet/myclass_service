<!DOCTYPE html>
<html lang="vi">
<head>
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/style.css"/>
</head>
<body>
<div class="subscriber" style="padding-top: 50px;">

</div>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Đăng ký</h2>

            <form action="{{ route('getInfo') }}" data-toggle="validator" method="POST">
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
                                       data-error="Mã số sinh viên không đúng! Hoặc đã có vấn đề gì đó đã xảy ra.">
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
                        <input type="email" name="email" class="form-control" id="email" data-error="Email chưa hợp lệ."
                               required>
                    </div>
                    <div class="help-block with-errors"></div>
                </div>

                {{--@include('api.recapcha')--}}

                <div class="form-group">
                    <button class="btn btn-success btn-block" type="submit">Submit</button>
                </div>

            </form>
        </div>
        <div class="col-md-6">
            <h2>Danh sách môn thi</h2>
            <table class="table">
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
</div>

</div>

<script src='//www.google.com/recaptcha/api.js'></script>
<script src="{{ url('/')  }}/assets/js/jquery.min.js"></script>
<script src="{{ url('/')  }}/assets/js/bootstrap.min.js"></script>
<script src="{{ url('/')  }}/assets/js/validator.min.js"></script>
<script src="{{ url('/')  }}/assets/js/main.js"></script>
</body>
</html>
