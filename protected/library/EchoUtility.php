<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/5/14
 * Time: 3:14 PM
 */
class EchoUtility
{

    public static function echoCommonFailed($msg, $code = 400, $data = array())
    {
        echo CJSON::encode(array('code' => $code, 'msg' => $msg, 'data' => $data));
    }

    public static function echoMsgTF($result, $option = '保存', $data = array())
    {
        if ($result) {
            echo CJSON::encode(array('code' => 200, 'msg' => $option . '成功！', 'data' => $data));
        } else {
            echo CJSON::encode(array('code' => 400, 'msg' => $option . '失败！', $data));
        }
    }

    public static function echoMsg($result, $base = '', $failed_msg = '', $data = array())
    {
        switch ($result) {
            case -1:
                echo CJSON::encode(array('code' => 401, 'msg' => $failed_msg ? $failed_msg : '未找到' . $base));
                break;
            case 0:
                echo CJSON::encode(array('code' => 400, 'msg' => '保存失败！'));
                break;
            case 1:
                echo CJSON::encode(array('code' => 200, 'msg' => '保存成功！', 'data' => $data));
                break;
        }
    }

    public static function echoByResult($data, $success_msg = 'Ok.', $failed_msg = 'Failed.')
    {
        if (count($data) > 0) {
            echo CJSON::encode(array('code' => 200, 'msg' => $success_msg, 'data' => $data));
        } else {
            echo CJSON::encode(array('code' => 400, 'msg' => $failed_msg));
        }
    }

    public static function echoCommonMsg($code, $msg = '', $data = array())
    {
        echo CJSON::encode(array('code' => $code, 'msg' => $msg, 'data' => $data));
    }


    public static function echoJson($data, $options = 271)
    {
        $result = ['code' => 200, 'msg' => 'OK', 'data' => $data];
        echo json_encode($result, $options);
    }
}