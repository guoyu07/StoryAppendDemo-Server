<div class="city-header blur"
     ms-css-background-image="url({{ local.header.image_url }}?imageView/5/w/1400/h/300)">
    <div class="wrap">
        <h1 class="city-name">
            <div class="cn-name">{{ local.header.title }}</div>
            <div class="en-name">{{ local.header.en_title }}</div>
        </h1>
    </div>
    <div class="city-breadcrumb">
        <div class="wrap">
            <div class="breadcrumb-wrap">
                <span class="breadcrumb-item"><a href="<?= $this->createUrl('home/index'); ?>">首页</a></span>
                <span class="breadcrumb-item">/ <a ms-href="data.city.country.link_url">{{ data.city.country_cn_name }}</a></span>
                <span class="breadcrumb-item">/ <a ms-href="data.city.link_url">{{ data.city.cn_name }}</a></span>
                <span class="breadcrumb-item" ms-visible="local.current_label">- <span>{{ local.current_label }}</span></span>
            </div>
            <div class="city-search">
                <label class="search-wrap">
                    <input class="search-input" type="text" ms-attr-placeholder="搜索 {{ data.city.cn_name }} 相关商品" ms-duplex="local.search_key" ms-keypress="doCityEnterSearch(local.search_key, $event)">
                    <i class="icon-magnifier search-btn" ms-click="doCitySearch(local.search_key)"> </i>
                </label>
            </div>
        </div>
    </div>
</div>