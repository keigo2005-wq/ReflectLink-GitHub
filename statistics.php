<?php

require_once "db.php";

$sql = "
    SELECT position, COUNT(*) AS comment_count
    FROM comments
    GROUP BY position
    ORDER BY comment_count DESC
";

$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$positionLabels = [];
$commentCounts = [];

foreach ($results as $result) {
    $positionLabels[] = $result["position"];
    $commentCounts[] = (int)$result["comment_count"];
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>コメント集計</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main>
    <h1>ポジション別コメント件数</h1>

    <?php if (empty($results)): ?>
        <p>集計できるコメントがありません。</p>
    <?php else: ?>
        <ul>
            <?php foreach ($results as $result): ?>
                <li>
                    <?= htmlspecialchars(
                        $result["position"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                    ：
                    <?= (int)$result["comment_count"] ?>件
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <div class="chart-container">
        <canvas id="positionChart"></canvas>
　　</div>

    <a href="list.php">投稿一覧へ戻る</a>
</main>

<script>
    window.positionChartData = {
        labels: <?= json_encode(
            $positionLabels,
            JSON_UNESCAPED_UNICODE
        ) ?>,
        counts: <?= json_encode($commentCounts) ?>
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="statistics.js"></script>

</body>
</html>