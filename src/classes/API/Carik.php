<?php

namespace API;

use DB;
use PDO;
use API;
use Error;
use PDOException;
use Contracts\APIContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \API
 */
class Carik implements APIContract
{
	/**
	 * @return void
	 */
	public function run(): void
	{
		if (!isset($_GET["action"])) {
			error_api("Bad Request: \"action\" parameter required!", 400);
		}

		switch ($_GET["action"]) {
			case "count_participants":
				$this->countParticipants();
				break;
			default:
				error_api("Bad Request: Invalid action: \"{$_GET["action"]}\"", 400);
				break;
		}
	}

	/**
	 * @return void
	 */
	private function countParticipants()
	{
		try {
			$pdo = DB::pdo();
			$st = $pdo->prepare("SELECT COUNT(1) FROM `participants`;");
			$st->execute();
			$st = $st->fetch(PDO::FETCH_NUM);
		} catch (PDOException $e) {
			// Close PDO connection.
			$st = $pdo = null;
			
			error_api("Internal Server Error: {$e->getMessage()}", 500);

			unset($e, $st, $pdo, $i);
			exit;
		}

		print API::json001("success", ["registered_participants" => $st[0]]);
	}
}
