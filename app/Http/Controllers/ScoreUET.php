<?php

namespace App\Http\Controllers;

use FCurl;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Log;
use stdClass;

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
        if ($request->getMethod() != 'POST') {
            return view('subscribe')->with('recapcha', 'a');
        }

        $recapcha = $request->get('g-recaptcha-response');
        if ($recapcha) {
            $email = $request->get('email');
        }
        $mssv = $request->get('mssv');

        return view('subscribe')->with('recapcha', $recapcha);
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
        $myHost = url('/');
        $host = $request->getHost();
        $host_1 = $request->getHttpHost();
        $host_2 = $request->getRequestUri();

        var_dump($host);
        var_dump($host_1);
        var_dump($host_2);
        var_dump($request);
        dd($request);
        die();

        if ($host == '' || strpos($myHost, $host) === false) {
            abort(404);
        }

        $maSV = $request->get('msv');

        if (strlen($maSV) !== 8) {
            abort(404);
        }

        $url = 'http://203.113.130.218:50223/congdaotao/module/qldt/?SinhvienLmh%5BmasvTitle%5D=' . $maSV
            . '&SinhvienLmh%5BhotenTitle%5D=&SinhvienLmh%5BngaysinhTitle%5D=&SinhvienLmh%5BlopkhoahocTitle%5D=&SinhvienLmh%5BtenlopmonhocTitle%5D=&SinhvienLmh%5BtenmonhocTitle%5D=&SinhvienLmh%5Bnhom%5D=&SinhvienLmh%5BsotinchiTitle%5D=&SinhvienLmh%5Bghichu%5D=&SinhvienLmh%5Bterm_id%5D=019&SinhvienLmh_page=1&ajax=sinhvien-lmh-grid';

        $browser = new fCurl();
        $browser->refer = $url;
        $browser->resetopt();
        $browser->get($browser->refer, true, 0);

        $content = $browser->return;
        $content = explode('id="sinhvien-lmh-grid"', $content)[1];
        $content = explode('</tbody>', $content)[0];
        $content = explode('<tbody>', $content)[1];

        $trs = explode('</tr>', $content);
        $count_str = count($trs);

        if ($count_str == 2) {
            return false;
        }

        $tr_first = $trs[0];
        //Name
        $name_sv = explode('<td style="width: 100px">', $tr_first)[1];
        $name_sv = explode('</td>', $name_sv)[0];

        //QH-2013-I/CQ-C-CLC
        $qh = explode('<td style="width: 100px">', $tr_first)[2];
        $qh = explode('</td>', $qh)[0];

        $arrLMH = [];
        for ($i = 0; $i < $count_str - 1; $i++) {
            $tr = $trs[$i];

            $maLMH = explode('<td style="width: 50px">', $tr)[1];
            $maLMH = explode('</td>', $maLMH)[0];

            $nhom = explode('<td style="width: 15px">', $tr)[1];
            $nhom = explode('</td>', $nhom)[0];
            if ($nhom == 'CL') {
                $nhom = 0;
            }

            $lmh = new stdClass();
            $lmh->maLMH = $maLMH;
            $lmh->nhom = intval($nhom);

            $arrLMH[] = $lmh;
        }

        return [
            'name' => $name_sv,
            'qh' => $qh,
            'timetable' => $arrLMH,
        ];
    }
}
