<?php

require __DIR__."/../bootstrap/init.php";

/**
 * @param $u array
 * @return bool
 */
function sendMail(array $u): bool
{
	$to = $u["email"];
	$subject = "SME Summit 2019 - Payment Instructions Email";
	ob_start();
	require BASEPATH."/mail_templates/payment_instruction.php";
	$message = ob_get_clean();

	$headers = [
		"MIME-Version: 1.0",
		"Content-type: text/html; charset=iso-8859-1",
		"To: {$u["name"]} <{$u["email"]}>",
		"From: Payment SME SUMMIT <payment@smesummit.id>"
	];

	return (bool)mail($to, $subject, $message, implode("\r\n", $headers));
}

$ticketPrice = "Rp.500.000";

$u = [
	"email" => "ammarfaizi2@gmail.com",
	"phone" => "085867152777",
	"name" => "Ammar Faizi",
	"position" => "Owner",
	"company_name" => "Tea Inside",
	"ticketPrice" => $ticketPrice
];

sendMail($u);
exit(0);


/**
 * Kirim email ke semua participant yang belum mendapatkan instruksi pembayaran.
 */
$pdo = DB::pdo();
$st = $pdo->prepare(
	"SELECT `id`,`email`,`phone`,`name`,`position`,`company_name` FROM `participants` WHERE `email_verif_sent` = '0';"
);
$st->execute();
while ($u = $st->fetch(PDO::FETCH_ASSOC)):
	$u["ticket_price"] = $ticketPrice;
	if (sendMail($u)) {
		$pdo->prepare("UPDATE `participants` SET `email_verif_sent` = '1' WHERE `id` = :participant_id LIMIT 1;")
			->execute([":participant_id" => $u["id"]]);
		printf("[Success] %s\n", $to);
	} else {
		printf("[Failed] %s\n", $to);
	}
endwhile;

$st = $pdo = null;
unset($st, $pdo);
