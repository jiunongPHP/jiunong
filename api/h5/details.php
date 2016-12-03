<?php

require(dirname(__FILE__) . '/../aip_init.php');

use Illuminate\Database\Capsule\Manager as DB;

$smarty = new Smarty;

$mod = $_GET['mod'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

switch($mod){
    case 'activity':
        $data = activity($id);
        $view = '';
        break;
    case 'inviteRegister':
        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
        $view = 'yqzc.html';
        $data = inviteRegister($uid);
}


$smarty->assign('data', $data); //详细信息
$smarty->display($view);


/*-----------------函数----------------*/

/**
 *  活动详情H5
 */
function activity($id)
{
    $oneInfo = [];
    //获取数据
    return $oneInfo;
}

/*
 * 邀请注册
 */
function inviteRegister($uid){
    $userInfo = DB::table('users')->where('user_id',$uid)->first();
    return $userInfo;
}