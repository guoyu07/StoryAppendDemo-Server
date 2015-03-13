<?php
/**
 * @project hitour.server
 * @file Sms.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-8-6 下午2:03
 **/

class Sms {
    private $check = 'http://self.zucp.net';
    private $gate = 'http://sdk.entinfo.cn:8060/webservice.asmx/';
    private $account = 'SDK-BBX-010-19693';
    private $password = 'f-5c]c-4';
    private $params = array();

    public function __construct()
    {

    }

    private function setParams($key, $value)
    {
        $key = strtolower($key);
        if ($key == 'pwd') {
            $this->params[$key] = strtoupper(md5($this->params['sn'] . $value));
        }else{
            $this->params[$key] = iconv("UTF-8", "GB2312//IGNORE", $value);
        }
    }

    private function parseResult($xmldata)
    {
        $retdata = '';
        $xmldom = new DOMDocument();
        $xmldom->loadXML($xmldata);

        $items = $xmldom->getElementsByTagName('string');
        if (!empty($items) && $items->item(0)) {
            $retdata = $items->item(0)->nodeValue;
        }
        return $retdata;
    }

    public function register()
    {
        $result = array('code' => 200, 'msg' => 'Register OK');
        $this->setParams('op', 'Register');
        $this->setParams('Sn', $this->account);
        $this->setParams('Pwd', $this->password);
        $this->setParams('Province', '北京');
        $this->setParams('City', '北京');
        $this->setParams('Trade', '互联网');
        $this->setParams('Entname', '北京欢途智行信息技术有限公司');
        $this->setParams('Linkman', '南欢');
        $this->setParams('Phone', '84765088');
        $this->setParams('Mobile', '18911596805');
        $this->setParams('Email', 'huanhuan@hitour.cc');
        $this->setParams('Fax', '84765088');
        $this->setParams('Address', '北京市朝阳区望京SOHO Tower 1 B座1201');
        $this->setParams('Postcode', 100012);
        $this->setParams('Sign', '');
        $data = $this->request();
        if ($data == 'Fail') {
            $result['code'] = 401;
            $result['msg'] = 'Load url interface failed.';
        }else{
            $vars = explode(' ', $data);
            if ($vars[0] !== '0') {
                $result['code'] = 400;
                $result['msg'] = 'Repeat register:['.$data.']';
            }
        }
        return $result;
    }

    public function send($to, $content, $ext = '', $assign_time = '', $batch = 0)
    {
        $result = array('code' => 200, 'msg' => 'Send OK');

        if (empty($to) || empty($content)) {
            $result['code'] = 400;
            return $result;
        }
        if (strlen($content) > 750) {
            $result['code'] = 401;
            return $result;
        }
        $suffix = '【玩途】';
        if ($batch > 0) {
            $suffix = '【玩途自由行】';
            $ext = 1;
        }

        $this->setParams('op', 'mt');
        $this->setParams('Sn', $this->account);
        $this->setParams('Pwd', $this->password);
        $this->setParams('Mobile', $to);
        $this->setParams('Content', $content.$suffix);
        $this->setParams('Ext', $ext);
        $this->setParams('stime', $assign_time);
        $this->setParams('Rrid', '');
        $data = $this->request();
        if ($data == 'Fail') {
            $result['code'] = 401;
            $result['msg'] = 'Load url interface failed.';
        }else{
            if (empty($data)) {
                $result['code'] = 402;
                $result['msg'] = 'Return code is empty';
            }else{
                $result['data'] = $data;
                $this->saveHistory($to, $content.$suffix, $data, $batch);
            }
        }
        return $result;
    }

    private function saveHistory($to, $content, $data, $batch = 0)
    {
        $smsHistory = new HtSmsHistory();
        $smsHistory['batch_id'] = $batch;
        $smsHistory['content'] = $content;
        $smsHistory['mobile'] = $to;
        $smsHistory['rrid'] = $data;
        $smsHistory->insert();
    }

    public function balance()
    {
        $result = array('code' => 200, 'msg' => 'Balance OK');
        $this->setParams('op', 'balance');
        $this->setParams('Sn', $this->account);
        $this->setParams('Pwd', $this->password);
        $data = $this->request();
        if ($data == 'Fail') {
            $result['code'] = 401;
            $result['msg'] = 'Load url interface failed.';
        }else{
            if (empty($data)) {
                $result['code'] = 402;
                $result['msg'] = 'Return code is empty';
            }else{
                $result['data'] = $data;
            }
        }
        return $result;
    }

    public function chargUp($cardno, $cardpwd)
    {
        $result = array('code' => 200, 'msg' => 'ChargUp OK');
        if (empty($cardno) || empty($cardpwd)) {
            $result['code'] = 400;
            $result['msg'] = 'cardno or cardpwd is empty.';
            return $result;
        }
        $this->setParams('op', 'ChargUp');
        $this->setParams('Sn', $this->account);
        $this->setParams('Pwd', $this->password);
        $this->setParams('cardno', $cardno);
        $this->setParams('cardpwd', $cardpwd);
        $data = $this->request();
        if ($data == 'Fail') {
            $result['code'] = 401;
            $result['msg'] = 'Load url interface failed.';
        }else{
            if (empty($data)) {
                $result['code'] = 402;
                $result['msg'] = 'Return code is empty';
            }else{
                $result['data'] = $data;
            }
        }
        return $result;
    }

    public function receive()
    {
        $result = array('code' => 200, 'msg' => 'Receive OK');
        $this->setParams('op', 'mo');
        $this->setParams('Sn', $this->account);
        $this->setParams('Pwd', $this->password);
        $data = $this->request();
        if ($data == 'Fail') {
            $result['code'] = 401;
            $result['msg'] = 'Load url interface failed.';
        }else{
            if (empty($data)) {
                $result['code'] = 402;
                $result['msg'] = 'Return code is empty';
            }else{
                $result['data'] = $data;
            }
        }
        return $result;
    }

    private function request()
    {
        $data = '';
        $url = $this->gate . $this->params['op'];
        $result = HTTPRequest::smsRequest($url, $this->params);
        if ($result['Status'] == 'OK' && !empty($result['Content'])) {
            $data = $this->parseResult($result['Content']);
        }else{
            $data = 'Fail';
        }
        $this->params = array();
        return $data;
    }
}