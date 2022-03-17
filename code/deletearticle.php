<?php
session_start();
include("config.php");
include("lib/db.php");

$aid = $_GET['aid'];
$author = $_SESSION['id'];
$result=get_article_perms($dbconn, $aid);
$row = pg_fetch_array($result, 0);
if($row['author'] == $author || $author == "1"){
$result = delete_article($dbconn, $aid);
}
echo $author;
echo $row['author'];
#echo "aid=".$aid."<br>";
#echo "result=".$result."<br>";
# Check result
echo "<script> location.href='/admin.php'; </script>"

?>
