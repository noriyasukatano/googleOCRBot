<?php
function amazonURLmaker($titleName){
$aws_access_key_id = 'AKIAIHEIKBRQKCZVWPTA';
$aws_secret_key = '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';
$AssociateTag='submarinenet-22';

//URL生成
$endpoint = 'webservices.amazon.co.jp';
$uri = '/onca/xml';

	//パラメータ群
	$params = array(
		'Service' => 'AWSECommerceService',
		'Operation' => 'ItemSearch',
		'AWSAccessKeyId' => $aws_access_key_id,
		'AssociateTag' => $AssociateTag,
		'SearchIndex' => 'Books',
		'ResponseGroup' => 'Medium',
		'Keywords' => $titleName,
		'ItemPage' => 1
	);

  //timestamp
	if (!isset($params['Timestamp'])) {
		$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
	}

  //パラメータをソート
	ksort($params);

	$pairs = array();
	foreach ($params as $key => $value) {
		array_push($pairs, rawurlencode($key).'='.rawurlencode($value));
	}

  //リクエストURLを生成
	$canonical_query_string = join('&', $pairs);
	$string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;
	$signature = base64_encode(hash_hmac('sha256', $string_to_sign, $aws_secret_key, true));
	$request_url = 'http://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);

	$amazon_xml=simplexml_load_string(@file_get_contents($request_url));//@はエラー回避

//検索結果からURLを生成
$arryURL = array(); //URL格納配列

	foreach((object)$amazon_xml->Items->Item as $item_a=>$item){
		$detailURL=$item->DetailPageURL;//商品のURL
    $CutdetailURL = explode("/", $detailURL); //文字列をスラッシュで分解
    $CutdetailURLCount =count($CutdetailURL);
    $codedetailURL = explode("&", $CutdetailURL[5]);
    $bookNum = explode("=", $codedetailURL[5]);

    $newURL = "https://www.amazon.co.jp/gp/product/".$bookNum[1]."?ie=UTF8&".$codedetailURL[3]."&".$codedetailURL[4]."&".$codedetailURL[5]."&linkCode=shr&tag=submarinenet-22";

    //配列に新しURLを格納
    array_push($arryURL, $newURL);

		print PHP_EOL;
	}

	//1秒おく
	sleep(1);
  $y = mt_rand(1, count($arryURL));
  $mainURL = $arryURL[$y];

  return $mainURL;
}
?>
