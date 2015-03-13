
var dataFactory=new HitourDataFactory($request_urls.getProducts,function(data){
    var groups = data.product_groups;
    for(var i = 0;i < groups.length;i++) {
        if(groups[i].products == ""){
            groups.splice(i,1);
            i--;
        }
    }
    if(data.city_image==null){
        data.city_image={};
    }
    data.product_groups.sort(function(a,b){

        if(a.type> b.type){
            return 1;
        }
        else if(a.type== b.type){
            return 0;
        }
        else {
            return -1;
        }
    });

    return data;
});

var cityInfo=new ViewModel('cityInfo','en_name,cn_name,country_name,country_url,city_image|{}');
var favorite=new ViewModel('favorite','products|[],turnLeft,turnRight,showIndex|0,abc');
var groupCatalog=new ViewModel('groupCatalog','list|[],switchGroup,changeOver,resetLine,cWidth,cLeft');
var groupInfo=new ViewModel('groupInfo','type,cover_image_url,name,description,tags|[],products|[]');

cityInfo.bindData(dataFactory.getData(new DataAdapter({
    country_name:'country.cn_name',
    country_url:'country.link_url'
})));
/*favorite.bindData(dataFactory.getData(new DataAdapter({
    turnLeft:function(){
        return function(){
            if(favorite.viewModel.showIndex>0){
                favorite.viewModel.showIndex--;
            }
        }
    },
    turnRight:function(val,data){
        return function(){
            if(favorite.viewModel.showIndex<Math.ceil(data.products.length/4)-1){
                favorite.viewModel.showIndex++;
            }
        }
    }
},'favoriteGroups//products')));*/
var hoverTm;
groupCatalog.bindData(dataFactory.getData(new DataAdapter({
    list:function(val,src){
        var list=[];
        for(var i=0;i<src.product_groups.length;i++){
            var group=src.product_groups[i];
            list.push({
                name:group.name,
                counts:group.products.length,
                active:false,
                group_id:group.group_id,
                type:group.type

            });
        }
        if(list.length>0){
            list[0].active=true;
        }
        return list;
    },
    switchGroup:function(){
        var lastActive=0;
        return function(evt){
            var idx=this.getAttribute('data-index');
            var el=this;

                $('.all-product').addClass('fade-out');
            setTimeout(function(){
                    groupCatalog.viewModel.list[lastActive].active=false;
                    groupCatalog.viewModel.list[idx].active=true;
                    lastActive=idx;
                    groupCatalog.viewModel.cLeft=el.offsetLeft;
                    groupCatalog.viewModel.cWidth=el.width;
                    groupInfo.bindData(dataFactory.getData(new DataAdapter({
                    },'product_groups.'+idx+'//*')));
                    if((document.body.scrollTop||document.documentElement.scrollTop)>270){
                        $('body,html').animate({scrollTop:295},400);
                    }
                    $('.all-product').removeClass('fade-out');
                },400);



        }
    },

    changeOver : function() {
        return function(evt) {
            clearTimeout(hoverTm);
            groupCatalog.viewModel.cLeft=this.offsetLeft;
            groupCatalog.viewModel.cWidth=this.offsetWidth - 54;
            evt.stopPropagation();
        }
    },

    resetLine : function() {
        return function() {
            hoverTm=setTimeout(function(){
                groupCatalog.viewModel.cLeft=$(".eachtab.active").position().left;
                groupCatalog.viewModel.cWidth=$(".eachtab.active").width();
            },100);

        }
    }

}),'^')).then(function(){
        $(function(){
            var gid=location.hash.slice(1);
            var lis=$('#catalog_list li');

            if(gid!==''){
                for(var i= 0,l=lis.length;i<l;i++){
                    var li=lis[i];
                    if(li.getAttribute('gid')==gid){
                        li.click();
                        return;
                    }
                }
                lis[0].click()
            }
            else if(groupCatalog.viewModel.list.length>0){
                groupCatalog.viewModel.list[0].active=true;
                console.log(lis[0].offsetWidth)

                setTimeout(function(){
                    var li=$('#catalog_list li')[0];
                    groupCatalog.viewModel.cLeft=li.offsetLeft;
                    groupCatalog.viewModel.cWidth=li.offsetWidth-54;
                },200);
            }
        });



});

groupInfo.bindData(dataFactory.getData(new DataAdapter({
},'product_groups.0//*'))).then(function(){
    $(function(){
        $(document.body).scrollTop=0;
        $('.loading-mask').hide();
    });
});


//ViewModel.render();
//FIXME 兼容处理
avalon.ready(function(){

    var h=$('.fix-hold').offset().top,
        $fix=$('#city_cover_ctn'),
        $backTop=$('.back-top').on('click',function(){
            $('body,html').animate({scrollTop:0},500);
        });
    window.onscroll=function(){
        var st=document.body.scrollTop||document.documentElement.scrollTop;
        if(st>=h){
            $fix.addClass('fix-top');
            $backTop.addClass('display');
        }
        else{
            $fix.removeClass('fix-top');
            $backTop.removeClass('display');
        }
    }
});

