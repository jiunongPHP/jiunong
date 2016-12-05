<?php

/* 
 * 接收微信支付回调
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

//捕获输入信息
//$request = Request::capture();

$log_id = DB::table('wxpay_alipay_log')->where('log_id','1')
            ->update(
                    [   
                        'wx_return_code'       => 123,
                        'return_json_data'     => json_encode($_POST),
                        'return_time'       => time(),
                    ]
                );
        