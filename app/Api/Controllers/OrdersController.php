<?php

namespace App\Api\Controllers;

use App\Model\Product;
use App\Model\ProductForm;
use App\Model\ShoppingCart;
use Illuminate\Http\Request;

class OrdersController
{
   public function saveOrders(Request $request)
   {
       $order = [];
       $product = [];

       $user_id = $request->post('user_id');
       $form_address_id = $request->post('address_id');
       $form_freght = $request->post('psf');
       $form_cost = $request->post('sfk');
       $product_cost = $request->post('spzj');
       $cart_id = $request->post('shopping_cart');

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
                  $num_temp[] = $val['num'];//商品id
                  $product_cost += $eachProduct['present_price'];
                  $form_freght += $eachProduct['product_freght'];
              }
           }

           $temp['user_id'] = $user_id;
           $temp['product_id'] = json_encode($pro_temp);
           $temp['num'] = json_encode($num_temp);
           $temp['form_num'] = '2019'.time().$value;
           $temp['product_cost'] = $product_cost;
           $temp['form_freght'] = $form_freght;
           $temp['form_cost'] = $product_cost+$form_freght;
           $temp['form_address_id'] = $form_address_id;
           $temp['status'] = ProductForm::PLACE_ORDER;
           $temp['pay_type'] = ProductForm::PAY_ON_LINE;
//           $order[] = $temp;

           //存入数据库
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
           $productForm->save();
       }

       return '保存订单成功';
   }
}
