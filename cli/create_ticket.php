<?php

require __DIR__."/../bootstrap/init.php";
require __DIR__."/../vendor/autoload.php";

$pdo = DB::pdo();
$st = $pdo->prepare("SELECT `a`.`name`,`a`.`company_name`,`a`.`email`,`a`.`phone`,CONCAT('par',`b`.`ticket_code`) AS `ticket_code` FROM `participants` AS `a` INNER JOIN `participants_ticket` AS `b` ON `a`.`id` = `b`.`participant_id`;");
$st->execute();
while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	print json_encode($r)."\n";
	
	// $barcode = new Picqer\Barcode\BarcodeGeneratorPNG();
	// $qrCode = new Endroid\QrCode\QrCode($r["ticket_code"]);

	// file_put_contents(
	// 	BASEPATH."/storage/tickets/qrcode/{$r["ticket_code"]}.png",
	// 	$qrCode->writeString()
	// );
	// file_put_contents(
	// 	BASEPATH."/storage/tickets/barcode/{$r["ticket_code"]}.png",
	// 	$barcode->getBarcode($r["ticket_code"], $barcode::TYPE_CODE_128)
	// );
}
