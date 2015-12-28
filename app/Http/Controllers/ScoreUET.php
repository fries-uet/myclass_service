<?php

namespace App\Http\Controllers;

use App\s_list_exam;
use App\s_score;
use App\s_user;
use Exception;
use FCurl;
use Illuminate\Http\Request;

use App\Http\Requests;
use Hash;
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

    public function crawl()
    {
        $scores = $this->getScoreUET();

        foreach ($scores as $index => $score) {
            if (!$score['href']) {
                $href = '';
            } else {
                $href = $score['href'];
            }

            $code = $score['maLMH'];
            $s = s_score::all()
                ->where('code', $code);
            $count_s = $s->count();

            if ($count_s > 0) {
                $s->first()->update([
                    'href' => $href,
                ]);

                var_dump('Updated!');
            } else {
                $s_new = s_score::create([
                    'code' => $code,
                    'href' => $href
                ]);

                var_dump('Created!');
            }
        }
    }

    /**
     * Register subscriber
     *
     * @param Request $request
     * @return $this
     */
    public function registerSubscriber(Request $request)
    {
        $email = (!$request->get('email') ? '' : $request->get('email'));
        $msv = (!$request->get('msv') ? '' : $request->get('msv'));
        $name = (!$request->get('name') ? '' : $request->get('name'));
        $recapcha = (!$request->get('g-recaptcha-response') ? '' : $request->get('g-recaptcha-response'));

        $data = [];
        $data['data'] = [
            'email' => $email,
            'msv' => $msv,
            'name' => $name,
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
            $this->createUser($data['data']);
        } catch (Exception $e) {
            return view('subscribe')->with('data', $data)
                ->withErrors([
                    'msg' => $e->getMessage()
                ]);
        }

        return view('subscribe')->with('data', $data);
    }

    /**
     * Xác nhận đăng ký
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function confirm(Request $request)
    {
        $token = (!$request->get('token') ? '' : $request->get('token'));
        $email = (!$request->get('email') ? '' : $request->get('email'));

        $validation = Validator::make(
            array(
                'token' => $token,
                'email' => $email,
            ),
            array(
                'token' => 'required',
                'email' => 'required|email|max:255',
            )
        );
        $errors = $validation->errors()->getMessages();

        if (count($errors) > 0) {
            return view('confirm')->withErrors($errors);
        }

        $check = Hash::check($email, $token);
        if (!$check) {
            return view('confirm')->withErrors([
                'token_broken' => 'Email và token không khớp!'
            ]);
        }

        $user = s_user::all()
            ->where('email', $email)->first();

        $msv = $user->msv;
        $id = $user->id;
        $info = getInfoStudent($msv);
        if ($info == false) {
            return view('confirm')->withErrors([
                'info' => 'Đã có cái gì đó sai sai ở đây!'
            ]);
        }

        $timetable = $info['timetable'];
        foreach ($timetable as $i => $t) {
            $code = $t->code;
            $name = $t->name;

            $check_again = s_list_exam::all()
                ->where('user_id', $id)
                ->where('subject_code', $code)->count();

            if ($check_again == 0) {
                s_list_exam::create([
                    'user_id' => $id,
                    'subject_code' => $code,
                    'subject_name' => $name,
                ]);
            }
        }

        // Tài khoản đã được active
        if ($user->is_active) {
            return view('confirm')->withErrors([
                'confirmed' => 'Tài khoản của bạn đã được xác nhận trước đó.!'
            ]);
        }

        // Update is_active
        $active = $user->update([
            'is_active' => 1
        ]);

        // Update active thất bại
        if (!$active) {
            return view('confirm')->withErrors([
                'active_broken' => 'Có cái gì đó sai sai ở đây!'
            ]);
        }

        return view('confirm');
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
            'name' => 'required',
        ], [
                'email.unique' => 'Email đã được sử dụng.',
                'email.email' => 'Email không hợp lệ.',
                'email.required' => 'Trường Email là bắt buộc',
                'msv.required' => 'Trường Mã số Sinh Viên là bắt buộc',
            ]
        );
    }

    protected function createUser(array $data)
    {
        return s_user::create([
            'email' => $data['email'],
            'msv' => $data['msv'],
            'name' => $data['name'],
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

    /**
     * Get info student
     *
     * @param Request $request
     * @return array|bool
     */
    public function getInfoStudent(Request $request)
    {
        $maSV = $request->get('msv');
        $maSV = intval($maSV);

        if (strlen($maSV) !== 8) {
            abort(404);
        }

        $info = getInfoStudent($maSV);
        if (!$info) {
            abort(404);
        }

        return $info;
    }
}
