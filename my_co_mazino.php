<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
$mode = $_REQUEST["mode"];
$path = $_REQUEST["path"];
$page = basename($_SERVER["PHP_SELF"]);
$fileName = $_GET["fileName"];
$dbHost = $_POST["dbHost"];
$dbId = $_POST["dbId"];
$dbPw = $_POST["dbPw"];
$dbName = $_POST["dbName"];
$query = $_POST["query"];
$inputPw = $_POST["inputPw"];
$accessPw = "e36208f0215f4dae4951568f958d3e80";
$accessFlag = $_SESSION["accessFlag"];
if($mode=="login" && ($accessPw == md5($inputPw))){
    $_SESSION["accessFlag"] = "Y";
    echo "<script>location.href='{$page}';</script>";
    exit();
}else if($mode == "logout"){
    unset($_SESSION["accessFlag"]);
    session_destroy();
    echo "<script>location.href='{$page}'</script>";
    exit();
}

?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <title>mazino-moon</title>
        <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
        <script
            src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script></script>
    </heade>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">


                <?php if($accessFlag != "Y"){ ?>
                        <h3>Login
                    </h3>
                    <hr>
                        <form action="<?=$page?>?mode=login" method="POST">
                        <div class="input-group">
                            <span class="input-group-addon">PW</span>
                            <input type="text" class="form-control" placeholder="PW Input..." name="inputPw">
                        </div>
                        <br>
                        <p class="text-center">
                            <button class="btn btn-default" type="submit">Auth</button>
                        </p>
                    </form>
                    <?php }else{ ?>
                    <h3>Mysql Co
                        <small>Create by MazinoMoon</small>
                    </h3>
                    <hr>
                    <ul class="nav nav-tabs">
                        <li >
                            <a href="<?=$page?>?mode=db">DB Connector</a>
                        </li>
                        <li >
                            <a href="<?=$page?>?mode=logout">Logout</a>
                        </li>
                    </ul>
                    <br>
<?php }  ?>
   


                    <?php 
                    
                    
                    if($accessFlag == "Y"){
                    
                    if($mode == "db"|| empty($mode)){?>
                    <?php
if(empty($dbHost)||empty($dbId)||empty($dbName)||empty($dbPw)){
?>

                    <form action="<?=$page?>?mode=db" method="POST">
                        <div class="input-group">
                            <span class="input-group-addon">HOST</span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Host Input..."
                                name="dbHost">
                            <span class="input-group-addon">ID</span>
                            <input type="text" class="form-control" placeholder="ID Input..." name="dbId">
                            <span class="input-group-addon">PW</span>
                            <input type="text" class="form-control" placeholder="PW Input..." name="dbPw">
                            <span class="input-group-addon">DB</span>
                            <input type="text" class="form-control" placeholder="DB Input..." name="dbName">
                        </div>
                        <br>
                        <p class="text-center">
                            <button class="btn btn-default" type="submit">Connect</button>
                        </p>
                    </form>
                <?php }else{

$dbConn = new mysqli($dbHost,$dbId,$dbPw,$dbName);
if($dbConn ->connect_errno){
    echo "DB연결 실패";
    exit();
}
?>
                    <form action="<?=$page?>?mode=db" method="POST">
                        <div class="input-group">
                            <span class="input-group-addon">SQL</span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Qeuery Input..."
                                name="query"
                                value="<?=$query?>">
                        </div>
                        <br>
                        <p class="text-center">
                            <button class="btn btn-default" type="submit">Execution</button>
                        </p>
                        <input type="hidden" name="dbHost" value="<?=$dbHost?>">
                        <input type="hidden" name="dbName" value="<?=$dbName?>">
                        <input type="hidden" name="dbId" value="<?=$dbId?>">
                        <input type="hidden" name="dbPw" value="<?=$dbPw?>">
                    </form>
                    <?php

if(!empty($query))
{
$result = $dbConn->query($query);
$rowCnt = $result->num_rows;
echo '<table
class="table table-bordered table-hover"
style="table-layout: fixed; word-break: break-all;">';

for($i=0; $i<$rowCnt;$i++){
$row=$result->FETCH_ASSOC();
if($i==0){
    $ratio = 100 /count($row);
?>
                    <thead>
                        <tr class="active">
                            <?php
foreach($row as $key => $value){
                            ?>
                            <th style="width:50%: <?=$ratio?>%" class="text-center"><?=$key?></th>

                            <?php
}

?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                }
echo "<tr>";
foreach($row as $key => $value){

?>
                        <td style="vertical-align:middle" class="text-center"><?=$value?></td>
                        <?php
?>
                        <?php
}
echo "</tr>";
?>
                    </tbody>
                    <?php
}
}?>

                </table>
                <?php

}?>
                <?php 
        }?>

<?php }
?>
                <hr>
                <p class="text-muted text-center">Copyright 2022, MazinoMoon🌙, All rights reserved.</p>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
</body>
</html>