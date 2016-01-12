<?php

namespace App\Http\Controllers;

use App\GCMUser;
use FCurl;
use Illuminate\Http\Request;

use App\Http\Requests;
use stdClass;

class TestController extends Controller
{
    public function test_helper()
    {
        $url = 'http://www.coltech.vnu.edu.vn/news4st/test.php';

        $browser = new fCurl();
        $browser->refer = $url;
        $browser->resetopt();
        $fields = [
            'lstClass' => 819,
        ];
        $browser->post($url, $fields, 1, 0);

        $content = $browser->return;

        $table = explode('<table border="0" cellspacing="2" cellpadding="0" width="100%">', $content)[1];
        $table = trim($table);

        $li = explode('<LI>', $table);

        $arr_score = [];

        for ($i = 1; $i < count($li); $i++) {
            $name = explode('</td>', $li[$i])[0];
            $name = trim($name);

            /**
             * Đã có điểm
             */
            if (strpos($name, '<b>') !== false) {
                $link = explode('href="../', $name)[1];
                $link = explode('" class=', $link)[0];
                $href = 'http://www.coltech.vnu.edu.vn/' . $link;

                $maLMH = explode('<b>', $name)[1];
                $maLMH = explode('</b>', $maLMH)[0];
                $maLMH = explode('(', $maLMH)[0];
                $maLMH = explode(' - ', $maLMH);

                if (count($maLMH) > 1) {
                    $maLMH = $maLMH[count($maLMH) - 1];
                    $maLMH = strtoupper($maLMH);

                    $arr_score[] = [
                        'href' => $href,
                        'maLMH' => $maLMH,
                    ];
                }
            } else {// Chưa có điểm
                $maLMH = explode(' - ', $name);

                if (count($maLMH) > 1) {
                    $maLMH = $maLMH[count($maLMH) - 1];
                    $maLMH = strtoupper($maLMH);

                    $arr_score[] = [
                        'href' => false,
                        'maLMH' => $maLMH,
                    ];
                }
            }
        }
    }

    /**
     * Tu TV
     */
    public function tutv()
    {
    }

    public function dkmh($user, $pass, $arr_lmh)
    {
        $url = 'http://dangkyhoc.daotao.vnu.edu.vn/dang-nhap';
        $browser = new fCurl();
        $browser->refer = $url;
        $browser->resetopt();
        $browser->get($browser->refer, true, 1);
        $browser->get($browser->refer, true, 1);

        $str_temp = $browser->return;
        $str_temp = explode('__RequestVerificationToken', $str_temp)[1];
        $str_temp = explode('>', $str_temp)[0];
        $str_temp = explode('value="', $str_temp)[1];
        $verti = explode('"', $str_temp)[0];

        /**
         * Đăng nhập
         */
        $field = [
            'LoginName' => $user,
            'Password' => $pass,
            '__RequestVerificationToken' => $verti,
        ];
        $browser->post($url, $field, 1, 0);

        $contentX = $browser->return;

        if (strpos($contentX, '/Account/Logout') === false) {
            echo 'Login failed!';

            return;
        }

        /**
         * Lấy source trang thời khóa biểu
         */
        $url_time = 'http://dangkyhoc.daotao.vnu.edu.vn/danh-sach-mon-hoc/1/1';
        $browser->post($url_time, null, 1, 0);
        $source_html = $browser->return;

        $source_html = html_entity_decode($source_html);
        $source_html = trim($source_html);

        $arr = explode('<tr title="', $source_html);

        $maLMH = $arr_lmh;

        foreach ($arr as $index => $a) {
            if ($a == '') {
                continue;
            }

            foreach ($maLMH as $i => $lmh) {
                if (strpos($a, $lmh) !== false) {
                    // Input checkbox
                    $input = explode('text-align:center;">', $a)[1];
                    $input = explode('</td>', $input)[0];
                    $input = trim($input);

                    // Name subject
                    $name = explode('<td>', $a)[1];
                    $name = explode('</td>', $name)[0];
                    $name = explode('(', $name)[0];
                    $name = trim($name);

                    if (strpos($a, 'checkbox') !== false) {
                        //dk
                        $row_index = explode('data-rowindex="', $input)[1];
                        $row_index = explode('"', $row_index)[0];

                        $url_choose = 'http://dangkyhoc.daotao.vnu.edu.vn/chon-mon-hoc/' . $row_index . '/1/1';
                        $browser->post($url_choose, null, 1, 0);

                        $url_confirm = 'http://dangkyhoc.daotao.vnu.edu.vn/xac-nhan-dang-ky/1';
                        $browser->post($url_confirm, null, 1, 0);

//						$content_email = 'Đã đăng kí thành công môn ' . $name . ' :]]]';
//						$subject       = $name . ' còn trống!';

//						$sender = new MailController();
//						if ( $sender->sendMail( $subject, $content_email, [ 'tutv95@gmail.com' ] ) ) {
//							echo 'success';
//						}

                        echo $name . ' success<br>';
                    } else {
                        echo $name . ' Full HD<br>';
                    }
                }
            }
        }
    }

