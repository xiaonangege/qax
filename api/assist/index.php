<?php
require_once '../../inc/global.php';

$name = addslashes($_POST['name']);
if (!$name) {
    ajaxCallback(0, '缺少参数');
}
$db = Db::init('database');
$sql = "SELECT `id`,`assist` FROM `light_user` where `name` = '{$name}' limit 1";
$user_info = $db->FetchRow($sql);
if (!$user_info) {
    ajaxCallback(0, '没有该老师');
}
$assist = (int)$user_info['assist'] + 1;
$data = [
    'assist' => $assist
];
$cond = "`id` = {$user_info['id']}";
$result = $db->update('light_user',$data,$cond);
if ($result) {
    ajaxCallback(1, '成功');
}
ajaxCallback(0, '失败');