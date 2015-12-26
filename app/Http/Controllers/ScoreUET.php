<?php

namespace App\Http\Controllers;

use App\s_user;
use Exception;
use FCurl;
use Illuminate\Http\Request;

use App\Http\Requests;
use stdClass;
use Validator;

class ScoreUET extends Controller
{
    public function home()
    {
        return view('subscribe');
    }

    public function updateScoreUET()
    {
        $arr_score = $this->getScoreUET();

        print_r($arr_score);
    }

    public function registerSubscriber(Request $request)
    {
        $email = (!$request->get('email') ? '' : $request->get('email'));
        $msv = (!$request->get('msv') ? '' : $request->get('msv'));
        $recapcha = (!$request->get('g-recaptcha-response') ? '' : $request->get('g-recaptcha-response'));

        $data = [];
        $data['data'] = [
            'email' => $email,
            'msv' => $msv,
        ];
        $data['error'] = false;
        $data['msg'] = 'Bạn vui lòng kiểm tra mail để xác nhận đăng ký. Nếu không có hãy kiểm tra trong mục Spam.';

        if ($request->getMethod() != 'POST') {
            return view('subscribe')->with('data', $data);
        }

        $validator = $this->validator($data['data']);
        $errors = $validator->errors()->getMessages();
        if (count($errors) > 0) {
            return view('subscribe')->with('data', $data)
                ->withErrors($errors);
        }

        /**
         * Chưa xác thực Captcha
         */
        if ($recapcha == '') {
            return view('subscribe')->with('data', $data)
                ->withErrors([
                    'msg' => 'Vui lòng xác nhận CAPTCHA.'
                ]);
        }

        // Create
        try {
            $this->create($data['data']);
        } catch (Exception $e) {
            return view('subscribe')->with('data', $data)
                ->withErrors([
                    'msg' => $e->getMessage()
                ]);
        }

        return view('subscribe')->with('data', $data);
    }

    /**
     * Validate request
     *
     * @param array $data
     * @return \Illuminate\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'msv' => 'required|max:8|min:8',
            'email' => 'required|email|max:255|unique:s_users',
        ], [
                'email.unique' => 'Email đã được sử dụng.',
                'email.email' => 'Email không hợp lệ.',
                'email.required' => 'Trường Email là bắt buộc',
                'msv.required' => 'Trường Mã số Sinh Viên là bắt buộc',
            ]
        );
    }

    protected function create(array $data)
    {
        return s_user::create([
            'email' => $data['email'],
            'msv' => $data['msv'],
        ]);
    }

    /**
     * Get list status score UET
     *
     * @return array
     */
    public function getScoreUET()
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

        return $arr_score;
    }

    public function getInfoStudent(Request $request)
    {
        $maSV = $request->get('msv');
        $maSV = intval($maSV);

        if (strlen($maSV) !== 8) {
            abort(404);
        }

        $url = 'http://203.113.130.218:50223/congdaotao/module/dsthi_new/';
        $browser = new fCurl();
        $browser->refer = $url;
        $browser->resetopt();
        $fields = [
            'keysearch' => $maSV,
        ];

        $browser->post($browser->refer, $fields, true, 0);

        $content = $browser->return;
        $content = explode('<table class="items">', $content)[1];
        $content = explode('</table>', $content)[0];

        if (strpos($content, '<td colspan="14" class="empty">') !== false) {
            abort(404);
        }

        $trs = explode('</tr>', $content);
        $count_str = count($trs);

        if ($count_str == 2) {
            abort(404);
        }

        $tr_first = $trs[1];
        $sv = explode('</td><td>', $tr_first);

        $maSV_ = intval($sv[1]);
        if ($maSV_ != $maSV) {
            abort(404);
        }
        $name_sv = $sv[2];
        $qh = $sv[4];

        $arrLMH = [];
        for ($i = 1; $i < $count_str - 1; $i++) {
            try {
                $tr = $trs[$i];
                $td = explode('</td><td>', $tr);
                $maLMH = $td[6];
                $name_subject = $td[7];

                $lmh = new stdClass();
                $lmh->code = $maLMH;
                $lmh->name = $name_subject;

                $arrLMH[] = $lmh;
            } catch (Exception $e) {
                abort(404, $e->getMessage());
            }
        }

        return [
            'msv' => $maSV_,
            'name' => $name_sv,
            'qh' => $qh,
            'timetable' => $arrLMH,
        ];
    }
}
