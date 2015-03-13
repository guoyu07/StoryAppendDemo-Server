<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/22/14
 * Time: 16:05 PM
 */
class Activity extends CComponent
{
    const UNION_LINK = 'http://www.unionpayintl.com/shopping';
    const SUMMER_SALE = 100;
    const KID_ADULT = 118;
    const DOUBLE_11 = 119;
    const SHOPPING = 126;

    public function init()
    {
        require_once('qrcode/full/qrlib.php');
        return true;
    }

    public function isSummerSale($activity_id)
    {
        return self::SUMMER_SALE == $activity_id;
    }

    public function checkActivity($product_id, $activity_id, $contacts_telephone = '', $contacts_email = '', $ip = '', $user_agent = '')
    {
        $result = array('code' => 200, 'msg' => 'OK');

        $aty = $this->getActivityInfo($product_id, $activity_id, false);

        if(empty($aty)) {
            $result = ['code' => 404, 'msg' => '该商品没有参加活动！'];

            return $result;
        }

        if($aty['status'] == HtActivity::AS_PENDING) {
            $result = ['code' => 301, 'msg' => '活动将于' . $aty['product_start_date'] . '开始，敬请期待！'];

            return $result;
        } else if($aty['status'] == HtActivity::AS_OUTDATED) {
            $result = ['code' => 302, 'msg' => '活动已经于' . $aty['product_end_date'] . '结束，感谢您对玩途的关注！'];

            return $result;
        }

        $activity_rule = HtActivityRule::model()->findOneWithCouponByPk($activity_id);

        if($activity_rule['max_order_num'] > 0 && !Yii::app()->customer->isLogged()) {
            $result = ['code' => 203, 'msg' => '需要先登录后才能参加该活动！'];

            return $result;
        } else {
            $customer_id = Yii::app()->customer->getCustomerId();
            $c = new CDbCriteria();
            $c->addCondition('activity_id = ' . $activity_id);

            $cond = 'customer_id = ' . $customer_id;
            if(!empty($contacts_telephone)) {
                $cond .= ' OR contacts_telephone = "' . $contacts_telephone . '"';
            }
            if(!empty($contacts_email)) {
                $cond .= ' OR contacts_email  = "' . $contacts_email . '"';
            }

            if(in_array($product_id, [1305, 3370, 3372])) {
                $cond .= ' OR ip  = "' . $ip . '"';
            }

            $c->addCondition($cond);
            $c->addNotInCondition('status_id', [HtOrderStatus::ORDER_CANCELED, HtOrderStatus::ORDER_NOTPAY_EXPIRED, HtOrderStatus::ORDER_REFUND_SUCCESS]);
            $number = HtOrder::model()->count($c);
            if($number >= $activity_rule['max_order_num'] && $activity_rule['max_order_num'] > 0) {
                $result = ['code' => 204, 'msg' => '您已经达到本次活动的限制，不能继续参加！'];

                return $result;
            }
        }

        $result['activity_rule'] = $activity_rule;

        return $result;
    }

