<?php

require_once('../DataManager.php');

$DebugConnection = new PDO ('mysql:dbname=cropapp-test;host=127.0.0.1', 'root', 'admin');
$DebugConnection -> query("DELETE FROM farmers");
$DebugConnection -> query("DELETE FROM crofts");
$DataManager = new DataManager ('cropapp-test');

$DataManager -> addFarmer ('1');
$DataManager -> addFarmer ('2');
$DataManager -> addFarmer ('3');

$DataManager -> addCroft ('1', 'croft1-1', '0', '0');
$DataManager -> addCroft ('1', 'croft1-2', '1', '0');
$DataManager -> addCroft ('1', 'croft1-3', '2', '0');
$DataManager -> addCroft ('1', 'croft1-4', '20', '0');

$result = $DataManager -> getCrofts ();
print_r ($result);
$result = $DataManager -> getNeighbours ('1', 'croft1-1', 500);

print_r ($result);
?>
