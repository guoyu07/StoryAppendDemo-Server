<?php

class Converter
{
    /**
     * Created by PhpStorm.
     * User: wenzi
     * Date: 5/5/14
     * Time: 2:02 PM
     */
    public static function convertModelToArray($models, array $filterAttributes = null)
    {
        if (is_null($models))
            return null;
        if (is_array($models))
            $arrayMode = TRUE;
        else {
            $models = array($models);
            $arrayMode = FALSE;
        }
        $result = array();
        foreach ($models as $model) {
            $attributes = $model->getAttributes();
            if (isset($filterAttributes) && is_array($filterAttributes)) {
                foreach ($filterAttributes as $key => $value) {
                    if (strtolower($key) == strtolower($model->tableName()) && strpos($value, '*') === FALSE) {
                        $value = str_replace(' ', '', $value);
                        $arrColumn = explode(",", $value);
                        foreach ($attributes as $key => $value)
                            if (!in_array($key, $arrColumn))
                                unset($attributes[$key]);
                    }
                }
            }
            $relations = array();
            foreach ($model->relations() as $key => $related) {
                if ($model->hasRelated($key)) {
                    if (($model->$key instanceof CModel) || is_array($model->$key)) {
                        $relations[$key] = self::convertModelToArray($model->$key, $filterAttributes);
                    } else {
                        if ($related[0] != CActiveRecord::STAT && $related[0] != CActiveRecord::BELONGS_TO && $related[0] != CActiveRecord::HAS_ONE && is_null($model->$key)) {
                            $relations[$key] = array();
                        } else {
                            $relations[$key] = $model->$key;
                        }
                    }
                }
            }
            $all = array_merge($attributes, $relations);
            if ($arrayMode)
                array_push($result, $all);
            else
                $result = $all;
        }
        return $result;
    }

    public static function parseMdHtml($orig_str, $decode_type = 'url')
    {
        if ($decode_type == 'url') {
            $tmp = rawurldecode($orig_str);
        } else if ($decode_type == 'html') {
            $tmp = html_entity_decode($orig_str);
        } else {
            return $orig_str;
        }
        $tmp_arr = json_decode($tmp, true);
        return isset($tmp_arr['md_html']) ? $tmp_arr['md_html'] : $tmp;
    }

    public static function mergeResult($result)
    {
        if (empty($result) || !is_array($result)) {
            return array('code'=>500, 'msg'=>'Fatal error');
        }else{
            $rcode = 200;
            $rmsg = '';
            foreach($result as $rkey => $row) {
                if ($row['code'] > $rcode) {
                    $rcode = $row['code'];
                }
                if ($row['code'] > 200) {
                    $rmsg .= '[code:'.$row['code'].';msg:'.$row['msg'].']';
                }
            }
            return array('code'=>$rcode, 'msg'=>$rmsg);
        }
    }

    public static function translateWeekday($str_date)
    {
        $eweeks = array(0=>'周日',1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六');
        $zweeks = array(0=>array('周日','星期天','Sunday'),
                        1=>array('周一','星期一','Monday'),
                        2=>array('周二','星期二','Tuesday'),
                        3=>array('周三','星期三','Wednesday'),
                        4=>array('周四','星期四','Thursday'),
                        5=>array('周五','星期五','Friday'),
                        6=>array('周六','星期六','Saturday'));
        foreach($zweeks as $zkey => $zday) {
            foreach($zday as $zstr) {
                $str_date = str_replace($zstr, $eweeks[$zkey], $str_date);
            }
        }
        return $str_date;
    }
}

?>