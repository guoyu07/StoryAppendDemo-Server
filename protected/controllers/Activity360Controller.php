<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 7/20/14
 * Time: 10:41 PM
 */
class Activity360Controller extends Controller
{
    public $layout = '//layouts/360';
    public $resource_refs;

    public function actionPromotion()
    {
      $this->resource_refs = 'promotion.res';
      $data = $this->initData();
      $this->request_urls = array_merge(
          $this->request_urls,
          array(
              'getPromotionDetail' => $this->createUrl('promotion/promotionDetail',
                  array('promotion_id' => 13))
          )
      );
      $this->render('promotion13', $data);
    }

    public function actionFridaySale(){
      $data = $this->initData();
        $this->setPageTitle('玩途五折特卖_境外游门票_全场限时五折预订_玩途自由行');
        $this->setKeywords('玩途周五特卖,景点门票5折,自由行产品5折');
        $this->setDescription('玩途周五特卖,每周五晚5点起，爆款商品5折，还有更多海外自由行产品6折预订，轻松海外游，尽在玩途自由行！');

        $this->render('fridaysale', $data);
    }

    public function actionNewYearSale(){
        $data = $this->initData();
        $this->setPageTitle('玩途新春5折来袭，扫码领券5折享不停');
        $this->setKeywords('玩途新年特卖，景点门票5折，自由行产品5折');
        $this->setDescription('玩途新年特卖，爆款商品5折，还有更多海外自由行产品6折预订，轻松海外游，尽在玩途自由行！');
        $this->render('newyearsale', $data);
    }

    public function action1111() {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getKidAdultData' => $this->createUrl('activity/double11Data'))
        );

        $this->setPageTitle('玩途双11立减特惠_全球乐园门票_玩途自由行');
        $this->setKeywords('玩途双11立减，全球乐园门票');
        $this->setDescription('玩途双11，下单立减50，畅玩迪士尼、海洋公园等全球32座顶级乐园!');

        $this->render("kidadult");
    }

    public function actionShopping() {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getKidAdultData' => $this->createUrl('activity/shoppingData'))
        );

        $this->setPageTitle('玩途双11立减特惠_全球乐园门票_玩途自由行');
        $this->setKeywords('玩途双11立减，全球乐园门票');
        $this->setDescription('玩途双11，下单立减50，畅玩迪士尼、海洋公园等全球32座顶级乐园!');

        $this->render("kidadult");
    }
}
