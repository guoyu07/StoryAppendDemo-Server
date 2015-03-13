<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 6/21/14
 * Time: 12:30 PM
 */
class Product
{
    const MAP_URL_PREFIX = 'http://maps.google.com/maps?q=';

    static function lands_cmp($a, $b)
    {
        return count($a) - count($b);
    }

    public function init()
    {
        require_once('qrcode/full/qrlib.php');

        return true;
    }

    public function getNextProduct($product_id)
    {
        $criteria = new CDbCriteria;
        $criteria->addCondition('p.city_code =(SELECT city_code FROM ht_product tmp WHERE product_id =:pid) ');
        $criteria->addCondition('p.product_id <>:pid');
        $criteria->params = [':pid' => $product_id];
        $criteria->order = 'RAND()';

        $next = HtProduct::model()->with('city.country', 'description', 'cover_image')->published()->find($criteria);
        if(!$next['product_id']) {
            return array();
        }
        $next = Converter::convertModelToArray($next);
        $next['show_prices'] = HtProductPricePlan::model()->getShowPrices($next['product_id']);

        return $next;
    }

    public function getSimplifiedData($product_id, $start_date = '')
    {
        if((int)$product_id != $product_id || 0 == $product_id) {
            return false;
        }

        $product = Converter::convertModelToArray(HtProduct::model()->with(['description' => ['select' => 'name,summary,benefit,service_include'], 'cover_image'])->findByPk($product_id));
        if(empty($product)) {
            return $product;
        }
        $product['rules'] = $this->getRuleDesc($product_id);
        $product['description']['service_include'] = $this->refineServiceInclude($product['description']['service_include']);
        $product['show_prices'] = HtProductPricePlan::model()->getShowPrices($product_id, '', $start_date);

        return $product;
    }

    public function getRuleDesc($product_id)
    {
        return HtProduct::model()->getRuleDesc($product_id);
    }

    public function refineServiceInclude($raw)
    {
        $service_include = Converter::parseMdHtml($raw);
        $matched = [];
        $service_include = is_string($service_include) ? $service_include : '';
        $p1 = preg_match('/<h2\s?\S*>/', $service_include, $matched);
        if($p1 === 0) {
            return '';
        } else {
            return $service_include;
        }

//        $p1 = strpos($service_include, '<h2>');
//        if($p1 === false) {
//            return '';
//        } else {
//            $p2 = strpos($service_include, '<h2>', $p1 + 4);
//            if($p2 === false) {
//                return $service_include;
//            } else {
//                return substr($service_include, 0, $p2);
//            }
//        }
    }

    public function getBaseData($product_id)
    {
        $product = HtProduct::model()->with(['album_info', 'description', 'city.country'])->findByPk($product_id);
        $product = Converter::convertModelToArray($product);
        $product['rules'] = $this->getRuleDesc($product_id);
        $product['images'] = $this->getImages($product_id);

        //parse mark down
        $product['description']['how_it_works'] = Converter::parseMdHtml($product['description']['how_it_works']);
        $product['description']['service_include'] = Converter::parseMdHtml($product['description']['service_include']);

        $product['description']['package_service'] = array_filter(explode("\n", $product['description']['package_service']));
        $product['description']['package_gift'] = array_filter(explode("\n", $product['description']['package_gift']));
        $product['description']['package_recommend'] = array_filter(explode("\n", $product['description']['package_recommend']));

        return $product;
    }

    public function getImages($product_id)
    {
        $sliders = array();
        $sample = array();
        $cover = array();

        $image_raw = HtProductImage::model()->with('landinfo_image.mark_image')->findAllByAttributes(['product_id' => $product_id]);
        $images = Converter::convertModelToArray($image_raw);

        foreach($images as $img) {
            if($img['image_usage'] == 2) {
                $image = ['name' => isset($img['landinfo_image']['mark_image']['name']) ? $img['landinfo_image']['mark_image']['name'] : '', 'short_desc' => $img['landinfo_image']['reason'] ? $img['landinfo_image']['reason'] : '', 'image_url' => $img['landinfo_image']['image_url']];
            } else {
                $image = ['name' => $img['name'], 'short_desc' => $img['short_desc'], 'image_url' => (isset($img['image_url']) ? $img['image_url'] : '')];
            }

            if(empty($image['image_url'])) {
                continue;
            }

            if($img['image_usage'] == 0) {
                $sample = $image;
            } else {
                $sliders[] = $image;
            }

            if($img['as_cover']) {
                $cover = $img;
            }
        }

        return ['sliders' => $sliders, 'sample' => $sample, 'cover' => $cover];
    }

    public function getProductIntroduction($product_id)
    {
        $introduction = array();
        $intro_model = HtProductIntroduction::model()->findByPk($product_id);
        if($intro_model && $intro_model['status'] == 1) {
            $introduction['please_read']['buy_note'] = $this->addDivTagForIntroduction(Converter::parseMdHtml($intro_model['buy_note']));
            $introduction['please_read']['tips'] = $this->addDivTagForIntroduction(Converter::parseMdHtml($intro_model['tips']));
            $introduction['redeem_usage']['usage'] = $this->addDivTagForIntroduction(Converter::parseMdHtml($intro_model['usage']));
        }
        return $introduction;
    }

    
    private function addDivTagForIntroduction($str){
        return str_replace(['<h2','</ol>','</ul>'],['<div><h2','</ol></div>','</ul></div>'],$str);
    }

