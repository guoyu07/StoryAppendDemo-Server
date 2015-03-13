<?php
/**
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 15-3-9
 * Time: 下午5:36
 */
class ActivitySougouController extends Controller
{
    public $layout = '//layouts/sougou';
    public $resource_refs;

    public function actionFridaySale(){
        $data = $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getFridaySaleData' => $this->createUrl('activity/fridaySaleData'))
        );
        $this->setPageTitle('玩途五折特卖_境外游门票_全场限时五折预订_玩途自由行');
        $this->setKeywords('玩途周五特卖,景点门票5折,自由行产品5折');
        $this->setDescription('玩途周五特卖,每周五晚5点起，爆款商品5折，还有更多海外自由行产品6折预订，轻松海外游，尽在玩途自由行！');

        $this->render('fridaysale', $data);
    }

}
