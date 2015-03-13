<?php
/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/6/14
 * Time: 11:57 AM
 */

define("TOKEN", "hitour");
define("APP_ID", "wx68125d5ef326531f");
define("APP_SECRET", "c3e07541a5b4be5eeb89d503f0aa0304");
define("ACCESS_TOKEN_TIMEOUT", 7200);

class WeChatServiceController extends Controller
{

    private function getAccessToken()
    {
        $item = HtWechatAccessToken::model()->find();
        if (empty($item)) {
            list($access_token, $expires_in) = $this->requestAccessToken();
            if (!empty($access_token)) {
                $item = new HtWechatAccessToken();
                $item['status'] = 1;
                $item['access_token'] = $access_token;
                $item['update_time'] = time();
                $item['expires_in'] = $expires_in;
                $item->insert();
            }

            return $access_token;
        } else {
            if ($item['status'] == 1) {
                // TODO check the update_time and request new access_token if needed
                $update_time = (int)$item['update_time'];
                $expires_in = (int)$item['expires_in'];
                if (time() - $update_time > ($expires_in - 60)) {
                    list($access_token, $expires_in) = $this->requestAccessToken();
                    $item['access_token'] = $access_token;
                    $item['update_time'] = time();
                    $item['expires_in'] = $expires_in;

                    $item->update();
                }

                return $item['access_token'];
            } else {
                // TODO wait a moment?
                return '';
            }
        }
    }

    private function requestAccessToken()
    {
        $ret = array('', 100);
        //  request a new access token
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . APP_ID . "&secret=" . APP_SECRET;
        $result = HTTPRequest::request($url);
        if ($result['Status'] == 'OK') {
            $data = CJSON::decode($result['content'], true);
            if (isset($data['errcode'])) {
                Yii::log('Error occurred, error code: ' . $data['errcode'] . ', error message: ' . $data['errmsg'],
                         CLogger::LEVEL_ERROR);
            } else {
                $ret = array($data['access_token'], $data['expires_in']);
            }
        } else {
            Yii::log('Failed to get access_token: ' . $result['info'], CLogger::LEVEL_ERROR);
        }

        return $ret;
    }

    public function actionQRCode()
    {
        HtWechatQrcode::model()->deleteAll('unix_timestamp() - create_time > expire_seconds');

        $rand = mt_rand(1, 2147483647);
        $item = HtWechatQrcode::model()->findByPk($rand);
        while (!empty($item)) {
            $rand = mt_rand(1, 2147483647);
            $item = HtWechatQrcode::model()->findByPk($rand);
        }

        $item = new HtWechatQrcode();
        $item['scene_id'] = $rand;
        $item['ticket'] = '';
        $item['expire_seconds'] = 0;
        $item['create_time'] = 0;
        $item['action_name'] = 'QR_SCENE';

        $item->insert();

        // TODO request ticket
        $access_token = $this->getAccessToken();
        if (empty($access_token)) {
            HtWechatQrcode::model()->deleteByPk($rand);
            EchoUtility::echoCommonFailed('获取AccessToken失败，未能生成QRCode');
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
            $post = CJSON::encode(array('expire_seconds' => 1800, 'action_name' => 'QR_SCENE',
                                      'action_info' => array('scene' => array('scene_id' => $rand))));

            $result = HTTPRequest::request($url, 15, null, $post);

            if ($result['Status'] == 'OK') {
                $data = CJSON::decode($result['content'], true);
                if (isset($data['errcode'])) {
                    Yii::log('Error occurred, error code: ' . $data['errcode'] . ', error message: ' . $data['errmsg'],
                             CLogger::LEVEL_ERROR);
                    EchoUtility::echoCommonFailed('获取QRCode所需Ticket失败。');
                } else {
                    $ticket = $data['ticket'];
                    $expire_seconds = $data['expire_seconds'];

                    $item['ticket'] = $ticket;
                    $item['create_time'] = time();
                    $item['expire_seconds'] = $expire_seconds;
                    $item->update();

                    EchoUtility::echoCommonMsg(true, '', array(
                        'scene_id' => $rand,
                        'ticket' => $ticket,
                        'url' => rawurlencode('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket)));
                }
            } else {
                Yii::log('Failed to get ticket for qrcode: ' . $result['info'], CLogger::LEVEL_ERROR);
                EchoUtility::echoCommonFailed('获取QRCode所需Ticket失败。');
            }
        }
    }


    public function actionTest()
    {
        $access_token = $this->getAccessToken();
        echo 'access_token: ' . $access_token;
    }

}