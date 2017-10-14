<?php
/**
 * 
 * Получение json по ссылке
 * @param $request_url
 */
function get_url($request_url) {
	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $request_url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 0);
	curl_setopt($curl_handle, CURLOPT_TIMEOUT, 0);
	curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
	$JsonResponse = curl_exec($curl_handle);
	$http_code = curl_getinfo($curl_handle);
	return($JsonResponse);
}

/**
 * 
 * Получение chat_id в случает работы без вебхука
 */
function getChatId(){
	$varGetUpd = "https://api.telegram.org/bot".TOKEN."/getUpdates";
	$text = get_url($varGetUpd);
	$phpArr = json_decode($text, true);
	return $phpArr["result"][0]["message"]["chat"]["id"];
}
?>