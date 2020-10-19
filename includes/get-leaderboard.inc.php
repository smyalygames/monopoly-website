<?php

include 'dbh.inc.php';

$sql ="SELECT users.user_username, plays.user_plays
FROM plays
INNER JOIN users ON users.user_id=plays.user_id
ORDER BY user_plays DESC
LIMIT 10;";
$sth = mysqli_query($conn, $sql);
$rows = array();
while($r = mysqli_fetch_assoc($sth)) {
    $rows[] = $r;
}
print json_encode($rows);