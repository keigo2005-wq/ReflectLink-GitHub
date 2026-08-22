<?php

require_once "db.php";
require_once "functions.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postId = (int)($_POST["post_id"] ?? 0);
    $position = $_POST["position"] ?? "";
    $commenterName = trim($_POST["commenter_name"] ?? "");
    $comment = trim($_POST["comment"] ?? "");

    $positions = ["GK", "DF", "MF", "FW", "監督・スタッフ"];

    if (
        $postId <= 0 ||
        !in_array($position, $positions, true) ||
        $commenterName === "" ||
        $comment === ""
    ) {
        $error = "すべての項目を正しく入力してください。";
    } else {
        $sql = "
            INSERT INTO comments
                (post_id, position, commenter_name, comment)
            VALUES
                (:post_id, :position, :commenter_name, :comment)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":post_id", $postId, PDO::PARAM_INT);
        $stmt->bindValue(":position", $position);
        $stmt->bindValue(":commenter_name", $commenterName);
        $stmt->bindValue(":comment", $comment);
        $stmt->execute();

        header("Location: detail.php?id=" . $postId . "&commented=1");
        exit;
    }
}

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    exit("投稿番号が正しくありません。");
}

$sql = "SELECT * FROM soccer_posts WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$stmt->execute();

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    exit("指定された投稿は見つかりませんでした。");
}

$commentSql = "
    SELECT
        id,
        position,
        commenter_name,
        comment,
        created_at
    FROM comments
    WHERE post_id = :post_id
    ORDER BY created_at DESC, id DESC
";

$commentStmt = $pdo->prepare($commentSql);
$commentStmt->bindValue(":post_id", $id, PDO::PARAM_INT);
$commentStmt->execute();

$comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投稿詳細</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="post-detail">
    <h1>投稿詳細</h1>

    <h2><?= escape($post["match_name"]) ?></h2>
    
    <?php if (!empty($post["image_name"])): ?>
　      <div class="post-image">
            <img
            src="uploads/<?= escape(basename($post["image_name"])) ?>"
            alt="投稿されたプレー画像"
            >
        </div>
　　<?php endif; ?>

    <p>
        発生した課題：<br>
        <?= displayText($post["issue"]) ?>
    </p>

    <p>
        原因：<br>
        <?= displayText($post["cause"]) ?>
    </p>

    <p>
        改善案：<br>
        <?= displayText($post["improvement"]) ?>
    </p>

　　<section class="comment-section">
        <h2>ポジション別の意見</h2>

        <?php if ($error !== ""): ?>
            <p class="error-message"><?= escape($error) ?></p>
        <?php endif; ?>

        <?php if (isset($_GET["commented"]) && $_GET["commented"] === "1"): ?>
            <p class="success-message">
                意見を投稿しました。
            </p>
        <?php endif; ?>

        <form action="detail.php?id=<?= (int)$post["id"] ?>" method="post">
            <input type="hidden" name="post_id" value="<?= (int)$post["id"] ?>">

            <div>
                <label for="position">ポジション</label>
                    <select id="position" name="position" required>
                        <option value="">選択してください</option>
                        <option value="GK">GK</option>
                        <option value="DF">DF</option>
                        <option value="MF">MF</option>
                        <option value="FW">FW</option>
                        <option value="監督・スタッフ">監督・スタッフ</option>
                    </select>
            </div>

            <div>
                <label for="commenter_name">名前</label>
                <input type="text" id="commenter_name" name="commenter_name" maxlength="50" required>
            </div>

            <div>
                <label for="comment">意見</label>
                <textarea id="comment" name="comment" maxlength="500" required></textarea>
            </div>

            <button type="submit">意見を投稿する</button>
        </form>
　　</section>
　　
　　<section class="comment-list">
    <h2>投稿された意見</h2>

    <?php if (empty($comments)): ?>
        <p>まだ意見はありません。</p>
    <?php else: ?>
        <?php foreach ($comments as $commentData): ?>
            <article class="comment-card">
                <p class="comment-info">
                    <span class="position-label">
                        <?= escape($commentData["position"]) ?>
                    </span>

                    <strong>
                        <?= escape($commentData["commenter_name"]) ?>
                    </strong>

                    <time>
                        <?= escape($commentData["created_at"]) ?>
                    </time>
                </p>

                <p class="comment-text">
                    <?= displayText($commentData["comment"]) ?>
                </p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

    <a href="list.php">投稿一覧へ戻る</a>
</main>

</body>
</html>