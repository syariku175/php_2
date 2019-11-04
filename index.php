<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>製品一覧</title>
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
        <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
		<link rel="stylesheet" href="https://milligram.github.io/styles/main.css">
    </head>
    <body>
        <main class="wrapper">
            <section class="container" id="live-list">
                <h2 class="title">製品管理</h2>
                <h1>製品一覧</h1>
                <div class="example">
                    <table>
                        <thead>
                            <th>製品</th>
                            <th>部品表示</th>
                        </thead>
                        <tbody>

<?php

//DB設定読み込み
require_once __DIR__ . '/conf/database_conf.php';

//h()関数読み込み
require_once __DIR__ . '/lib/h.php';

//例外処理
try{

    //DB接続
    $db = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8","$dbUser","$dbPass");
    $db ->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    //SQL準備
    $sql = 'SELECT * FROM live ORDER BY live_id';
    $prepare = $db->prepare($sql);
    //クエリ実行
    $prepare->execute();

    //製品をひとつずつrowに設定
    foreach ($prepare as $row) {
?>

                            <tr>
                                <td>
                                    <?= h($row['live_name']) ?>
                                </td>
                                <td>
                                    <form method="GET" action="seihin_maintenance.php">
                                        <input type="hidden" name="live_id" value="<?= h($row['live_id']) ?>">
                                        <input type="submit" value="choice">
                                    </form>
                                </td>
                            </tr>

<?php
    }
}catch (PDOException $e) {
    echo 'データベースエラー発生：' . h($e->getMessage());
}catch (Exception $e){
    echo 'エラー発生' . h($e->getMessage());
}
?>

                        </tbody>
                    </table>
                </div>
                <p>
                    <a href="seihin_maintenance.php">
                        製品一覧
                    </a>
                </p>
                <p>
                    <a href="buhin_maintenance.php">
                        部品修正
                    </a>
                </p>
            </section>
        </main>
    </body>
</html>