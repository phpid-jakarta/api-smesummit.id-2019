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
class PaymentConfirmation implements APIContract
{
	/**
	 * @var string
	 */
	private $action;

	/**
	 * @var string
	 */
	private $captcha;

	/**
	 * @var array
	 */
	private $userInfo = [];

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
			default:
				break;
		}
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
	 * @return void
	 */
	private function save(array &$i): void
	{
		try {
			$pdo = DB::pdo();
			$st = $pdo->prepare(
				"INSERT INTO `payment_confirmation` (`email_user_id`, `phone`, `total_payment`, `payment_type`, `date_transfer`, `no_ref`, `bank_name`, `bank_username`, `screenshot`, `status`, `created_at`) VALUES (:email_user_id, :phone, :total_payment, :payment_type, :date_transfer, :no_ref, :bank_name, :bank_username, :screenshot, :status, :created_at);"
			);
			$st->execute(
				[
					":email_user_id" => $this->userInfo["id"],
					":phone" => $i["phone"],
					":total_payment" => $i["total_payment"],
					":payment_type" => $i["payment_type"],
					":date_transfer" => $i["date_transfer"],
					":no_ref" => $i["no_ref"],
					":bank_name" => $i["bank_name"],
					":bank_username" => $i["bank_username"],
					":screenshot" => $i["screenshoot"],
					":status" => "pending",
					":created_at" => date("Y-m-d H:i:s")
				]
			);

			if (file_exists("/usr/sbin/sendmail")) {
				$this->sendMail($this->userInfo);
			}

			print API::json001("success",
				[
					"message" => "register_success"
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
			"email",
			"phone",
			"total_payment",
			"payment_type",
			"date_transfer",
			"no_ref",
			"bank_name",
			"bank_username",
			"screenshoot",
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

		$pdo = DB::pdo();
		$st = $pdo->prepare("SELECT `id`,`name`,`company_name`,`position`,`phone`,`email` FROM `participants` WHERE `email` LIKE :email LIMIT 1;");
		$st->execute([":email" => $i["email"]]);
		if (!($st = $st->fetch(PDO::FETCH_ASSOC))) {
			error_api(
				"{$m} Email \"{$i["email"]}\" is not registered in our database. Please register as participants before confirm the payment.",
				400
			);
			return;
		}

		$this->userInfo = $st;

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

		if (!in_array($i["payment_type"], ["participant", "sponsor", "coacher"])) {
			error_api("{$m} Invalid payment_type", 400);
			return;
		}

		$c = strlen($i["date_transfer"]);
		if (($c < 6) || ($c > 15)) {
			error_api("{$m} Invalid date_transfer", 400);
			return;
		}

		$c = strlen($i["no_ref"]);
		if (($c < 3) || ($c > 255)) {
			error_api("{$m} Invalid no_ref", 400);
			return;
		}

		$c = strlen($i["bank_name"]);
		if (($c < 3) || ($c > 64)) {
			error_api("{$m} Invalid bank_name", 400);
			return;
		}

		$c = strlen($i["bank_username"]);
		if (($c < 4) || ($c > 24)) {
			error_api("{$m} Invalid bank_username", 400);
			return;
		}

		unset($c, $i, $st);
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
	 * @param array &$u
	 * @return void
	 */
	private function sendMail(array &$u): void
	{
		$to = $u["email"];
		$subject = "SME Summit 2019 - Payment Instructions Email";
		ob_start();
		require BASEPATH."/mail_templates/payment_instruction.php";
		$message = ob_get_clean();

		// To send HTML mail, the Content-type header must be set
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=iso-8859-1';

		// Additional headers
		$headers[] = 'To: {$u["name"]} <{$u["email"]}>';
		$headers[] = "From: Payment SME SUMMIT <payment@smesummit.id>";		

		// Mail it
		mail($to, $subject, $message, implode("\r\n", $headers));
	}
}
