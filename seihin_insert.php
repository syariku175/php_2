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

    //登録する製品のseihin_idを受け取っていれば
    if(isset($_POST['seihin_id'])){
        //seiban_idバリデーション
        validation($_POST['seiban_id'],'製番ID',7);
        //追加する製品のseiban_idを取得
        $seiban_id = $_POST['seiban_id'];
        //seihin_nameバリデーション
        validation($_POST['seihin_name'],'製品名','');
        //追加する製品のseihin_nameを取得
        $seihin_name = $_POST['seihin_name'];
        //performance_numバリデーション
        validation($_POST['performance_num'],'出荷順','');
        //suuryouバリデーション
        validation($_POST['suuryou'],'リードタイム','');
        //追加する製品のリードタイムを取得
        $suuryou = $_POST['suuryou'];
        //seihin_idバリデーション
        validation($_POST['seihin_id'],'製品ID',4);
        //追加する製品のseihin_idを取得
        $seihin_id = $_POST['seihin_id'];
        //SQL準備(seihinテーブルに各項目を挿入)
        $sql = "INSERT INTO seihin
        (seiban_id,seihin_id,seihin_name,performance_num,suuryou)
        VALUES (:seiban_id,:seihin_id,:seihin_name,:performance_num,:suuryou)";
        $prepare = $db -> prepare($sql);
        //seiban_idに挿入する変数と型を指定
        $prepare -> bindValue(':seihin_id',$seihin_id,PDO::PARAM_STR);
        //seihin_nameに挿入する変数と型を指定
        $prepare -> bindValue(':seihin_name',$seihin_name,PDO::PARAM_STR);
        //suuryouに挿入する変数と型を指定
        $prepare -> bindValue(':suuryou',$suuryou,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        echo '<p>追加完了</p>';
    }

    //出荷製品一覧からseiban_idを受け取っていたら
    if(isset($_POST['seiban_id'])){
        //seiban_idを取得
        $seiban_id = $_POST['seiban_id'];
    }
}catch (PDOException $e) {
    echo 'データベースエラー発生：' . h($e->getMessage());
}catch (Exception $e){
    echo 'エラー発生：' . h($e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>製品登録</title>
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
        <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
		<link rel="stylesheet" href="https://milligram.github.io/styles/main.css">
    </head>
    <body>
        <main class="wrapper">
            <section class="container">
<?php
try{
    //もしseiban_idがPOST送信されてこのページに来たら
    if (isset($_POST['seiban_id'])) {
        //seiban_ideバリデーション
        validation($_POST['seiban_id'],'製番ID',7);
        //seiban_idを取得
        $seiban_id = $_POST['seiban_id'];
        //SQL準備
        $sql = 'SELECT * FROM seiban WHERE seiban_id = :seiban_id';
        $prepare = $db->prepare($sql);
        //バインド
        $prepare -> bindValue(':seiban_id',$seiban_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare->execute();
        //出力
        foreach ($prepare as $row) {
            echo "<h1>" . h($row['seiban_name']) . "</h1>";
        }
    }
}catch (PDOException $e) {
    echo 'データベースエラー発生：' . h($e->getMessage());
}catch (Exception $e){
    echo 'エラー発生：' . h($e->getMessage());
}
?>
                <h2>製品登録</h2>
                <!--製品入力フォーム-->
                <form method="POST">
                    <fieldset>
                        <label for="nameField">製品名</label>
                        <input type="text" name="seihin_name"  maxlength="30" placeholder="製品名">
                        <label for="performanceTimeField">数量</label>
                        <input type="text" name="suuryou" maxlength="20" placeholder="(例)1,2">
                        <label for="seihinIdField">部品ID</label>
                        <input type="text" name="seihin_id" maxlength="4" placeholder="(例)B001">
                        <p>半角英数字4文字で入力してください</p>
                        <p>入力例：B001→(この製品の部品1番目</p>
                        <p>部品IDが被ると登録出来ません</p>
                        <p>前ページから他部品のIDを確認してから入力してください</p>
                        <input type="hidden" name="seiban_id" value="<?= $seiban_id ?>">
                        <input type="submit" value="add">
                    </fieldset>suuryou
                </form>
                <form method="GET" action="seihin_maintenance.php">
                    <input type="hidden" name="seiban_id" value="<?= $seiban_id ?>">
                    <input type="submit" value="戻る">
                </form>
            </section>
        </main>
    </body>
</html>