    public function getActivityInfo($product_id, $activity_id = 0, $can_cache = true)
    {
        $activity_info = array();

        $condition = ['status' => 1];
        if($activity_id) {
            $condition ['activity_id'] = $activity_id;
        }

        $key = HtActivity::CACHE_ALL_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . $activity_id . '_1';
        if($can_cache) {
            $activities = Yii::app()->cache->get($key);
        }
        if(empty($activities)) {
            $raw = HtActivity::model()->with('activity_product', 'activity_rule')->findAllByAttributes($condition);
            $activities = Converter::convertModelToArray($raw);
            if($can_cache) {
                Yii::app()->cache->set($key, $activities, 5 * 60);
            }
        }

        if(empty($activities)) {
            return $activity_info;
        }

        $available = array();

        foreach($activities as $aty) {
            foreach($aty['activity_product'] as $ap) {
                $product_id_arr = json_decode($ap['product_ids'], true);
                if(in_array($product_id, $product_id_arr)) {
                    $now = $this->getNow();
                    if($aty['status'] == HtActivity::AS_IN_SALE) {
                        if($now < $aty['start_date']) {
                            $aty['status'] = HtActivity::AS_PENDING;
                            $aty['buy_label'] = $this->getActivityPendingLabel($aty, $ap['start_date']);
                        } else if($now > $aty['end_date']) {
                            $aty['status'] = HtActivity::AS_OUTDATED;
                            $aty['buy_label'] = '活动结束';
                        } else {
                            if($now < $ap['start_date']) {
                                $aty['status'] = HtActivity::AS_PENDING;
                                $aty['buy_label'] = $this->getActivityPendingLabel($aty, $ap['start_date']);
                            } else if($now > $ap['end_date']) {
                                $aty['status'] = HtActivity::AS_OUTDATED;
                                $aty['buy_label'] = '活动结束';
                            } else {
                                $aty['status'] = HtActivity::AS_ONGOING;
                                $aty['buy_label'] = '购买';
                            }
                        }
                    }


                    $activity_info = $aty;
                    unset($activity_info['activity_product']);
                    $activity_info['product_start_date'] = $ap['start_date'];
                    $activity_info['product_end_date'] = $ap['end_date'];

                    if(!empty($activity_info['tag_image'])) {
                        $activity_info['tag_url'] = 'themes/public/images/activities/activity_' . $activity_info['tag_image'];
                    } else {
                        $activity_info['tag_url'] = '';
                    }

                    $rule = $activity_info['activity_rule'];
                    $is_mobile = HTTPRequest::isMobile();

                    if($rule['display_only_in_activity'] == HtActivityRule::DIS_ANYWAY) {
                        if($is_mobile) {
                            $activity_info['display_in_city'] = in_array($rule['terminal'], [HtActivityRule::T_ALL, HtActivityRule::T_MOBILE]);
                        } else {
                            $activity_info['display_in_city'] = in_array($rule['terminal'], [HtActivityRule::T_ALL, HtActivityRule::T_PC]);
                        }
                    } else if($rule['display_only_in_activity'] == HtActivityRule::DIS_ONLY_ACTIVITY) {
                        $activity_info['display_in_city'] = 0;
                    } else if($rule['display_only_in_activity'] == HtActivityRule::DIS_ONGOING_ACTIVITY) {
                        if($activity_info['status'] == HtActivity::AS_ONGOING) {
                            if($is_mobile) {
                                $activity_info['display_in_city'] = in_array($rule['terminal'], [HtActivityRule::T_ALL, HtActivityRule::T_MOBILE]);
                            } else {
                                $activity_info['display_in_city'] = in_array($rule['terminal'], [HtActivityRule::T_ALL, HtActivityRule::T_PC]);
                            }
                        } else {
                            $activity_info['display_in_city'] = 0;
                        }
                    } else {
                        $activity_info['display_in_city'] = 1;
                    }
                    unset($activity_info['activity_rule']);

                    if($activity_info['status'] != HtActivity::AS_ONGOING) {
                        if($rule['sale_only_in_activity'] == 0) {//允许活动外售卖，此时活动未进行，所以商品正常售卖，活动信息应该为空
                            $activity_info = array();
                        }
                    }
                    array_push($available, $activity_info);
//                    break;
                }
            }
//            if(!empty($activity_info)){
//                break;
//            }
        }

        //如果存在一个商品在多个活动中同时存在，则选出正在进行中的活动
        if(!empty($available)) {
            foreach($available as $aty) {
                if($aty && $aty['status'] == HtActivity::AS_ONGOING) {
                    $activity_info = $aty;
                    break;
                }
                //此处可做其他状态判断，如一个商品同时存在两个过期活动中
            }
        }

        if($activity_info) {
            $activity_info['show_activity_tag'] = $this->getActivityShowTag($activity_info);
        }

        return $activity_info;
    }

    public function getNow()
    {
        $now = date('Y-m-d H:i:s');

        if(Yii::app()->params['PAYMENT_REALLY'] == 0) {
            $now = date('Y-m-d H:i:s', strtotime('+0Month+3Days+22Hours+15Minutes'));
        }

        return $now;
    }

    private function getActivityPendingLabel($aty, $start_date)
    {
        $activity_id = $aty['activity_id'];
        if($activity_id == 100) {
            return date('n.j开抢', strtotime($start_date));
        } else if($activity_id == 101) {
            return '敬请关注';
        } else {
            return '敬请关注';
        }
    }


    private function getActivityShowTag($activity_info)
    {
        $activity_id = $activity_info['activity_id'];
        $hidden_tag_activity_ids = [103];

        return in_array($activity_id, $hidden_tag_activity_ids) ? 0 : 1;
    }

    public function getActivityData($activity_id)
    {
        $raw = HtActivity::model()->with('activity_product')->findByPk($activity_id);
        if(empty($raw)) {
            return array();
        }
        $aty = Converter::convertModelToArray($raw);

        $now = $this->getNow();
        if($aty['status'] == HtActivity::AS_IN_SALE) {
            if($now < $aty['start_date']) {
                $aty['status'] = HtActivity::AS_PENDING;
            } else if($now > $aty['end_date']) {
                $aty['status'] = HtActivity::AS_OUTDATED;
            }
        }

        $phase_data = array();
        foreach($aty['activity_product'] as $ap) {
            $phase = array();
            $phase['phase_id'] = $ap['phase_id'];
            if($now < $ap['start_date']) {
                $phase['status'] = HtActivity::AS_PENDING;
            } else if($now > $ap['end_date']) {
                $phase['status'] = HtActivity::AS_OUTDATED;
            } else {
                $phase['status'] = HtActivity::AS_ONGOING;
            }

            $pids = json_decode($ap['product_ids']);
            $phase['date_range'] = sprintf('%s—%s', date('n.j', strtotime($ap['start_date'])),
                date('n.j', strtotime($ap['end_date'])));
            $phase['bg_color'] = $this->getPhaseBgColor($phase['phase_id']);
            $phase['mobile_bg_color'] = $this->getMobilePhaseBgColor($phase['phase_id']);
            $phase['text_color'] = $this->getPhaseTextColor($phase['phase_id']);
            $phase['groups'] = $this->getProductData($pids, $phase['phase_id'], $phase['status']);

            $phase_data[] = $phase;
        }

        unset($aty['activity_product']);
        $aty['phases'] = $phase_data;

        return $aty;
    }

    private function getPhaseBgColor($phase_id)
    {
        $bg_colors = [
            '1' => '#fdc737',
            '2' => '#a6d789',
            '3' => '#dc6751',
            '4' => '#348ac7',
            '5' => '#999bcd',
        ];

        return $bg_colors[$phase_id];
    }

