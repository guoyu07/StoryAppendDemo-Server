
<div class="city-more" ms-controller="otherCityRec">
    <div class="other-cities container clearfix">
        <div class="left-title">{{ country_name }}其他城市</div>
        <ul class="right-content" ms-if="other_cities.groups">
            <li class="group-item clearfix" ms-repeat-gp="other_cities.groups">
                <div class="left-group-title"><span>{{ gp.name }}</span></div>
                <ul class="right-group-contnt">
                    <li class="city-item" ms-repeat-ct="gp.cities">
                        <a ms-href="ct.link_url">{{ ct.cn_name }}</a></li>
                </ul>
            </li>
        </ul>
        <ul class="right-content" ms-if="other_cities.cities">
            <li class="city-item" ms-repeat-ct="other_cities.cities">
                <a ms-href="ct.link_url">{{ ct.cn_name }}</a>
            </li>
        </ul>
    </div>
    <div class="rec-cities container clearfix" ms-if="rec_cities.length > 0">
        <div class="left-title">推荐城市</div>
        <ul class="right-content">
            <li class="city-item" ms-repeat-ct="rec_cities">
                <a ms-href="ct.link_url">{{ ct.cn_name }}</a>
            </li>
        </ul>
    </div>
</div>