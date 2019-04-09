<?php

require __DIR__."/../bootstrap/init.php";
require __DIR__."/../vendor/autoload.php";

use Endroid\QrCode\QrCode;
use Picqer\Barcode\BarcodeGeneratorJPG;

$pdo = DB::pdo();
$st = $pdo->prepare("SELECT `a`.`name`,`a`.`position`,`a`.`company_name`,`a`.`email`,`a`.`phone`,CONCAT('par',`b`.`ticket_code`) AS `ticket_code` FROM `participants` AS `a` INNER JOIN `participants_ticket` AS `b` ON `a`.`id` = `b`.`participant_id` WHERE `a`.`name` = 'Ammar Faizi';");
$st->execute();
$u = $st->fetch(PDO::FETCH_ASSOC);
sendMail($u);

function gen_content($u) {
	$hash = sha1(json_encode($u));
	file_put_contents(
		BASEPATH."/storage/tickets/qrcode/{$u["ticket_code"]}_{$hash}.png",
		(new QrCode($u["ticket_code"]))->writeString()
	);

	file_put_contents(
		BASEPATH."/storage/tickets/barcode/{$u["ticket_code"]}_{$hash}.png",
		(new BarcodeGeneratorJPG())->getBarcode($u["ticket_code"], BarcodeGeneratorJPG::TYPE_CODE_128)
	);
	gen_pdf($u, $hash);
	$pdfFile = "https://api.smesummit.id/tickets/pdf/{$u["ticket_code"]}_{$hash}.pdf";
	$barCode = "https://api.smesummit.id/tickets/barcode/{$u["ticket_code"]}_{$hash}.png";
	$qrCode = "https://api.smesummit.id/tickets/qrcode/{$u["ticket_code"]}_{$hash}.png";

	$u["phone"] = preg_replace("/[^\d\+]/", "", $u["phone"]);

	foreach ($u as &$v) {
		$v = htmlspecialchars($v);
	}
	unset($v);

	require BASEPATH."/mail_templates/ticket.php";
}

function gen_pdf($u, $hash) {
	ob_start();
	$qrCode = base64_encode(file_get_contents(BASEPATH."/storage/tickets/qrcode/{$u["ticket_code"]}_{$hash}.png"));
	$barcode = base64_encode(file_get_contents(BASEPATH."/storage/tickets/barcode/{$u["ticket_code"]}_{$hash}.png"));
	require __DIR__."/../mail_templates/ticket_pdf.php";
	$mpdf = new \Mpdf\Mpdf();
	$mpdf->WriteHTML(ob_get_clean());
	ob_start();
	$mpdf->Output();
	file_put_contents(
		BASEPATH."/storage/tickets/pdf/{$u["ticket_code"]}_{$hash}.pdf",
		ob_get_clean()
	);
}

/**
 * @param $u array
 * @return bool
 */
function sendMail(array $u): bool
{
	$to = "memorycopy33@gmail.com";
	$subject = "SME Summit 2019 - Ticket";
	ob_start();
	gen_content($u);
	$message = ob_get_clean();

	$headers = [
		"MIME-Version: 1.0",
		"Content-type: text/html; charset=iso-8859-1",
		"To: {$u["name"]} <{$u["email"]}>",
		"From: Admin SMESUMMIT <admin@smesummit.id>"
	];

	return (bool)mail($to, $subject, $message, implode("\r\n", $headers));
}