    public function getLandData($product_id)
    {
        $product = HtProduct::model()->with('album_info', 'qa')->findByPk($product_id);
        $product = Converter::convertModelToArray($product);

        $supplementary_information = Converter::parseMdHtml($product['album_info']['landinfo_md']);
        $supplementary_arr = json_decode($supplementary_information, true);
        if(!$supplementary_arr)
            $supplementary_arr = array();
        foreach($supplementary_arr as &$s) {
            if(isset($s['list']['md_html'])) {
                $s['list'] = $s['list']['md_html'];
            }
        }
        $product['all_landinfo'] = $supplementary_arr;
        unset($product['album_info']['landinfo_md']);

        //pick album
        $product['pick_landinfo_groups'] = $this->refinePickLandGroups($product['album_info']);
        $product['related'] = $this->getRelatedProducts($product_id);

        $product['qa'] = Converter::parseMdHtml($product['qa']['qa']);
        $album_info = $product['album_info'];

        if($album_info && $album_info['need_album'] && $album_id = $album_info['album_id']) {
            $album = Album::model()->with('album_info_ref.landinfo.landmark', 'communications',
                'additionals')->findByPk($album_id);
            $album = Converter::convertModelToArray($album);
            $lands = array();
            $land_names = array();
            $idx = 1;
            if($album) {
                foreach($album['album_info_ref'] as $ref) {
                    $l = $this->refineLandInfo($ref['landinfo']);
                    $l['pass_benefit'] = $ref['pass_benefit'];
                    $l['land_order'] = $ref['land_order'] == AlbumInfoRef::UNSORT ? ($ref['land_order'] + $idx++) : $ref['land_order'];
                    $land_names[] = $l['name'];
                    if(!isset($lands[$l['land_order']]))
                        $lands[$l['land_order']] = [];
                    $lands[$l['land_order']][] = $l;
                }
            }
            ksort($lands);
            $lands = array_merge($lands);
            $product['landinfo_groups'] = $lands;
            $product['communications'] = $album['communications'];
            $product['additionals'] = $album['additionals'];
        }

        //Tour plan
        $product['tour_plan'] = HtProductTourPlan::model()->getProductTourPlan($product_id);

        return $product;
    }

    public function getPickticketLandinfoData($product_id) {
        $album_info = HtProductAlbum::model()->findByPk($product_id);

        $pickLandGroups =  $this->refinePickLandGroups($album_info);

        return ['pick_ticket_map' => $album_info['pick_ticket_map'], 'pickLandGroups' => $pickLandGroups];
    }

    private function refinePickLandGroups($album_info)
    {
        $pick_land_groups = array();
        if($album_info && $album_info['need_pick_ticket_album'] && $pick_album_id = $album_info['pick_ticket_album_id']) {
            $pick_album = Album::model()->with('landinfos.landmark', 'additionals')->findByPk($pick_album_id);
            $pick_album = Converter::convertModelToArray($pick_album);
            $product['additionals'] = $pick_album['additionals'];
            $pick_lands = array();
            if($pick_album) {
                foreach($pick_album['landinfos'] as $land) {
                    $pick_lands[$land['landinfo_id']] = $this->refinePickLand($land);
                }
            }

            $pick_groups = CJSON::decode($album_info['pt_group_info']);
            if(!empty($pick_groups) && is_array($pick_groups)) {
                foreach($pick_groups as $pg) {
                    $pg_lands = array();
                    foreach($pg['items'] as $land_id) {
                        if(isset($pick_lands[$land_id]))
                            $pg_lands[] = $pick_lands[$land_id];
                    }
                    $pick_land_groups[] = ['title' => $pg['title'], 'landinfos' => $pg_lands];
                }
            }
        }

        return $pick_land_groups;
    }

    public function refinePickLand($land)
    {
        $result = array();
        $result['name'] = $land['landmark']['name'];
        $result['en_name'] = $land['landmark']['en_name'];
        $result['address'] = $land['landmark']['address'];
        $result['open_time'] = $land['landmark']['open_time'];
        $result['close_time'] = $land['landmark']['close_time'];
        $result['phone'] = $land['landmark']['phone'];
        $result['pass_benefit'] = $land['pass_benefit'];
        $result['image_url'] = $land['image_url'];
        $result['location_latlng'] = $land['location_latlng'];
        $result['website'] = $land['landmark']['website'];
        $result['communication'] = $land['landmark']['communication'];
        $result['map_url'] = self::MAP_URL_PREFIX . $land['location_latlng'];

        return $result;
    }

    public function getRelatedProducts($product_id)
    {
        $related_products = array();
        $related = HtProductRelated::model()->with('product.description',
            'product.cover_image')->findAllByAttributes(array('product_id' => $product_id));
        $related = Converter::convertModelToArray($related);
        foreach($related as $r) {
            if($r['product']['status'] != 3) {
                continue;
            }
            $p = $r['product'];
            $refine_p = ['product_id' => $p['product_id'], 'name' => $p['description']['name'], 'summary' => $p['description']['summary'], 'link_url' => $p['link_url'], 'image_url' => isset($p['cover_image']['image_url']) ? $p['cover_image']['image_url'] : '', 'show_prices' => HtProductPricePlan::model()->getShowPrices($p['product_id'])];
            $related_products[] = $refine_p;
        }

        return $related_products;
    }

    public function refineLandInfo($land)
    {
        $result = array();
        $result['name'] = $land['landmark']['name'];
        $result['en_name'] = $land['landmark']['en_name'];
        $result['reason'] = $land['reason'];
        $result['location_latlng'] = $land['location_latlng'];
        $result['image_url'] = $land['image_url'];

        return $result;
    }

    public function getSaleData($product_id, $spc = '')
    {
        $data['show_prices'] = HtProductPricePlan::model()->getShowPrices($product_id, $spc);
        $data['date_rule'] = $this->getDateRule($product_id);
        $data['special_info'] = $this->getSpecialCodes($product_id);
        $data['departure_rule'] = $this->getDepartureRule($product_id);
        $ticket_types = $this->getTicketTypes($product_id);
        $sale_rule = $this->getSaleRule($product_id);

        if($sale_rule['sale_in_package'] && $pr = $sale_rule['package_rules']) {
            $ticket_type_package = array();
            foreach($ticket_types as $tt) {
                if($tt['ticket_id'] == HtTicketType::TYPE_PACKAGE) {
                    $ticket_type_package = $tt;
                    break;
                }
            }
            $ticket_type_package['package_rule'] = $pr;
            $desc = '每套包含: ';
            foreach($pr as $sub_ticket) {
                $name = $ticket_types[$sub_ticket['ticket_id']]['cn_name'];
                if(empty($name)) {
                    $name = $ticket_types[$sub_ticket['ticket_id']]['ticket_type']['cn_name'];
                }
                $desc .= $name . '×' . $sub_ticket['quantity'];
                if(!empty($ticket_types[$sub_ticket['ticket_id']]['description']) && strtoupper($ticket_types[$sub_ticket['ticket_id']]['description']) != 'NULL') {
                    $desc .= '(' . $ticket_types[$sub_ticket['ticket_id']]['description'] . ')';
                }
                $desc .= ' + ';
            }
            $desc = substr($desc, 0, -3);
            $ticket_type_package['description'] = $desc;

            $ticket_types = [HtTicketType::TYPE_PACKAGE => $ticket_type_package];
        }

        unset($sale_rule['package_rules']);

        $data['ticket_types'] = $ticket_types;
        $data['sale_rule'] = $sale_rule;
        $data['price_plan'] = HtProductPricePlan::model()->getPricePlanWithMap($product_id);

        /*if(!function_exists('sortBySP')) {
            function sortBySP($sp1, $sp2)
            {
                global $sale;
                if(empty($sale['price_plan'][0]['price_map'])) {
                    return 0;
                }
                $price_map = $sale['price_plan'][0]['price_map'];
                $p1 = empty($price_map[$sp1['special_code']]) ? '' : $price_map[$sp1['special_code']];
                $p2 = empty($price_map[$sp2['special_code']]) ? '' : $price_map[$sp2['special_code']];

                $v1 = 0;
                if(is_array($p1)) {
                    foreach($p1 as $ty => $pi) {
                        foreach($pi as $quantity => $price) {
                            $v1 = $price['price'];
                            break;
                        }
                        break;
                    }
                }

                $v2 = 0;
                if(is_array($p2)) {
                    foreach($p2 as $ty => $pi) {
                        foreach($pi as $quantity => $price) {
                            $v2 = $price['price'];
                            break;
                        }
                        break;
                    }
                }

                if($v1 == $v2) {
                    return strcmp($sp1['cn_name'], $sp2['cn_name']);
                }

                return ($v1 < $v2) ? -1 : 1;
            }
        }
        usort($data['special_codes'], 'sortBySP');*/

        return $data;
    }

    /**
     * @param $product_id
     * @return mixed
     */
    public function getDateRule($product_id)
    {
        $tour_date_rule = array();
        $date_rule = HtProductDateRule::model()->findByPk($product_id);
        $date_rule = Converter::convertModelToArray($date_rule);

        $tour_date_rule['need_tour_date'] = $date_rule['need_tour_date'];
        if($tour_date_rule['need_tour_date']) {
            $tour_date_rule['close_dates'] = $date_rule['close_dates'];
            $operations = HtProductTourOperation::model()->findAllByAttributes(['product_id' => $product_id]);
            $operations = Converter::convertModelToArray($operations);
            $cur_day=date('N');//当前是星期几
            $buy_in_advance=(int)($date_rule['buy_in_advance']);
            if($buy_in_advance>0){
                if($date_rule['day_type'] == HtProductDateRule::WORKING_DAY) {//计算buy_in_advance天内有几个周末
                    $increase=(int)($buy_in_advance/7)*2;
                    if($cur_day-7+$buy_in_advance%7==-1){
                        $increase+=1;
                    }
                    else if($cur_day-7+$buy_in_advance%7>=0){
                        $increase+=2;

                    }
                    $start = date('Y-m-d',strtotime(($buy_in_advance+$increase).'day'));
                }
                else{
                    $start = date('Y-m-d',strtotime($date_rule['buy_in_advance']? $date_rule['buy_in_advance'] : ''));
                }
            }
            else{
                $start=date('Y-m-d');
            }
            $start = max($start, $date_rule['from_date']);
            $end = $date_rule['to_date'];
            if($date_rule['sale_range_type'] == 1) {
                $limit_end = date('Y-m-d', strtotime($date_rule['sale_range'] . '-1day'));
                $end = min($end, $limit_end);
            }

            $tour_date_rule['start'] = $start;
            $tour_date_rule['end'] = $end;
            $tour_date_rule['operations'] = $operations;
        }

        return $tour_date_rule;

    }

    /**
     * @param $product_id
     * @return array
     */
    public function getSpecialCodes($product_id)
    {
        $special_info = HtProductSpecialCombo::model()->getAllComboSpecialDetail($product_id);
        return empty($special_info) ? NULL : $special_info;
    }

    public function getDepartureRule($product_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('to_date >="' . date('Y-m-d') . '"');
        $criteria->addCondition('valid_region = 0', 'OR');
        $criteria->order = 'pdep.departure_point';
        $raw = HtProductDeparturePlan::model()->with('departure')->findAllByAttributes(['product_id' => $product_id],
            $criteria);
        $data = Converter::convertModelToArray($raw);
        foreach($data as $dkey => $drow) {
            if(empty($drow['departure_code'])) {
                unset($data[$dkey]);
            }
        }

        return $data;
    }

    /**
     * @param $product_id
     * @return array
     */
    public function getTicketTypes($product_id)
    {
        return HtProductTicketRule::model()->getTicketRuleMap($product_id);
    }

