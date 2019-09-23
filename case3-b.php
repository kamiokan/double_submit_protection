<?php
// エラーを強制表示する
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', "On");
require_once './config.php';
require_once './lib/h.php';
require_once './lib/connection.php';
header('X-FRAME-OPTIONS: SAMEORIGIN');
session_cache_limiter('private_no_expire');
session_start();

// 完了トークンが入っていたら、登録完了画面からのブラウザバックなので、入力画面へ飛ばす
if (isset($_SESSION['token']['complete'])) {
    //入力画面に戻る
    header('Location: case3-a.php');
    exit();
}

// 確認トークン発行
$_SESSION['token']['confirm'] = bin2hex(random_bytes(32));
$token_confirm = $_SESSION['token']['confirm'];
$name = h($_POST['name']);
$body = h($_POST['body']);
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
<h1>確認画面 - 二重サブミットのテスト</h1>
<h2>トークンを使って二重サブミット防止</h2>
<h3>jQueryでボタンクリック時に非活性化</h3>
<h3>POST送信後にリダイレクト（PRGパターン）</h3>
<hr>
<form id="yourFormId" method="post" action="case3-c.php">
    <div>
        TOKEN ※本来は type="hidden"<br>
        <input type="text" name="token_confirm" value="<?= $token_confirm ?>" style="width:600px;">
    </div>
    <div>
        NAME<br>
        <input type="text" name="name" value="<?= $name ?>">
    </div>
    <div>
        BODY<br>
        <textarea name="body"><?= $body ?></textarea>
    </div>
    <div>
        <input id="submit-btn" type="submit" value="送信する">
    </div>
</form>

<h2>Sessions</h2>
<?php
var_export($_SESSION);
?>

</body>
</html>
