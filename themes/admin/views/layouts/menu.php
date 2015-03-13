<?php
if(!Yii::app()->user->isGuest) {
    $items = array(
        array(
            'url' => $this->createUrl('product/index'),
            'label' => '商品'
        ),
        array(
            'url' => $this->createUrl('order/index'),
            'label' => '订单'
        ),
        array(
            'url' => $this->createUrl('article/index'),
            'label' => '文章'
        ),
        array(
            'url' => $this->createUrl('statistics/index'),
            'label' => '统计'
        ),
        array(
            'label' => '运营',
            'items' => array(
                array(
                    'url' => $this->createUrl('home/index'),
                    'label' => '首页管理'
                ),
                array(
                    'url' => $this->createUrl('country/index'),
                    'label' => '国家管理'
                ),
                array(
                    'url' => $this->createUrl('city/index'),
                    'label' => '城市管理'
                ),
                array(
                    'url' => $this->createUrl('promotion/index'),
                    'label' => '活动管理'
                ),
                array(
                    'url' => $this->createUrl('operation/todo'),
                    'label' => '待办事项'
                ),
                array(
                    'url' => $this->createUrl('edm/index'),
                    'label' => 'EDM管理'
                ),
                array(
                    'url' => $this->createUrl('errorPage/index'),
                    'label' => '错误页面管理'
                ),
                array(
                    'url' => $this->createUrl('expert/index'),
                    'label' => '专家管理'
                )
            ),
        ),
        array(
            'label' => '其他',
            'items' => array(
                array(
                    'url' => $this->createUrl('supplier/index'),
                    'label' => '供应商管理'
                ),
                array(
                    'url' => $this->createUrl('coupon/index'),
                    'label' => '优惠券管理'
                ),
                array(
                    'url' => $this->createUrl('stock/index'),
                    'label' => '库存管理'
                ),
                array(
                    'url' => $this->createUrl('invoice/index'),
                    'label' => '对账管理'
                ),
            ),
        ),
    );
}

if(Yii::app()->user->isGuest) {
    $items[] = array(
        'url' => $this->createUrl('site/login'),
        'label' => 'Login'
    );
} else {
    $items[] = array(
        'url' => $this->createUrl('site/logout'),
        'label' => 'Logout'
    );
}

$this->widget('bootstrap.widgets.TbNavbar', array(
    'type' => 'default',
    'fixed' => false,
    'brand' => 'Hitour',
    'brandUrl' => Yii::app()->createUrl('admin/product/index'),
    'collapse' => true,
    'items' => array(
        array(
            'class' => 'bootstrap.widgets.TbMenu',
            'items' => $items
        ),
    )
));