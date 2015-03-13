var productTourModel = avalon.define("productTourCtrl", function(vm) {

    vm.data = {};

    vm.DataInitializer = {
        'setData' : function(data) {
            productTourModel.data = data;
        }
   };
});