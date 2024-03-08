<?php

//メッセージを保存するファイルのパス設定
define('FILENAME','./message.txt');

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

//変数の初期化
$current_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();

if ( !empty($_POST['btn_submit']) ){

    if(empty($_POST['view_name'])){
        $error_message[] = '表示名を入力してください。';
    }

    //メッセージの入力チェック
    if(empty($_POST['message'])){
        $error_message[] = '一言メッセージを入力してください';
    }

    if(empty($error_message)){
        if($file_handle = fopen(FILENAME,"a")){
        
            //書き込み日時の取得
            $current_date = date("Y-m-d H:i:s");

            //書き込むデータの作成
            $data = "'".$_POST['view_name']."','".$_POST['message']."','".$current_date."'\n";

            //書き込み
            fwrite($file_handle, $data);

            //ファイルを閉じる
            fclose($file_handle);

            $success_message = 'メッセージを書き込みました。';
        }
    }


}

if( $file_handle = fopen( FILENAME,'r') ) {
    while( $data = fgets($file_handle) ){
        $split_data = preg_split( '/\'/', $data);

        $message = array(
            'view_name' => $split_data[1],
            'message' => $split_data[3],
            'post_date' => $split_data[5]
        );
        array_unshift( $message_array, $message);
    }

    // ファイルを閉じる
    fclose( $file_handle);
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ひと言掲示板</title>

</head>
<body>
<h1>ひと言掲示板</h1>
<?php if( !empty($success_message) ): ?>
    <p class="success_message"><?php echo $success_message; ?></p>
<?php endif; ?>
<?php if( !empty($error_message) ): ?>
	<ul class="error_message">
		<?php foreach( $error_message as $value ): ?>
			<li><?php echo $value; ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<form method="post">
	<div>
		<label for="view_name">表示名</label>
		<input id="view_name" type="text" name="view_name" value="">
	</div>
	<div>
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message"></textarea>
	</div>
	<input type="submit" name="btn_submit" value="書き込む">
</form>
<!-- ここにメッセージの入力フォームを設置 -->
<hr>
<section>
<!-- ここに投稿されたメッセージを表示 -->
<?php if( !empty($message_array) ): ?>
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