    public function getSaleRule($product_id)
    {
        $sale_rule = HtProductSaleRule::model()->with('package_rules.ticket_type')->findByPk($product_id);
        $sale_rule = Converter::convertModelToArray($sale_rule);
        $sale_rule['allow_use_coupon'] = 1;

        return $sale_rule;
    }

    public function getBundleProducts($product_id)
    {
        $bundles = HtProductBundle::model()->with('items')->findAllByAttributes(['product_id' => $product_id]);
        if ($bundles) {
            $bundles = Converter::convertModelToArray($bundles);
            foreach ($bundles as &$b) {
                $products = array();
                foreach ($b['items'] as $bi) {
                    $prod = array();
                    $p = Converter::convertModelToArray(HtProduct::model()->with('descriptions',
                                                                                 'cover_image')->findByPk($bi['binding_product_id']));
                    $prod['product_id'] = $p['product_id'];
                    $prod['type'] = $p['type'];
                    $prod['link_url'] = $p['link_url'];
                    $prod['cover_image_url'] = $p['cover_image']['image_url'];
                    foreach ($p['descriptions'] as $d) {
                        if ($d['language_id'] == 2) { //只取中文版
                            $prod['name'] = $d['name'];
                            $prod['summary'] = $d['summary'];
                        } else {
                            $prod['en_name'] = $d['name'];
                        }
                    }

                    if ($p['type'] == HtProduct::T_HOTEL) {
                        $prod['hotel'] = $this->getHotelInfo($p['product_id'],$product_id);
                        $price_plans = HtProductPricePlan::model()->getPricePlan($p['product_id']);
                        if(!empty($price_plans[0]['items'])){
                            foreach ( $price_plans[0]['items'] as &$pi) {
                                unset($pi['cost_price']);
                            }

                            $prod['price_plan_items'] = $price_plans[0]['items'];
                        }
                        $sale_data = $this->getSaleData($p['product_id']);
                        $prod['date_rule'] = $sale_data['date_rule'];
                        $prod['sale_rule'] = $sale_data['sale_rule'];
                        $prod['ticket_types'] = $sale_data['ticket_types'];
                        $prod['rules'] = $this->getRuleDesc($p['product_id']);
                        $prod['show_prices'] = $sale_data['show_prices'];
                    } else {
                        $prod['show_prices'] = HtProductPricePlan::model()->getShowPrices($p['product_id']);
                        $sale_data = $this->getSaleData($p['product_id']);
                        $prod['ticket_types'] = $sale_data['ticket_types'];
                    }

                    $prod['comment_stat'] = HtProductComment::getStatInfo($p['product_id']);
                    $bi['group_type'] = $b['group_type'];
                    $prod['bundle_info'] = $bi;
                    $products[] = $prod;
                }
                $b['products'] = $products;
                unset($b['items']);
            }
        }

        return $bundles;
    }

    public function getBundles($product_id)
    {
        $result = array();
        $bundles = $this->getBundleProducts($product_id);
        foreach ($bundles as $b) {
            if ($b['group_type'] == HtProductBundle::GT_SELECTION) {
                $result['hotel']['products'] = $b['products'];
                if (count($b['products']) > 1) {
                    $result['hotel']['title'] = sprintf('酒店 %d 选 1', count($b['products']));
                    $result['hotel']['desc'] = sprintf('玩途精心为您挑选%d家性价比超高的酒店，您可以选择', count($b['products']));
                } else {
                    $result['hotel']['title'] = '入住酒店';
                    $result['hotel']['desc'] = '玩途精心为您挑选性价比超高的酒店';
                }
            } else if ($b['group_type'] == HtProductBundle::GT_REQUIRED) {
                $result['complimentary']['products'] = $b['products'];
                $result['complimentary']['title'] = sprintf('玩途赠送 - %d项贴心服务', count($b['products']));
                $sub_total = 0;
                foreach ($b['products'] as $p) {
                    $sub_total += $p['show_prices']['price'];
                }
                if (!empty($sub_total)) {
                    $result['complimentary']['desc'] = sprintf('玩途共送您%d元大礼', $sub_total);
                }

            } else if ($b['group_type'] == HtProductBundle::GT_OPTIONAL) {
                $result['optional']['products'] = $b['products'];
                $result['optional']['title'] = '独享特惠';
                $discount = 0;
                foreach ($b['products'] as $p) {
                    if ($p['bundle_info']['discount_type'] == 'F') {
                        $discount += $p['bundle_info']['discount_amount'];
                    } else if ($p['bundle_info']['discount_type'] == 'P') {
                        $discount += (int)($p['show_prices']['price'] * $p['bundle_info']['discount_amount'] / 100);
                    } else {
                        $discount += 0;
                    }
                }
                $result['optional']['desc'] = sprintf('您还可以优惠购买以下服务，最多可省%d元/人', $discount);
            }
        }


        return $result;
    }

