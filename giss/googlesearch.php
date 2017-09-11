<!DOCTYPE html>
<html lang = "ja">
<head>
<meta charset = "UFT-8">
<link rel="stylesheet" type="text/css" href="style.php">
<title>フォームからデータを受け取る</title>
</head>
<body>

<?php
echo '<form method="post" action = "googlesearch.php">';
echo '<dl>';
echo '<dt class="text3em">検索</dt><dd><input type="text" name="SEARCH_TEXT" value="'.$search_text.'"></dd>';
echo '<dt class="text5em">開始位置</dt><dd><input type="text" name="PAGE_NUM" value="'.$startNum.'" size="4"></dd></dl>';
echo '<button type="submit">検索</button>';
echo '</form>';

if(@$_POST['SEARCH_TEXT']){
  $search_text = $_POST['SEARCH_TEXT'];
  $startNum = $_POST['PAGE_NUM'];
};
echo '<dl><dt class="text8em">検索キーワード</dt><dd>'.$search_text.'</dd>';
echo '<dt class="text8em">検索キーワード</dt><dd>'.$startNum.'</dd></dl>';
?>

<hr>

<table>
  <col width="3%">
  <col width="20%">
  <col width="18%">
  <col width="10%">
  <col width="14%">
  <col width="35%">
<thead>
  <tr>
    <th>No.</th>
    <th>画像</th>
    <th>画像情報</th>
    <th>画像解析</th>
    <th>セキュリティ</th>
    <th>画像内文字</th>
  </tr>
</thead>
<tbody>
<?php
require "./googleImageAna.php";
require "./config.php";

//------------------------------------
// google画像検索と画像解析を使った画像判定出力シスてく管理画面
//------------------------------------

$apiKey = "AIzaSyDF-psjMfN99x5R_GDuHzctwd9UNOXZCRM";
$searchEngineId = "006410985409460812946:5a_v8bv4ibg";
$baseUrl = "https://www.googleapis.com/customsearch/v1?";


if(isset($_GET[‘comment’])){
$comment = $_GET[‘comment’];
echo $comment;
}


$query = $lineText."$search_text";

//------------------------------------
// リクエストパラメータ生成
//------------------------------------
$paramAry = array(
                'q' => $query,
                'key' => $apiKey,
                'searchType' => 'image',
                //'imgSize' => 'medium',
                'imgDominantColor' => 'black',
                'cx' => $searchEngineId,
                'alt' => 'json',
                'start' => $startNum
        );
$param = http_build_query($paramAry);

//------------------------------------
// 実行＆結果取得
//------------------------------------
$reqUrl = $baseUrl . $param;
$retJson = file_get_contents($reqUrl, true);
$ret = json_decode($retJson, true);

$getNum = 0;
//---google 画像解析パラメーター
$googleAnaType = array(
  'cat' => 'LABEL_DETECTION',
  'safe' => 'SAFE_SEARCH_DETECTION',
  'ocr' => 'TEXT_DETECTION'
                );
$gAna = new googleAna();

for ($i=0; $i<10; $i++){

//画像URLの取得
$get_url = $ret["items"][$i]["link"];
echo '<tr>';
echo '<td>'.$startNum.'</td>';
echo '<td><img src="'.$get_url.'" width="200"></td>';

//画像スペックの取得
echo '<td><dl>';
echo '<dt class="text4em">格納先</dt><dd class="urlText"><a href="'.$ret["items"][$i]["image"]["contextLink"].'" target="_blank">'.$ret["items"][$i]["image"]["contextLink"].'</a></dd>';
echo '<dt class="text4em">高さ</dt><dd>'.$ret["items"][$i]["image"]["height"].'</dd>';
echo '<dt class="text4em">幅</dt><dd>'.$ret["items"][$i]["image"]["width"].'</dd>';
echo '<dt class="text4em">容量</dt><dd>'.$ret["items"][$i]["image"]["byteSize"].'</dd>';
echo '</dl></td>';

//画像解析：画像概要
$getNum = 10;
echo '<td><ul>';
$ana_img_array = $gAna->googlrImageAna($config['key'], $get_url, $googleAnaType['cat'],$getNum);
$y = 0;
while($y <= 9){
echo '<li>'.$ana_img_array["responses"][0]["labelAnnotations"][$y]["description"].'</li>';
$y++;
};
echo '</ul></td>';

//画像解析：有害コンテンツ解析
echo '<td><dl>';
$getNum = 3;
$ana_safe_array = $gAna->googlrImageAna($config['key'], $get_url, $googleAnaType['safe'],$getNum);
$ana_safe_res =array($ana_safe_array["responses"][0]["safeSearchAnnotation"]["adult"],$ana_safe_array["responses"][0]["safeSearchAnnotation"]["spoof"],$ana_safe_array["responses"][0]["safeSearchAnnotation"]["medical"],$ana_safe_array["responses"][0]["safeSearchAnnotation"]["violence"]
);
$ana_safe_res_text=array();
for($x=0; $x<4; $x++){
switch ($ana_safe_res[$x]) {
    case "VERY_LIKELY":
        $ana_safe_res_text[] = "非常に高いレベル";
        break;
    case "LIKELY":
        $ana_safe_res_text[] = "高いレベル";
        break;
    case "POSSIBLE":
        $ana_safe_res_text[] = "そうだと言えるレベル";
        break;
    case "UNLIKELY":
        $ana_safe_res_text[] = "低いレベル";
        break;
    case "VERY_UNLIKELY":
        $ana_safe_res_text[] = "非常に低いレベル";
        break;
    case "NKNOWN":
        $ana_safe_res_text[] = "判定不能";
        break;
};
};

echo '<dt>アダルト度</dt><dd>'.$ana_safe_res_text[0].'</dd>';
echo '<dt>画像加工度</dt><dd>'.$ana_safe_res_text[1].'</dd>';
echo '<dt>グロ度</dt><dd>'.$ana_safe_res_text[2].'</dd>';
echo '<dt>暴力度</dt><dd>'.$ana_safe_res_text[3].'</dd>';
echo '</dl></td>';

//画像解析：テキストの抽出
$getNum=1;
$ana_ocr_array = $gAna->googlrImageAna($config['key'], $get_url, $googleAnaType['ocr'],$getNum);

echo '<td><dl">';
echo '<dt class="text3em">言語</dt><dd>'.$ana_ocr_array["responses"][0]["textAnnotations"][0]["locale"].'</dd>';
echo '<dt class="text3em">文言</dt><dd>'.$ana_ocr_array["responses"][0]["textAnnotations"][0]["description"].'</dd>';
echo '</dl></td>';

$startNum++;
};
?>
</tr>
</tbody>
</table>
</body>
</html>
