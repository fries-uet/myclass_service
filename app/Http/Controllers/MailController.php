<?php

namespace App\Http\Controllers;

use FriesMail;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    public function sendMail($subject, $content, $arrTo)
    {
        $friesMap = new FriesMail($subject, $content);
        $friesMap->setFromName('Fries Team')->setFrom('fries.uet@gmail.com');

        foreach ($arrTo as $a) {
            $friesMap->addTo($a);
        }

        return $friesMap->sendMail();
    }

    public function sendMailConfirm($email)
    {
        $token = bcrypt($email);
        $link = route('confirm') . '?email=' . $email . '&token=' . $token;

        $subject = 'Xác nhận đăng ký nhận điểm thi!';
        $content = 'Chào bạn,<br>' . PHP_EOL;
        $content .= 'Bạn đã đăng ký dịch vụ nhận điểm thi qua Email. Vui lòng click vào link bên dưới để xác nhận đăng ký.<br>' . PHP_EOL;
        $content .= '<p style="text-align: center"><a href="' . $link . '">Xác nhận</a></p><br>' . PHP_EOL;
        $content .= '<p>Nếu không phải bạn  đăng ký thì hãy bỏ qua mail này.</p><br>';
        $content .= 'Thân,<br>Fries Team.';
        return $this->sendMail($subject, $content, [$email]);
    }

    public function sendMailResultExam($email, $name, $href)
    {
        $subject = 'Đã có điểm của môn ' . $name;

        $content = 'Chào bạn,<br>' . PHP_EOL;
        $content .= 'Dưới đây là link kết quả điểm thi môn ' . $name . '.<br>' . PHP_EOL;
        $content .= '<p style="text-align: center"><a href="' . $href . '">' . $href . '</a></p><br>' . PHP_EOL;
        $content .= 'Thân,<br>Fries Team.';

        return $this->sendMail($subject, $content, [$email]);
    }

    public function sendMailHappyNewYear($email, $name)
    {
        $subject = 'Chúc mừng năm mới năm 2016';

        $content = 'Chào ' . $name . ',<br>' . PHP_EOL;
        $content .= 'Nhân dịp năm mới Fries chúc bạn cùng gia đình có một năm mới dồi dào sức khỏe, vui vẻ và hạnh phúc.' . '<br>' . PHP_EOL;
        $content .= 'Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của bọn mình.' . '<br><br>' . PHP_EOL . PHP_EOL;
        $content .= 'Thân chào và quyết thắng,<br>' . PHP_EOL . 'Fries Team.' . PHP_EOL;
        $content .= '<div style="color: #eeeeee; padding-top: 20px;">----------------------------------------------------<br>' . PHP_EOL;
        $content .= 'Mọi ý kiến đóng góp bạn có thể gửi cho bọn mình tại email này.<br> ' . PHP_EOL . 'Cảm ơn bạn.';
        $content .= '</div>';

        return $this->sendMail($subject, $content, [$email]);
    }

    public function sendMailActivateCode($email, $activate_code, $name)
    {
        $link_confirm = route('activate_code', array($email, $activate_code));

        $subject = 'Xác nhận tài khoản';

        $content = 'Chào ' . $name . ',<br>' . PHP_EOL;
        $content .= 'Chỉ còn 1 bước nữa là xong. Hãy click vào link dưới đây để hoàn tất.' . '<br>' . PHP_EOL;
        $content .= 'Link: ' . $link_confirm . '<br><br>' . PHP_EOL . PHP_EOL;
        $content .= 'Thân chào và quyết thắng,<br>' . PHP_EOL . 'Fries Team.' . PHP_EOL;
        $content .= '<div style="color: #eeeeee; padding-top: 20px;">----------------------------------------------------<br>' . PHP_EOL;
        $content .= 'Mọi ý kiến đóng góp bạn có thể gửi cho bọn mình tại email này.<br> ' . PHP_EOL . 'Cảm ơn bạn.';
        $content .= '</div>';

        return $this->sendMail($subject, $content, [$email]);
    }
}
