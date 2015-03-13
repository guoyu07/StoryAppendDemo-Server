<?php

/**
 * Class Notify
 */
class EmailNotify
{
    private $template_path = '';

    function __construct()
    {
        $this->template_path = dirname(Yii::app()->basePath) . Yii::app()->params['THEME_BASE_URL'];
    }

    public function init()
    {
        $this->template_path = dirname(Yii::app()->basePath) . Yii::app()->params['THEME_BASE_URL'];

        return true;
    }

    public function notifySupplier($order, $order_product, $preview = false)
    {
        if($order_product['product']['type']==HtProduct::T_COUPON){
            return true;//秒杀商品不需要通知供应商
        }
        $product_id = $order_product['product']['product_id'];
        $shipping_rule = HtProductShippingRule::model()->findByPk($product_id);
        $language_id = $shipping_rule['language_id'];
        $to = $shipping_rule['supplier_email'];
        $order_status_id = $order['status_id'];
        $order_id = $order['order_id'];
        if (empty($to)) {
            Yii::log('Supplier Email Empty.Order_id = ' . $order['order_id'] . ',status_id=' . $order_status_id,
                     CLogger::LEVEL_ERROR, 'hitour.service.notify');

            return true; //没有供应商 email 地址，说明不需要通知供应商
        }

        $setting = HtNotifySetting::model()->with('email_setting',
                                                  'template')->findByAttributes(array('order_status_id' => $order_status_id, 'notify_obj_name' => HtNotifySetting::SUPPLIER));

        list($passed, $check_result) = $this->checkSetting($setting, $order['order_id'], $order_status_id);
        if (!$passed) {
            return $check_result;
        }

        $order['label'] = $this->getLabelByLanguage($language_id);
        $order['label']['introduction'] = $order['label']['booking_introduction'];
        if ($order_status_id == HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION) {
            $order['label']['introduction'] = $order['label']['refund_introduction'];
        }

        $label = $this->getLabelByLanguage($language_id);
        $label['introduction'] = $label['booking_introduction'];
        if ($order_status_id == HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION) {
            $label['introduction'] = $label['refund_introduction'];
        }
        $order_product['label'] = $label;

        $body = $this->templateRender($this->template_path . $setting['template']->path, $order_product); //渲染邮件模板
        if ($preview) {
            return $body;
        }

        if (Yii::app()->params['PAYMENT_REALLY'] == 0) {
            $to = $order['contacts_email'];//Test环境，下单人同时作为“供应商”邮件接收者
        }

        list($result, $subject, $attachment) = $this->getSubjectAndAttachmentForSupplier($language_id, $order_status_id,
                                                                                         $shipping_rule, $order,
                                                                                         $order_product);
        if ($result===true) {
            return $result;
        }

        $subject .= $order_id . ' (Booking Ref.' . $order_product['supplier_order']['hitour_booking_ref'] . ')';

        //发送邮件
        $result = Mail::sendBySetting($setting['email_setting'], $to, $subject, $body, $attachment);

        return $result;
    }

    private function getSubjectAndAttachmentForSupplier($language_id, $order_status_id, $shipping_rule, $order, $order_product)
    {
        $result = false;
        $subject = '';
        $attachment = array();

        $target_language = $language_id == 1 ? 'en' : 'zh';
        switch ($order_status_id) {
            case HtOrderStatus::ORDER_WAIT_CONFIRMATION:
            case HtOrderStatus::ORDER_PAYMENT_SUCCESS:
                //只有 Email 预定方式在“等待供应商确认“状态时，需要给供应商发邮件
                if ($shipping_rule['booking_type'] == HtProductShippingRule::BT_EMAIL) {
                    $subject .= Yii::t('service', 'reservation', array(), null, $target_language);
                    $supplier_id = $order_product['product']['supplier_id'];
                    if ($supplier_id == HtSupplier::S_HUAPANG) {
                        $huapang = new HuaPang();
                        $result = $huapang->impl_export_order($order['order_id']);
                        if ($result['code'] != 200) {
                            $result = false;
                            Yii::log('Export Huapang order failed:order_id=' . $order['order_id']);
                        } else {
                            $attachment[] = $result['export_file'];
                            $result = false;
                        }
                    }
                } else {
                    $result = true;
                }
                break;
            case HtOrderStatus::ORDER_TO_DELIVERY:
            case HtOrderStatus::ORDER_SHIPPING_FAILED:
                if ($shipping_rule['booking_type'] == HtProductShippingRule::BT_HITOUR || $shipping_rule['need_notify_supplier']) {
                    $subject .= Yii::t('service', 'reservation', array(), null, $target_language);
                    if ($shipping_rule['supplier_need_attachment_voucher']) {
                        $supplier_order_id = $order_product['supplier_order']['supplier_order_id'];
                        $supplier_order = HtSupplierOrder::model()->findByPk($supplier_order_id);
                        $voucher_pdfs = json_decode($supplier_order['voucher_ref']);
                        foreach ($voucher_pdfs as $pdf) {
                            $attachment[] = $order['order']['voucher_path'] . $pdf;
                        }
                    }
                } else {
                    $result = true;
                }
                break;
            case HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION:
                $subject .= Yii::t('service', 'refund', array(), null, $target_language);
                break;
            default:
                Yii::log('订单该状态不需要通知供应商；order_id:' . $order['order']['order_id'], CLogger::LEVEL_WARNING,
                         'hitour.service.notify');

                $result = true;
        }

        return [$result, $subject, $attachment];
    }

