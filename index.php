<?php

//メッセージを保存するファイルのパス設定
define('FILENAME','./message.txt');
define('IMAGEPLACE', './images_after');

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
$allowed_file = array("png", "pdf", "jpg"); //ファイルの種類

session_start();

if(!empty($_FILES)){
    $filename = $_FILES['upload_image']['name'];#$_FILESからファイル名取得
    #$_FILESからから保存先の取得と、images_afterというローカルフォルダに移す
    $uploaded_path='images_after/'.$filename;

    $check_ext = strtolower(pathinfo($uploaded_path,PATHINFO_EXTENSION));
        $result=move_uploaded_file($_FILES['upload_image']['tmp_name'],$uploaded_path);

        if($result){
            $MSG='アップロード成功！ファイル名：'.$filename;
            $img_path=$uploaded_path;
        }
        else{$MSG='アップロード失敗！エラーコード：'.$_FILES['upload_image']['error'];}
}

if ( !empty($_POST['btn_submit']) ){

    if(empty($_POST['view_name'])){$error_message[]='表示名を入力してください。';}

    //メッセージの入力チェック
    if(empty($_POST['message'])){$error_message[]='一言メッセージを入力してください';}

    if(empty($error_message)){
        if($file_handle=fopen(FILENAME,"a")){        
            $current_date=date("Y-m-d H:i:s");//書き込み日時の取得

            //書き込むデータの作成
            $data = "'".$_POST['view_name']."','".$_POST['message']."','".$current_date."','".$uploaded_path."'\n";
            fwrite($file_handle, $data);//書き込み
            fclose($file_handle);//ファイルを閉じる
        }

        $_SESSION['success_message']='メッセージを書き込みました。';
        header('Location: ./');
		exit();
        }
}

if( $file_handle = fopen(FILENAME,'r') ) {
    while( $data = fgets($file_handle) ){
        $split_data = preg_split( '/\'/', $data);

        $message = array(
            'view_name' => $split_data[1],
            'message' => $split_data[3],
            'post_date' => $split_data[5],
            'img_data' => $split_data[7]
        );
        array_unshift( $message_array, $message);
    }
    fclose( $file_handle);//ファイルを閉じる
}

?>