    public function dkmh2($user, $pass, $arr_lmh)
    {
        $url = 'http://dangkyhoc.daotao.vnu.edu.vn/dang-nhap';
        $browser = new fCurl();
        $browser->refer = $url;
        $browser->resetopt();
        $browser->get($browser->refer, true, 1);
        $browser->get($browser->refer, true, 1);

        $str_temp = $browser->return;
        $str_temp = explode('__RequestVerificationToken', $str_temp)[1];
        $str_temp = explode('>', $str_temp)[0];
        $str_temp = explode('value="', $str_temp)[1];
        $verti = explode('"', $str_temp)[0];

        /**
         * Đăng nhập
         */
        $field = [
            'LoginName' => $user,
            'Password' => $pass,
            '__RequestVerificationToken' => $verti,
        ];
        $browser->post($url, $field, 1, 0);

        $contentX = $browser->return;

        if (strpos($contentX, '/Account/Logout') === false) {
            echo 'Login failed!';

            return;
        }

        /**
         * Lấy source trang thời khóa biểu
         */
        $url_time = 'http://dangkyhoc.daotao.vnu.edu.vn/danh-sach-mon-hoc/1/2';
        $browser->post($url_time, null, 1, 0);
        $source_html = $browser->return;

        $source_html = html_entity_decode($source_html);
        $source_html = trim($source_html);

        $arr = explode('<tr title="', $source_html);

        $maLMH = $arr_lmh;

        foreach ($arr as $index => $a) {
            if ($a == '') {
                continue;
            }

            foreach ($maLMH as $i => $lmh) {
                if (strpos($a, $lmh) !== false) {
                    // Input checkbox
                    $input = explode('text-align:center;">', $a)[1];
                    $input = explode('</td>', $input)[0];
                    $input = trim($input);

                    // Name subject
                    $name = explode('<td>', $a)[1];
                    $name = explode('</td>', $name)[0];
                    $name = explode('(', $name)[0];
                    $name = trim($name);

                    if (strpos($a, 'checkbox') !== false) {
                        //dk
                        $row_index = explode('data-rowindex="', $input)[1];
                        $row_index = explode('"', $row_index)[0];

                        $url_choose = 'http://dangkyhoc.daotao.vnu.edu.vn/chon-mon-hoc/' . $row_index . '/1/2';
                        $browser->post($url_choose, null, 1, 0);

                        $url_confirm = 'http://dangkyhoc.daotao.vnu.edu.vn/xac-nhan-dang-ky/1';
                        $browser->post($url_confirm, null, 1, 0);

                        $content_email = 'Đã đăng kí thành công môn ' . $name . ' :]]]';
                        $subject = $name . ' còn trống!';

                        $sender = new MailController();
                        if ($sender->sendMail($subject, $content_email, ['tutv95@gmail.com'])) {
                            echo 'success';
                        }
                    } else {
                        echo $name . ' Full HD<br>';
                    }
                }
            }
        }
    }

    public function testCronb()
    {
        $sender = new MailController();
        $sender->sendMail('ok', date('Y-m-d H:m:i', time()), ['tutv95@gmail.com']);
    }

    public function register(Request $request)
    {
        $regID = $request->input('regId');
        $email = $request->input('email');
        $name = $request->input('name');

        $user = GCMUser::create([
            'name' => $name,
            'email' => $email,
            'regID' => $regID,
        ]);

        if ($user) {
            echo 'done!';
        }
    }

    public function send_push_notification(Request $request)
    {
        define('GOOGLE_API_KEY', 'AIzaSyAQ8q8To5VZLRwKMnroS_k4Dg19mvUJmb8');

        $regID = $request->input('regID');
        $message = $request->input('msg');

        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

//        $fields = array(
//            'data' => array(
//                'to' => '/topics/global',
//                'message' => $message
//            ),
//        );

        $jData = new stdClass();
        $jData->message = $message;

        $jGcmData = new stdClass();
        $jGcmData->to = $regID;
//        $jGcmData->registration_ids = (object)array($regID);
        $jGcmData->data = $jData;

        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        //print_r($headers);
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jGcmData));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        echo $result;
    }
}
