<?php
/**
 * @project hitour.server
 * @file ProductAsk.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 15-2-10 下午5:02
 **/

class ProductAskController extends AdminController
{

    public function actionGet()
    {
        $param['sort']         = $this->getParam('sort', array());
        $param['paging']       = $this->getParam('paging', array());
        $param['query_filter'] = $this->getParam('query_filter', array());
        $param['product_id']   = $this->getParam('product_id');
        $asks = HtProductAsk::getAsks($param);
        EchoUtility::echoCommonMsg(200, '', array('data' => $asks));
    }

    public function actionSave()
    {
        $product_id = $this->getParam('product_id');
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'get') {
        } elseif ($request_method == 'post') {
            $data = $this->getPostJsonData();
            $ask_id = (int)$data['ask_id'];
            if (empty($ask_id)) {
                $ask = new HtProductAsk();

                ModelHelper::fillItem($ask, $data,
                    array('ask_type', 'priority', 'status','contact_phone','contact_weixin','contact_qq','contact_mail','contact_name','question','answer','date_expected','is_online'));
                $ask['product_id'] = $product_id;
                $ask['user_id'] = Yii::app()->user->id;

                $result = $ask->insert();
                if($result) {
                    $recordUser = User::model()->findByPk($ask['user_id']);
                    $setting = HtEmailSetting::model()->findByAttributes(array('setting_name' => 'op'));
                    Mail::sendBySetting($setting, 'huanhuan@hitour.cc', $recordUser['screen_name'].'在商品'.$ask['product_id'].'下添加了新的Q&A', '记得去查看哟~', '');
                }

                EchoUtility::echoMsgTF($result, '添加', array('ask_id'=>$ask->getPrimaryKey()));
            } else {
                $ask = HtProductAsk::model()->findByPk($ask_id);
                if (empty($ask->date_answered)) {
                    $data['date_answered'] = date('Y-m-d H:i:s');
                }
                $data['date_modified'] = date('Y-m-d H:i:s');
                $result = ModelHelper::updateItem($ask, $data,
                    array('ask_type', 'priority', 'status','contact_phone','contact_weixin','contact_qq','contact_mail','contact_name','question','answer','date_expected','is_online'));

                EchoUtility::echoMsgTF($result == 1, '更新', $ask);
            }
        } elseif ($request_method == 'delete') {
            $ask_id = (int)$this->getParam('ask_id');

            $result = HtProductAsk::model()->deleteByPk($ask_id);

            EchoUtility::echoMsgTF($result > 0, '删除');
        }

    }
} 