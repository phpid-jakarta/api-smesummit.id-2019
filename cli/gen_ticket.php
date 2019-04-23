<?php

require __DIR__."/../bootstrap/init.php";

$pdo = DB::pdo();
$st = $pdo->prepare("SELECT * FROM `participants`;");
$st->execute();
$query = "INSERT INTO `participants_ticket` (`participant_id`, `ticket_code`, `created_at`) VALUES ";
$now = date("Y-m-d H:i:s");
$i = 0;
$d[":created_at"] = $now;
while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	$id = sprintf("%04x", $r["id"]);
	$query .= "(:participant_id{$i}, :ticket_code{$i}, :created_at),";
	$d[":participant_id{$i}"] = $r["id"];
	$d[":ticket_code{$i}"] = $id;
	$i++;
}
$query = rtrim($query, ",");
$st = $pdo->prepare($query);
$st->execute($d);
unset($pdo);