    private function checkSetting($setting, $order_id, $order_status_id)
    {
        $passed = true;
        $check_result = true;
        if (!$setting) {
            Yii::log('HtNotifySetting not found.Order_id = ' . $order_id . ',status_id=' . $order_status_id,
                     CLogger::LEVEL_ERROR, 'hitour.service.notify');

            $passed = false;
        }

        if (!$setting['email_setting']) {
            Yii::log('HtEmailSetting not found.Order_id = ' . $order_id . ',status_id=' . $order_status_id,
                     CLogger::LEVEL_ERROR, 'hitour.service.notify');

            $passed = false;
            $check_result = false;
        }

        if (!$setting['template']) {
            Yii::log('HtNotifyTemplate not found.Order_id = ' . $order_id . ',status_id=' . $order_status_id,
                     CLogger::LEVEL_ERROR, 'hitour.service.notify');

            $passed = false;
            $check_result = false;
        }

        return [$passed, $check_result];
    }

    private function getLabelByLanguage($language_id)
    {
        $target_language = $language_id == 1 ? 'en' : 'zh';

        return array(
            'booking_introduction' => Yii::t('service', 'booking_introduction', array(), null, $target_language),
            'refund_introduction' => Yii::t('service', 'refund_introduction', array(), null, $target_language),
            'order_id' => Yii::t('service', 'order_id', array(), null, $target_language),
            'hitour_booking_ref' => Yii::t('service', 'hitour_booking_ref', array(), null, $target_language),
            'product_name' => Yii::t('service', 'product_name', array(), null, $target_language),
            'quantity' => Yii::t('service', 'quantity', array(), null, $target_language),
            'pax_num' => Yii::t('service', 'pax_num', array(), null, $target_language),
            'pax' => Yii::t('service', 'pax', array(), null, $target_language),
            'lead' => Yii::t('service', 'lead', array(), null, $target_language),
            'child' => Yii::t('service', 'child', array(), null, $target_language),
            'name' => Yii::t('service', 'name', array(), null, $target_language),
            'male' => Yii::t('service', 'male', array(), null, $target_language),
            'female' => Yii::t('service', 'female', array(), null, $target_language),
        );
    }

    /**
     * @param $template
     * @param string $data
     * @return string
     */
    private function templateRender($template, $data = '')
    {
        $result = Mail::templateRender($template, $data);
        if ($result == '') {
            //TODO:throw exception
            Yii::log('Template[' . $template . '] render failed. --EXIT--!!!', CLogger::LEVEL_ERROR);
            exit();
        }

        return $result;
    }

    public function notifyOP($order, $order_product, $preview = false, $priority_type = 0)
    {
        $order_status_id = $order['status_id'];
        $setting = HtNotifySetting::model()->with('email_setting',
                                                  'template')->findByAttributes(array('order_status_id' => $order_status_id, 'notify_obj_name' => HtNotifySetting::OP));

        list($passed, $check_result) = $this->checkSetting($setting, $order['order_id'], $order_status_id);
        if (!$passed) {
            return $check_result;
        }

        $body = $this->templateRender($this->template_path . $setting['template']->path,
                                      ['order' => $order, 'order_product' => $order_product]); //渲染邮件模板
        if ($preview) {
            return $body;
        }

        $language_id = 2;

        $subject = $this->getSubjectToOP($order_status_id, $priority_type);
        $subject .= '单号:' . $order_product['order_id'] . ',' . $order_product['product']['descriptions'][$language_id]['name'];

        $supplier = HtSupplier::model()->findByPk($order_product['product']['supplier_id']);
        $supplier_name = $supplier['cn_name'] ? $supplier['cn_name'] : $supplier['name'];
        $subject = '[' . $supplier_name . ']' . $subject;

        if (Yii::app()->params['PAYMENT_REALLY'] == 1) {
            $to = 'op@hitour.cc';
        } else {
            $to = $order['contacts_email'];
        }

        //发送邮件
        $is_ok = Mail::sendBySetting($setting['email_setting'], $to, $subject, $body);

        return $is_ok;
    }