    public function getHotelInfo($product_id,$parent_id = 0)
    {
        $hotel = Converter::convertModelToArray(HtProductHotel::model()->with('room_types.images','room_types.services','room_types.policies','rates.source','bankcards.item')->findByPk($product_id));
        if($hotel){
            $hotel['highlight'] = array_values(array_filter(explode("\n",$hotel['highlight'])));
        }

        //需要剔除没有在套餐商品中出现的房型
        if(!empty($parent_id)){
            $filtered_room_types = array();

            //normal plan
            $special_codes = '';
            $price_plans = Converter::convertModelToArray(HtProductPricePlan::model()->findAllByAttributes(['product_id' => $parent_id]));
            foreach ($price_plans as $plan) {
                $special_codes.=$plan['special_codes'].';';
            }
            $special_codes = array_filter(explode(';',$special_codes));

//            $criteria = new CDbCriteria();
//            $criteria->addInCondition('special_code',$special_codes);
//            $special_codes = Converter::convertModelToArray(HtProductSpecialCode::model()->findAllByAttributes(['product_id'=>$parent_id],$criteria));

            $specials = array();
            $special_info = HtProductSpecialCombo::getSpecialDetail($parent_id);
            if(!empty($special_info)){
                foreach($special_info as $special){
                    if(in_array($special['special_id'],$special_codes)){
                        array_push($specials,$special['items'][0]);
                    }
                }
            }

            foreach ($hotel['room_types'] as $rt) {
                foreach ($specials as $sp) {
                    if($rt['special_code'] == $sp['mapping_special_code'] && $product_id == $sp['mapping_product_id']) {
                        $filtered_room_types[] = $rt;
                        break;
                    }
                }
            }
            $hotel['room_types'] = $filtered_room_types;
        }

        foreach($hotel['room_types'] as &$rt) {
            $rt['bed_policy_md'] = Converter::parseMdHtml($rt['bed_policy_md']);
            $rt['breakfast_md'] = Converter::parseMdHtml($rt['breakfast_md']);
        }

        return $hotel;
    }

    public function getDepartureListByDate($product_id, $date)
    {
        $criteria = new CDbCriteria();
        if(!empty($date) && $date != '0000-00-00') {
            $criteria->addCondition('to_date >="' . $date . '"');
            $criteria->addCondition('from_date <="' . $date . '"');
        }
        $criteria->order = 'pdep.departure_point';
        $raw = HtProductDeparturePlan::model()->with('departure')->findAllByAttributes(['product_id' => $product_id],
            $criteria);
        $data = Converter::convertModelToArray($raw);
        foreach($data as $dkey => $drow) {
            if(empty($drow['departure_code'])) {
                unset($data[$dkey]);
            }
            if($drow["additional_limit"] != "" && (stripos($drow["additional_limit"],
                        date('N', strtotime($date))) === false)
            ) {
                unset($data[$dkey]);
            }
        }

        return array_values($data);
    }

    public function getProductManual($product_id)
    {
        $files = array();
        $product_voucher_path = dirname(Yii::app()->BasePath) . Yii::app()->params['PRODUCT_VOUCHER_PATH'] . $product_id . DIRECTORY_SEPARATOR;
        if(!file_exists($product_voucher_path . $product_id . '.pdf') || true) { //TODO: true for TEST
            $this->generateProductManual($product_id);
        }

        if(is_dir($product_voucher_path)) {
            if($dh = opendir($product_voucher_path)) {
                while(($file = readdir($dh)) !== false) {
                    if(strpos($file, '.pdf')) {
                        $files[] = $product_voucher_path . $file;
                    }
                }
            }
        }

        return $files;
    }

    public function generateProductManual($product_id, $preview = false)
    {
        $product_voucher_path = dirname(Yii::app()->BasePath) . Yii::app()->params['PRODUCT_VOUCHER_PATH'] . $product_id . DIRECTORY_SEPARATOR;
        if(!file_exists($product_voucher_path)) {
            mkdir($product_voucher_path, 0755, true);
        }

        $product_data = $this->getVoucherData($product_id);
        foreach($product_data['pick_landinfo_groups'] as &$pg) {
            foreach($pg['landinfos'] as &$land) {
                $map_url = $land['map_url'];
                $map_url_qrcode_file = substr(md5($map_url), 0, 8) . '.png';
                if(!file_exists($product_voucher_path . $map_url_qrcode_file)) {
                    QRcode::png($map_url, $product_voucher_path . $map_url_qrcode_file, QR_ECLEVEL_M, 4, 1);
                }
                $land['map_qrcode'] = Yii::app()->getBaseurl(true) . Yii::app()->params['PRODUCT_VOUCHER_PATH'] . $product_id . '/' . $map_url_qrcode_file;
            }
        }

        $content = FileUtility::render(Yii::app()->basePath . '/../themes/public/views/account/voucher_product.php',
            $product_data);
        if($preview) {
            return $content;
        } else {
            $voucher_html = $product_voucher_path . $product_id . '.html';
            file_put_contents($voucher_html, $content);
            $voucher_pdf = $product_voucher_path . $product_id . '.pdf';
            system(Yii::app()->params['DIR_PDF_SCRIPT'] . "html2pdf -B 0 -T 0 $voucher_html $voucher_pdf");
        }
    }

