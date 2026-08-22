<?php
require_once("db.php");

// POST送信以外では処理しない
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("不正なアクセスです。");
}

// 削除対象の投稿番号を受け取る
$id = (int)($_POST["id"] ?? 0);

if ($id <= 0) {
    exit("投稿番号が正しくありません。");
}

// 投稿を削除する
$sql = "DELETE FROM soccer_posts WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$stmt->execute();

// 一覧画面へ戻る
header("Location: list.php?deleted=1");
exit;