<!DOCTYPE html><html lang="ja">
<head><meta charset="utf-8"><title>BBS</title>
<style>
body{margin:auto;background:#FFF;position:relative;}
.space0{
    height:10vh;
}
.space1{
    height:1vh;
}
header{background:#376169;width:100%;margin:auto;}
#header{font-size:50px;text-align:center;padding-top:80px;color:#FFF;}.centre{text-align:center;}
#openMenu{
    height:calc(tan(70deg)*30px/2);
    width:100px;
    clip-path:polygon(5% 10%, 94% 10%,48% 100%);
    background:#CCC;
    margin:auto;
}
/*================================ここからフォームのCSS==========================================*/
#form-box{height:488px;background:#FFF;width:95%;right:0;left:0;margin:auto;bottom:50px;position:relative;border:solid 3px #000;border-radius:40px;/*background:red;*/}
.form-box0{width:92%;margin:auto;}
input, textarea{border:2px solid #000;box-sizing:border-box;}
#messageArea{height:20em;font-size:10px;/*background:skyblue;*/}
#inputBtn0{color:#0099FF;font-size:26px;background:#DDD;border-radius:20px;}/*画像を選択のボタン*/
#inputBtn1{color:#FFF;font-weight:bold;font-size:26px;background:#FF3300;border-radius:20px;padding:6px;}/*送信ボタン*/
/*================================ここまでフォームのCSS==========================================*/

.display-flex{display:flex;width:92%;margin:auto;/*background:red;*/}/*ブロック要素を横並びにするクラスなので多分必要*/

#box{overflow-y:scroll;height:100vh;background:#FFF;width:100%;margin:auto;bottom:0;position:relative;border:solid 1px #000;/*background:red;*/}
#moveBtn{
    font-size:75px;color:#FFF;background-color:#00CCCC;opacity:0.7;
    left:35%;bottom:120px;margin:auto;padding:5px;position:relative;border-radius:100%;text-align:center;height:92px;width:92px;z-index:2;
}

.showImg{width:100%;}
.lineHeight{line-height:0.5px;}
#postsTable{border-radius:30px;width:90%;margin:auto;box-shadow:10px 5px 5px #888;/*background:#ffcc00;*/}

/*.box0{width:95%; position:relative; right:0; left:0; margin:auto;}*/
td{flex-wrap:wrap;background:transparent;}

.photo{width:50%; text-align:center;}
.comment{width:35%;} .msg{font-size:30px;line-break:anywhere;}
.time{font-size:15px;} /*time*/

.placeName{line-height:1px;}
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/css/lightbox.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/js/lightbox.min.js" type="text/javascript"></script>
</head>
<body>
<!--<header><div id="header">𝗛𝗮𝗶𝗤𝘂𝗿𝗶</div></header>-->

<div id="box">
        <div id="m2">
            <form method="post", enctype = "multipart/form-data">
                <p class="space0"></p>
                <div id="form-box">
                    <p class="space1"></p>
                    <p class="form-box0">
                        <textarea id="messageArea" name="message" style="width:100%;"></textarea>
                    </p>
                    <p class="form-box0"><br>
                        <label for="view_name">場所</label>
                        <input id="view_name" type="text" name="view_name" value="" style="width:50%;">
                    </p>
                    <p class="form-box0">
                        <div class="display-flex">
                            <p class="centre">
                                <span id="inputBtn0" style="padding:6px 25px;">
                                    <label><input type="file" name="upload_image" style="display:none;">画像をアップロード</label>
                                </span>
                            </p>
                            <p class="centre" style="margin-left:auto;">
                                <span id="inputBtn1" style="padding:6px 45px;">
                                    <label><input type="submit" name="btn_submit" style="display:none;">POST</label>
                                </span>
                            </p>
                        </div>
                        <?php if( !empty($error_message) ): ?>
                            <ul class="error_message">
                                <?php foreach( $error_message as $value ): ?><li><?php echo $value; ?></li><?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if( empty($_POST['btn_submit']) && !empty($_SESSION['success_message']) ): ?>
                            <span class="success_message">
                                <?php echo htmlspecialchars( $_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>
                        <!--<?php if( !empty($success_message) && !empty($_SESSION['success_message']) ): ?>
                            <span class="success_message">
                                <?php echo htmlspecialchars( $_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        <?php endif; ?>-->
                    </p>
                </div>
            </form>
        </div>

        <div class="menu1" onclick="m0()">
            <div id="openMenu"></div>
        </div><br>
    <!-- ここまでメッセージの入力フォーム -->


    <!-- ここに投稿されたメッセージを表示 -->
    <div class="box0">
        <?php if( !empty($message_array) ): ?>
        <?php foreach( $message_array as $value ): ?>
            <table id="postsTable">
                <td class="photo"><!--画像を表示している箇所-->
                    <?php if(!empty ($value['img_data'])){;?>
                        <a href="<?php echo $value['img_data'];?>" data-lightbox="group">
                            <img class="showImg" src = "<?php echo $value['img_data'];?>" alt="">
                        </a>
                    <?php }; ?>
                </td>
                <td class="comment"><!--メッセージを表示しているところ-->
                    <h3 class="placeName" style="line-break:anywhere;"><?php echo $value['view_name']; ?></h3>
                    <p class="msg"><?php echo $value['message']; ?></p>

                    <p class="time"><!--投稿時間-->
                        <span><?php echo date('m月d日', strtotime($value['post_date'])); ?></span>
                        <span class="lineHeight"><?php echo date('H:i', strtotime($value['post_date'])); ?></span>
                    </p>
                </td>
            </table><br>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<p id="moveBtn" onclick="moveTop()">↑</p><!--スクロール用ボタン-->

<script>
    document.getElementById("m2").style.display="none";
    function m0(){
        let m1=document.getElementById("m2");
        if(m1.style.display=="block"){m1.style.display="none";} else{m1.style.display="block";}
    }

    let box=document.getElementById("box");
    let topBtn=class{
        constructor(y,blue,defaultc){
            this.y=y; this.blue=blue; this.defaultc=defaultc;
        }
    }
    let topInstance=new topBtn(0,"background:blue;","background:#00BFFF;");
    let moveBtn=document.getElementById("moveBtn");
    let moveTop=function(n=true){
    moveBtn.style=topInstance.blue;
    if(n==true && box.scrollTop>topInstance.y){
        let cnt=0;
        function cntUp(){
            cnt++;
            let time=setTimeout(cntUp,100);
            console.log(cnt);
            if(cnt==2){
                clearTimeout(time); console.log("quit");
                moveBtn.style=topInstance.defaultc;
            }
        } cntUp();
    }
    else{moveBtn.style=topInstance.defaultc;}
    box.scrollTop=topInstance.y; //0
    }
</script>
</body>
</html>