    public function getVoucherData($product_id)
    {
        $product = HtProduct::model()->with('album_info', 'descriptions')->findByPk($product_id);
        foreach($product['descriptions'] as $pd) {
            if($pd['language_id'] == 1) {
                $en_name = $pd['name'];
            } else {
                $cn_name = $pd['name'];
                //parse mark down
                $how_it_works = Converter::parseMdHtml($pd['how_it_works']);
                $service_include = Converter::parseMdHtml($pd['service_include']);

            }
        }
        $product = Converter::convertModelToArray($product);

        //pick album
        $product['pick_landinfo_groups'] = array();
        $album_info = $product['album_info'];
        if($album_info && $album_info['need_pick_ticket_album'] && $pick_album_id = $album_info['pick_ticket_album_id']) {
            $pick_album = Album::model()->with('landinfos.landmark')->findByPk($pick_album_id);
            $pick_album = Converter::convertModelToArray($pick_album);
            $pick_lands = array();
            if($pick_album) {
                foreach($pick_album['landinfos'] as $land) {
                    $pick_lands[$land['landinfo_id']] = $this->refinePickLand($land);
                }
            } else {
                Yii::log('Pick album not found.Album_id:' . $pick_album_id, CLogger::LEVEL_ERROR,
                    'hitour.service.product');
            }

            $pick_groups = CJSON::decode($album_info['pt_group_info']);
            foreach($pick_groups as $pg) {
                $pg_lands = array();
                foreach($pg['items'] as $land_id) {
                    if(isset($pick_lands[$land_id]))
                        $pg_lands[] = $pick_lands[$land_id];
                }
                $product['pick_landinfo_groups'][] = ['title' => $pg['title'], 'landinfos' => $pg_lands];
            }
        }

        $product['desc'] = ['name' => $cn_name, 'en_name' => $en_name, 'how_it_works' => $how_it_works, 'service_include' => $service_include];

        $introduction = Yii::app()->product->getProductIntroduction($product_id);
        if(!empty($introduction)) {
            $introduction['please_read']['rules'] = empty($product['rules'])?[]:$product['rules'];
            $introduction['redeem_usage']['pickup_landinfos'] = [];
            if(!empty($product['pick_landinfo_groups'][0]['landinfos'])) {
                $introduction['redeem_usage']['pickup_landinfos'] = $product['pick_landinfo_groups'][0]['landinfos'];
            }
            $introduction['service_include'] = $this->formatServiceInclude($service_include);
            $product['introduction'] = $introduction;
        }

        return $product;
    }

    public function getBindingProductDetail($binding_product_id, $parent_id = 0)
    {
        $product = Converter::convertModelToArray(HtProduct::model()->with('descriptions')->findByPk($binding_product_id));
        $expert = $this->getExpertByProduct($product);
        if(!empty($expert)) {
            $product['hitour_expert'] = $expert;
        }

        $pds = array();
        foreach($product['descriptions'] as $desc) {
            $pds[$desc['language_id']] = $desc;
        }
        $pd = $pds[2];
        $pd['service_include'] = Yii::app()->product->refineServiceInclude($pd['service_include']);
        $pd['en_name'] = $pds[1]['name'];
        $product['description'] = $pd;
        unset($product['descriptions']);

        $landinfo = Converter::convertModelToArray(HtProduct::model()->with('album_info')->findByPk($binding_product_id));

        if($product['type'] == 7) {
            $product['hotel'] = $this->getHotelInfo($binding_product_id, $parent_id);
        }

        $supplementary_information = Converter::parseMdHtml($landinfo['album_info']['landinfo_md']);
        $supplementary_arr = json_decode($supplementary_information, true);
        if(!$supplementary_arr) {
            $supplementary_arr = array();
        }
        foreach($supplementary_arr as &$s) {
            if(isset($s['list']['md_html'])) {
                $s['list'] = $s['list']['md_html'];
            }
        }
        $product['all_landinfo'] = $supplementary_arr;

        if($landinfo['album_info'] && $landinfo['album_info']['need_album']) {
            $album = Album::model()->with('album_info_ref.landinfo.landmark',
                'communications')->findByPk($landinfo['album_info']['album_id']);
            $album = Converter::convertModelToArray($album);
            $lands = array();
            $land_names = array();
            $idx = 1;
            if($album) {
                foreach($album['album_info_ref'] as $ref) {
                    $l = $this->refineLandInfo($ref['landinfo']);
                    $l['pass_benefit'] = $ref['pass_benefit'];
                    $l['land_order'] = $ref['land_order'] == AlbumInfoRef::UNSORT ? ($ref['land_order'] + $idx++) : $ref['land_order'];
                    $land_names[] = $l['name'];
                    if(!isset($lands[$l['land_order']]))
                        $lands[$l['land_order']] = [];
                    $lands[$l['land_order']][] = $l;
                }
            }
            ksort($lands);
            $lands = array_merge($lands);
            $product['landinfo_groups'] = $lands;
            $product['communications'] = $album['communications'];
        }

        $tour_plan = HtProductTourPlan::model()->with('groups.items')->findAllByAttributes(['product_id' => $binding_product_id],
            ['order' => 'ptp.the_day ASC,groups.display_order ASC, ptpi.display_order ASC']);
        if(isset($tour_plan[0]) && $tour_plan[0]['is_online']) {
            $tour_plan = Converter::convertModelToArray($tour_plan);
            $product['tour_plan'] = $tour_plan;
        } else {
            $product['tour_plan'] = array();
        }

        $comments_state = HtProductComment::getStatInfo($binding_product_id);
        $product['comments_state'] = $comments_state;
        $product['show_prices'] = HtProductPricePlan::model()->getShowPrices($binding_product_id);
        $price_plans = HtProductPricePlan::model()->getPricePlan($binding_product_id);
        if(!empty($price_plans[0]['items'])) {
            foreach($price_plans[0]['items'] as &$pi) {
                unset($pi['cost_price']);
            }

            $product['price_plan_items'] = $price_plans[0]['items'];
        }
        $product['images'] = $this->getImages($binding_product_id);

        if(!empty($parent_id)) {
            $bundles = Converter::convertModelToArray(HtProductBundle::model()->with('items')->findAllByAttributes(['product_id' => $parent_id]));
            foreach($bundles as $bundle) {
                foreach($bundle['items'] as $bi) {
                    if($bi['binding_product_id'] == $binding_product_id) {
                        $bi['group_type'] = $bundle['group_type'];
                        $product['bundle_info'] = $bi;
                    }
                }
            }
        }

        return $product;
    }

    private function getExpertByProduct($product)
    {
        $data = [];
        $manager = HtProductManager::model()->findByAttributes(['product_id' => $product['product_id']]);
        if($manager) {
            $manager_name = $manager['manager_name'];
            $manager_name = preg_replace('/@.*/i', '', $manager_name);
            if(!empty($manager_name)) {
                $data['name'] = ucfirst(strtolower($manager_name));
                $data['title'] = '玩途旅行专家';
                $data['avatar_url'] = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['EXPERT_IMAGE'] . strtolower($manager_name) . '.png';
            }
        }

        return $data;
    }

