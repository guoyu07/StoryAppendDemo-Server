
<div class="main-wrap">
    <section ms-controller="countryInfo" class="country-cover-ctn">
        <div class="img-ctn" ms-css-background-image="url({{country_image.cover_url}})">
            <div class="nav-ctn">
                <nav><a href="<?= $this->createUrl('home/index'); ?>">首页</a><span class="sep">></span><a
                        href="javascript:;">{{cn_name}}</a></nav>
            </div>
        </div>
        <div class="country-name-ctn"><span class="country-cn-name">{{cn_name}}</span><span class="country-en-name">{{en_name}}</span>
        </div>
    </section>

    <section class="group-list container">
        <div ms-controller='groupList' ms-repeat-gp="city_groups" class="group-ctn">
            <div class="group-title row">
                <div class="group-cover" ms-css-background-image="url({{gp.cover_url}})"></div>
                <div class="group-info">
                    <div class="group-name">{{gp.name}}</div>
                    <div class="group-desc">{{gp.description}}</div>
                </div>
            </div>
            <div class="group-list row">

                <div class="city-block col-md-3" ms-repeat-city="gp.cities">
                    <a ms-attr-href="{{city.link_url}}"
                       ms-css-background-image="url({{city.city_image.grid_image_url}}?imageView/5/w/320/h/212)" class="city">
                        <div class="city-name-zone">
                            <div class="city-name">{{city.cn_name}}</div>
                            <div class="city-en-name">{{city.en_name}}</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>


    </section>
    <section class="city-list container">
        <div ms-controller='cityList' class="group-ctn">

            <div class="group-list row">

                <div class="city-block col-md-3" ms-repeat-city="cities">
                    <a ms-attr-href="{{city.link_url}}"
                       ms-css-background-image="url({{city.city_image.grid_image_url}}?imageView/5/w/320/h/212)" class="city">
                        <div class="city-name-zone">
                          <div class="city-name">{{city.cn_name}}</div>
                          <div class="city-en-name">{{city.en_name}}</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </section>
</div>