    private function getMobilePhaseBgColor($phase_id)
    {
        $bg_colors = [
            '1' => ['#e7b01d', '#cd990e'],
            '2' => ['#a6d789', '#74b64d'],
            '3' => ['#dc6751', '#be442d'],
            '4' => ['#348ac7', '#579fd2'],
            '5' => ['#999bcd', '#adafe2'],
        ];

        return $bg_colors[$phase_id];
    }

    private function getPhaseTextColor($phase_id)
    {
        $text_colors = [
            '1' => '#dc6751',
            '2' => '#65a73e',
            '3' => '#953321',
            '4' => '#0d598e',
            '5' => '#535695',
        ];

        return $text_colors[$phase_id];
    }

    private function getProductData($pids, $phase_id, $status = HtActivity::AS_ONGOING)
    {
        $product_groups = array();
        foreach($pids as $pid) {
            $raw_product = HtProduct::model()->with(['description' => ['select' => 'name,summary'], 'city', 'cover_image'])->findByPk($pid);
            $product = Converter::convertModelToArray($raw_product);

            $rp['product_id'] = $product['product_id'];
            $rp['name'] = $product['description']['name'];
            $rp['summary'] = $product['description']['summary'];
            $rp['link_url'] = $product['link_url'];
            $rp['cover_image_url'] = $product['cover_image']['image_url'];
            $show_prices = HtProductPricePlan::model()->getShowPrices($pid);
            $rp['show_price'] = $show_prices['price'];

            $city = ['city_code' => $product['city']['city_code'], 'cn_name' => $product['city']['cn_name'], 'en_name' => $product['city']['en_name'], 'link_url' => $product['city']['link_url']];
            $city['bg_color'] = $this->getCityBgColor($phase_id, $city['city_code']);
            $city['nav_image_url'] = $this->getCityImage($phase_id, $city['city_code']);
            $city_exist = false;
            foreach($product_groups as &$pg) {
                if($pg['city']['city_code'] == $city['city_code']) {
                    $pg['products'][] = $rp;
                    $city_exist = true;
                    break;
                }
            }

            if(!$city_exist) {
                $campaign = $this->getCampaignData($phase_id, $city['city_code']);
                $product_groups[] = ['city' => $city, 'products' => [$rp], 'campaign' => $campaign, 'campaign_link' => Yii::app()->urlmanager->createUrl('ad/index',
                    ['url' => self::UNION_LINK, 'src' => $city['city_code']])];
            }
        }

        return $product_groups;
    }

    private function getCityBgColor($phase_id, $city_code)
    {
        $colors = array(
            '1_HKG' => '#ffb400',
            '1_TPE' => '#eaa613',
            '1_HENG' => '#eaa613',
            '1_SIN' => '#ff9c00',
            '1_BKK' => '#e7ae32',

            '2_CJU' => '#8cbb70',
            '2_SEL' => '#79c74b',
            '2_TYO' => '#7cbb57',
            '2_OSA' => '#55b51c',
            '2_DXB' => '#5d9e36',

            '3_SFO' => '#be5820',
            '3_LAX' => '#cc4e4e',
            '3_HNL' => '#c54c35',
            '3_LAS' => '#db3a1b',
            '3_NYC' => '#c2371d',

            '4_PAR' => '#1669a3',
            '4_LON' => '#56abe7',
            '4_MIL' => '#0071c0',

            '5_HKG' => '#6b6fc9',
            '5_SIN' => '#4b50bc',
            '5_HNL' => '#595c95',
            '5_LON' => '#8185ec',
            '5_DISN' => '#8185ec',
            '5_LAX' => '#4c50b5',
            '5_SYD' => '#6b81c9',
        );

        if(!empty($colors[$phase_id . '_' . $city_code])) {
            return $colors[$phase_id . '_' . $city_code];
        }

        return '';
    }

    private function getCityImage($phase_id, $city_code)
    {
        return 'themes/public/images/activities/summer-sale/' . $phase_id . '_' . $city_code . '.png';
    }

