<?php
// エラーを強制表示する
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', "On");
require_once './config.php';
require_once './lib/h.php';
require_once './lib/connection.php';
header('X-FRAME-OPTIONS: SAMEORIGIN');
session_start();
// 登録完了トークンをリセットする
unset($_SESSION['token']['complete']);

try {
    $db = connect($dsn);
    $sql = 'SELECT id, name, body FROM users';
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "接続できませんでした 原因： " . h($e->getMessage());
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="">
    <link rel="shortcut icon" href="">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $("#yourFormId").submit(function () {
                $("#submit-btn").attr("disabled", true);
                return true;
            });
        });
    </script>
</head>
<body>
<h1>二重サブミットのテスト</h1>
<h2>トークンを使って二重サブミット防止</h2>
<h3>jQueryでボタンクリック時に非活性化</h3>
<h3>POST送信後にリダイレクト（PRGパターン）</h3>
<hr>
<form id="yourFormId" method="post" action="case3-b.php">
    <div>
        NAME<br>
        <input type="text" name="name" value="">
    </div>
    <div>
        BODY<br>
        <textarea name="body"></textarea>
    </div>
    <div>
        <input id="submit-btn" type="submit" value="確認する">
    </div>
</form>

<h2>Sessions</h2>
<?php
var_export($_SESSION);
?>

<h2>User Records</h2>
<?php
if (isset($result) && $result != []) {
    foreach ($result as $r) {
        echo "{$r['name']} - {$r['body']}<br>";
    }
}
?>
</body>
</html>
