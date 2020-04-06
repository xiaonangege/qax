<?php
/**
 * Created by PhpStorm.
 * User: 23168
 * Date: 2018/5/16
 * Time: 15:25
 */

/**
 * Created by PhpStorm.
 * User: 13248
 * Date: 2018/5/1
 * Time: 12:07
 */

/**
 * json格式化输出
 *
 * @param int 状态码 成功为0，失败为其他数字，一般是-1
 * @param string 信息，失败时包含说明
 * @param array 数据，成功是返回的数据
 * @return none
 */
function ajaxCallback($status, $message = '', $data = array()) {
	$return = array(
		'status' => strval($status),//状态 成功返回0 失败返回其他数字
		'message' => strval($message),//信息，失败时包含说明
		'data' => $data,//数据，成功时返回的数据
	);
	#header('Content-type: text/html; charset=utf-8');
	header('Content-type: application/json');
	echo json_encode($return);
	exit;
}

/**
 * 模拟http请求
 *
 * @param string 链接
 * @param array 头参数
 * @param array post参数
 * @return string
 */
function httpRequest($url, $header = array(), $post_data = array())
{
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_TIMEOUT => 10,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_AUTOREFERER => 1,
		CURLOPT_USERAGENT => 'Mozilla/5.0 AppleWebKit/537.36 Chrome/58.0.3029.81 Safari/537.36',
	);
	if ($header) {
		$options[CURLOPT_HTTPHEADER] = $header;
	}
	if ($post_data) {
		$options[CURLOPT_POST] = 1;
		$options[CURLOPT_POSTFIELDS] = http_build_query($post_data);
	}
	if (substr($url, 0, 5) == 'https') {
		$options[CURLOPT_SSL_VERIFYHOST] = 1;
		$options[CURLOPT_SSL_VERIFYPEER] = 0;
	}
	$ch = curl_init();
	curl_setopt_array($ch, $options);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}