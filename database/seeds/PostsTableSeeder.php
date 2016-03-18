<?php

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        $user_id = DB::table('users')->insertGetId([
            'name' => 'Trần Văn Tú',
            'msv' => '13020499',
            'class' => 6,
            'type' => 'student',
            'activated' => 1,
            'email' => 'tutv95@gmail.com',
            'password' => md5('123456')
        ]);

        $teacher_id = DB::table('users')->insertGetId([
            'name' => 'TS.Tô Văn Khánh',
            'msv' => '99999999',
            'class' => 6,
            'type' => 'teacher',
            'activated' => 1,
            'email' => 'khanhtv@vnu.edu.vn',
            'password' => md5('123456')
        ]);

        $post = DB::table('posts')->insert([
            'title' => 'Tốc độ môn học',
            'content' => 'Các bạn cần chữa / code mẫu cho những bài nào? Edit vào phần trả lời chung của sinh viên nhé.',
            'author' => $teacher_id,
            'base' => 'class_xes',
            'group' => 6
        ]);

        $post = DB::table('posts')->insert([
            'title' => 'Link tài liệu dùng tạm trong khi trang ltnc2016w bị trục trặc',
            'content' => 'Do nhà trường đang cài đặt một số thứ tại máy chủ chứa website môn học, nên thỉnh thoảng bị trục trặc. Hiện đang bị mất trang index chưa up lại được.
Các bạn dùng tạm link tại đây.
02_FlowOfControl.pptx
BT01, BT02, BT03 (đang cập nhật nội dung)
Kết quả chấm bài tập (đã xong b1)
Bài giảng 03_Arrays.pptx',
            'author' => $teacher_id,
            'base' => 'class_xes',
            'group' => 6
        ]);

        $post = DB::table('posts')->insert([
            'title' => 'Bài tập lớn thay cho toàn bộ bài tập nhỏ và thi cuối kì',
            'content' => 'Các bạn xem chi tiết tại đây, thầy Hoàng Minh Đường sẽ bổ sung thêm
https://docs.google.com/document/d/18ljhH1-lVpDCctg7h7iMXXp1OvM27fc9W5XBq8ZFd9o/edit?usp=sharing  (cập nhật link lúc 12:30 7/3)
Chỉ cần làm được một bài trong số đó là đủ yêu cầu của tôi cho môn học này.',
            'author' => $teacher_id,
            'base' => 'class_xes',
            'group' => 6
        ]);

        $post = DB::table('posts')->insert([
            'title' => 'Hỏi cách làm các bài phần A',
            'content' => 'Đa số các bài phần A toàn là các bài phải thử nhiều lần ví dụ:
phần a bảo khai báo phần một mảng a[2][12];
nhưng phần b lại bảo bỏ bớt kích thước mảng (số cột hoặc số dòng) vậy thì phải làm sang 1 file cpp khác hay khai báo 1 mảng khác hay xóa mảng cũ đi viết lại.',
            'author' => $user_id,
            'base' => 'class_xes',
            'group' => 6
        ]);

        $post = DB::table('posts')->insert([
            'title' => 'Hỏi về lịch nộp bài hàng tuần',
            'content' => 'Em thưa cô là từ giờ hạn nộp bài là 23:59 thứ 7 hàng tuần chứ ạ?',
            'author' => $user_id,
            'base' => 'class_xes',
            'group' => 6
        ]);

        $post = DB::table('posts')->insert([
            'title' => 'Hỏi về pull / merge',
            'content' => 'Sau khi add và push bài lên trên repo, em xóa các file trong thư mục và thử pull lại. Nhưng em khi dùng cả pull lẫn merge, git đều thông báo "already updated" nhưng lại không có file nào đc tải về. Vấn đề này xử lý như nào và nó có thể bị xảy ra trên máy khác không ạ?
Em cảm ơn cô rất nhiều ạ.',
            'author' => $user_id,
            'base' => 'class_xes',
            'group' => 6
        ]);

        $post = DB::table('posts')->insert([
            'title' => 'Điền tên bitbucket',
            'content' => 'Thưa cô , em thấy trong trang https://docs.google.com/spreadsheets/d/1SB9Lmb5Wzpn3X4DZu8oHfxgKNWdxp-A4q3YehWgUxaA/edit#gid=0 nick bitbucket của em chưa có. Nên em mong cô thể điền vào giúp em được không ạ? nick bitbucket của em là minh_chau. Em xin cảm ơn!',
            'author' => $user_id,
            'base' => 'class_xes',
            'group' => 6
        ]);


    }
}
