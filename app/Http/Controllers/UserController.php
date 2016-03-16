<?php

namespace App\Http\Controllers;

use App\ClassSubject;
use App\ClassX;
use App\GCM_Token;
use App\Media;
use App\SubClassSubject;
use App\TimeTable;
use App\User;
use File;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Response;
use stdClass;
use Storage;

class UserController extends Controller
{
    public function activate_code($mail, $activate_code)
    {
        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        $users = User::all()->where('email', $mail);
        if ($users->count() < 0) {//Không tồn tại người dùng
            $response->error = true;
            $response->error_msg = 'Không tồn tại người dùng này';

            return response()->json($response);
        }

        $user = $users->first();
        $activate_code_ = $user->activate_code;
        if ($activate_code_ != $activate_code) {
            $response->error = true;
            $response->error_msg = 'Mã xác nhận chưa đúng';

            return response()->json($response);
        }

        $updated = User::where('email', $mail)->update(['activated' => 1]);

        $response->activated = $updated;

        return response()->json($response);
    }

    /**
     * API Register
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        onlyAllowPostRequest($request);

        $all = $request->only([
            'email',
            'password',
            'mssv',
            'lop',
        ]);

        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        if (!filter_var($all['email'], FILTER_VALIDATE_EMAIL)) {
            $response->error = true;
            $response->error_msg = 'Email không hợp lệ!';

            return response()->json($response);
        }

        /**
         * Kiểm tra password
         */
        if (strlen($all['password']) < 6) {
            $response->error = true;
            $response->error_msg
                = 'Password quá ngắn! Yêu cầu tối thiểu trên 6 kí tự';

            return response()->json($response);
        }

        /**
         * Tìm user đã tồn tại chưa?
         */
        $user = User::all()->where('email', $all['email']);

        if ($user->count() > 0) {//Đã tồn tại người dùng
            $response->error = true;
            $response->error_msg = 'Đã tồn tại người dùng với email ' . $all['email'];

            return response()->json($response);
        }

        /**
         * Get timetable UET
         */
        $res = getTimeTableUET($all['mssv']);

        /**
         * Dữ liệu trả về
         */

        if ($res == false) {//Không tồn tại MSV
            $response->error = true;
            $response->error_msg = 'Mã số sinh viên không hợp lệ!';

            return response()->json($response);
        }

        $name = $res['name'];
        $qh = $res['qh'];
        $timetable = $res['timetable'];

        /**
         * Tìm kiếm lớp khóa học
         */
        $classXes = ClassX::all()->where('name', $qh);
        if ($classXes->count() > 0) {
            $classX_id = $classXes->first()->id;

        } else {
            $classX = ClassX::create([
                'name' => $qh,
            ]);

            $classX_id = $classX->id;
        }

        $activate_code = generate_activate_code();

        $mail = new MailController();
        $mail->sendMailActivateCode($all['email'], $activate_code, $name);

        $type = 'student';//Mặc định người dùng đăng ký là sinh viên
        $user = User::create([
            'email' => $all['email'],
            'password' => md5($all['password']),
            'msv' => $all['mssv'],
            'class' => $classX_id,
            'type' => $type,
            'name' => $name,
            'activate_code' => $activate_code
        ]);

