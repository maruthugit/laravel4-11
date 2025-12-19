<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Price Update</title>
  </head>
  <body>
        <div class="row" id="main-form">
            <div class="col-md-12" style="padding:50px;"> 
                <div class="row" style="margin-top:100px;">
                    <div class="input-group mb-3  col-md-6 pull-right">
                        <input type="text" class="form-control" id="option_id" placeholder="Price Option ID" aria-label="option id" aria-describedby="button-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2" v-on:click="addList()">Add Price Option</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive-lg" style="margin-top:50px;">
                    <table class="table  table-striped">
                        <thead>
                            <th>Product ID</th>
                            <th>Option ID</th>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Label</th>
                            <th >Price</th>
                            <th >Promo Price</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in list" >
                                <td>@{{item.ProductID}}</td>
                                <td>@{{item.id}}</td>
                                <td>@{{item.sku}}</td>
                                <td>@{{item.name}}</td>
                                <td>@{{item.label}}</td> 
                                <td style="width:150px;"><input v-bind:value="item.price" v-bind:id="'price_'+item.id" class="form-control" type="text"></td>
                                <td style="width:150px;"><input v-bind:value="item.price_promo" v-bind:id="'price_promo_'+item.id" class="form-control" type="text"></td>
                                <td><button class="btn btn-primary" v-on:click="updatePrice(item)">Update Price</button> <button v-on:click="removePrice(item,index)" class="btn btn-danger">Remove</button></td>
                            </tr>
                            <tr v-if="list.length <= 0">
                                <td colspan="8" style="text-align:center;">No record</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script>

    var nM = new Vue({
	el: '#main-form',
	data: {
        list : [],
	    },
        mounted:function() {
            this.getList();
        },
        watch: {
           
        },
        methods: {
            readyUI:function(){
               // Get category list
            },
            reset: function(){
            },
            addList: function(){
                var app = this;
                var optionid = $("#option_id").val();
                console.log(optionid);
                if(optionid.length > 0 ){
                    $.ajax({
                        type: 'POST',
                        url: '/product/addprice',
                        data: {
                            "optionid" : optionid
                        },
                        dataType: "json",
                        success: function(resultData) { 
                            alert(resultData.message);
                            app.getList();
                        }
                    });
                }
            },
            updatePrice: function(item){

                var app = this;
                var optionid = item.id ;
                var price = $("#price_"+item.id).val();
                var price_promo = $("#price_promo_"+item.id).val();
           
                if(optionid !== ""){
                    $.ajax({
                        type: 'POST',
                        url: '/product/saveprice',
                        data: {
                            "optionid" : optionid,
                            "price" : price,
                            "price_promo" : price_promo
                        },
                        dataType: "json",
                        success: function(resultData) { 
                            alert(resultData.message);
                        }
                    });
                }  
            },
            removePrice: function(item,index){

                var app = this;
                var optionid = item.id ;
                var txt;
                if(optionid !== ""){
                    var r = confirm("Are you sure to remove this price option from list ?");
                    if (r == true) {
                        $.ajax({
                            type: 'POST',
                            url: '/product/removeprice',
                            data: {
                                "optionid" : optionid
                            },
                            dataType: "json",
                            success: function(resultData) { 
                                alert(resultData.message);
                                app.list.splice(index , 1);
                            }
                        });
                    } 
                } 
            },
            getList: function(){
                var app = this;
                $.ajax({
                    type: 'GET',
                    url: '/product/productupdateprice',
                    data: {},
                    dataType: "json",
                    success: function(resultData) { 

                        app.list = resultData;
                        
                    }
                });
            }
        },
    });
    
    
    </script>
</body>
 
</html>