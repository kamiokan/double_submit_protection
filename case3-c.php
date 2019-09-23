<?php
// エラーを強制表示する
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', "On");
require_once './config.php';
require_once './lib/h.php';
require_once './lib/connection.php';
header('X-FRAME-OPTIONS: SAMEORIGIN');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token_confirm = filter_input(INPUT_POST, 'token_confirm');
    $name = h($_POST['name']);
    $body = h($_POST['body']);

    // 確認トークンが発行されていない、または確認トークンがPOSTされていない
    if (empty($_SESSION['token']['confirm']) || empty($token_confirm)) {
        //入力画面に戻る
        header('Location: case3-a.php');
        exit();
    }

    echo "$token_confirm<br>";
    echo "{$_SESSION['token']['confirm']}<br>";
    echo "$name<br>";
    echo "$body<br>";

    echo "Point1<br>";


    // 確認トークンが一致、完了トークンが発行されていない
    if (hash_equals($_SESSION['token']['confirm'], $token_confirm) && empty($_SESSION['token']['complete'])) {

        echo "Point2<br>";

        // わざとダブルサブミットを引き起こすために時間をおく
        sleep(3);

        echo "Point3<br>";

        try {
            // 処理（DB登録）
            $db = connect($dsn);
            $sql = 'INSERT INTO users (name, body) VALUES (:name, :body);';
            $prepare = $db->prepare($sql);
            $prepare->bindValue('name', $name, PDO::PARAM_STR);
            $prepare->bindValue('body', $body, PDO::PARAM_STR);
            $prepare->execute();

            // 完了トークン発行
            $_SESSION['token']['complete'] = bin2hex(random_bytes(32));
        } catch (PDOException $e) {
            echo "接続できませんでした 原因： " . h($e->getMessage());
            exit();
        }
    }

    // 完了トークンが発行されていない
    if (empty($_SESSION['token']['complete'])) {
        // エラー処理（入力画面に戻す処理など）
        header('Location: case3-err.php');
        exit();
    }

    // 確認トークンを更新
    $_SESSION['token']['confirm'] = bin2hex(random_bytes(32));

    // 正常に全ての処理が完了
//    header('Location: case3-d.php');
    exit();

} else {
    header('Location: case3-post_err.php');
    exit();
}
