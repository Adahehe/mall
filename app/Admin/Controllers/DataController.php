<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Model\ProductForm;
use App\Model\Store;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Echarts\Echarts;
use Encore\Admin\Widgets\InfoBox;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
//        dd(Admin::user()->permissions);
        //今天的时间
        $start = strtotime(date('Y/m/d' , time()));
        $end = $start + 86400;
        $store = Auth::guard('admin')->user()->id;
        $business = Store::select(DB::raw("count(*) as num"))
            ->where('status' , Store::PENDING_APPLICATION)->groupBy("status")->first();
        $pro_form = ProductForm::select(DB::raw("count(*) as num"))->where('store_id' , $store)
            ->whereIn('status' , [ProductForm::SIGN_FOR_GOOD , ProductForm::READY_GOOG])
            ->groupBy("store_id")->first();
//        $money = ProductForm::select(DB::raw("sum(product_cost) as num"))->where('store_id' , $store)
//            ->whereBetween('created_at' ,[$start , $end])
//            ->groupBy("store_id")->first();

        //SELECT count(*)as num FROM mall_product_form where store_id=2 and `status`=1 GROUP BY store_id
        $send_to = ProductForm::select(DB::raw("count(*) as num"))->where([['store_id' , $store],['status' , ProductForm::PAY_ON_LINE]])
            ->groupBy("store_id")->first();

        $business_num = isset($business->num)?$business->num:0;
        $order = isset($pro_form->num)?$pro_form->num:0;
//        $money = isset($money->num)?$money->num:0;
        $send_to = isset($send_to->num)?$send_to->num:0;

//        dd($order);
        if(Admin::user()->can('Business')) {
            return $content
                ->header('消息')
                ->row(function (Row $row) use ($business_num, $order ,$send_to) {
                    if (Admin::user()->can('*')) {
                        $row->column(3, new InfoBox('供应商申请待审核', 'users', 'aqua', '/admin/store', $business_num));
                    }
                    $row->column(3, new InfoBox('成交订单', 'shopping-cart', 'green', '/admin/productform', $order));
//                    $row->column(3, new InfoBox('待发货', 'book', 'yellow', '/admin/productform', $money));
                    $row->column(3, new InfoBox('待发货', 'book', 'yellow', '/admin/productform',$send_to ));
                })
                ->row(function (Row $row) {
                    $row->column(5, function (Column $column) {
                        $column->row(new Box('本月成交订单', view('Admin.chartjs')));
//                    $column->row(new InfoBox('New Users', 'users', 'aqua', '/demo/users', '1024'));
                    });
                    $row->column(5, function (Column $column) {
                        $column->row(new Box('本月收入', view('Admin.incomeChartjs')));
                    });
                });
        }else{
            $memo = Store::where('admin_id' , $store)->value('memo');
            return $content
                ->header('消息')
                ->withError('你已被关入小黑屋', '原因:'.$memo);
        }

    }


    public function countOrderNumByStore()
    {
        $data = [];
        $store_id = Auth::guard('admin')->user()->id;
        $y_m = date('Ym',time());
        $firstday = date('Y-m-01',time());
        $start = strtotime($firstday);//本月开始的时间戳
        $end = strtotime("$firstday +1 month -1 day");//本月结束的时间戳
        $lastday = date('d' , $end);
        //一个月的天数
        $month_date = range(1,$lastday);
        //获取传过来的状态
        $type = request('type');


        if($type==1){//一个月每天的订单数
            $monthAllData = ProductForm::select(DB::raw("FROM_UNIXTIME(created_at,'%Y%m%d') as date, count(*) as num"))
                ->where('store_id' , $store_id)->groupBy("date")->get();
        }else if($type == 2){
            //统计收入 只有再买家确认收货或才算是商家的收入
            $monthAllData = ProductForm::select(DB::raw("FROM_UNIXTIME(updated_at,'%Y%m%d') as date, sum(product_cost) as num"))
                ->where('store_id' , $store_id)
                ->whereIn('status' , [ProductForm::SIGN_FOR_GOOD , ProductForm::READY_GOOG])
                ->groupBy("date")->get();
        }
        $res = [];
        for($i=1 ; $i<= $lastday ; $i++){
            $res[] = 0;
            foreach ($monthAllData as $value){
                if(substr($value->date,0,6) == $y_m){
                    $day = substr($value->date,-2);
                    if($i == $day) {
                        $res[$i] = $value->num;
                    }
                }
            }
        }
        $data['count_num'] = $res;
        $data['month_date'] = $month_date;

        return $data;
    }


}
