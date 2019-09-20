<?php
// エラーを強制表示する
error_reporting(E_ALL & ~ E_DEPRECATED & ~ E_USER_DEPRECATED & ~ E_NOTICE);
ini_set('display_errors', "On");
require_once './config.php';
require_once './lib/h.php';
require_once './lib/connection.php';
header('X-FRAME-OPTIONS: SAMEORIGIN');
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = h($_POST['name']);
    $body = h($_POST['body']);

    // わざとダブルサブミットを引き起こすために時間をおく
    sleep(3);

    try {
        $db = connect($dsn);
        $sql = 'INSERT INTO users (name, body) VALUES (:name, :body);';
        $prepare = $db->prepare($sql);
        $prepare->bindValue('name', $name, PDO::PARAM_STR);
        $prepare->bindValue('body', $body, PDO::PARAM_STR);
        $prepare->execute();
    } catch (PDOException $e) {
        echo "接続できませんでした 原因： " . h($e->getMessage());
        exit();
    }
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
</head>
<body>
<h1>二重サブミットのテスト</h1>
<hr>
<form method="post" action="<?= h($_SERVER['SCRIPT_NAME']); ?>">
    <div>
        NAME<br>
        <input type="text" name="name" value="">
    </div>
    <div>
        BODY<br>
        <textarea name="body"></textarea>
    </div>
    <div>
        <input type="submit" value="送信する">
    </div>
</form>

<h2>User Records</h2>
<?php
if (isset($result) && $result != []){
    foreach ($result as $r) {
        echo "{$r['name']} - {$r['body']}<br>";
    }
}
?>
</body>
</html>
