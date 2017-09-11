<?php
require "./config.php";

// OAuthライブラリの読み込み
require '../twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;


//Twitterに接続
$connection = new TwitterOAuth($twitterkey['consumerKey'], $twitterkey['consumerSecret'], $twitterkey['accessToken'], $twitterkey['accessTokenSecret']);

//MySQLに接続しデータベースからデータを取得
$dsn = "mysql:dbname=".$mysqlkey['dbname'].";host=".$mysqlkey['hostname'].";charset=utf8";

try {
$pdo = new PDO($dsn,$mysqlkey['uname'],$mysqlkey['upass'], array(PDO::ATTR_EMULATE_PREPARES => false));
$e="接続できたよ";
} catch (PDOException $e) {
 exit('データベース接続失敗。'.$e->getMessage());
};
echo '<p>'.$e.'</p>';

$stmt = $pdo->query("SELECT * FROM test02MySQL");
//$stmt->execute();
//$count=$stmt->rowCount();

$randumId = array();
foreach ($stmt as $row) {
  array_push($randumId, $row['id']);
};
$count = count($randumId);
$i = mt_rand(1, $count);

var_dump($randumId);
echo '<p>表示:'.$i.'</p>';
echo '<p>ID:'.$randumId[$i].'</p>';

$stmt = $pdo->query("SELECT * FROM test02MySQL WHERE id=" .$randumId[$i] );

foreach ($stmt as $row) {
  $message_txt = $row['comicText'];
  $message_title = $row['bookTitle'];
  $url_img = $row['imageURL'];
};

//改行削除
$message_txt = str_replace("\r\n", '', $message_txt);
var_dump($message_txt);
echo '<br>';

$message = $message_txt." #".$message_title." #コミック名台詞 ";
$media1 = $connection->upload('media/upload', ['media' => $url_img]);

// ツイートするためのパラメータをセット
  $parameters = [
    'status' => $message,
    'media_ids' => implode(',', [
        $media1->media_id_string,
      ])
  ];
//ツイート
$res = $connection->post("statuses/update", $parameters);

//レスポンス確認
var_dump($res);
?>
