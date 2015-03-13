<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/29/14
 * Time: 4:18 PM
 */
class Setting
{
    private static $_instance = null;
    private $settings = array();

    public function __construct()
    {
        $this->reload();
    }

    public function get($key, $default = '')
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }

        return $default;
    }

    public function reload()
    {
        $all_settings = HtSetting::model()->findAll();
        foreach ($all_settings as $item) {
            $this->settings[$item['key']] = $item['value'];
        }
    }

    public static function instance($className = __CLASS__)
    {
        if (self::$_instance == null) {
            self::$_instance = new $className(null);
        }

        return self::$_instance;
    }
}