    private function getCampaignData($phase_id, $city_code)
    {
        $data = array(
            '1_HKG' => array(
                array(
                    'name' => '海港城',
                    'title' => '消费满2,500港币获FACESSS或Citysuper 100港币现金券',
                    'discount_date' => '2014/7/14-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/63ba6d684b6859ad8d9e1b96f534a669.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_HongKong/sq_HongKong_jsz/221224.shtml',
                ),
                array(
                    'name' => '尖沙咀凯悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/1d1a64dc4c8182cf9e4702945c7b1218.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_HongKong/sq_HongKong_jsz/221225.shtml',
                ),
            ),
            '1_TPE' => array(
                array(
                    'name' => '台北101购物中心',
                    'title' => '消费满新台币30,000元获精美赠礼',
                    'discount_date' => '2014/7/1-2014/12/31',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/9442a5b0bf7c9b4d31bfccd98667d797.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_TaiWan/sq_TaiWan_one/221233.shtml',
                ),
                array(
                    'name' => '台北阪急百货',
                    'title' => '消费满新台币10,000元，即赠送商品抵用券新台币200元',
                    'discount_date' => '2014/7/1-2014/9/30',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/eab50e9e79bedd824d8238340642f89c.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_TaiWan/sq_TaiWan_one/221235.shtml',
                ),
            ),
            '1_SIN' => array(
                array(
                    'name' => 'ION Orchard',
                    'title' => '购物满888新元，即可获得20新元ION购物礼券',
                    'discount_date' => '2014/8/1-2015/2',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/edb7bf1b23989ace510ebc429d69847f.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Singapore/sq_Singapore_wjl/221269.shtml',
                ),
                array(
                    'name' => '新加坡DFS环球免税店',
                    'title' => '银联卡消费满650新币送45新币抵扣券',
                    'discount_date' => '2014/7/15-2014/10/15',
                    'scope' => '银联白金卡/钻石卡(卡号以62开头)持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/02b9cdfe8aaea57c370e608eece9932a.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Singapore/sq_Singapore_wjl/221272.shtml',
                ),
            ),
            '1_BKK' => array(
                array(
                    'name' => '曼谷君悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/de2fa5038ad76df43bde72eed3ce8ab2.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Singapore/sq_Singapore_wjl/221272.shtml',
                ),
                array(
                    'name' => '尚泰百货',
                    'title' => '消费满15,000泰铢，获赠价值2,950泰铢的FCUK Tote包',
                    'discount_date' => '2014/8/1-2015/2/28',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/650dfb17e02e8b2ce41bfab07eb2a219.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Thailand/sq_Thailand_smf/221276.shtml',
                ),
            ),

            '2_CJU' => array(
                array(
                    'name' => '济州岛凯悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/1f33d3ca5b9e026d10ea9d19cb4d497a.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Korea/sq_Korea_jzd/221268.shtml',
                ),

                array(
                    'name' => '济州岛新罗免税店',
                    'title' => '出示银联卡即赠2万及4万韩元2种代金券',
                    'discount_date' => '2014/7/1-2014/10/31',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/2bddbceabf0cd566402992bacfa2bdd1.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Korea/sq_Korea_jzd/221266.shtml',
                ),

            ),
            '2_SEL' => array(
                array(
                    'name' => '明洞乐天免税店总店',
                    'title' => '银联卡消费立享5%折扣；高端卡享5-15%折扣特惠',
                    'discount_date' => '2014/7/1-2015/2/28',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/4914c426cbf230b08309120b1a37fa3d.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Korea/sq_Korea_md/221254.shtml',
                ),
                array(
                    'name' => '东大门新罗免税店',
                    'title' => '银联持卡人在免税店内出示银联卡时可获得赠2万及4万韩元2种代金券',
                    'discount_date' => '2014/7/1-2014/10/31',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/ad85b82723a136b2243a297272103ccc.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Korea/sq_Korea_ddm/221260.shtml',
                ),
            ),
            '2_TYO' => array(
                array(
                    'name' => '东京柏悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/2e97fbd28105ac8115a7295b7c6ecf2e.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Japan/sq_Japan_xs/221249.shtml',
                ),
                array(
                    'name' => '冲绳DFS环球免税店',
                    'title' => '消费满30,000日元送1,500日元抵扣券',
                    'discount_date' => '2014/7/15-2014/10/15',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/15aaf2896f5886b93e62522c5388c867.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Japan/sq_Japan_cs/221251.shtml',
                ),
            ),
            '2_OSA' => array(
                array(
                    'name' => '阪神百货梅田店',
                    'title' => '超过1000日元的商品，银联卡尊享9.5折优惠',
                    'discount_date' => '2014/7/1-2014/12/31',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/9c4d3d958acd1bac9a96dd28d7dff23c.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Japan/sq_Japan_mt/221253.shtml',
                ),
                array(
                    'name' => '阪急百货梅田总店',
                    'title' => '超过1000日元的商品，银联卡尊享9.5折优惠',
                    'discount_date' => '2014/7/1-2014/12/31',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/aaba338071572f48c3f3f85582aa2af1.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Japan/sq_Japan_mt/221252.shtml',
                ),
            ),
            '2_DXB' => array(
                array(
                    'name' => '迪拜购物中心',
                    'title' => '当日累计消费满1万迪拉姆，可免费享用咖啡+甜品一份',
                    'discount_date' => '2014/8/1-2014/9/30',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/d49d933827cc382ca1e31d6913202251.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_UAE/sq_UAE_db/221324.shtml',
                ),
                array(
                    'name' => '金茂君悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/801a2cddf5cc7b1e4a079cb94bd72baf.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_UAE/sq_UAE_db/221326.shtml',
                ),
            ),

            '3_SFO' => array(
                array(
                    'name' => "旧金山Macy's百货",
                    'title' => '立享8.5折优惠',
                    'discount_date' => '2014/7/1-2014/12/31',
                    'scope' => '所有银联卡（卡号以62开头）持卡人 购物请提示促销号码 : 00000000001318040115',
                    'image_url' => 'http://hitour.qiniudn.com/92be15423462de28684ecfe4d2e8e4fc.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_jjs/221308.shtml',
                ),
                array(
                    'name' => '旧金山联合广场凯悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/e3cad6749e404e99ac838d7f439711ef.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_jjs/221310.shtml',
                ),
            ),
            '3_LAX' => array(
                array(
                    'name' => 'South Coast Plaza',
                    'title' => '刷银联卡购$1,000美元及以上购物券，即可获赠50美元购物券',
                    'discount_date' => '2014/7/1-2014/11/30',
                    'scope' => '所有银联卡（卡号以62开头）持卡人 购物请提示促销号码 : 00000000001318040115',
                    'image_url' => 'http://hitour.qiniudn.com/7e951de2bfc41ddaf7c6c453004eeee1.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_lsj/221307.shtml',
                ),
                array(
                    'name' => '世纪广场凯悦丽晶酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/4f39283b1965116bafa5ca161a433edb.png',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_lsj/221304.shtml',
                ),
            ),
            '3_HNL' => array(
                array(
                    'name' => '夏威夷DFS环球免税店',
                    'title' => '银联卡消费满1,000美元送50美元代金券',
                    'discount_date' => '2014/7/1-2014/10/15',
                    'scope' => '所有银联卡（卡号以62开头）持卡人 购物请提示促销号码 : 00000000001318040115',
                    'image_url' => 'http://hitour.qiniudn.com/479ca581bb54c70b9bd26ff298af20a4.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_xwy/221316.shtml',
                ),
                array(
                    'name' => '威基基海滩凯悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/93bed2871dad41fbd94d1f3ba5b6a5ae.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_xwy/221317.shtml',
                ),
            ),
            '3_LAS' => array(
                array(
                    'name' => 'Grand Canal Shoppes',
                    'title' => '刷银联卡消费满1000美元可获得2张威尼斯人酒店表演入场券',
                    'discount_date' => '2014/7/1-2015/2/28',
                    'scope' => '所有银联卡（卡号以62开头）持卡人 购物请提示促销号码 : 00000000001318040115',
                    'image_url' => 'http://hitour.qiniudn.com/fcb4ea502b5f9da57390b5e80e7d7ebb.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_ls/221303.shtml',
                ),
                array(
                    'name' => 'Fashion Show',
                    'title' => '刷银联卡消费满1000美元可获得2张威尼斯人酒店表演入场券',
                    'discount_date' => '2014/7/1-2015/2/28',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/b13a06869fdee0ed66d1e4ad041a2844.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_ls/221302.shtml',
                ),
            ),
            '3_NYC' => array(
                array(
                    'name' => "Macy's百货旗舰店",
                    'title' => '立享8.5折优惠',
                    'discount_date' => '2014/7/1-2014/12/31',
                    'scope' => '所有银联卡（卡号以62开头）持卡人 购物请提示促销号码 : 00000000001318040115',
                    'image_url' => 'http://hitour.qiniudn.com/a8fde9dec2d2d2b5b1d7d996edab5356.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_hnd/221300.shtml',
                ),
                array(
                    'name' => '纽约凯悦大酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/38c78fa706ac32997c80508e284ad28c.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_hnd/221306.shtml',
                ),
            ),

            '4_PAR' => array(
                array(
                    'name' => '巴黎春天百货',
                    'title' => '高端卡享贵宾室迎宾饮品、私人休息室、即时现金退税、市内快递服务',
                    'discount_date' => '2014/7/1-2014/12/31',
                    'scope' => '银联白金卡/钻石卡(卡号以62开头)持卡人',
//                    'image_url' => 'http://hitour.qiniudn.com/a89d22272ce49108bc135b66ba9504ac.jpg',
                    'image_url' => 'http://hitour.qiniudn.com/92f876327ef332ef279cc3e6d83044f8.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_France/sq_France_asm/221281.shtml',
                ),
                array(
                    'name' => "巴黎柏悦酒店",
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/b74cdcb6cb21a9b6db36a5bdf655bf1f.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_France/sq_France_asm/221283.shtml',
                ),
            ),
            '4_LON' => array(
                array(
                    'name' => '伦敦Bicester Village',
                    'title' => '凭银联卡免费获取VIP卡，享10%折扣',
                    'discount_date' => '2014/7/15-2015/2/15',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/1bab44392128999abaa87ed9633eeb29.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_England/sq_England_ld/221280.shtml',
                ),
                array(
                    'name' => '阿姆斯特丹安达仕凯悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/82f9fbc911f40cb05fbb8c4866b6f72e.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Netherlands/sq_Netherlands_gc/221290.shtml',
                ),
            ),
            '4_MIL' => array(
                array(
                    'name' => '米兰文艺复兴百货',
                    'title' => '凭银联卡享10%折扣',
                    'discount_date' => '2014/7/1-2014/12/31',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/aca261de1bb2078079922cd180db0289.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Italy/sq_Italy_hj/221286.shtml',
                ),
                array(
                    'name' => '米兰柏悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/f7e5beed7e795549ed316ec1f1539540.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Italy/sq_Italy_hj/221287.shtml',
                ),
            ),

            '5_HKG' => array(
                array(
                    'name' => '尖沙咀凯悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/1d1a64dc4c8182cf9e4702945c7b1218.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_HongKong/sq_HongKong_jsz/221225.shtml',
                ),
                array(
                    'name' => 'DFS希慎广场店',
                    'title' => '消费满3,000港币送150港币抵扣券',
                    'discount_date' => '2014/7/15-2014/10/15',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/f7f988318c6ceb93ed96316d264329fa.JPG',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_HongKong/sq_HongKong_tlw/221229.shtml',
                ),
            ),
            '5_SIN' => array(
                array(
                    'name' => 'ION Orchard',
                    'title' => '购物满888新元，即可获得20新元ION购物礼券',
                    'discount_date' => '2014/8/1-2015/2',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/edb7bf1b23989ace510ebc429d69847f.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Singapore/sq_Singapore_wjl/221269.shtml',
                ),
                array(
                    'name' => '新加坡DFS环球免税店',
                    'title' => '银联卡消费满650新币送45新币抵扣券',
                    'discount_date' => '2014/7/15-2014/10/15',
                    'scope' => '银联白金卡/钻石卡(卡号以62开头)持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/02b9cdfe8aaea57c370e608eece9932a.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Singapore/sq_Singapore_wjl/221272.shtml',
                ),
            ),
            '5_HNL' => array(
                array(
                    'name' => '夏威夷DFS环球免税店',
                    'title' => '银联卡消费满1,000美元送50美元代金券',
                    'discount_date' => '2014/7/1-2014/10/15',
                    'scope' => '所有银联卡（卡号以62开头）持卡人 购物请提示促销号码 : 00000000001318040115',
                    'image_url' => 'http://hitour.qiniudn.com/479ca581bb54c70b9bd26ff298af20a4.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_xwy/221316.shtml',
                ),
                array(
                    'name' => '威基基海滩凯悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/93bed2871dad41fbd94d1f3ba5b6a5ae.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_xwy/221317.shtml',
                ),
            ),
            '5_LON' => array(
                array(
                    'name' => '伦敦Bicester Village',
                    'title' => '凭银联卡免费获取VIP卡，享10%折扣',
                    'discount_date' => '2014/7/15-2015/2/15',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/1bab44392128999abaa87ed9633eeb29.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_England/sq_England_ld/221280.shtml',
                ),
                array(
                    'name' => '阿姆斯特丹安达仕凯悦酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/82f9fbc911f40cb05fbb8c4866b6f72e.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Netherlands/sq_Netherlands_gc/221290.shtml',
                ),
            ),
            '5_LAX' => array(
                array(
                    'name' => 'South Coast Plaza',
                    'title' => '刷银联卡购$1,000美元及以上购物券，即可获赠50美元购物券',
                    'discount_date' => '2014/7/1-2014/11/30',
                    'scope' => '所有银联卡（卡号以62开头）持卡人 购物请提示促销号码 : 00000000001318040115',
                    'image_url' => 'http://hitour.qiniudn.com/7e951de2bfc41ddaf7c6c453004eeee1.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_lsj/221307.shtml',
                ),
                array(
                    'name' => '世纪广场凯悦丽晶酒店',
                    'title' => '入住即可获取“凯悦金护照”3,000奖励积分',
                    'discount_date' => '2014/7/20-2014/9/14',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/4f39283b1965116bafa5ca161a433edb.png',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_America/sq_America_lsj/221304.shtml',
                ),
            ),
            '5_SYD' => array(
                array(
                    'name' => 'DFS悉尼环球免税店',
                    'title' => '银联卡消费满800澳币送40澳币抵扣券',
                    'discount_date' => '2014/7/15-2014/10/15',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/aff991c260567426e91b29bd8bf2cfb0.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Australian/sq_Australian_xn/221323.shtml',
                ),
                array(
                    'name' => 'David Jones百货',
                    'title' => '单笔消费500澳元或以上时，即可获得价值50澳元礼品卡',
                    'discount_date' => '2014/7/1-2014/7/31, 2014/9/15-2014/10/31',
                    'scope' => '所有银联卡（卡号以62开头）持卡人',
                    'image_url' => 'http://hitour.qiniudn.com/4a3e4fabd51d0402a7db92aeeda67e09.jpg',
                    'rule_url' => 'http://www.unionpayintl.com/article/sq_qqth/sq_Australian/sq_Australian_xn/221322.shtml',
                ),
            ),

        );

        if(!empty($data[$phase_id . '_' . $city_code])) {
            return $data[$phase_id . '_' . $city_code];
        }

        return array();
    }

