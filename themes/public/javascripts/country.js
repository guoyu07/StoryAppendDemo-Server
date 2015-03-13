/**
 * Created by godsong on 14-5-11.
 */


var dataFactory=new HitourDataFactory($request_urls.getCities);
var countryInfo=new ViewModel('countryInfo','en_name,cn_name,cover_url,country_image|{}');
var groupList=new ViewModel('groupList','city_groups|[]');
var cityList=new ViewModel('cityList','cities|[]');

countryInfo.bindData(dataFactory.getData(new DataAdapter({
    'country_image':function(val,data){
        if(val==null){
            return {cover_url:''}
        }
        else return val;
    }
})));
groupList.bindData(dataFactory.getData()).then(function(){
    $(function(){
        $(document.body).scrollTop=0;
        $('.loading-mask').hide();
    });
});
cityList.bindData(dataFactory.getData()).then(function(){
    console.log(window.innerHeight>document.body.offsetHeight);
    if(window.innerHeight>document.body.offsetHeight){

        $('.main-footer').addClass('fix-to-bottom')
    }
});