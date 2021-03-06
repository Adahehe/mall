<?php

namespace App\Api\Controllers;

use App\Model\Integral;
use App\Model\Product;
use App\Model\ProductForm;
use App\Model\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController
{
   public function saveOrders()
   {
       $order = [];
       $product = [];

       $user_id = \request('user_id');
       $form_address_id = \request('address_id');
       $cart_id = \request('shopping_cart');
       $integral = \request('integral');
       $memo = \request('memo');

       //如果地址为空 不能提交订单 返回原来页面
//       if(!$form_address_id){
//           return back();
//       }

       //如果是在购物车里购买
       if($cart_id[0]){
           foreach ($cart_id as $value){
               $product[] = ShoppingCart::where('shopping_cart_id' , $value)->first()->toArray();
           }
            //涉及到的商店
           $store_arr = array_unique(array_column($product,'store_id'));
           $temp = [];
           foreach ($store_arr as $value){
               $temp['store_id'] = $value;
               $pro_temp = [];
               $num_temp = [];
               $product_cost = 0;
               $form_freght = 0;
                   foreach ($product as $val){
                       $eachProduct = Product::where('product_id',$val['product_id'])->first()->toArray();
                       if($val['store_id'] == $value){
                           $pro_temp[] = $val['product_id'];//商品id
                           $num_temp[] = $val['num'];//商品数量
                           $product_cost += $eachProduct['present_price']*$val['num'];
                           $form_freght += $eachProduct['product_freght'];
                       }
                       //改变商品的销量 和 库存
                       Product::where('product_id', $eachProduct['product_id'])
                           ->update([
                               'sales_volume' => $eachProduct['sales_volume'] + $val['num'],
                               'product_num' => $eachProduct['product_num'] - $val['num'],
                           ]);
                   }
               $temp['user_id'] = $user_id;
               $temp['product_id'] = json_encode($pro_temp);
               $temp['num'] = json_encode($num_temp);
               $temp['form_num'] = '2019'.time().$value;
               $temp['product_cost'] = $product_cost;
               $temp['form_freght'] = $form_freght;
               $temp['form_cost'] = $product_cost+$form_freght;
               $temp['form_address_id'] = $form_address_id;
               //todo 订单提交的状态
    //           $temp['status'] = ProductForm::PLACE_ORDER;
               $temp['status'] = ProductForm::WAIT_DELIVER_GOODS;
               $temp['pay_type'] = ProductForm::PAY_ON_LINE;

               //存入数据库
               DB::beginTransaction();
               $productForm = new ProductForm();
               $productForm->user_id = $temp['user_id'];
               $productForm->product_id = $temp['product_id'];
               $productForm->num = $temp['num'];
               $productForm->form_num = $temp['form_num'];
               $productForm->product_cost = $temp['product_cost'];
               $productForm->form_freght = $temp['form_freght'];
               $productForm->form_cost = $temp['form_cost'];
               $productForm->form_address_id = $temp['form_address_id'];
               $productForm->status = $temp['status'];
               $productForm->store_id = $temp['store_id'];
               $productForm->pay_type = $temp['pay_type'];
               $productForm->memo = $memo;
               $productForm->pay_time = time();
               $productForm->save();
               DB::commit();
           }
       }else{
           //如果是直接购买
           $pro = \request('pro');
           $pro_temp = [];
           $num_temp = [];
           $eachProduct = Product::where('product_id',$pro)->first()->toArray();
           $pro_temp[] = (int)$pro;
           $num_temp[] = 1;
           $product_cost = $eachProduct['present_price'];
           $form_freght = $eachProduct['product_freght'];
           //改变销量 和 库存
           Product::where('product_id', $eachProduct['product_id'])
               ->update([
                   'sales_volume' => $eachProduct['sales_volume'] + 1,
                   'product_num' => $eachProduct['product_num'] - 1,
               ]);
           //存入数据库
           DB::beginTransaction();
           $productForm = new ProductForm();
           $productForm->user_id = $user_id;
           $productForm->product_id = json_encode($pro_temp);
           $productForm->num = json_encode($num_temp);
           $productForm->form_num = '2019'.time().$eachProduct['store_id'];
           $productForm->product_cost = $product_cost;
           $productForm->form_freght = $form_freght;
           $productForm->form_cost = $product_cost+$form_freght;
           $productForm->form_address_id = $form_address_id;
           $productForm->status = ProductForm::WAIT_DELIVER_GOODS;
           $productForm->store_id = $eachProduct['store_id'];
           $productForm->pay_type = ProductForm::PAY_ON_LINE;
           $productForm->memo = $memo;
           $productForm->pay_time = time();
           $productForm->save();
           DB::commit();
       }

       //写入积分表
       $created_integral = new Integral;
       $created_integral->user_id = $user_id;
       $created_integral->integral = $integral;
       $created_integral->product_form_id = $productForm->form_id;
       $created_integral->save();

       //更改商品表（已做）

       return $productForm;
   }

    /**
     * 更改状态为  待发货状态
     * @param Request $request
     */
    public function updateFormStatus(Request $request)
    {
        $form_id = $request->get('id');
        $order = ProductForm::find($form_id);
        $order->status = ProductForm::WAIT_DELIVER_GOODS;
        $order->save();
    }
}