    public function getAdBanner($activity_id, $product, $is_mobile = 0)
    {
        $ad = array();
        if(empty($activity_id)) {
            return $ad;
        }

        if($activity_id == self::KID_ADULT) {
            $ad['image_url'] = 'themes/public/images/activities/kidadult/banner.jpg';
            $ad['link_url'] = Yii::app()->urlmanager->createUrl('activity/kidadult');
        }

        if($activity_id == self::KID_ADULT) {
            $ad['image_url'] = 'themes/public/images/activities/kidadult/banner.jpg';
            $ad['link_url'] = Yii::app()->urlmanager->createUrl('activity/kidadult');
        } else if($activity_id == self::DOUBLE_11) {
            if($is_mobile) {
                $ad['image_url'] = 'themes/public/images/activities/carnival-11.11/m_banner.png';
            } else {
                $ad['image_url'] = 'themes/public/images/activities/carnival-11.11/banner.jpg';
                $ad['qr_code_link'] = Yii::app()->product->getQrCodeLink($product['product_id']);
            }
        } else if($activity_id == self::SHOPPING) {
            if($is_mobile) {
                $ad['image_url'] = 'themes/public/images/activities/shopping/m_banner.png';
            } else {
                $ad['image_url'] = 'themes/public/images/activities/shopping/banner.jpg';
                $ad['qr_code_link'] = Yii::app()->product->getQrCodeLink($product['product_id']);
            }
        }


        return $ad;

//        if (!$this->isInActivity()) {
//            return $ad;
//        }
//
//        if ($activity_id > 0) {
//            if ($activity_id == self::SUMMER_SALE)
////                $ad['image_url'] = 'themes/public/images/activities/summer-sale/' . ($is_mobile ? 'm_banner_' : 'banner_') . 'default.png';
//                return $ad;
//        }
//
//        if ($is_mobile) {
//            return $ad;
//        }
//
//        $ads_all = array(
//            'HK' => ['hgc.png', 'hyatt.png', 'xs_dfs.png'],
//            'TW' => ['101.png', 'sogo.png', 'bjbh.png'],
//            'JP' => ['cs_dfs.png', 'hyatt.png', 'swyz.png'],
//            'KR' => ['mdlt.png', 'ddm.png', 'hyatt.png'],
//            'SG' => ['ion.png', 'sin_dfs.png', 'hyatt.png'],
//            'MY' => ['suria.png', 'pavilion.png', 'hyatt.png'],
//            'TH' => ['shangtai.png', 'gaysorn.png', 'hyatt.png'],
//            'GB' => ['bicester.png', 'bijenkor.png', 'hyatt.png'],
//            'FR' => ['blct.png', 'hyatt.png', 'hyatt.png'],
//            'IT' => ['wyfx.png', 'hyatt.png', 'fidenza.png'],
//            'US' => ['macys.png', 'hyatt.png', 'xwy_dfs.png'],
//            'AU' => ['xn_dfs.png', 'david.png', 'westfield.png'],
//        );
//
//        if (isset($ads_all[$country_code])) {
//            $ads = $ads_all[$country_code];
//            $idx = rand(0, count($ads) - 1);
//            if (isset($ads[$idx])) {
//                $ad['image_url'] = 'themes/public/images/activities/summer-sale/banner_' . $ads[$idx];
//                $ad['link_url'] = Yii::app()->urlmanager->createUrl('ad/index',
//                                                                    ['url' => 'http://www.unionpayintl.com/shopping', 'src' => $product_id]);
//            }
//        }

//        return $ad;
    }

