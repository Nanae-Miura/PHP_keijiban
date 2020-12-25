<?php

//define('bbs.txt');
//$dataFile="bbs.txt";
//echo $dataFile;

date_default_timezone_set('Asia/Tokyo');

//変数の初期化
//変数の初期化とは、
//変数をあらかじめ空の値で宣言しておくことで存在しない変数を参照するエラーを防いだり、型をあらかじめ設定しておくことで意図しない動作を防ぐ
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message=null;
$error_message=array();
//サニタイズ機能
$clean=array();




if(!empty($_POST["btn_submit"])){
    if(empty($_POST['view_name']))
    $error_message[]='表示名を入力してください。';
}else{
    $clean['view_name']=htmlspecialchars($_POST['view_name'],ENT_QUOTES);
    //$clean['view_name']=preg_replace( '/\\r\\n|\\n|\\r/', '', $clean['view_name']);
}   
    if(empty($_POST['message'])){
        $error_message[]='ひとことメッセージを入力してください。';
    }else{
        //空じゃなかった場合にサニタイズを行う
        $clean['message']=htmlspecialchars($_POST['message'],ENT_QUOTES);
        //↓/\\r\\n|\\n|\\r/'があれば'<br>'に置き換えて！
        $clean['message'] = preg_replace( '/\\r\\n|\\n|\\r/', '<br>', $clean['message']);
    }
    

if(empty($error_message)){

    //if($file_handle=fopen($dataFile,"a")){
//       
//        $now_date=date("Y-m-d H:i");
//        //それぞれの投稿は改行で区切る
//        $data = "'".$clean['view_name']."','".$clean['message']."','".$now_date."'\n";
//        //書き込み
//        fwrite($file_handle,$data);
//        //データの取得はできてる
//        //echo $data;
//        fclose($file_handle);
//
//        $success_message="メッセージを書き込みました！";
//    }
//データベースに接続
$mysqli=new mysqli('localhost','hoge','V4%Ni9v7hb*a6#@','board');
//接続エラーがないか確認
if( $mysqli->connect_errno ) {
    $error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} 
else {
    //文字コード設定
    $mysqli->set_charset('utf8');
			
    // 書き込み日時を取得
    $now_date = date("Y-m-d H:i");
    
    // データを登録するSQL作成
    //なぜか名前がデータベースに表示されていない？？？
    $sql = "INSERT INTO message (view_name, message, post_date) VALUES ( '$clean[view_name]', '$clean[message]', '$now_date')";
    
    // データを登録
    $res = $mysqli->query($sql);

    if( $res ) {
        $success_message = 'メッセージを書き込みました。';
    } else {
        $error_message[] = '書き込みに失敗しました。';
    }

    // データベースの接続を閉じる
    $mysqli->close();
}
    }
//    if($file_handle=fopen($dataFile,'r')){
//        while($data=fgets($file_handle)){
//
//            $split_data = preg_split( '/\'/', $data);
//
//            //arrayの中身の番号
//            $message = array(
//                'view_name' => $split_data[1],
//                'message' => $split_data[3],
//                'post_date' => $split_data[5]
//            );
//            array_unshift( $message_array, $message);
//            //echo $data."<br>";
//        }
//        fclose($file_handle);
//    }
//データベースへ接続
$mysqli=new mysqli('localhost','hoge','V4%Ni9v7hb*a6#@','board');
// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
    //SQLとは、Structured Query Language。リレーショナルデータベース(RDB)のデータを操作するための言語です。
    //DESC→降順にデータを取得する
    //ASC→古い順に表示する
	$sql = "SELECT view_name,message,post_date FROM message ORDER BY post_date DESC";
	$res = $mysqli->query($sql);
	
	if( $res ) {
		$message_array = $res->fetch_all(MYSQLI_ASSOC);
	}
	//echo view_name;
	$mysqli->close();
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="style.css">
<title>ひと言掲示板</title>

<style>

</style>
</head>
<body>
<h1>ひと言掲示板</h1>
<!-- empty関数は値が入っているかどうかを確認することができる -->
<!-- 入っていない場合にtrue -->
<?php if(!empty($success_message)):?>
    <p class="success_message"><?php echo $success_message;?></p>
<?php endif;?>
<!-- 入力されているかのチェック -->
<?php if(!empty($error_message)):?>
<ul class="error_message">
    <?php foreach($error_message as $value): ?>
        <li>・<?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
<?php endif; ?>

<form method="post">
	<div>
        <!-- label for とid -->
		<label for="view_name">表示名</label>
		<input id="view_name" type="text" name="view_name" value="">
	</div>
	<div>
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message"></textarea>
	</div>
	<input type="submit" name="btn_submit" value="書き込む">
</form>
<hr>
<section>
<?php if( !empty($message_array) ): ?>
<!-- $message_arrayをそれぞれvalueとして取り出す -->
<?php foreach( $message_array as $value ): ?>
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
    </div>
    <p><?php echo $value['message']; ?></p>
</article>
<?php endforeach; ?>
<?php endif; ?>
</section>
</body>
</html>
