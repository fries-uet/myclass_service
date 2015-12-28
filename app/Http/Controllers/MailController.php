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
        $content = 'Chào bạn,<br>';
        $content .= 'Bạn đã đăng ký dịch vụ nhận điểm thi qua Email. Vui lòng click vào link bên dưới để xác nhận đăng ký.<br>';
        $content .= '<p style="text-align: center"><a href="' . $link . '">Xác nhận</a></p><br>';
        $content .= '<p>Nếu không phải bạn  đăng ký thì hãy bỏ qua mail này.</p><br>';
        $content .= 'Thân,<br>Fries Team.';
        return $this->sendMail($subject, $content, [$email]);
    }

    public function sendMailResultExam($email, $name, $href)
    {
        $subject = 'Đã có điểm của môn ' . $name;

        $content = 'Chào bạn,<br>';
        $content .= 'Dưới đây là link kết quả điểm thi môn ' . $name . '.<br>';
        $content .= '<p style="text-align: center"><a href="' . $href . '">' . $href . '</a></p><br>';
        $content .= 'Thân,<br>Fries Team.';

        return $this->sendMail($subject, $content, [$email]);
    }
}