    private function getSubjectToOP($order_status_id, $priority_type = 0)
    {
        $subject = '';
        switch ($priority_type) {
            case 1:
                $subject .= '！！「订单处理中」邮件发送失败，需要联系用户确认邮箱～';
                return $subject;
            default:
                break;
        }
        switch ($order_status_id) {
            case HtOrderStatus::ORDER_WAIT_CONFIRMATION:
                $subject .= '新订单请尽快处理~';
                break;
            case HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION:
                $subject .= '退订申请请尽快处理~';
                break;
            case HtOrderStatus::ORDER_BOOKING_FAILED:
            case HtOrderStatus::ORDER_SHIPPING_FAILED:
            case HtOrderStatus::ORDER_STOCK_FAILED:
                $subject .= '重要！！！订单失败~';
                break;
            case HtOrderStatus::ORDER_RETURN_CONFIRMED:
                $subject .= '订单需要到银行后台人工退款~';
                break;
            default:
                $subject .= '订单处理~';
                break;
        }

        return $subject;
    }

    /**
     * @param $order_data
     * @param bool $preview
     * @return bool|string
     */
    public function notifyCustomer($order_data, $preview = false, $with_pdf = false)
    {
        $order = $order_data['order'];
        $main_product = $order_data['order_products'][0]['product'];

        $order_data['order']['account_order_url'] = Yii::app()->createAbsoluteUrl('account/account#orders');
        $order_status_id = $order['status_id'];
        $setting = HtNotifySetting::model()->with('email_setting',
                                                  'template')->findByAttributes(array('order_status_id' => $order_status_id, 'notify_obj_name' => HtNotifySetting::CUSTOMER));

        list($passed, $check_result) = $this->checkSetting($setting, $order['order_id'], $order_status_id);
        if (!$passed) {
            return $check_result;
        }

        $template = $setting['template']->path;

        //overwrite template
        $template = '/views/email/new_email.php';

        //coupon 类产品发货时邮件模板需要特殊处理,用 order_success_coupon.php
        if ($main_product['type'] == HtProduct::T_COUPON && in_array($order_status_id,[HtOrderStatus::ORDER_TO_DELIVERY,HtOrderStatus::ORDER_SHIPPED,HtOrderStatus::ORDER_SHIPPING_FAILED])) {
            $template = str_replace('new_email', 'order_success_coupon', $template);
        }
        $body = $this->templateRender($this->template_path . $template, $order_data); //渲染邮件模板
        if ($preview) {
            return $body;
        }

        $language_id = 2;

        $subject = $this->getSubjectToCustomer($order_status_id, $main_product['type']);

        $subject .= ',单号:' . $order['order_id'] . ',' . $main_product['descriptions'][$language_id]['name'];
        $to = $order['contacts_email'];

        //attachment.最新的邮件设计，邮件中不直接包含附件，只有voucher 下载链接
        $attachment = array();

        //装填附件
        if (in_array($order_status_id,
                     [HtOrderStatus::ORDER_TO_DELIVERY, HtOrderStatus::ORDER_SHIPPED, HtOrderStatus::ORDER_SHIPPING_FAILED])) {
            $voucher_folder = $order['voucher_path'];
            if (is_dir($voucher_folder)) {
                if ($dh = opendir($voucher_folder)) {
                    while (($file = readdir($dh)) !== false) {
                        if (strpos($file, '.pdf')) {
                            $attachment[] = $voucher_folder . $file;
                        }
                    }
                }
            }
        }

        //发货前检查附件是否需要
        //coupon 类产品发货时不发送 voucher，直接在邮件里发送优惠券信息
        //除coupon外 $with_pdf作为强制发pdf条件
        //附件数量大于8个不发送附件，(附件大小不超过 $VOUCHER_MAX_SIZE 不发送附件 ?)
        $MAX_NUM = 8;
        if($main_product['type'] == HtProduct::T_COUPON) {
            $attachment = [];
        } else {
            if ($with_pdf) {
                //不操作，强制发送pdf
            } else {
                if (count($attachment) > $MAX_NUM) {
                    $attachment = [];
                }
            }
        }


        Yii::log('Send mail to customer: to[' . $to . ']subject[' . $subject . ']', CLogger::LEVEL_INFO);

        //发送邮件
        $is_ok = Mail::sendBySetting($setting['email_setting'], $to, $subject, $body, $attachment);

        return $is_ok;
    }

    private function getSubjectToCustomer($order_status_id, $product_type)
    {
        $subject = '';
        switch ($order_status_id) {
            case HtOrderStatus::ORDER_PAYMENT_SUCCESS:
                $subject .= '订单处理中';
                break;
            case HtOrderStatus::ORDER_PAYMENT_FAILED:
                $subject .= '支付失败';
                break;
            case HtOrderStatus::ORDER_TO_DELIVERY:
            case HtOrderStatus::ORDER_SHIPPED:
            case HtOrderStatus::ORDER_SHIPPING_FAILED:
                $subject .= '订单成功';
                if ($product_type != HtProduct::T_COUPON) {
                    $subject .= '(出行前务必下载并打印兑换单)';
                }
                break;
            case HtOrderStatus::ORDER_REFUND_SUCCESS:
                $subject .= '订单已退订';
                break;
            default:
                $subject .= '订单处理中';
                break;
        }

        return $subject;
    }
}