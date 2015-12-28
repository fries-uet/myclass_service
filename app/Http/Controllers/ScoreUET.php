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

    public function update()
    {
        $scores = s_score::all()->toArray();

        $s_list = s_list_exam::all()
            ->where('sent', 0);

        foreach ($scores as $index => $score) {
            $code = $score['code'];
            $href = $score['href'];

            if ($href != '') {
                $href = str_replace(' ', '%20', $href);
                $s_l = $s_list->where('subject_code', $code);

                if ($s_l->count() > 0) {
                    $arrTemp = $s_l->toArray();

                    foreach ($arrTemp as $i => $a) {
                        $id = intval($a['id']);
                        $user_id = intval($a['user_id']);
                        $subject_name = $a['subject_name'];

                        $user = s_user::all()
                            ->where('id', $user_id)->first();
                        $email = $user->email;

                        $mailController = new MailController();
                        $send = $mailController->sendMailResultExam($email, $subject_name, $href);

                        if ($send) {
                            $s_l_update = $s_l->where('id', $id)->first();
                            $s_l_update->update([
                                'sent' => 1
                            ]);
                        }
                    }
                }
            }
        }
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

        /**
         * Đếm số môn có điểm
         */
        $x_scores = s_score::all();
        $x_scores_count = $x_scores->count() - $x_scores->where('href', '')->count();
        $data['count_subject'] = $x_scores_count;

        /**
         * Số người dùng
         */
        $x_user_count = s_user::all()->count();
        $data['count_user'] = $x_user_count;

        if ($request->getMethod() != 'POST') {
            return view('subscribe')->with('data', $data);
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

        $validator = $this->validator($data['data']);
        $errors = $validator->errors()->getMessages();
        if (count($errors) > 0) {
            return view('subscribe')->with('data', $data)
                ->withErrors($errors);
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

        $emailController = new MailController();
        $emailController->sendMailConfirm($email);


        return redirect()->route('subscribe.success');
    }

    public function success()
    {
        return view('subscribe_success');
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

    public function reconfirm_front()
    {
        $data['email'] = '';

        return view('reconfirm')->with('data', $data);
    }

    public function reconfirm(Request $request)
    {
        $email = (!$request->get('email') ? '' : $request->get('email'));
        $recapcha = (!$request->get('g-recaptcha-response') ? '' : $request->get('g-recaptcha-response'));

        $data = [];

        $data['email'] = $email;

        /**
         * Chưa xác thực Captcha
         */
        if ($recapcha == '') {
            return view('reconfirm')->with('data', $data)
                ->withErrors([
                    'msg' => 'Vui lòng xác nhận CAPTCHA.'
                ]);
        }

        $user = s_user::all()
            ->where('email', $email);

        if ($user->count() == 0) {
            return view('reconfirm')->with('data', $data)
                ->withErrors([
                    'msg' => 'Không tồn tại người dùng này.'
                ]);
        }

        $count_active = $user->where('is_active', 1)->count();
        if ($count_active > 0) {
            return view('reconfirm')->with('data', $data)
                ->withErrors([
                    'msg' => 'Tài khoản đã được kích hoạt rồi.'
                ]);
        }

        $emailController = new MailController();
        $emailController->sendMailConfirm($email);

        return view('reconfirm')->with('data', $data);
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
