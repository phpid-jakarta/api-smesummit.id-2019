<?php

require __DIR__."/../../bootstrap/init.php";
require __DIR__."/../../vendor/autoload.php";

$pdo = DB::pdo();
$st = $pdo->prepare(
	"SELECT `a`.`voucher_code`,`a`.`name`,`a`.`position`,`a`.`company_name`,`a`.`email`,`a`.`phone`,CONCAT('par',`b`.`ticket_code`) AS `ticket_code` FROM `participants` AS `a` INNER JOIN `participants_ticket` AS `b` ON `a`.`id` = `b`.`participant_id` WHERE `a`.`created_at` >= '2019-04-23 00:00:00';"
);
$st->execute();
$i = 1;
while ($u = $st->fetch(PDO::FETCH_ASSOC)) {
	//if (preg_match("/(php)|(biznet)/i", $u["voucher_code"])) {
		if (!empty($u["email"])) {
			// print json_encode($u)."\n";
			// unset($u["voucher_code"]);
			$hash = sha1(json_encode($u));
			// print "Hash: {$hash}\n";
			// gen_pdf($u, $hash);	
			send_mail($i++, $u, $hash);
		}
	//}
}

use Endroid\QrCode\QrCode;
use PHPMailer\PHPMailer\PHPMailer;
use Picqer\Barcode\BarcodeGeneratorJPG;

function gen_pdf($u, $hash) {
	print "Generating PDF...\n";
	file_put_contents(
		BASEPATH."/storage/tickets/qrcode/{$u["ticket_code"]}_{$hash}.png",
		(new QrCode($u["ticket_code"]))->writeString()
	);

	file_put_contents(
		BASEPATH."/storage/tickets/barcode/{$u["ticket_code"]}_{$hash}.png",
		(new BarcodeGeneratorJPG())->getBarcode(
			$u["ticket_code"],
			BarcodeGeneratorJPG::TYPE_CODE_128
		)
	);

	ob_start();
	$qrCode = base64_encode(file_get_contents(BASEPATH."/storage/tickets/qrcode/{$u["ticket_code"]}_{$hash}.png"));
	$barCode = base64_encode(file_get_contents(BASEPATH."/storage/tickets/barcode/{$u["ticket_code"]}_{$hash}.png"));
	require __DIR__."/../../mail_templates/ticket_pdf.php";
	$pdfOut = ob_get_clean();
	$u["hash"] = $hash;
	$tmpName = "/tmp/pdf__".(time().rand().rand());
	file_put_contents($tmpName, $pdfOut);
	print shell_exec(escapeshellarg(PHP_BINARY)." ".escapeshellarg(__DIR__."/pdf_bin.php")." ".escapeshellarg($tmpName)." ".escapeshellarg(json_encode($u))." 2>&1");

	print json_encode($u)."\n";
	unlink($tmpName);
}


function send_mail($no, $u, $hash) {
	$no = sprintf("%04d", $no);
	//print "Sending mail...\n";

	// $targetEmail = $u["email"];
	// $targetName = $u["name"];
	$targetEmail = "memorycopy33@gmail.com";
	$targetName = "Memory Copy";//$u["name"];

	$pdfFile = "https://api.smesummit.id/tickets/pdf/{$u["ticket_code"]}_{$hash}.pdf";
	print "{$no} | ".$pdfFile." | {$u["voucher_code"]} | ".$u["email"]."\n";

	return;
	// $barCode = "https://api.smesummit.id/tickets/barcode/{$u["ticket_code"]}_{$hash}.png";
	// $qrCode = "https://api.smesummit.id/tickets/qrcode/{$u["ticket_code"]}_{$hash}.png";

	// $u["phone"] = preg_replace("/[^\d\+]/", "", $u["phone"]);

	// foreach ($u as &$v) {
	// 	$v = htmlspecialchars($v);
	// }
	// unset($v);
	// ob_start();
	// require BASEPATH."/mail_templates/ticket.php";
	// $content = ob_get_clean();

	// $mail = new PHPMailer;
	// $mail->isSMTP();
	// $mail->SMTPDebug = 2;
	// $mail->Host = 'smtp.gmail.com';
	// $mail->Port = 587;
	// $mail->SMTPSecure = 'tls';
	// $mail->SMTPAuth = true;
	// $mail->Username = "teainside99@gmail.com";
	// $mail->Password = "triosemut123";
	// $mail->setFrom('teainside99@gmail.com', 'Tea Inside SMESUMMIT');
	// $mail->addReplyTo('teainside99@gmail.com', 'Tea Inside SMESUMMIT');
	// $mail->addAddress($targetEmail, $targetName);
	// $mail->Subject = "SME Summit 2019 - Ticket";
	// $mail->msgHTML($content, __DIR__);
	// $mail->addAttachment(BASEPATH."/storage/tickets/pdf/{$u["ticket_code"]}_{$hash}.pdf");
	// global $pdo;
	// if (!$mail->send()) {
	// 	echo "Mailer Error: " . $mail->ErrorInfo;
	// } else {
	// 	echo "Message sent!";
	// 	$pdo
	// 		->prepare("UPDATE `participants` SET `ticket_sent` = '1' WHERE `email` = :email LIMIT 1;")
	// 		->execute([":email" => $u["email"]]);
	// }
}