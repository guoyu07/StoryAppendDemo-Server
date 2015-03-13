<?php

class TagController extends AdminController
{

    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = 'tag列表';

        $request_urls = array(
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = 'TAG编辑';

        $tag_id = Yii::app()->request->getParam('tag_id');

        $request_urls = array(
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('edit');
    }

    //获取所有tag（parent+sub两级）
    public function actionGetTags()
    {
        $return = array();
        $tags = HtTag::model()->findAll();
        $tags = Converter::convertModelToArray($tags);
        foreach($tags as $tag){
            if($tag['parent_tag_id'] == 0){
                $return['parent_tag'][] = $tag;
            }else{
                $return['sub_tag'][] = $tag;
            }
        }
        echo CJSON::encode(array('code' => 200, 'msg' => '获取商品tag成功！', 'data' => $return));
    }

}
