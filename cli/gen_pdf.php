<?php

require __DIR__."/../bootstrap/init.php";
require __DIR__."/../vendor/autoload.php";

use Endroid\QrCode\QrCode;
use Picqer\Barcode\BarcodeGeneratorJPG;

$pdo = DB::pdo();
$st = $pdo->prepare("SELECT `a`.`name`,`a`.`position`,`a`.`company_name`,`a`.`email`,`a`.`phone`,CONCAT('par',`b`.`ticket_code`) AS `ticket_code` FROM `participants` AS `a` INNER JOIN `participants_ticket` AS `b` ON `a`.`id` = `b`.`participant_id`;");
$st->execute();
while ($u = $st->fetch(PDO::FETCH_ASSOC)) {
	$hash = sha1(json_encode($u));
	// print "Generating PDF to {$u["email"]}...";
	gen_pdf($u,$hash);
	//print "\n";
}

function gen_pdf($u, $hash) {
	file_put_contents(
		BASEPATH."/storage/tickets/qrcode/{$u["ticket_code"]}_{$hash}.png",
		(new QrCode($u["ticket_code"]))->writeString()
	);

	file_put_contents(
		BASEPATH."/storage/tickets/barcode/{$u["ticket_code"]}_{$hash}.png",
		(new BarcodeGeneratorJPG())->getBarcode($u["ticket_code"], BarcodeGeneratorJPG::TYPE_CODE_128)
	);
	ob_start();
	$qrCode = base64_encode(file_get_contents(BASEPATH."/storage/tickets/qrcode/{$u["ticket_code"]}_{$hash}.png"));
	$barCode = base64_encode(file_get_contents(BASEPATH."/storage/tickets/barcode/{$u["ticket_code"]}_{$hash}.png"));
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
