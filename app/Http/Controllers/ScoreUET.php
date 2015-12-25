<?php

namespace App\Http\Controllers;

use Exception;
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
