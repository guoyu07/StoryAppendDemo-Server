<?php
/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 8/4/14
 * Time: 11:44 AM
 */

function array_filter_by_keys($array, $keys = array())
{
    if (empty($keys))
        return $array;

    $result = array();

    foreach ($keys as $k) {
        if (isset($array[$k])) {
            $result[$k] = $array[$k];
        }
    }

    return $result;
}

function getAge($birth_date)
{
    $year = $month = $day = 0;

    if (is_array($birth_date)) {
        extract($birth_date);
    } else {
        if (strpos($birth_date, '-') !== false) {
            list($year, $month, $day) = explode('-', $birth_date);
            $day = substr($day, 0, 2); //get the first two chars in case of '2000-11-03 12:12:00'
        }
    }
    $age = date('Y') - $year;
    if (date('m') < $month || (date('m') == $month && date('d') < $day)) $age--;

    return $age;
}

//手机号码归属地查询
function getTelephoneZone($telephone)
{
    $mobileZoneApi = 'https://www.baifubao.com/callback?cmd=1059&callback=phone&phone=';
    $raw_result = file_get_contents($mobileZoneApi . $telephone);
    $head = strpos($raw_result, '{');
    $tail = strrpos($raw_result, '}');

    $zone = '';
    if ($head !== false && $tail > 0) {
        $raw_result = substr($raw_result, $head, $tail - $head + 1);
        $api_result = json_decode($raw_result, true);
        if ($api_result && !empty($api_result['data']['area'])) {
            $zone = '(' . $api_result['data']['area'] . ')';
        }
    }

    return $zone;
}