    public function getQrCodeLink($product_id)
    {
        $qr_file = dirname(Yii::app()->BasePath) . Yii::app()->params['QR_IMAGE'] . $product_id . '_qr.png';
        if(!file_exists($qr_file)) {
            QRcode::png(Yii::app()->createAbsoluteUrl('product/index', ['product_id' => $product_id]), $qr_file,
                QR_ECLEVEL_M, 3, 1);
        }
        $url = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['QR_IMAGE'] . $product_id . '_qr.png';

        return $url;
    }


    public function formatServiceInclude($orig_data){
        $data = [];


        $groups = explode('<h2',$orig_data);
        foreach($groups as $group) {
            if(empty($group))
                continue;
            $matched = [];
            $p1 = preg_match_all('/<h2\s?\S*>(.*)<\/h2>(.*)/s', '<h2'.$group, $matched);
            if($p1 === 0) {
                continue;
            } else {
                $data[] = ['title'=>$matched[1][0],'detail'=>$matched[2][0]];
            }
        }

//        &#10;&#10;<ol>&#10;<li>&#20262;&#25958;&#22612;&#12289;&#23041;&#26031;&#25935;&#26031;&#29305;&#25945;&#22530;&#12289;&#20262;&#25958;&#22612;&#26725;&#31561;&#36229;&#36807;60&#20010;&#20262;&#25958;&#26368;&#31934;&#21326;&#26223;&#28857;&#30340;&#20813;&#31080;&#28216;&#35272;</li>&#10;<li>&#20262;&#25958;&#22612;&#12289;&#27721;&#26222;&#39039;&#23467;&#12289;&#28201;&#33678;&#22478;&#22561;&#31561;&#26223;&#28857;&#20813;&#25490;&#38431;</li>&#10;<li>&#21487;&#36873;&#8220;&#20132;&#36890;&#21345;&#8221;&#65292;&#22312;&#35268;&#23450;&#26102;&#38388;&#20869;&#26080;&#38480;&#20351;&#29992;&#20262;&#25958;&#20844;&#20849;&#20132;&#36890;</li>&#10;<li>&#27888;&#26212;&#22763;&#28216;&#33337;&#12289;&#20262;&#25958;&#26725;&#39740;&#23627;&#12289;&#29699;&#22330;&#21442;&#35266;&#31561;&#29420;&#20855;&#29305;&#33394;&#30340;&#27963;&#21160;&#20307;&#39564;&#12290;</li>&#10;<li>160&#39029;&#20262;&#25958;&#25351;&#21335;&#20070;</li>&#10;</ol>
//        <h2>&#26381;&#21153;&#21253;&#21547;</h2>&#10;&#10;<ol>&#10;<li>&#20262;&#25958;&#22612;&#12289;&#23041;&#26031;&#25935;&#26031;&#29305;&#25945;&#22530;&#12289;&#20262;&#25958;&#22612;&#26725;&#31561;&#36229;&#36807;60&#20010;&#20262;&#25958;&#26368;&#31934;&#21326;&#26223;&#28857;&#30340;&#20813;&#31080;&#28216;&#35272;</li>&#10;<li>&#20262;&#25958;&#22612;&#12289;&#27721;&#26222;&#39039;&#23467;&#12289;&#28201;&#33678;&#22478;&#22561;&#31561;&#26223;&#28857;&#20813;&#25490;&#38431;</li>&#10;<li>&#21487;&#36873;&#8220;&#20132;&#36890;&#21345;&#8221;&#65292;&#22312;&#35268;&#23450;&#26102;&#38388;&#20869;&#26080;&#38480;&#20351;&#29992;&#20262;&#25958;&#20844;&#20849;&#20132;&#36890;</li>&#10;<li>&#27888;&#26212;&#22763;&#28216;&#33337;&#12289;&#20262;&#25958;&#26725;&#39740;&#23627;&#12289;&#29699;&#22330;&#21442;&#35266;&#31561;&#29420;&#20855;&#29305;&#33394;&#30340;&#27963;&#21160;&#20307;&#39564;&#12290;</li>&#10;<li>160&#39029;&#20262;&#25958;&#25351;&#21335;&#20070;</li>&#10;</ol><h2>&#26381;&#21153;&#21253;&#21547;</h2>&#10;&#10;<ol>&#10;<li>&#20262;&#25958;&#22612;&#12289;&#23041;&#26031;&#25935;&#26031;&#29305;&#25945;&#22530;&#12289;&#20262;&#25958;&#22612;&#26725;&#31561;&#36229;&#36807;60&#20010;&#20262;&#25958;&#26368;&#31934;&#21326;&#26223;&#28857;&#30340;&#20813;&#31080;&#28216;&#35272;</li>&#10;<li>&#20262;&#25958;&#22612;&#12289;&#27721;&#26222;&#39039;&#23467;&#12289;&#28201;&#33678;&#22478;&#22561;&#31561;&#26223;&#28857;&#20813;&#25490;&#38431;</li>&#10;<li>&#21487;&#36873;&#8220;&#20132;&#36890;&#21345;&#8221;&#65292;&#22312;&#35268;&#23450;&#26102;&#38388;&#20869;&#26080;&#38480;&#20351;&#29992;&#20262;&#25958;&#20844;&#20849;&#20132;&#36890;</li>&#10;<li>&#27888;&#26212;&#22763;&#28216;&#33337;&#12289;&#20262;&#25958;&#26725;&#39740;&#23627;&#12289;&#29699;&#22330;&#21442;&#35266;&#31561;&#29420;&#20855;&#29305;&#33394;&#30340;&#27963;&#21160;&#20307;&#39564;&#12290;</li>&#10;<li>160&#39029;&#20262;&#25958;&#25351;&#21335;&#20070;</li>&#10;</ol>
        //Test data
//        $data[]=['title'=>'服务包含','detail'=>'<ol><li>第一段</li><li>第二段</li></ol>'];
//        $data[]=['title'=>'服务不包含','detail'=>'<ol><li>第一不段</li><li>第二不段</li><li>第3段</li></ol>'];
        return $data;
    }

