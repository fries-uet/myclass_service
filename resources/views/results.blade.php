@extends('layouts.wrapper')

@section('head')
    <title>Kết quả điểm thi của các lớp môn học</title>
@endsection

@section('head.style')
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/style.css?v=1.2.0"/>
@endsection
<?php
$exam_results = $data;
?>

@section('body')
    <div class="container">
        <div class="exam_results">
            <div class="back">
                <a href="{{ route('subscribe') }}">Quay lại trang đăng ký</a>
            </div>
            <h3>Danh sách các lớp có kết quả thi</h3>

            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã LMH</th>
                    <th class="hidden-xs hidden-sm">Tên MH</th>
                </tr>
                </thead>
                <tbody>
                @foreach($exam_results as $index => $exam)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><a href="{{ $exam['href'] }}" target="_blank">{{ $exam['code'] }}</a></td>
                        @if ($exam['name'])
                            <td class="hidden-xs hidden-sm">{{ $exam['name'] }}</td>
                        @else
                            <td class="hidden-xs hidden-sm"></td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('body.script')
    <?php enqueueScript(url('/') . '/assets/js/jquery.min.js', '1.11.3'); ?>
    <?php enqueueScript(url('/') . '/assets/js/bootstrap.min.js', '3.3.6'); ?>
@endsection
