<?php
/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 4/18/14
 * Time: 3:50 PM
 */
function upload_to_qiniu($filepath)
{
    require_once("qiniu/io.php");
    require_once("qiniu/rs.php");

    $bucket = "hitour";
    $qiniu_link = '';

    $names = pathinfo($filepath);
    $qiniu_key = md5($filepath) . (empty($names['extension']) ? '' : '.' . $names['extension']);

    $putPolicy = new Qiniu_RS_PutPolicy($bucket);
    $upToken = $putPolicy->Token(null);
    $putExtra = new Qiniu_PutExtra();
    $putExtra->Crc32 = 1;
    list($ret, $err) = Qiniu_PutFile($upToken, $qiniu_key, $filepath, $putExtra);
//	echo "[" . (date('Ymd H:i:s', time())) . "] ====> Qiniu_PutFile result: \n";
    if ($err !== null) {
        if ($err->Code == 614) {
            $qiniu_link = 'http://hitour.qiniudn.com/' . $qiniu_key;
        }
//		echo $filepath . "\n";
//		var_dump($err);
    } else {
//		var_dump($ret);
        if (!empty($ret['key'])) {
            $qiniu_link = 'http://hitour.qiniudn.com/' . $ret['key'];
        }
    }

    return $qiniu_link;
}
