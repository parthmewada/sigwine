<?php
$hostname = 'localhost';
/*** mysql username ***/
$username = 'SigwineApp';
/*** mysql password ***/
$password = 'Sigwine2015';
try {
    $dbh = new PDO("mysql:host=$hostname;dbname=mysql", $username, $password);
    /*** echo a message saying we have connected ***/
    echo 'Connected to database';
}
catch(PDOException $e)
{
    echo $e->getMessage();
}
?>