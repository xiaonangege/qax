<?php
require_once '../../inc/global.php';

$name = addslashes($_POST['name']);
if (!$name) {
    ajaxCallback(0, '缺少参数');
}
$db = Db::init('database');
$sql = "SELECT * FROM `light_user` where `name` = '{$name}' limit 1";
$user_info = $db->FetchRow($sql);
if (!$user_info) {
    ajaxCallback(0, '没有该老师');
}
ajaxCallback(1, '成功', ['user_info' => $user_info]);