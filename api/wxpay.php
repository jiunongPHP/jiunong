<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define('IN_ECS', true);

// 载入初始化文件
include __DIR__ . '/aip_init.php';

//数据库ORM
use Illuminate\Database\Capsule\Manager as DB;
//输入过滤器
use Illuminate\Http\Request;

class wxpay_api{
    //微信支付基础类实例化用
    private $wxpay_instance;
    //返回信息格式组
    private $return_infos = array('errcode'=>'','message'=>'','data'=>array());
            
    function __construct() {
        require_once 'wxpay_base.class.php';
        //商户号，appid，api安全key
        $this->wxpay_instance = new WxpayService('1416615602', 'wx18cbe67e51c13f42', '123jiunongwenhuafazhanbeijingyou');

    }
    
    function return_a_error_info($errcode,$message){
        $this->return_infos['errcode'] = $errcode;
        $this->return_infos['message'] = $message;
        $this->return_infos['data'] = array();
        
        $this->response($this->return_infos);
    }
    
    /**
     * 微信支付统一下单接口
     * @param Request
     */
    function create_wxpay_order(Request $request){
        $order_id  = $request->input('order_id') ? intval($request->input('order_id')) : 0;
        $user_id = $request->input('user_id') ? intval($request->input('user_id')) : 0;
        
        if($order_id == 0){  $this->return_a_error_info('2001', '订单id错误！');}
        if($user_id == 0){  $this->return_a_error_info('2002', '用户id错误！');}
        
        $order_info = DB::table('order_info')
                ->select('order_sn','order_amount')
                ->where('order_id',$order_id)
                ->where('user_id',$user_id)
                ->where('order_status','>','-1')
                ->first();
        if(count($order_info) < 1){ return $this->return_a_error_info('2003', '没有找到订单，请核对信息！');}
        
        //发送订单回调url
        $notify_url = 'https://www.jiunonghuabao.com/api/recive_wxpay_return.php';
        //发送到微信时间
        $send_time = time();
        
        //向微信支付发送订单
        $wxpay_return_info = $this->wxpay_instance->createJsBizPackage($order_info->order_amount, $order_info->order_sn, $order_info->order_sn, '$notify_url', $send_time);
        
        //发送数据日志用
        $send_datas['order_mount'] = $order_info->order_amount;
        $send_datas['order_sn'] = $order_info->order_sn;
        $send_datas['order_name'] = $order_info->order_sn;
        
        //add log
        $return = $this->add_log(__FUNCTION__,$send_datas, $send_time, $wxpay_return_info);
        if(!$return){ $this->return_a_error_info('2004', 'add log fail!');}
        
        $this->response(array('errcode'=>200,'message'=>'向微信支付生成预支付订单成功','data'=>$wxpay_return_info));
     }
     
     /**
      * 添加到发送日志
      * @param type 方法名
      * @param type 发送数据数组
      * @param type 发送时间
      * @param type 返回值数据数组
      * @return bool 写日志是否成功
      */
     private function add_log($function_name,$send_datas,$send_time,$return_datas){
        $send_json_datas = json_encode($send_datas);
        $return_json_datas = json_encode($return_datas);
        
        $log_id = DB::table('wxpay_alipay_log')->insertGetId(
                    [   
                        'pay_type' => '1',
                        'function_name'      => $function_name,
                        'send_json_data'       => $send_json_datas,
                        'return_json_data'     => $return_json_datas,
                        'send_time'       => $send_time,
                        'return_time'       => time(),
                    ]
                );
         return $log_id > 0 ? true : false;
     }


     /*
     * 输出函数
     */
    public function response($res = ['errcode' => '2001', 'message' => 'action not found', 'data' => []]){
        header('Content-type:text/json');
        die(json_encode($res));
    }
 }
 
$wxpay_instance = new wxpay_api();
//捕获输入信息
$request = Request::capture();
//参数路由
$act = $request->input('act');

switch ($act){
    case 'create_wxpay_order':
        $wxpay_instance->create_wxpay_order($request);
        break;
    default:
        $Order->response();
        break;
}
 

 
