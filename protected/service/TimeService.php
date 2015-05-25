<?php
/**
 * Created by PhpStorm.
 * User: Veaer
 * Date: 15/5/22
 * Time: 上午11:38
 */

class TimeService {

    public function init() {
        return true;
    }

    public function getTime() {
        $time = date('Y-m-d H:i:s');
        return $time;
    }

    public function getNow() {
        $now = date('Y-m-d');
        return $now;
    }

    public function getIndex() {
        $now = strtotime($this->getNow());
        $startTime = strtotime('2015-04-21');
        return ($now - $startTime)/86400;
    }

}