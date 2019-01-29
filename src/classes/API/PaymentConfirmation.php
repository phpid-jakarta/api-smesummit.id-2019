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
	 * @var int
	 */
	private $userId;

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
					":email_user_id" => $this->userId,
					":phone" => $i["phone"],
					":total_payment" => $i["total_payment"],
					":payment_type" => $i["payment_type"],
					":date_transfer" => $i["date_transfer"],
					":no_ref" => $i["no_ref"],
					":bank_name" => $i["bank_name"],
					":bank_username" => $i["bank_username"],
					":screenshot" => $i["screenshot"],
					":status" => $i["status"],
					":created_at" => date("Y-m-d H:i:s")
				]
			);
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
		$st = $pdo->prepare("SELECT `id` FROM `participants` WHERE `email` LIKE :email LIMIT 1;");
		$st->execute([":email" => $i["email"]]);
		if (!($st = $st->fetch(PDO::FETCH_NUM))) {
			error_api(
				"{$m} Email \"{$i["email"]}\" is not registered in our database. Please register as participants before confirm the payment.",
				400
			);
			return;
		}

		$this->userId = (int) $st[0];

		

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
}
