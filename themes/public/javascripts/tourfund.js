/**
 * Created by godsong on 14-9-3.
 */
var dataFactory=new HitourDataFactory($request_urls.getFundInfo);
var fund=new ViewModel('fund','fund_total,remain,available,dandelions|[]');
fund.bindData(dataFactory.getData({
    fund_total:function(val){
        return val|0;
    },
    remain:function(val,data){
        var r= 0,a=0;
        for(var i=0;i<data.dandelions.length;i++){
            if(data.dandelions[i].shared==0){
                r++;
                a+=+data.dandelions[i].discount;
            }
        }

        this.available=a;
        return r;
    }
})).then(function(){
    $(function () {
        $('.loading-mask').hide();
    });
});