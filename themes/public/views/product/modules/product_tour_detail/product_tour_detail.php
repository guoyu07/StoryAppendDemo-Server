<div class="hi-modal" id="product_tour_detail">
    <div class="full-overlay" data-close="product_tour_detail">
        <i class="icon-x"></i>
    </div>
    <div class="modal-wrap tour-detail">
        <div class="nav-col">
            <div class="day-nav" data-bind="tour-content">
                <div class="day-item clearfix" ms-repeat-day="data.tour_plan"
                     ms-attr-data-target="day_{{ $index + 1 }}" ms-class="active : $index == 0">
                    <div class="icon-circle-bg"></div>
                    <div class="item-text"><span>D{{ $index + 1 }}</span>{{ day.title }}</div>
                </div>
            </div>
        </div>
        <div class="content-col">
            <!--            <div class="tour-title">-->
            <!--                <div class="title-text">{{ data.description.name }}</div>-->
            <!--            </div>-->
            <div class="days" id="tour-content">
                <div class="day" ms-repeat-day="data.tour_plan" ms-attr-id="day_{{ $index + 1 }}">
                    <div class="day-title clearfix">
                        <div class="day-date"><span>DAY</span><em>{{ $index + 1 }}</em></div>
                        <div class="day-name">{{ day.title }}</div>
                        <div class="day-highlights">{{ day.local_highlight }}</div>
                    </div>
                    <div class="day-content">
                        <div class="day-group" ms-repeat-group="day.groups">
                            <i class="icon-clock-circle-bg"></i>
                            <div class="group-content">
                                <div class="group-title">
                                    <span>{{ group.time }}</span>&nbsp;&nbsp;<span>{{ group.title }}</span></div>
                                <div class="group-item" ms-repeat-item="group.items">
                                    <div class="item-image" ms-if="item.image_url">
                                        <img ms-src="{{ item.image_url }}?imageView2/5/w/590/h/300">
                                    </div>
                                    <div class="item-image2" ms-if="item.image_url2">
                                        <img ms-src="{{ item.image_url2 }}?imageView2/5/w/590/h/300">
                                    </div>
                                    <div class="item-title" ms-visible="item.title">{{ item.title }}</div>
                                    <p class="item-desc" ms-visible="item.description">{{ item.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>