        /**
         * Import timetable
         */
        foreach ($timetable as $index => $t) {
            $maLMH = $t->maLMH;
            $nhom = intval($t->nhom);

            if ($nhom == 0) {//Nhóm lý thuyết
                $lmhs = ClassSubject::all()->where('maLMH', $maLMH);

                if ($lmhs->count() > 0) {
                    $lmh = $lmhs->first();
                    $lmh_id = $lmh->id;
                    $subs = SubClassSubject::all()->where('classSubject', $lmh_id);

                    foreach ($subs as $s) {
                        $sub_id = $s->id;

                        $tt = TimeTable::create([
                            'user' => $user->id,
                            'subClass' => $sub_id,
                        ]);
                    }
                }
            } else {//Nhóm thực hành
                $lmhs = ClassSubject::all()->where('maLMH', $maLMH);

                if ($lmhs->count() > 0) {
                    $lmh = $lmhs->first();
                    $lmh_id = $lmh->id;
                    $subs = SubClassSubject::all()->where('classSubject', $lmh_id);
                    if ($subs->count() > 0) {
                        foreach ($subs as $s) {
                            $sub_id = $s->id;
                            if (intval($s->nhom) == 0
                                || intval($s->nhom == $nhom)
                            ) {
                                $tt = TimeTable::create([
                                    'user' => $user->id,
                                    'subClass' => $sub_id,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        $response->error = false;
        $response->uid = $user->getAttribute('id');
        $response->user = User::getInfoById($user->id);

        return response()->json($response);
    }

    /**
     * API Login
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        onlyAllowPostRequest($request);

        $all = $request->only([
            'email',
            'password',
        ]);

        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        $users = User::all()->where('email', $all['email']);
        if ($users->count() < 0) {//Không tồn tại người dùng
            $response->error = true;
            $response->error_msg = 'Không tồn tại người dùng này';

            return response()->json($response);
        }

        $user = $users->first();
        $pass_encode = md5($all['password']);

        if ($user->getAttribute('password') != $pass_encode) {//Sai mật khẩu
            $response->error = true;
            $response->error_msg = 'Mật khẩu của bạn không đúng!';

            return response()->json($response);
        }

        if ((boolean)$user->getAttribute('activated') == false) {//Chưa kích hoạt
            $response->error = true;
            $response->error_msg = 'Bạn chưa xác thực tài khoản!';

            return response()->json($response);
        }

        $response->error = false;
        $response->uid = $user->getAttribute('id');
        /**
         * Trả về dữ liệu người dùng
         */

        $response->user = User::getInfoById($user->id);

        return response()->json($response);
    }

    public function updateAvatar(Request $request)
    {
        $user_id = $request->get('uid');
        $avatar = $request->get('avatar');

        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        $users = User::all()->where('id', intval($user_id));
        if ($users->count() < 0) {//Không tồn tại người dùng
            $response->error = true;
            $response->error_msg = 'Không tồn tại người dùng này';

            return response()->json($response);
        }

        $user = $users->first();

        $msv = $user->msv;

        if (!isset($avatar) || $avatar == '') {
            $response->error = true;
            $response->error_msg = 'Ava rỗng!';

            return response()->json($response);
        }

        $file_avatar = base64_decode($avatar);
        $name_file_avatar = $msv . '.jpg';
        $dir_uploads = 'uploads';

        $exists = Storage::disk('local')->exists($dir_uploads . '/' . $name_file_avatar);
        Storage::disk('local')->put($dir_uploads . '/' . $name_file_avatar, $file_avatar);

        if (!$exists) {
            $media = Media::create([
                'name' => $name_file_avatar,
                'dir' => 'uploads',
                'type' => 'jpg'
            ]);
        }

        $response->error = false;
        $response->url = route('getAvatar', $msv);

        return response()->json($response);
    }

    public function getAvatar($msv)
    {
        $file_avatar = $msv . '.jpg';
        $dir_uploads = 'uploads';

        $exists = Storage::disk('local')->exists($dir_uploads . '/' . $file_avatar);

        if (!$exists) {
            abort(404);
        }

        $file = $disk = Storage::disk('local')->get($dir_uploads . '/' . $file_avatar);

        $response = Response::make($file, 200);
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    /**
     * Update information user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $error = true;
        onlyAllowPostRequest($request);

        $all = $request->only([
            'email',
            'name',
            'mssv',
            'lop',
            'avartar'
        ]);

        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        /**
         * Xử lý lớp khóa học
         */
        $classX = $all['lop'];
        $id_class = ClassX::getIdByClassName($classX);

        if ($id_class == false) {//Lớp khóa học không tồn tại
            $response->error = true;
            $response->error_msg = 'Lớp khóa học không tồn tại';

            return response()->json($response);
        }

        /**
         * Tìm user bằng email
         */
        $users = User::where('email', $all['email']);

        if ($users->count() == 0) {
            $response->error = true;
            $response->error_msg = 'Đã có lỗi gì đó xảy ra!';

            return response()->json($response);
        }

        $updated = $users->update([
            'name' => ucwords($all['name']),
            'msv' => $all['mssv'],
            'class' => $id_class,
        ]);

        if ($updated == 0) {
            $response->error = true;
            $response->error_msg = 'Cập nhật không có gì thay đổi!';

            return response()->json($response);
        }

        $user = $users->first();

        $response->error = false;
        $response->uid = $user->id;
        $response->user = User::getInfoById($user->id);

        return response()->json($response);
    }

    public function login_token(Request $request)
    {
        onlyAllowPostRequest($request);

        $all = $request->only([
            'id',
            'token',
        ]);
    }

    public function send_push_notification($regids, $user, $title)
    {
        define('GOOGLE_API_KEY', 'AIzaSyAQ8q8To5VZLRwKMnroS_k4Dg19mvUJmb8');

        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $jData = new stdClass();
        $jData->title = $title;
        $jData->teacher = $user;

        $jGcmData = new stdClass();
        $jGcmData->registration_ids = $regids;
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

        // Close connection
        curl_close($ch);
    }

    public function feed($user_id)
    {
        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        $users = User::all()->where('id', $user_id);
        if ($users->count() == 0) {//Không tồn tại người dùng
            $response->error = true;
            $response->error_msg = 'Không tồn tại người dùng này';

            return response()->json($response);
        }

        $user = $users->first();

        dd($user);

        return null;
    }
}
