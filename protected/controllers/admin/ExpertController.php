<?php
/**
 * Created by PhpStorm.
 * User: Veaer
 * Date: 15/3/10
 * Time: 下午3:45
 */

class ExpertController  extends AdminController
{
    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '编辑管理';

        $request_urls = array(
            'expert' => $this->createUrl('expert/expert', array('id' => '')),
            'updateExpertImage' => $this->createUrl('expert/updateExpertImage', array('id' => '')),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('index');
    }

    public function actionExpert()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if($request_method == 'get') {
            $result = Converter::convertModelToArray(HtTravelExpert::model()->findAll());
            EchoUtility::echoMsgTF(true, '获取专家列表', $result);
        } else if($request_method == 'post') {
            $data = $this->getPostJsonData();
            $item = HtTravelExpert::model()->findByPk($data['id']);
            if (empty($item)) {
                $item = new HtTravelExpert();
                $result = $item->insert();
                EchoUtility::echoMsgTF($result, '添加', Converter::convertModelToArray($item));
            } else {
                $item['name'] = $data['name'];
                $item['brief'] = $data['brief'];
                $result = $item->update();
                EchoUtility::echoMsgTF($result, '修改', $data);
            }
        } else {
            $expert_id = (int)$this->getParam('id');

            $result = HtTravelExpert::model()->deleteByPk($expert_id);

            EchoUtility::echoMsgTF($result > 0, '删除');
        }
    }

    public function actionUpdateExpertImage()
    {
        $id = $_POST['id'];
        $cg = HtTravelExpert::model()->findByPk($id);

        if (empty($cg)) {
            EchoUtility::echoCommonFailed('未找到id为' . $id . '的专家。');

            return;
        }

        $to_dir = 'image/upload/expert/' . $id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $cg['avatar'] = $image_url;
            $result = $cg->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

}