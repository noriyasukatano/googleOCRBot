<?php
$hostname = "mysql121.phy.lolipop.lan";
$uname = "LAA0843755";
$upass = "ueueuemoN99";
$dbname = "LAA0843755-xperiment";
$dsn = "mysql:dbname=".$dbname.";host=".$hostname.";charset=utf8";

try {
$pdo = new PDO($dsn,$uname,$upass, array(PDO::ATTR_EMULATE_PREPARES => false));
$errer_ms="接続できたよ";
} catch (PDOException $e) {
 exit('データベース接続失敗。'.$e->getMessage());
 echo $e;
}

 ?>

<!DOCTYPE html>
<html lang = "ja">
<title>input test</title>
</head>
<body>
<p><?php echo $errer_ms ?></p>
<form method = "POST" action = "mysql_input_test.php">
<p>ID:<input type = "text" name = "id" size = "3" maxlength = "100"></p>
<p>文言:<input type = "text" name = "text" size = "10" maxlength = "10"></p>
<p><button name="submit" value="submit">登録</button></p>
<hr>
<p><button name="deleat" value="deleat">削除</button></p>
<?php


// PDO形式でMySQLに接続
$stmt = $pdo->query("select ID, discription from testMySQL");
foreach ($stmt as $row) {
    echo '<input type="checkbox" name="checkbox[]" value="'.$row['ID'].'">';
    echo $row['ID'].' : '.$row['discription'];
    echo '<br>';
};


//削除
if($_POST['deleat']) {
  $stmt = $pdo->prepare("DELETE FROM testMySQL WHERE ID = :ID");
  foreach($_POST['checkbox'] as $delid){
    $params = array(':ID'=> $delid);
    $stmt->execute($params);
  };
};

//新規登録
if($_POST['submit']) {
$stmt = $pdo->prepare("INSERT INTO testMySQL(ID, discription) VALUES(:ID, :discription)");
$params = array(':ID' => $_POST['id'], ':discription' => $_POST['text']);
$stmt->execute($params);
header("Location: {$_SERVER['PHP_SELF']}");
};
?>
</form>
</body>
</html>
