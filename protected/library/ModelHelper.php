<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/5/14
 * Time: 4:05 PM
 */
class ModelHelper
{

    public static function fillItem(&$item, $data, $columns = array())
    {
        if (is_null($item) || empty($data)) {
            return -1;
        }
        if (empty($columns)) {
            foreach ($data as $key => $value) {
                $item[$key] = $value;
            }
        } else {
            foreach ($columns as $column) {
                if (isset($data[$column])) {
                    $item[$column] = $data[$column];
                }
            }
        }

        return 1;
    }

    public static function updateItem($item, $data, $columns = array())
    {
        if(empty($item)) {
            return -1;
        }
        $result = ModelHelper::fillItem($item, $data, $columns);
        if ($result == 1) {
            $result = $item->update();

            return $result ? 1 : 0;
        }

        return $result;
    }


    public static function getList($data, $column_name)
    {
        $result = array();
        foreach ($data as $item) {
            if (isset($item[$column_name])) {
                array_push($result, $item[$column_name]);
            }
        }

        return $result;
    }

    public static function fixDateValue(&$item, $date_field)
    {
        if (is_array($date_field)) {
            foreach ($date_field as $field) {
                if (empty($item[$field])) {
                    $item[$field] = '0000-00-00';
                } else if (strlen($item[$field]) > 10) {
                    $item[$field] = substr($item[$field], 0, 10);
                }
            }
        } else if (empty($item[$date_field])) {
            $item[$date_field] = '0000-00-00';
        } else if (strlen($item[$date_field]) > 10) {
            $item[$date_field] = substr($item[$date_field], 0, 10);
        }
    }

} 