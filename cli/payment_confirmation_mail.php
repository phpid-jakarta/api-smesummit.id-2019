<?php

require __DIR__."/../bootstrap/init.php";

$pdo = DB::pdo();

$st = $pdo->prepare(
	"SELECT `id`,`email`,`phone`,`name`,`position`,`company_name` FROM `participants` WHERE `email_verif_sent` = 0;"
);
$st->execute();

while ($u = $st->fetch(PDO::FETCH_ASSOC)):

	// $u = [
	// 	"email" => "ammarfaizi2@gmail.com",
	// 	"phone" => "085867152777",
	// 	"name" => "Ammar Faizi",
	// 	"position" => "Owner",
	// 	"company_name" => "Tea Inside",
	// 	"ticket_price" => "Rp.500.000"
	// ];

	$to = $u["email"];
	$subject = "SME Summit 2019 - Payment Instructions Email";

	ob_start();
	require BASEPATH."/mail_templates/payment_instruction.php";
	$message = ob_get_clean();

	// To send HTML mail, the Content-type header must be set
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-type: text/html; charset=iso-8859-1";

	// Additional headers
	$headers[] = "To: {$u["name"]} <{$u["email"]}>";
	$headers[] = "From: Payment SME SUMMIT <payment@smesummit.id>";

	// Mail it
	$q = mail($to, $subject, $message, implode("\r\n", $headers));
	
	if ($q) {
		$pdo->prepare("UPDATE `email_verif_sent` WHERE `id` = :participant_id LIMIT 1;")
			->execute([":participant_id" => $u["id"]]);
		printf("[Success] %s\n", $to);
	} else {
		printf("[Failed] %s\n", $to);
	}

endwhile;