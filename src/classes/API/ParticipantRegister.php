<?php

namespace API;

use DB;
use PDO;
use API;
use PDOException;
use Contracts\APIContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \API
 */
class ParticipantRegister implements APIContract
{
	const DEFAULT_TICKET_PRICE = 500000;

	/**
	 * @var string
	 */
	private $action;

	/**
	 * @var string
	 */
	private $captcha;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if (!isset($_GET["action"])) {
			error_api("Bad Request: Invalid action", 400);
		}

		$this->action = $_GET["action"];
	}
	
	/**
	 * @return void
	 */
	public function run(): void
	{
		switch ($this->action) {
			case "get_token":
				$this->getToken();
				break;
			case "submit":
				$this->submit();
				break;
			case "voucher":
				$this->voucher();
				break;
			default:
				break;
		}
	}

	/**
	 * @return void
	 */
	private function voucher(): void
	{
		if ($_SERVER["REQUEST_METHOD"] !== "POST") {
			error_api("Method not allowed", 405);
		}

		$i = json_decode(file_get_contents("php://input"), true);
		if (!is_array($i)) {
			error_api("Invalid request body");
			return;
		}

		API::validateToken();


		if (!isset($i["voucher"])) {
			error_api("Invalid request", 400);
			return;
		}

		if (!is_string($i["voucher"])) {
			error_api("`voucher` must be a string", 400);
			return;
		}

		$pdo = DB::pdo();
		$st = $pdo->prepare("SELECT `discount_percent` FROM `vouchers` WHERE `code` = :code LIMIT 1;");
		$st->execute([":code" => $i["voucher"]]);
		if ($st = $st->fetch(PDO::FETCH_NUM)) {

			$ticketPrice = self::generatePrice();
			$st[0] = (double)$st[0];

			print API::json001("success",
				[
					"before_discount" => $ticketPrice,
					"after_discount" => ($ticketPrice - ($ticketPrice * $st[0] / 100)),
					"description" => "got discount {$st[0]}%"
				]
			);

		} else {
			error_api("Invalid voucher", 400);
		}

		exit(0);
	}

	/**
	 * @return void
	 */
	private function submit(): void
	{
		if ($_SERVER["REQUEST_METHOD"] !== "POST") {
			error_api("Method not allowed", 405);
		}
		
		$this->captcha = API::validateToken();

		// Validate input
		$i = json_decode(file_get_contents("php://input"), true);
		if (!is_array($i)) {
			error_api("Invalid request body");
			return;
		}
		$this->validateSubmitInput($i);
		$this->save($i);
	}

	/**
	 * @param float $percent_discount
	 * @return double
	 */
	private static function generatePrice(float $percent_discount = 0): float
	{
		$pdo = DB::pdo();

		$st = $pdo->prepare("SELECT `amount` FROM `price` WHERE `description` = 'early_bird' LIMIT 1;");
		$st->execute();
		if ($st = $st->fetch(PDO::FETCH_NUM)) {
			$p = (double)$st[0];
		} else {
			$p = self::DEFAULT_TICKET_PRICE;
		}


		return (double)($p - ($p * $percent_discount / 100));
	}

	/**
	 * @return void
	 */
	private function save(array &$i): void
	{
		try {
			$pdo = DB::pdo();
			$ticketPrice = self::generatePrice();
			$st = $pdo->prepare(
				"INSERT INTO `participants` (`name`, `company_name`, `company_sector`, `position`, `sector_to_be_coached`, `email`, `phone`, `problem_desc`, `ticket_price`, `created_at`) VALUES (:name, :company_name, :company_sector, :position, :sector_to_be_coached, :email, :phone, :problem_desc, :ticket_price, :created_at);"
			);
			$st->execute(
				[
					":name" => $i["name"],
					":company_name" => $i["company_name"],
					":sector_to_be_coached" => $i["coached_sector"],
					":position" => $i["position"],
					":company_sector" => $i["company_sector"],
					":email" => $i["email"],
					":phone" => $i["phone"],
					":problem_desc" => $i["problem_desc"],
					":payment_amount" => $ticketPrice,
					":created_at" => date("Y-m-d H:i:s")
				]
			);
			try {
				if (file_exists("/usr/sbin/sendmail")) {
					$u = [
						"email" => $i["email"],
						"phone" => $i["phone"],
						"name" => $i["name"],
						"position" => $i["position"],
						"company_name" => $i["company_name"],
						"ticket_price" => $ticketPrice
					];
					if ($this->sendMail($u)) {
						$pdo->prepare("UPDATE `participants` SET `payment_instruction_email_sent` = '1' WHERE `id` = :id LIMIT 1;")
						->execute(
							[
								":id" => $pdo->lastInsertId()
							]
						);
					}
				}
			} catch (\Error $e) {
				error_api("Internal Server Error: {$e->getMessage()}", 500);
			}

			print API::json001("success",
				[
					"message" => "register_success",
					// "payment_amount" => self::TICKET_PRICE
				]
			);
		} catch (PDOException $e) {
			// Close PDO connection.
			$st = $pdo = null;
			
			error_api("Internal Server Error: {$e->getMessage()}", 500);

			unset($e, $st, $pdo, $i);
			exit;
		}

		// Close PDO connection.
		$st = $pdo = null;
		unset($st, $pdo, $i);
	}

	/**
	 * @param array &$i
	 * @return void
	 */
	private function validateSubmitInput(array &$i): void
	{
		$m = "Bad Request:";
		$required = [
			"name",
			"company_name",
			"position",
			"company_sector",
			// "coached_sector",
			"email",
			"phone",
			"problem_desc",
			"captcha"
		];

		foreach ($required as $v) {
			if (!isset($i[$v])) {
				error_api("{$m} Field required: {$v}", 400);
				return;
			}
			if (!is_string($i[$v])) {
				error_api("{$m} Field `{$v}` must be a string", 400);
				return;
			}

			$i[$v] = trim($i[$v]);
		}

		if ($i["captcha"] !== $this->captcha) {
			error_api("{$m} Invalid captcha response", 400);
			return;
		}

		unset($required, $v);

		if (!preg_match("/^[a-z\.\'\s]{3,255}$/i", $i["name"])) {
			error_api("{$m} Field `name` must be a valid person", 400);
			return;
		}

		if (!preg_match("/^[a-z0-9\-\.\'\s]{3,255}$/i", $i["company_name"])) {
			error_api("{$m} Field `company_name` must be a valid company", 400);
			return;
		}

		if (!preg_match("/^[\\a-z0-9\-\.\'\s]{3,255}$/i", $i["position"])) {
			error_api("{$m} Field `position` must be a valid position", 400);
			return;
		}

		if (!preg_match("/^[a-z0-9\-\.\'\s\/\,]{3,255}$/i", $i["company_sector"])) {
			error_api("{$m} Field `company_sector` must be a valid sector", 400);
			return;
		}

		// if (!preg_match("/^[a-z0-9\-\.\'\s]{3,255}$/i", $i["coached_sector"])) {
		// 	error_api("{$m} Field `coached_sector` must be a valid sector", 400);
		// 	return;
		// }

		if (!isset($i["coached_sector"])) {
			$i["coached_sector"] = null;
		}


		if (!filter_var($i["email"], FILTER_VALIDATE_EMAIL)) {
			error_api("{$m} \"{$i["email"]}\" is not a valid email address", 400);
			return;
		}

		if (preg_match("/^[0-9\-\+]*$/", $i["phone"])) {
			if (!preg_match("/^[0\+]\d{4,13}$/", str_replace("-", "", $i["phone"]))) {
				error_api("{$m} Invalid phone number.", 400);	
				return;
			}
		} else {
			if (!preg_match("/^\@/", $i["phone"])) {
				error_api("{$m} Invalid telegram username: Telegram username must be started with '@' or enter your phone number instead", 400);
				return;
			}

			if (!preg_match("/^\@[a-z0-9][a-z0-9\_]{3,25}[a-z0-9]$/i", $i["phone"])) {
				error_api("{$m} Invalid telegram username", 400);
				return;
			}
		}

		$c = strlen($i["problem_desc"]);

		if ($c < 20) {
			error_api("{$m} `problem_desc` is too short. Please provide a description at least 20 bytes.", 400);
			return;
		}

		if ($c >= 1024) {
			error_api("{$m} `problem_desc` is too long. Please provide a description with size less than 1024 bytes.", 400);
			return;
		}

		unset($c, $i);
		return;
	}

	/**
	 * @return void
	 */
	private function getToken(): void
	{
		$expired = time()+3600;

		// By using this token, we don't need any session which saved at the server side.
		print API::json001(
			"success",
			[
				// Encrypted expired time and random code 32 bytes.
				"token" => cencrypt(json_encode(
					[
						"expired" => $expired,
						"code" => rstr(6, "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM")
					]
				), APP_KEY),

				// Show expired time.
				"expired" => $expired
			]
		);
	}

	/**
	 * @param $u array
	 * @return bool
	 */
	private function sendMail(array &$u): bool
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
}
