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
class VolunteerRegister implements APIContract
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
                "INSERT INTO `volunteers` (`name`, `email`, `phone`, `city`, `ig_link`, `fb_link`, `why_you_apply_desc`, `created_at`) VALUES (:name, :email, :phone, :city, :ig_link, :fb_link, :why_you_apply_desc, :created_at);"
            );
            $st->execute(
                [
                    ":name" => $i["name"],
                    ":email" => $i["email"],
                    ":phone" => $i["phone"],
                    ":city" => $i["city"],
                    ":ig_link" => $i["ig_link"],
                    ":fb_link" => $i["fb_link"],
                    ":why_you_apply_desc" => $i["why_you_apply_desc"],
                    ":created_at" => date("Y-m-d H:i:s")
                ]
            );
            
            print API::json001(
                "success",
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
            "name",
            "email",
            "phone",
            "why_you_apply_desc",
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

        if (!filter_var($i["email"], FILTER_VALIDATE_EMAIL)) {
            error_api("{$m} \"{$i["email"]}\" is not a valid email address", 400);
            return;
        }

        if (!preg_match("/^[0\+]\d{4,13}$/", $i["phone"])) {
            error_api("{$m} Invalid phone number", 400);
            return;
        }

        if (!isset($i["city"])) {
            $i["city"] = "";
        }

        if (!isset($i["fb_link"])) {
            $i["fb_link"] = "";
        }

        if (!isset($i["ig_link"])) {
            $i["ig_link"] = "";
        }

        if ($i["fb_link"] !== "") {
            if (!filter_var($i["fb_link"], FILTER_VALIDATE_URL)) {
                error_api("{$m} `fb_link` must be a valid URL", 400);
                return;
            }
        }

         if ($i["ig_link"] !== "") {
            if (!filter_var($i["ig_link"], FILTER_VALIDATE_URL)) {
                error_api("{$m} `ig_link` must be a valid URL", 400);
                return;
            }
        }

        if (!preg_match("/^[a-z\.\'\-\s]{0,255}$/i", $i["city"])) {
            error_api("{$m} Field `city` must be a valid city name", 400);
            return;
        }

        $c = strlen($i["why_you_apply_desc"]);

        if ($c < 20) {
            error_api("{$m} `why_you_apply_desc` is too short. Please provide a description at least 20 bytes.", 400);
            return;
        }

        if ($c >= 1024) {
            error_api("{$m} `why_you_apply_desc` is too long. Please provide a description with size less than 1024 bytes.", 400);
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
