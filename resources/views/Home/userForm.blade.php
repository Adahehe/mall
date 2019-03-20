@extends('Home.memberInfo')

@section('info')
<script type="text/javascript">
    $(document).ready(function(){

        setInterval(showTime, 1000);
        function timer(obj,txt){
            obj.text(txt);
        }
        function showTime(){
            var today = new Date();
            var weekday=new Array(7)
            weekday[0]="星期日"
            weekday[1]="星期一"
            weekday[2]="星期二"
            weekday[3]="星期三"
            weekday[4]="星期四"
            weekday[5]="星期五"
            weekday[6]="星期六"
            var y=today.getFullYear()+"年";
            var month=today.getMonth()+1+"月";
            var td=today.getDate();
            var d=weekday[today.getDay()];
            var h=today.getHours();
            var m=today.getMinutes();
            var s=today.getSeconds();
            timer($("#y"),y+month);
            //timer($("#MH"),month);
            timer($("h1"),td);
            timer($("#D"),d);
            timer($("#H"),h);
            timer($("#M"),m);
            timer($("#S"),s);
        }
    })
</script>
<div class="user_right">
    <div class="user_center_style">
        <div class="user_time">
            <h1></h1>
            <h4 id="D"></h4>
            <h4 id="y"></h4>
        </div>
        <ul class="user_center_info">
            <li>
                <img src="/image/home/user_img_05.png" />
                <h4 class="Money">余额￥34</h4>
            </li>
            <li><img src="/image/home/user_img_04.png" />
                <a href="#">代收货（3）</a>
            </li>
            <li><img src="/image/home/user_img_06.png" />
                <a href="#">积分234分</a>
            </li>
            <li><img src="/image/home/user_img_03.png" />
                <a href="#">订单评价（5）</a>
            </li>
        </ul>
    </div>
    <div class="Order_form">
        <div class="user_Borders">
            <div class="title_name">
                <span class="name">我的订单</span>
                <a href="#">更多订单&gt;&gt;</a>
            </div>
            <div class="Order_form_list">
                <table>
                    <thead>
                    <td class="list_name_title0">商品</td>
                    <td class="list_name_title1">单价(元)</td>
                    <td class="list_name_title2">数量</td>
                    <td class="list_name_title4">实付款(元)</td>
                    <td class="list_name_title5">订单状态</td>
                    <td class="list_name_title6">操作</td>
                    </thead>
                    <tbody>
                    <tr><td colspan="6" class="Order_form_time">2015-12-3 订单号：445454654654654</td></tr>
                    <tr>
                        <td colspan="3">
                            <table class="Order_product_style">
                                <tr>
                                    <td>
                                        <div class="product_name clearfix">
                                            <a href="#"><img src="/image/test/2.jpg"  width="80px" height="80px"/></a>
                                            <a href="3">天然绿色多汁香甜无污染水蜜桃</a>
                                            <p class="specification">礼盒装20个/盒</p>
                                        </div>
                                    </td>
                                    <td>5</td>
                                    <td>2</td>
                                </tr>
                            </table>
                        </td>
                        <td class="split_line">100</td>
                        <td class="split_line">已发货，待收货</td>
                        <td></td>
                    </tr>
                    </tbody>
                    <tbody>
                    <tr><td colspan="6" class="Order_form_time">2015-12-3 订单号：445454654654654</td></tr>
                    <tr>
                        <td colspan="3">
                            <table class="Order_product_style">
                                <tr>
                                    <td>
                                        <div class="product_name clearfix">
                                            <a href="#"><img src="/image/test/2.jpg"  width="80px" height="80px"/></a>
                                            <a href="3">天然绿色多汁香甜无污染水蜜桃</a>
                                            <p class="specification">礼盒装20个/盒</p>
                                        </div>
                                    </td>
                                    <td>5</td>
                                    <td>2</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="product_name clearfix">
                                            <a href="#"><img src="/image/test/2.jpg"  width="80px" height="80px"/></a>
                                            <a href="3">天然绿色多汁香甜无污染水蜜桃</a>
                                            <p class="specification">礼盒装20个/盒</p>
                                        </div>
                                    </td>
                                    <td>5</td>
                                    <td>2</td>
                                </tr>
                            </table>
                        </td>

                        <td class="split_line">100</td>
                        <td class="split_line">已发货，待收货</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection