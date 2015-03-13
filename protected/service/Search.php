<?php
/**
 * @project hitour.server
 * @file Search.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 15-2-6 上午11:21
 **/

class Search extends CComponent
{
    private $city_code = '';

    public function init()
    {
        return true;
    }

    public function setCity($city_code)
    {
        if (!empty($city_code)) {
            $this->city_code = $city_code;
        }
    }

    public function query($words, &$hwords = array())
    {
        $products = array();
        if (empty($words)) {
            return $products;
        }

        $words = CodeUtility::utf8SubString($words, 0, 128);
        $words = $this->specialCharsFilter($words);
        $vars = explode(' ', $words);
        $swords = array_unique($vars);

        $sql = 'SELECT product_id FROM ht_product WHERE city_code="'.$this->city_code.'" AND status=3';
        $results = Yii::app()->db->createCommand($sql)->queryAll();
        if ($results && count($results) > 0) {
            foreach($results as $rkey => $result) {
                $product_id = $result['product_id'];
                if (HtProduct::model()->isProductVisible($product_id)) {
                    $sql = 'SELECT name,service_include FROM ht_product_description WHERE product_id="'.$product_id.'" AND language_id=2';
                    $res = Yii::app()->db->createCommand($sql)->queryRow();
                    if ($res) {
                        $search_text = array();
                        $search_text[] = $res['name'];
                        $search_text[] = $this->fetchDataFromRaw($res['service_include']);
                        $hwords_m = array();
                        if ($this->match($search_text, $swords, $hwords_m)) {
                            $products[] = HtProduct::getProductInfo($product_id);
                            $hwords = array_merge($hwords, $hwords_m);
                        }
                    }
                }
            }
        }
        $hwords = array_unique($hwords);
        return $products;
    }

    private function match($texts, $words, &$hwords)
    {
        $matched = false;
        foreach($words as $vkey => $word) {
            foreach($texts as $tkey => $text) {
                if (strstr($text, $word) !== false) {
                    $hwords = array_merge($hwords, array($word));
                    break;
                }
            }
        }
        if (count($words) == count($hwords)) {
            $matched = true;
        }else{
            $hwords = array();
        }
        return $matched;
    }

    private function fetchDataFromRaw($content)
    {
        $ret_str = '';
        $contents = json_decode(urldecode($content));
        $content = (string)($contents->md_text);
        $contents = explode('##', $content);
        if (!empty($contents) && is_array($contents)) {
            $content = '';
            foreach($contents as $ckey => $citem) {
                if (strstr($citem, '服务包含') !== false ||
                    strstr($citem, '包含服务') !== false) {
                    $content = $citem;
                    break;
                }
            }
            $content = str_replace('服务包含', '', $content);
            $content = str_replace('包含服务', '', $content);
            for($i = 1; $i <= 20; $i++) {
                $content = str_replace($i . '. ', '1. ', $content);
            }
            $vars = explode('1.', $content);
            if (!empty($vars) && is_array($vars)) {
                foreach($vars as $vkey => $item) {
                    if (!empty($item)) {
                        $ret_str .= ' '.$item.' ';
                    }
                }
            }else{
                $ret_str = ' '. $content . ' ';
            }
        }else{
            $ret_str = ' '. $content .' ';
        }
        return $ret_str;
    }

    private function specialCharsFilter($str){
        $str = str_replace('`', ' ', $str);
        $str = str_replace('·', ' ', $str);
        $str = str_replace('~', ' ', $str);
        $str = str_replace('!', ' ', $str);
        $str = str_replace('！', ' ', $str);
        $str = str_replace('@', ' ', $str);
        $str = str_replace('#', ' ', $str);
        $str = str_replace('$', ' ', $str);
        $str = str_replace('￥', ' ', $str);
        $str = str_replace('%', ' ', $str);
        $str = str_replace('^', ' ', $str);
        $str = str_replace('……', ' ', $str);
        $str = str_replace('&', ' ', $str);
        $str = str_replace('*', ' ', $str);
        $str = str_replace('(', ' ', $str);
        $str = str_replace(')', ' ', $str);
        $str = str_replace('（', ' ', $str);
        $str = str_replace('）', ' ', $str);
        $str = str_replace('-', ' ', $str);
        $str = str_replace('_', ' ', $str);
        $str = str_replace('——', ' ', $str);
        $str = str_replace('+', ' ', $str);
        $str = str_replace('=', ' ', $str);
        $str = str_replace('|', ' ', $str);
        $str = str_replace('\\', ' ', $str);
        $str = str_replace('[', ' ', $str);
        $str = str_replace(']', ' ', $str);
        $str = str_replace('【', ' ', $str);
        $str = str_replace('】', ' ', $str);
        $str = str_replace('{', ' ', $str);
        $str = str_replace('}', ' ', $str);
        $str = str_replace(';', ' ', $str);
        $str = str_replace('；', ' ', $str);
        $str = str_replace(':', ' ', $str);
        $str = str_replace('：', ' ', $str);
        $str = str_replace('\'', ' ', $str);
        $str = str_replace('"', ' ', $str);
        $str = str_replace('“', ' ', $str);
        $str = str_replace('”', ' ', $str);
        $str = str_replace(',', ' ', $str);
        $str = str_replace('，', ' ', $str);
        $str = str_replace('<', ' ', $str);
        $str = str_replace('>', ' ', $str);
        $str = str_replace('《', ' ', $str);
        $str = str_replace('》', ' ', $str);
        $str = str_replace('.', ' ', $str);
        $str = str_replace('。', ' ', $str);
        $str = str_replace('/', ' ', $str);
        $str = str_replace('、', ' ', $str);
        $str = str_replace('?', ' ', $str);
        $str = str_replace('？', ' ', $str);
        return trim($str);
    }
}