<?php
ini_set("display_errors", true);
require __DIR__."/../bootstrap/init.php";

if (!isset($_GET["email"])) {
	error_api("email required", 400);
	exit;
}

if (!is_string($_GET["email"])) {
	error_api("email must be a string", 400);
	exit;
}

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


$pdo = DB::pdo();
$st = $pdo->prepare(
	"SELECT `id`,`email`,`phone`,`name`,`position`,`company_name`,`payment_amount` AS `ticket_price` FROM `participants` WHERE `email` LIKE :email LIMIT 1;"
);
$st->execute([":email" => $_GET["email"]]);
if ($u = $st->fetch(PDO::FETCH_ASSOC)):
	if (sendMail($u)) {
		$pdo->prepare("UPDATE `participants` SET `payment_instruction_email_sent` = '1' WHERE `id` = :participant_id LIMIT 1;")
			->execute([":participant_id" => $u["id"]]);
		$status = "success";
	} else {
		$status = "failed";
	}
	print json_encode(
		[
			"status" => $status
		]
	);
else:
	print json_encode(
		[
			"status" => "error",
			"message" => "Email not found!"
		]
	);
endif;
