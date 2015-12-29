<div class="stats">
    <div class="row text-center">
        <div class="col-md-12">
            <p class="text">Đã có <span>{{ $count_subject  }}</span> lớp có điểm thi và <span>{{ $count_user }}</span>
                người dùng tin tưởng và sử dụng.
            </p>
        </div>
    </div>
    <div class="link_results">
        <a href="{{ route('results') }}">Danh sách chi tiết</a>
    </div>
</div>