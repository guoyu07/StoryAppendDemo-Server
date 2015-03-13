<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 12/5/14
 * Time: 12:02 PM
 */
class CacheUtility
{
    public static function deleteCaches($keys)
    {
        if (is_array($keys)) {
            foreach ($keys as $key) {
                Yii::app()->cache->delete($key);
            }
        } else {
            Yii::app()->cache->delete($keys);
        }
    }
} 