    public function getEmailAd($city_code)
    {
        $email_ad = array();
        if(!$this->isInActivity()) {
            return $email_ad;
        }
        $ads_all = [
            'HKG' => [
                '香港海港城优惠：银联卡（卡号以62开头）持卡人购物，消费满2,500港币获FACESSS或Citysuper 100港币现金券 ',
                '香港HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
                '香港DFS希慎广场店优惠：银联卡（卡号以62开头）持卡人购物，消费满3,000港币送150港币抵扣券',
            ],
            'TPE' => [
                '台北101购物中心优惠：银联卡（卡号以62开头）持卡人购物，消费满新台币30,000元获精美赠礼',
                '台北SOGO百货优惠：银联卡（卡号以62开头）持卡人购物，消费满新台币30,000元，即赠送SOGO礼券新台币500元 ',
                '台北阪急百货优惠：银联卡（卡号以62开头）持卡人购物，消费满新台币10,000元，即赠送商品抵用券新台币200元 ',

            ],
            'OKA' => [
                '冲绳DFS环球免税店优惠：银联卡（卡号以62开头）持卡人购物，消费满30,000日元送1,500日元抵扣券 ',
            ],
            'TYO' => [
                '东京HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
                '东京松屋银座百货优惠：银联卡（卡号以62开头）持卡人购物，凭打印或扫描条形码，尊享9.8折优惠 ',

            ],
            'SEL' => [
                '首尔明洞乐天免税店总店优惠：银联卡（卡号以62开头）持卡人购物，消费立享5%折扣；高端卡享5-15%折扣特惠 ',
                '首尔东大门新罗免税店优惠：银联卡（卡号以62开头）持卡人购物，出示银联卡可获得赠2万及4万韩元2种代金券 ',
            ],
            'CJU' => [
                '济州岛HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
            ],
            'SIN' => [
                '新加坡ION Orchard优惠：银联卡（卡号以62开头）持卡人购物，消费满888新元，即可获得20新元ION购物礼券 ',
                '新加坡DFS环球免税店优惠：银联白金卡/钻石卡(卡号以62开头)持卡人购物，消费满650新币送45新币抵扣券 ',
                '新加坡HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
            ],
            'KUL' => [
                '吉隆坡Suria KLCC优惠：银联卡（卡号以62开头）持卡人购物，消费满1,500马币，即可在商场内的兑奖处获赠精美礼品 ',
                '吉隆坡HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
                '吉隆坡Pavilion优惠：银联卡（卡号以62开头）持卡人购物，消费满800马币，即可在商场内的兑奖处获赠旅行袋 ',
            ],
            'BKK' => [
                '曼谷尚泰百货优惠：银联卡（卡号以62开头）持卡人购物，消费满15,000泰铢，获赠价值2,950泰铢的FCUK Tote包 ',
                '曼谷HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
                '曼谷Gaysorn Plaza优惠：银联卡（卡号以62开头）持卡人购物，消费满6,200泰铢，获赠价值590泰铢的Benetton包 ',
            ],
            'LON' => [
                '伦敦Bicester Village优惠：银联卡（卡号以62开头）持卡人购物，凭银联卡免费获取VIP卡，享10%折扣 ',
            ],
            'AMS' => [
                '阿姆斯特丹HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
                '阿姆斯特丹De Bijenkorf百货优惠：银联卡（卡号以62开头）持卡人购物，享私人导购服务及中文接待及咨询服务 ',
            ],
            'PAR' => [
                '巴黎春天百货优惠：银联白金卡/钻石卡(卡号以62开头)持卡人购物，享贵宾室迎宾饮品、私人休息室、即时现金退税、市内快递服务 ',
                '巴黎HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
                '巴黎HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
            ],
            'DISN' => [
                '巴黎春天百货优惠：银联白金卡/钻石卡(卡号以62开头)持卡人购物，享贵宾室迎宾饮品、私人休息室、即时现金退税、市内快递服务 ',
                '巴黎HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
                '巴黎HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
            ],
            'MIL' => [
                '米兰文艺复兴百货优惠：银联卡（卡号以62开头）持卡人购物，享10%折扣 ',
                '米兰HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
                '米兰米兰Fidenza Village优惠：银联卡（卡号以62开头）持卡人出示银联卡免费获取VIP卡，享10%折扣 ',
            ],
            'NYC' => [
                "纽约Macy's百货旗舰店优惠：银联卡（卡号以62开头）持卡人购物，消费刷卡立享8.5折优惠",
            ],
            'HNL' => [
                '夏威夷DFS环球免税店优惠：银联卡（卡号以62开头）持卡人购物，消费满1,000美元送50美元代金券 ',
            ],
            'HBG' => [
                '夏威夷DFS环球免税店优惠：银联卡（卡号以62开头）持卡人购物，消费满1,000美元送50美元代金券 ',
            ],
            'SFO' => [
                '旧金山HYATT集团旗下酒店优惠：银联卡（卡号以62开头）持卡人入住，即可获取“凯悦金护照”3,000奖励积分 ',
            ],
            'SYD' => [
                '悉尼DFS环球免税店优惠：银联卡（卡号以62开头）持卡人购物，消费满800澳币送40澳币抵扣券 ',
                '悉尼David Jones百货优惠：银联卡（卡号以62开头）持卡人购物，单笔消费500澳元或以上时，即可获得价值50澳元礼品卡',
                '悉尼Westfield Sydney购物中心优惠：银联卡（卡号以62开头）持卡人购物，消费满400澳元，即可获得价值40澳元的礼品卡',
            ],
        ];

        if(isset($ads_all[$city_code])) {
            $ads = $ads_all[$city_code];
            $idx = rand(0, count($ads) - 1);
            if(isset($ads[$idx])) {
                $ad['image_url'] = '/images/activities/summer-sale/unionPay_md.png';
                $ad['title'] = $ads[$idx];
                $ad['link_url'] = Yii::app()->createAbsoluteUrl('ad/index',
                    ['url' => 'http://www.unionpayintl.com/shopping', 'src' => 'email']);
                $email_ad[] = $ad;
            }
        }

//        $email_ad[] = array(
//            'image_url' => '/images/activities/summer-sale/sale_icon.png',
//            'title' => '【玩途活动】全球40款旅行服务，银联支付立减150元/单！',
//            'link_url' => Yii::app()->createAbsoluteUrl('activity/summersale'),
//        );

        return $email_ad;
    }

