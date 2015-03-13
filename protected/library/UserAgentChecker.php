<?php
/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 9/2/14
 * Time: 6:39 PM
 */

class UserAgentChecker {

    public static function isWindows($user_agent) {
        if(strpos(strtolower($user_agent), 'windows') !== false) {
            return true;
        }
        return false;
    }

} 