<div class="product-tour">
    <div class="tour-brief-ctn">
        <div class="brief-img">
            <img ms-src="{{ data.recommendation.brief_avatar + '?imageView2/5/w/124/h/124' }}" alt="" />
            <p ms-text="data.recommendation.brief_author"></p>
        </div>
        <div class="split-decoration"></div>
        <div class="brief-ctn">
            <h4 class="brief-title" ms-text="data.recommendation.brief_title"></h4>
            <p class="brief-desc" ms-text="data.recommendation.brief_description"></p>
        </div>
    </div>
    <div class="tour-schedule-map"
         ms-css-background-image="url({{ data.recommendation.brief_image + '?imageView2/5/w/1000/h/400' }})">
    </div>
    <div class="tour-intro-ctn">
        <div class="tour-intro-img"
             data-target="product_tour_detail"
             ms-css-background-image="url({{ data.recommendation.trip_intro_image + '?imageView2/5/w/1000/h/254' }})"></div>
        <div class="access-btn" data-target="product_tour_detail">查看行程详情</div>
    </div>
    <div class="tour-outline">
        <h4 class="outline-title">行程概览</h4>
        <div class="outline-abstract">
            <ul class="clearfix">
                <li class="first">
                    <p class="text-center">
                        <span class="abstract-title abstract-head" ms-text="data.trip_highlight.total_days"></span>天
                    </p>
                    <p class="text-center" ms-text="data.trip_highlight.distance + '公里'"></p>
                </li>
                <li>
                    <p class="abstract-title">行程</p>
                    <p ms-text="data.trip_highlight.start_location"></p>
                    <p>到</p>
                    <p ms-text="data.trip_highlight.finish_location"></p>
                </li>
                <li>
                    <p class="abstract-title">您会看到</p>
                    <p ms-repeat-desc="data.trip_highlight.highlight_summary" ms-text="desc"></p>
                </li>
                <li class="last fix-border">
                    <p class="abstract-title">最佳旅游时间</p>
                    <p ms-text="data.trip_highlight.suitable_time"></p>
                </li>
            </ul>
        </div>
        <div class="outline-detail">
            <table>
                <thead>
                    <tr>
                        <th width="10%">时间</th>
                        <th width="20%">地点</th>
                        <th width="50%">行程亮点</th>
                        <th width="20%">住宿</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="day-row" ms-repeat-refs="data.trip_highlight.highlight_refs" data-target="product_tour_detail">
                        <td ms-text="'D' + refs.date" class="text-center"></td>
                        <td style="border-left: 1px solid #eee" ms-text="refs.location"></td>
                        <td style="border-left: 1px solid #eee">
                            <ul class="detail-highlights">
                                <li ms-repeat-highlight="refs.local_highlight" ms-text="highlight"></li>
                            </ul>
                        </td>
                        <td style="border-left: 1px solid #eee" ms-text="refs.lodging"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="show-detail-btn" data-target="product_tour_detail">查看行程详情&nbsp;&gt;</div>
    </div>
</div>