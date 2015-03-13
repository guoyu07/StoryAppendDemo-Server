//数据源和数据处理器
var carouselData = new HitourDataFactory($request_urls.getCarousel);
var recommendData = new HitourDataFactory($request_urls.getRecommend, null, function (data) {
    /*console.log(data);
    var html=datamation(data);
    console.log(html);
    $('.data-section').html(html);*/
    for (var j = 0; j < data.length; j++) {
        var src = data[j].items;
        data[j].actived = false;
        for (var i = 0; i < Math.min(src.length, 8); i++) {
            src[i].size = 3;
            src[i].width = 235;
            src[i].height = 288;
            src[i].image = (i % 4) + 1;
            if (data[j].type == 1) {
                src[i].headline = src[i].product_name;
                src[i].sub = src[i].city.cn_name;
                src[i].type=1;
            }
            else if (data[j].type == 2) {
                src[i].headline = src[i].city.cn_name;
                src[i].sub = src[i].city.en_name;
                src[i].link_url = src[i].city.link_url;
                src[i].type=2;
            }
            else if (data[j].type == 3) {
                src[i].headline = src[i].product_name;
                src[i].price_new = src[i].show_prices.price;
                src[i].price_old = src[i].show_prices.orig_price;
                src[i].type = 3;
            }
        }
        var first = src.slice(0, 4);
        var second = src.slice(4, 8);
        if (second.length == 3) {
            second[2]['size'] = 6;
            second[2]['width'] = 490;
            second[2]['height'] = 288;
            second[2]['image'] = 8;
        }
        else if (second.length == 2) {
            second[0]['size'] = 6;
            second[0]['width'] = 490;
            second[0]['height'] = 288;
            second[1]['size'] = 6;
            second[1]['width'] = 490;
            second[1]['height'] = 288;
            second[0]['image'] = 6;
            second[1]['image'] = 7;
        }

        data[j].first = first;
        data[j].second = second;
    }
    return data;
});
var cityData = new HitourDataFactory($request_urls.getCities);
//首页轮播图的viewModel同时绑定数据
var carouselList = new ViewModel("carouselList", "carousel_images|[]").bindData(carouselData.getData({
    carousel_images: '*'
})).then(function(){

    var getCurrentEle = function (posNumber){
        var items = $(".item");
        for(var i = 0;i < items.length;i++) {
            if(items.eq(i).attr("data-index") == posNumber) {
                return items.eq(i);
            }
        }
    };

    var getNextNumber = function (number, direction) {
        if(direction == "next") {
            var newNumber = parseInt(number) + 1;
            if(newNumber < $(".item").length) {
                return newNumber;
            }
            else {
                return 0;
            }
        }
        else if(direction == "prev") {
            if((number - 1) < 0) {
                return $(".item").length - 1;
            }
            else {
                number--;
                return number;
            }
        }
    };

    var $goLeft=$(".go-left").click(function(evt) {
        var currentIndex = $(".item.active").attr("data-index");
        $(".item.active").fadeOut(1000).removeClass("active");
        clearTimeout(cTimer);
        cTimer=setInterval(turnRight,8000);
        getCurrentEle(getNextNumber(currentIndex,"prev")).fadeIn(1000).addClass("active");
        evt.stopPropagation();
    });

    $(".go-right").click(function(evt) {
        var currentIndex = $(".item.active").attr("data-index");
        $(".item.active").fadeOut(1000).removeClass("active");
        getCurrentEle(getNextNumber(currentIndex,"next")).fadeIn(1000).addClass("active");
        clearTimeout(cTimer);
        cTimer=setInterval(turnRight,8000);
        evt.stopPropagation();
    });
    function turnRight(){
            var currentIndex = $(".item.active").attr("data-index");
            $(".item.active").fadeOut(1000).removeClass("active");
            getCurrentEle(getNextNumber(currentIndex,"next")).fadeIn(1000).addClass("active");
    }
    var cTimer=setInterval(turnRight,8000);

});


var cityList = new ViewModel('cityList', 'continents|[]');
var recommendlist = new ViewModel('recommendList', 'list|[],switchRecommend');
var active = new ViewModel('recommend1', 'title,brief,status,first|[],second|[]');
var left = new ViewModel('recommend3', 'title,brief,status,first|[],second|[]');
var right = new ViewModel('recommend2', 'title,brief,status,first|[],second|[]');


