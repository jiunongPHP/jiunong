<?php
//微信支付测试用
require_once './wxpay_base.class.php';

$wxpay_test_instance = new WxpayService('1416615602', 'wx18cbe67e51c13f42', '123jiunongwenhuafazhanbeijingyou');

$return = $wxpay_test_instance ->createJsBizPackage(1, 'jn12345', '酒侬红酒测试', 'http://jiunong.com/123.php', time());
var_dump($return);