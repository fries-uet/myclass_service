<?php
/**
 * Created by PhpStorm.
 * User: Tu TV
 * Date: 20/11/2015
 * Time: 7:08 PM
 */

use Illuminate\Http\Request;

require_once __DIR__ . '/browser/FCurl.php';
require_once __DIR__ . '/browser/simple_html_dom.php';

/**
 * Only allowed POST Request | Abort 404 when request different POST request
 *
 * @param $request
 */
function onlyAllowPostRequest(Request $request)
{
    if (method_exists($request, 'getMethod')
        && $request->getMethod() !== 'POST'
    ) {
        abort(404);
    }
}

/**
 * Generate activate code
 *
 * @return string
 */
function generate_activate_code()
{
    $string = str_random(40);
    $activate_code = substr(md5($string), 0, 20);

    return $activate_code;
}

/**
 * Enqueue script
 *
 * @param $uri
 * @param string $version
 */
function enqueueScript($uri, $version = '1.0.0')
{
    echo '<script src="' . $uri . '?v=' . $version . '"></script>';
}

function getInfoStudent($maSV)
{
    $maSV = intval($maSV);

    if (strlen($maSV) !== 8) {
        return false;
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
        return false;
    }

    $trs = explode('</tr>', $content);
    $count_str = count($trs);

    if ($count_str == 2) {
        return false;
    }

    $tr_first = $trs[1];
    $sv = explode('</td><td>', $tr_first);

    $maSV_ = intval($sv[1]);
    if ($maSV_ != $maSV) {
        return false;
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
            return false;
        }
    }

    return [
        'msv' => $maSV_,
        'name' => $name_sv,
        'qh' => $qh,
        'timetable' => $arrLMH,
    ];
}

function getTimeTableVNU($user, $pass)
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
        return false;
    }

    /**
     * Lấy source trang thời khóa biểu
     */
    $url_time
        = 'http://dangkyhoc.daotao.vnu.edu.vn/xem-va-in-ket-qua-dang-ky-hoc/1?layout=main';
    $browser->get($url_time, 1, 0);
    $source_html = $browser->return;

    $timeTable
        = explode('<table style="border:none; width: 100%; border-collapse:collapse;">', $source_html)[1];
    $timeTable = explode('</table>', $timeTable)[0];

    $trs = explode('<tr>', $timeTable);
    $count_str = count($trs);

    $arrLMH = [];
    for ($i = 2; $i < $count_str - 1; $i++) {
        $tr = $trs[$i];

        $maLMH = explode('<td', $tr)[7];
        $maLMH = explode('</td>', $maLMH)[0];
        $maLMH = explode('&nbsp;', $maLMH)[1];

        $tenMH = explode('<td', $tr)[3];
        $tenMH = explode('</td>', $tenMH)[0];
        $tenMH = explode('&nbsp;', $tenMH)[1];
        $tenMH = html_entity_decode($tenMH);

        $maMH = explode('<td', $tr)[2];
        $maMH = explode('</td>', $maMH)[0];
        $maMH = explode('&nbsp;', $maMH)[1];

        $thu = explode('<td', $tr)[8];
        $thu = explode('</td>', $thu)[0];
        $thu = explode('>', $thu)[1];

        $tiets = explode('<td', $tr)[9];
        $tiets = explode('</td>', $tiets)[0];
        $tiets = explode('>', $tiets)[1];
        $tiets = str_slug($tiets);

        $addressX = explode('<td', $tr)[10];
        $addressX = explode('</td>', $addressX)[0];
        $addressX = explode('>', $addressX)[1];
        $addressX = html_entity_decode($addressX);

        $sub = new stdClass();
        $sub->maMH = $maMH;
        $sub->maLMH = $maLMH;
        $sub->name = $tenMH;
        $sub->thu = $thu;
        $sub->tiets = $tiets;
        $sub->address = $addressX;

        $arrLMH[] = $sub;
    }

    /**
     * Lấy tên sinh viên
     */
    $name = explode('Chào mừng: ', $contentX)[1];
    $name = explode('<', $name)[0];
    $name = trim($name);
    $name = html_entity_decode($name);

    return [
        'timetable' => $arrLMH,
        'name' => $name,
    ];
}

function getTimeTableUET($maSV)
{
    $url
        = 'http://203.113.130.218:50223/congdaotao/module/qldt/?SinhvienLmh%5BmasvTitle%5D=' . $maSV
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