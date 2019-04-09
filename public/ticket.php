<?php

require __DIR__."/../bootstrap/init.php";
require __DIR__."/../vendor/autoload.php";

use Endroid\QrCode\QrCode;
use Picqer\Barcode\BarcodeGeneratorJPG;

$pdo = DB::pdo();
$st = $pdo->prepare("SELECT `a`.`name`,`a`.`position`,`a`.`company_name`,`a`.`email`,`a`.`phone`,CONCAT('par',`b`.`ticket_code`) AS `ticket_code` FROM `participants` AS `a` INNER JOIN `participants_ticket` AS `b` ON `a`.`id` = `b`.`participant_id`;");
$st->execute();
$u = $st->fetch(PDO::FETCH_ASSOC);
header("Content-Type: text/html");

$qrCode = base64_encode(
	(new QrCode($u["ticket_code"]))->writeString()
);
$barCode = base64_encode(
	(new BarcodeGeneratorJPG())->getBarcode($u["ticket_code"], BarcodeGeneratorJPG::TYPE_CODE_128)
);

$u["phone"] = preg_replace("/[^\d\+]/", "", $u["phone"]);

require __DIR__."/../mail_templates/ticket.php";
