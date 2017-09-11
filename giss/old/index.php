<!DOCTYPE html>
<html lang = “ja”>
<head>
  <meta charset = “UFT-8”>
  <title>Class テスト</title>
  </head>
<body>
  <h1>Class テスト</h1>

  <?php
  require "./classtest.php";
  require "./config.php";

  $classTestContet = new tidy_get_status();
  $text = $classTestContet->getContent();
  echo '<p>これはテストです</p>';
  echo '<p>'.$text.'</p>';

  echo '<p>'.$config['key'].'</p>';

$urlTest = "、そんなバカな。";
  $testConf = $classTestContet->getConfig($config['url'], $urlTest);
  echo '<p>'.$testConf.'</p>';
   ?>

 </body>
 </html>
