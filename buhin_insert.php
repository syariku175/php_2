<?php

// //DB設定読み込み
require_once __DIR__ . '/conf/database_conf.php';

// //h()関数読み込み
require_once __DIR__ . '/lib/h.php';

//validation() 関数読み込み
require_once __DIR__ . '/lib/validation.php';

try {
    //DB接続
    $db = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8","$dbUser","$dbPass");
    $db ->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    //追加ボタンが押されたら
    if (isset($_POST['buhin_insert'])) {
        //buhin_nameバリデーション
        validation($_POST['buhin_name'],'buhin_name','');
        //追加する部品の名前を取得
        $buhin_name = $_POST['buhin_name'];
        //buhin_idバリデーション
        validation($_POST['buhin_id'],'buhin_id',7);
        //追加する部品のIDを取得
        $buhin_id = $_POST['buhin_id'];
        //SQL準備(新規部品IDと名前をbuhinテーブルに追加)
        $sql = "INSERT INTO buhin (buhin_id,buhin_name) VALUES (:buhin_id,:buhin_name)";
        $prepare = $db -> prepare($sql);
        //buhin_idに挿入する変数と型を指定
        $prepare -> bindValue(':buhin_id',$buhin_id,PDO::PARAM_STR);
        //buhin_nameに挿入する変数と型を指定
        $prepare -> bindValue(':buhin_name',$buhin_name,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        echo '<p>追加完了</p>';
    }
} catch (PDOException $e) {
    echo 'データベースエラー発生：' . h($e->getMessage());
}catch (Exception $e){
    echo 'エラー発生' . h($e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>部品追加</title>
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
        <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
		<link rel="stylesheet" href="https://milligram.github.io/styles/main.css">
    </head>
    <body>
        <main class="wrapper">
            <section class="container">
                <h1>部品追加</h1>
                <div class="example">
                    <!--部品入力フォーム-->
                    <form method="POST">
                        <fieldset>
                            <div class="colomn">
                                <label for="buhinName">名前</label>
                                <input type="text" name="buhin_name" maxlength="20" placeholder="名前">
                                <label for="buhinId">部品ID</label>
                                <input type="text" name="buhin_id" maxlength="4" placeholder="(例)0001">
                                <p>半角数字4桁で入力してください</p>
                                <p>入力例：0001</p>	
                                <p>部品IDが被ると登録できません</p>
                                <p>前のページIDを確認してから入力してください</p>
                                <input type="submit" name="buhin_insert" value="add">
                            </div>
                        </fieldset>
                    </form>
                </div>
                <p>
                    <a href="buhin_maintenance.php">
                        戻る
                    </a>
                </p>
            </section>
        </main>
    </body>
</html>