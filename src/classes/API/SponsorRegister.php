<?php

namespace API;

use DB;
use API;
use PDOException;
use Contracts\APIContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \API
 */
class SponsorRegister implements APIContract
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
				"INSERT INTO `sponsors` (`company_name`, `company_sector`, `email_pic`, `phone`, `sponsor_type`, `created_at`) VALUES (:company_name, :company_sector, :email_pic, :phone, :sponsor_type, :created_at);"
			);
			$st->execute(
				[
					":company_name" => $i["company_name"],
					":company_sector" => $i["company_sector"],
					":email_pic" => $i["email_pic"],
					":phone" => $i["phone"],
					":sponsor_type" => $i["sponsor_type"],
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
			"company_name",
			"company_sector",
			"email_pic",
			"phone",
			"sponsor_type",
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

		if (!preg_match("/^[a-z0-9\-\.\'\s]{3,255}$/i", $i["company_name"])) {
			error_api("{$m} Field `company_name` must be a valid company", 400);
			return;
		}

		if (!preg_match("/^[a-z0-9\-\.\'\s]{3,255}$/i", $i["company_sector"])) {
			error_api("{$m} Field `company_sector` must be a valid company sector", 400);
			return;
		}

		if (filter_var($i["company_logo"], FILTER_VALIDATE_URL)) {
			error_api("{$m} `company_logo` must be a valid URL");
		}

		if (strlen($i["company_logo"]) > 255) {
			
		}

		if (!filter_var($i["email_pic"], FILTER_VALIDATE_EMAIL)) {
			error_api("{$m} \"{$i["email_pic"]}\" is not a valid email address", 400);
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

		if (!in_array($i["sponsor_type"], ["platinum", "silver", "gold"])) {
			error_api("{$m} \"{$i["sponsor_type"]}\" is not a valid sponsor type!", 400);
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
}