    public function fillMultiDayInfo(&$product){
        if ($product['type'] == HtProduct::T_MULTI_DAY) {
//            $recommendation = HtProductTripIntroduction::model()->getTripIntroductionByProductId($product['product_id']);
            $recommendation = Converter::convertModelToArray(HtProductTripIntroduction::model()->findByAttributes(['product_id'=>$product['product_id'],'status'=>1]));
            if(empty($recommendation)){
                return;
            }
            $trip_highlight = HtTripHighlight::model()->getProductTripHighlights($product['product_id']);
            $product['multi_day_general']['recommendation'] = $recommendation;
            $product['multi_day_general']['trip_highlight'] = $trip_highlight;
            if (!empty($product['multi_day_general']['trip_highlight']['highlight_refs'])) {
                foreach ($product['multi_day_general']['trip_highlight']['highlight_refs'] as &$ref) {
                    $ref['local_highlight'] = str_replace(array("\n", "\r\n", "\r"), "\n", $ref['local_highlight']);
                    $arr = explode("\n" , $ref['local_highlight']);
                    $ref['local_highlight'] = $arr;
                }
            }
        }
    }

    public function getProductSummaryForApp($product_id){
        $product = Converter::convertModelToArray(HtProduct::model()->with(['description'])->findByPk($product_id));
//        $product['rules'] = $this->getRuleDesc($product_id);
        $product['images'] = $this->getImages($product_id);
        if($product['status']!=3){
            return [];
        }
        $refs = HtProductGroupRef::model()->with('app_group')->findAll('product_id = '.$product_id);
        if($refs){
            $is_app = false;
            foreach($refs as $ref){
                if($ref['app_group']){
                    $is_app = true;
                    break;
                }
            }
            if(!$is_app) return[];
        }else{
            return [];
        }

        $data = [];
        $data['product_id'] = $product['product_id'];
        $data['status'] = $product['status'];
        $data['city_code'] = $product['city_code'];
        $data['link_url'] = Yii::app()->params['urlHome'].$product['link_url'];
        $data['name'] = $product['description']['name'];
        $data['summary'] = $product['description']['summary'];
        $data['cover_image'] = empty($product['images']['cover']['image_url'])?'':$product['images']['cover']['image_url'];
        $data['sale_num'] = HtOrder::model()->getSalesVolume($product_id);
        $data['comment_stat'] = HtProductComment::getStatInfo($product_id);
        $data['show_prices'] = HtProductPricePlan::model()->getShowPrices($product_id);

        return $data;
    }

    /**
     * @param $order_data 下单页面中传入的商品相关参数
     * @return array
     */
    public function getProductsForAddOrder($order_data) {
        $data = array();
        if (!empty($order_data) && is_array($order_data)) {
            foreach ($order_data as $p) {
                $data[] = $p;

                $p_model = HtProduct::model()->findByPk($p['product_id']);
                if ($p_model['type'] == HtProduct::T_HOTEL_BUNDLE) {//酒店套餐补充酒店商品
                    if (empty($p['special_code'])) {
                        continue;//酒店套餐商品应该有 special code，指向某个具体的酒店
                    }

                    $special_model = HtProductSpecialCombo::model()->getOneSpecialInfo($p['product_id'],$p['special_code']);
                    $p_hotel = $p;
                    $p_hotel['product_id'] = $special_model['mapping_product_id'];
                    $p_hotel['special_code'] = $special_model['mapping_special_code'];
                    $p_hotel['bundle_product_id'] = $p['product_id'];

                    $data[] = $p_hotel;
                } else if ($p_model['is_combo'] == 1) {//combo商品补充 Sub
                    $combos = Converter::convertModelToArray(HtProductCombo::model()->findAllByAttributes(['product_id' => $p['product_id']]));
                    $sale_rule = Converter::convertModelToArray(HtProductSaleRule::model()->with('package_rules')->findByPk($p['product_id']));
                    $real_quantities = array();
                    $real_quantities_sub = array();
                    if ($sale_rule['sale_in_package']) {
                        foreach ($sale_rule['package_rules'] as $pr) {
                            if ($pr['base_product_id'] == $p['product_id']) {
                                $real_quantities[$pr['ticket_id']] = $p['quantities'][HtTicketType::TYPE_PACKAGE] * $pr['quantity'];
                            }else {
                                if(!isset($real_quantities_sub[$pr['base_product_id']])){
                                    $real_quantities_sub[$pr['base_product_id']] = array();
                                }
                                $real_quantities_sub[$pr['base_product_id']][$pr['ticket_id']] = $p['quantities'][HtTicketType::TYPE_PACKAGE] * $pr['quantity'];
                            }
                        }
                    }
                    foreach ($combos as $combo) {
                        $pc = $p;
                        if(!empty($real_quantities_sub[$combo['sub_product_id']])){
                            $pc['quantities'] = $real_quantities_sub[$combo['sub_product_id']];
                        }else if(!empty($real_quantities)){
                            $pc['quantities'] = $real_quantities;
                        }

                        $pc['product_id'] = $combo['sub_product_id'];
                        $pc['bundle_product_id'] = $p['product_id'];
                        $data[] = $pc;
                    }
                }
            }
        }
        return $data;
    }
}