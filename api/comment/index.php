<?php
require_once '../../inc/global.php';

$db = Db::init('database');
$sql = "SELECT count(`id`) FROM `light_comment` limit 1";
$total = $db->fetchOne($sql);

$num_list = getMember(1, $total,10);

$list = [];
foreach ($num_list as $v) {
    $sql = "SELECT `id`,`comment` FROM `light_comment` where `id` = {$v} limit 1";
    $data = $db->fetchRow($sql);
    $list[] = $data;
}

ajaxCallback(1,"成功", $list);

function getMember($min, $max, $num)
{
    $count = 0;
    $return = [];
    while ($count < $num) {
        $return[] = mt_rand($min, $max);
        $return = array_flip(array_flip($return));
        $count = count($return);
    }
    shuffle($return);
    return $return;
}

