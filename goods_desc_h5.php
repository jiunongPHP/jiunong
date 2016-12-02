<?php


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;

if($goods_id == 0){
    exit("error,id = 0!");
}

$res = $db->getOne("SELECT goods_desc FROM " .$ecs->table('goods'). " WHERE goods_id=" . $goods_id ." LIMIT 1");

if(empty($res)){
    exit("失败,没有找到商品或没有商品详情！");
}

echo $res;
        