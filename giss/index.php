<!DOCTYPE html>
<html lang = "ja">
<head>
<meta charset = "UFT-8">
<link rel="stylesheet" type="text/css" href="style.php">
<title>コミック名言検索</title>
</head>
<body>

<h1>コミック名言検索</h1>

<form method="post" action = "index.php">
<dl>
<dt class="text4em">作品名</dt><dd><input type="text" name="TITLE_TEXT" value="" size="30"></dd>
<dt class="text8em">キャラクター名</dt><dd><input type="text" name="CHARA_TEXT" value="" size="30"></dd>
<dt class="text6em">開始位置</dt><dd><input type="text" name="PAGE_NUM" value="1" size="4"></dd>
</dl>
<button name='submit' value="submit">検索</button>
</form>
<hr>

<?php
require "./config.php";
require "./googleImageAna.php";
require "./amazon_make_url.php";

$booktile = $_POST['TITLE_TEXT'];
$item_url = amazonURLmaker($booktile);
echo '<dl>';
echo '<dt class="text4em">作品名</dt><dd class="text4em">'.$booktile.'</dd>';
echo '<dt class="text8em">キャラクター名</dt><dd class="text8em">'.$_POST['CHARA_TEXT'].'</dd>';
echo '<dt class="text6em">開始位置</dt><dd class="text6em">'.$_POST['PAGE_NUM'].'</dd>';
echo '<dt class="text6em">商品URL</dt><dd class="text6em">'.$item_url.'</dd>';
echo '</dl>';
?>

<form method="post" action = "index.php">
  <?php echo '<input type="hidden" name="booktitle" value="'.$booktile.'">'; ?>
<table>
  <col width="1%">
  <col width="1%">
  <col width="15%">
  <col width="18%">
  <col width="10%">
  <col width="20%">
  <col width="35%">
<thead>
  <tr>
    <th>check</th>
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
if(@$_POST['TITLE_TEXT']){
  $search_text = $_POST['TITLE_TEXT'].' 名言 '.$_POST['CHARA_TEXT'];
  $startNum = $_POST['PAGE_NUM'];
};

//------------------------------------
// google画像検索と画像解析を使った画像判定出力シスてく管理画面
//------------------------------------
$apiKey = $googleKey['apikey'];
$searchEngineId = $googleKey['searchid'];
$baseUrl = $googleKey['baseUrl'];

if(isset($_GET[‘comment’])){
$comment = $_GET[‘comment’];
echo $comment;
};

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

$gAna = new googleAna();


for ($i=0; $i<10; $i++){
//画像URLの取得
$get_url = $ret["items"][$i]["link"];

echo '<tr>';
echo '<td><input type="checkbox" name="checkbox[]" value="'.$i.'" id="lavel'.$i.'"></td>';
echo '<td>'.$i.'</td>';
echo '<td><lavel for="lavel'.$i.'"><input type="hidden" name="check_img'.$i.'" value="'.$get_url.'"><img src="'.$get_url.'" width="200"></lavel>
</td>';

//画像スペックの取得
echo '<td><dl>';
echo '<dt class="text4em">格納先</dt><dd class="text4em"><a href="'.$ret["items"][$i]["image"]["contextLink"].'" target="_blank">'.$ret["items"][$i]["image"]["contextLink"].'</a></dd>';
echo '<dt class="text3em">高さ</dt><dd class="text3em">'.$ret["items"][$i]["image"]["height"].'</dd>';
echo '<dt class="text3em">幅</dt><dd class="text3em">'.$ret["items"][$i]["image"]["width"].'</dd>';
echo '<dt class="text3em">容量</dt><dd class="text3em">'.$ret["items"][$i]["image"]["byteSize"].'</dd>';
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

echo '<dt class="text6em">アダルト度</dt><dd class="text6em">'.$ana_safe_res_text[0].'</dd>';
echo '<dt class="text6em">画像加工度</dt><dd class="text6em">'.$ana_safe_res_text[1].'</dd>';
echo '<dt class="text6em">グロ度</dt><dd class="text6em">'.$ana_safe_res_text[2].'</dd>';
echo '<dt class="text6em">暴力度</dt><dd class="text6em">'.$ana_safe_res_text[3].'</dd>';
echo '</dl></td>';

//画像解析：テキストの抽出
$getNum=1;
$ana_ocr_array = $gAna->googlrImageAna($config['key'], $get_url, $googleAnaType['ocr'],$getNum);

echo '<td><dl>';
echo '<dt class="text3em">言語</dt><dd class="text3em">'.$ana_ocr_array["responses"][0]["textAnnotations"][0]["locale"].'</dd>';
echo '<dt class="text3em">文言</dt><dd class="text3em"><lavel for="lavel'.$i.'"><input type="hidden" name="check_text'.$i.'" value="'.$ana_ocr_array["responses"][0]["textAnnotations"][0]["description"].'">'.$ana_ocr_array["responses"][0]["textAnnotations"][0]["description"].'</lavel></dd>';
echo '</dl></td></tr>';

};
?>
</tr>
</tbody>
</table>
<hr>
<p><button name='record' value="record">登録</button></p>
</form>


<?php
$dsn = "mysql:dbname=".$mysqlkey['dbname'].";host=".$mysqlkey['hostname'].";charset=utf8";

try {
$pdo = new PDO($dsn,$mysqlkey['uname'],$mysqlkey['upass'], array(PDO::ATTR_EMULATE_PREPARES => false));
$e="接続できたよ";
} catch (PDOException $e) {
 exit('データベース接続失敗。'.$e->getMessage());
};
echo '<p>'.$e.'</p>';

//新規登録
if($_POST['record']) {

foreach($_POST['checkbox'] as $ckeckid){

  $imgURL_form = $_POST['check_img'.$ckeckid];
  $image = file_get_contents($imgURL_form);
  $extension = pathinfo($imgURL_form, PATHINFO_EXTENSION);

  $text_form = $_POST['check_text'.$ckeckid];
  $bookname = $_POST['booktitle'];

  $stmt = $pdo->prepare("INSERT INTO test02MySQL (image, extension, imageURL, comicText, bookTitle) VALUES (:image, :extension, :imageURL, :comicText, :bookTitle)");
  $params = array(':image' => $image, 'extension' => $extension, ':imageURL' => $imgURL_form, ':comicText' => $text_form, ':bookTitle' => $bookname, ':bookURL' =>$item_url);
  $stmt->execute($params);
};
header("Location: {$_SERVER['PHP_SELF']}");
//  var_dump($params);
};

//画像表示
/*
echo '<hr>';
$MIMETypes = array(
   'png'  => 'image/png',
   'jpg'  => 'image/jpeg',
   'jpeg' => 'image/jpeg',
   'gif'  => 'image/gif',
   'bmp'  => 'image/bmp',
);

$stmt = $pdo->query("select image, extension from test02MySQL");
foreach ($stmt as $row) {
      $base64 = base64_encode($row['image']);
      $mime = $MIMETypes[$row['extension']];
      $result = 'data:'.$mime.';base64,'.$base64;

  echo '<img src='.$result.' alt="" />';
  echo '<br>';
};
*/
 ?>

</body>
</html>
