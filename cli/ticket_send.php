<?php

require __DIR__."/../bootstrap/init.php";
require __DIR__."/../vendor/autoload.php";

use Endroid\QrCode\QrCode;
use Picqer\Barcode\BarcodeGeneratorJPG;

$pdo = DB::pdo();
$st = $pdo->prepare("SELECT `a`.`name`,`a`.`position`,`a`.`company_name`,`a`.`email`,`a`.`phone`,CONCAT('par',`b`.`ticket_code`) AS `ticket_code` FROM `participants` AS `a` INNER JOIN `participants_ticket` AS `b` ON `a`.`id` = `b`.`participant_id`;");
$st->execute();
$u = $st->fetch(PDO::FETCH_ASSOC);
sendMail($u);

function gen_content($u) {
	file_put_contents(
		BASEPATH."/storage/tickets/qrcode/{$u["ticket_code"]}.png",
		(new QrCode($u["ticket_code"]))->writeString()
	);

	file_put_contents(
		BASEPATH."/storage/tickets/barcode/{$u["ticket_code"]}.png",
		(new BarcodeGeneratorJPG())->getBarcode($u["ticket_code"], BarcodeGeneratorJPG::TYPE_CODE_128)
	);
	$barCode = "https://api.smesummit.id/tickets/barcode/{$u["ticket_code"]}.png";
	$qrCode = "https://api.smesummit.id/tickets/qrcode/{$u["ticket_code"]}.png";

	$u["phone"] = preg_replace("/[^\d\+]/", "", $u["phone"]);

	foreach ($u as &$v) {
		$v = htmlspecialchars($v);
	}
	unset($v);

	require BASEPATH."/mail_templates/ticket.php";
}

/**
 * @param $u array
 * @return bool
 */
function sendMail(array $u): bool
{
	$to = "ammarfaizi2@gmail.com";
	$subject = "SME Summit 2019 - Ticket";
	ob_start();
	gen_content($u);
	$message = ob_get_clean();

	$headers = [
		"MIME-Version: 1.0",
		"Content-type: text/html; charset=iso-8859-1",
		"To: {$u["name"]} <{$u["email"]}>",
		"From: Payment SME SUMMIT <payment@smesummit.id>"
	];

	return (bool)mail($to, $subject, $message, implode("\r\n", $headers));
}