<?php

include 'dbh.inc.php';

$sql ="SELECT * FROM cards;";
$sth = mysqli_query($conn, $sql);
$rows = array();
while($r = mysqli_fetch_assoc($sth)) {
    $rows[] = $r;
}
print json_encode($rows);