    private function isInActivity()
    {
        $now = $this->getNow();

        if($now > '2014-10-15' || $now < '2014-08-19') {
            return false;
        } else {
            return true;
        }
    }

    public function getMobileHomeActivities()
    {
        $now = $this->getNow();

        //1111
//        if ($now < '2014-12-01') {
//            $url = 'http://hitour.qiniudn.com/f2e049a61a59ea4a5b64f422d9a40675.jpg';
//            $activities[] = ['image_url' => $url, 'link_url' => Yii::app()->createAbsoluteUrl('activity/1111')];
//        }

        if($now < '2014-12-16') {
            $url = 'http://hitour.qiniudn.com/90948be76a238e98ff1620022c6c69b5.jpg';
            $activities[] = ['image_url' => $url, 'link_url' => Yii::app()->createAbsoluteUrl('activity/shopping')];
        }

//      mobile home image
        $url = 'http://hitour.qiniudn.com/30675d48ca31f0f82c8b859de674d619.jpg';
//        $url = 'http://hitour.qiniudn.com/c67763a54da52842415498e6e2a8b59b.jpg';
        $activities[] = ['image_url' => $url, 'link_url' => Yii::app()->createAbsoluteUrl('activity/fridaysale')];

        return $activities;
    }

    public function getPaymentMethods($activity_id)
    {
        //payment methods
        $all_payment_methods = PayUtility::paymentMethods();

        if($activity_id) {
            $activity_rule = HtActivityRule::model()->findOneByPk($activity_id);
            if($activity_rule) {
                $data['sale_rule']['allow_use_coupon'] = $activity_rule['allow_use_coupon'];
                if(!empty($activity_rule['payment_types'])) {
                    $payment_types = json_decode($activity_rule['payment_types'], true);
                    $filtered_payment_methods = array();
                    foreach($payment_types as $t) {
                        if(isset($all_payment_methods[$t])) {
                            $filtered_payment_methods[$t] = $all_payment_methods[$t];
                        }
                    }
                    $all_payment_methods = $filtered_payment_methods;
                }
            }

            $discount = Converter::convertModelToArray(HtActivityDiscount::model()->findAllByAttributes(['activity_id' => $activity_id]));
            if(!empty($discount)) {
                foreach($discount as $k => $d) {
                    if(empty($d['payment_method'])) {
                        foreach($all_payment_methods as &$pay) {
                            $pay['discount'] = $d;
                        }
                        break;
                    } else {
                        if(isset($all_payment_methods[$d['payment_method']])) {
                            $all_payment_methods[$d['payment_method']]['discount'] = $d;
                        }
                    }
                }
            }
        }

        return $all_payment_methods;
    }

}
