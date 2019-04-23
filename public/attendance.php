<?php

require __DIR__."/../bootstrap/init.php";

if (isset($_POST["ticket_code"]) && is_string($_POST["ticket_code"])) {

	$pdo = DB::pdo();
	$st = $pdo->prepare("SELECT `participant_id` FROM `participants_ticket` WHERE `ticket_code` = :ticket_code LIMIT 1;");
	$st->execute([":ticket_code" => substr($_POST["ticket_code"], 3)]);
	if ($r = $st->fetch(PDO::FETCH_ASSOC)) {

		$st = $pdo->prepare("SELECT `created_at` FROM `participants_attendance` WHERE `participant_id` = :participant_id LIMIT 1;");
		$st->execute([":participant_id" => $r["participant_id"]]);

		if ($st->fetch(PDO::FETCH_ASSOC)) {
			print json_encode(
				[
					"status" => 200,
					"msg" => "This ticket has already been used!"
				]
			);
		} else {
			$pdo->prepare("INSERT INTO `participants_attendance` (`participant_id`,`created_at`) VALUES (:participant_id, :created_at);")
			->execute(
				[
					":participant_id" => $r["participant_id"],
					":created_at" => date("Y-m-d H:i:s")
				]
			);
			print json_encode(
				[
					"status" => 200,
					"msg" => "success"
				]
			);
		}
	} else {
		print json_encode(["status" => 200, "msg" => "Ticket \"{$_POST["ticket_code"]}\" does not exist"]);
	}

	unset($st, $pdo);
} else {
	http_response_code(400);
	print json_encode(["status" => 400, "msg" => "Bad Request"]);
}
