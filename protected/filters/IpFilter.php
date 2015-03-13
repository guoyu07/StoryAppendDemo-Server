<?php
/**
 * Created by PhpStorm.
 * User: hotblue
 * Date: 14-4-22
 * Time: 下午4:38
 */

class IpFilter extends CFilter{

    protected function preFilter($filterChain)
    {
        $return = false;
        $userIp = Yii::app()->request->userHostAddress;//获取用户IP
        $channel = Yii::app()->request->getParam('channel');
        $result = HtIpFilter::model()->findByAttributes(
            array('filter_type'=>'ip_white_list','channel'=>$channel)
        );//数据库中获取ip白名单集合
        if (empty($result)) {
            return false;
        }
        $ipWhiteList = $result->attributes['ips'];
        $ipWhiteListArr = explode(",",$ipWhiteList);//逗号区分每个IP白名单
        if(is_array($ipWhiteListArr)){
            foreach($ipWhiteListArr as $ip){
                if(false !== ($pos = strpos($ip,'-'))){//IP段形式白名单判断（横线区分）
                    $from = ip2long(trim(substr($ip, 0, $pos)));
                    $to = ip2long(trim(substr($ip, $pos+1)));
                    $userIpLong = ip2long($userIp);
                    if($userIpLong >= $from && $userIpLong <= $to){
                        $return = true;
                    }
                }else if(false !== ($pos = strpos($ip,'*'))){//带*IP白名单判断
                    if(substr($userIp,0,strrpos($userIp,'.')) === trim(substr($ip,0,$pos-1))){
                        $return = true;
                    }
                }
                else{
                    if($ip === $userIp){//单独IP白名单判断
                        $return = true;
                    }
                }
            }
        }
        return $return;
    }

} 