<script type="text/ng-template" id="editProductHotel.html">
<div class="view-edit-section last clearfix" data-ng-controller="editProductHotelCtrl">
<section class="one-section-action">
    <form name="product_hotel_form" novalidate>
        <div class="row edit-heading">
            <h2>基本信息</h2>
        </div>
        <div class="row edit-body grid-bottom">
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">位于:</label>
                    <div class="col-md-8">
                        <input class="form-control input-sm" type="text" data-ng-model="hotel.location" placeholder="普吉岛 巴东海滩" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">中文地址:</label>
                    <div class="col-md-8">
                        <input class="form-control input-sm" type="text" data-ng-model="hotel.address_zh"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">英文地址:</label>
                    <div class="col-md-8">
                        <input class="form-control input-sm" type="text" data-ng-model="hotel.address_en" placeholder="187/5 Rat U Tid 200 Pee Road" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">坐标:</label>
                    <div class="col-md-8">
                        <input class="form-control input-sm" type="text" data-ng-model="hotel.latlng" placeholder="7.338483,134.480828" required ng-pattern="latLng"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">星级:</label>
                    <div class="col-md-2">
                        <input class="form-control input-sm" type="text"
                               data-ng-model="hotel.star_level" required ng-pattern="onlyNumbers"/>
                    </div>
                    <label class="col-md-2">星</label>
                </div>
            </div>
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">评分:</label>
                    <div class="col-md-16">
                        <div class="row" ng-repeat="rate in rateSources">
                            <label class="col-md-2"><a ng-href="{{rate.website}}" target="_blank">{{rate.name}}</a></label>
                            <div class="col-md-2">
                                <input class="form-control input-sm" type="text" ng-click="toogleRate(rate.source_id)"
                                       data-ng-model="hotel.rates[rate.source_id].rate" ng-pattern="onlyNumbers"/>
                            </div>
                            <label class="col-md-2">分</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row edit-heading">
            <h2>酒店亮点</h2>
        </div>
        <div class="row edit-body grid-bottom">
            <div class="row">
                <div class="edit-content clearfix">
                    <div>
                        <textarea class="input-area editor" data-ng-model="hotel.highlight" required></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row edit-heading">
            <h2>酒店设施</h2>
        </div>
        <div class="row edit-body grid-bottom">
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">活动设施:</label>
                    <div class="col-md-8">
                        <input class="form-control input-sm" type="text" data-ng-model="hotel.facilities" placeholder="游泳池，网球场，健身中心，儿童游乐场" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">餐饮服务:</label>
                    <div class="col-md-8">
                        <input class="form-control input-sm" type="text" data-ng-model="hotel.food_service" placeholder="早餐收费" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">停车场:</label>
                    <div class="col-md-8">
                        <input class="form-control input-sm" type="text" data-ng-model="hotel.parking_lot" placeholder="酒店（需提前预定）可提供私人停车设施" required/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row edit-heading">
            <h2>酒店政策</h2>
        </div>
        <div class="row edit-body grid-bottom">
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">入住时间:</label>
                    <div class="col-md-8">
                        <input class="form-control input-sm" type="text" data-ng-model="hotel.check_in_time" placeholder="14：00后" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-2">退房时间:</label>
                    <div class="col-md-8">
                        <input class="form-control input-sm" type="text" data-ng-model="hotel.check_out_time" placeholder="次日12:00之前" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="edit-content clearfix">
                    <label class="col-md-4">酒店接受的银行卡类型:</label>
                    <div class="col-md-14 col-md-offset-2">
                        <div class="col-md-6" style="height: 40px" ng-repeat="bankcard in bankcard_items">
                            <div class="row" style="line-height: 230%">
                                <input type="checkbox"
                                       name="hotel.bankcards[]"
                                       value="{{bankcard.bankcard_id}}"
                                       ng-checked="hotel.bankcards.hasOwnProperty(bankcard.bankcard_id)"
                                       ng-click="toggleBankcard(bankcard.bankcard_id)">
                                {{bankcard.bankcard_name}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form"
                data-ng-click="submitChanges()" data-ng-disabled="product_hotel_form.$invalid">
            保存
        </button>
    </form>
</section>
</div>
</script>