cityList.bindData(cityData.getData(new DataAdapter({
    'continents': '*'
})));
active.bindData(recommendData.getData(new DataAdapter({

        status: new String('middle')
    },'0//*'
)));

left.bindData(recommendData.getData(new DataAdapter({
        status: new String('left')
    },'1//*'
)));

right.bindData(recommendData.getData(new DataAdapter({
        status: new String('right')
    },'2//*'
)));
/*$( function() {
 var model = avalon.define( "cityList", function( vm ) {
 vm.continents = [];
 } );
 //avalon.scan();
 $.getJSON( $request_urls.getCities ).done( function( data ) {
 model.continents = data.data;
 } );
 } );*/
var recommendCtrl={
    active:active,
    left:left,
    right:right
};

recommendlist.bindData(recommendData.getData(new DataAdapter({
    list: function (val, src) {
        var list = [];
        for (var i = 0; i < src.length; i++) {
            list.push({
                name: src[i].name,
                type: src[i].type,
                actived:i==0
            });
        }
        return list;
    },
    switchRecommend: function () {
        var slicing=false;
        var activeIdx=0;
        return function(idx){
        if (!slicing) {
            if (activeIdx != idx) {
                recommendlist.viewModel.list[activeIdx].actived = false;
                recommendlist.viewModel.list[idx].actived = true;
                slicing = true;
                if (idx > activeIdx) {
                    recommendCtrl.right.bindData(recommendData.getData(new DataAdapter(idx+'//*')));
                    //DataAdapter.setup(recommendCtrl.right, data[idx], 'title,brief,first,second');
                    recommendCtrl.active.viewModel.status = 'left';
                    recommendCtrl.right.viewModel.status = 'middle';
                    setTimeout(function () {
                        recommendCtrl.active.viewModel.status = 'quiet right';
                        setTimeout(function () {
                            recommendCtrl.active.viewModel.status = 'right';
                            recommendCtrl.active = [recommendCtrl.right, recommendCtrl.right = recommendCtrl.active][0]; //swap active and right for zhuangbility
                            slicing = false;
                        }, 100);

                    }, 600);
                }
                else {
                    recommendCtrl.left.bindData(recommendData.getData(new DataAdapter(idx+'//*')));
                    //DataAdapter.setup(rt.left, data[idx], 'title,brief,first,second');
                    recommendCtrl.active.viewModel.status = 'right';
                    recommendCtrl.left.viewModel.status = 'middle';
                    setTimeout(function () {
                        recommendCtrl.active.viewModel.status = 'quiet left';
                        setTimeout(function () {
                            recommendCtrl.active.viewModel.status = 'left';
                            recommendCtrl.active = [recommendCtrl.left, recommendCtrl.left = recommendCtrl.active][0]; //swap active and right for zhuangbility
                            slicing = false;
                        }, 100);

                    }, 600);


                }
                activeIdx = idx;
            }


        }
        }
    }
}))).then(function(){

    $(function(){
        $(document.body).scrollTop(0);
        $('.loading-mask').hide();
    });
});
function datamation($data){
    var $html='';
    var $firstKey=false;
    for(var $key in $data ){
        var $item=$data[$key];
        if($firstKey===false){
            $firstKey=$key;
        }

        if($firstKey==0){
            var $outer=1;
            $html+='<li>';
        }
        else{
            $outer=0;
        }

        if(typeof $item=='object'){
            $html+=datamation($item);
        }
        else{
            if($key=='link_url'){
                $html+='<a data-name="'+$key+'" href="'+$item+'">地址</a>';
            }
            else{
                $html+='<p data-name="'+$key+'">'+$item+'</p>';
            }

        }
        if($firstKey==0){
            $html+='</li>';
        }
        // echo $firstKey,'->',$key,"  [",gettype($item),']===',htmlentities($html),'<br>';
    }
    return $outer==1?('<ul>'+$html+'</ul>'):$html;
}
var d=[{a:1,c:[1]},{a:1,c:[2]}];
x=datamation(d);
