<?php

namespace tests\API;

use tests\Curl;
use PHPUnit\Framework\TestCase;

static $testToken = null;

/**
 * Class ini digunakan untuk mengetest API /volunteer_register.php
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \test\API
 */
class VolunteerRegisterTest extends TestCase
{
    use Curl;

    /**
     * @return void
     */
    public function testGetToken(): void
    {
        global $testToken;
        $o = $this->curl("http://localhost:8080/volunteer_register.php?action=get_token");
        $o = json_decode($o["out"], true);
        $this->assertTrue(
            isset(
                $o["status"],
                $o["data"],
                $o["data"]["token"],
                $o["data"]["expired"]
            )
        );
        $this->assertEquals($o["status"], "success");
        $testToken = $o["data"]["token"];
    }

    /**
     * @return array
     */
    private function validInput(): array
    {
        return [
            [[
                "name" => "Ammar Faizi",
                "email" => "ammarfaizi2@gmail.com",
                "phone" => "085867152777",
                "city" => "Solo",
                "ig_link" => "https://www.instagram.com/ammarfaizi12",
                "fb_link" => "https://www.facebook.com/ammarfaizi2",
                "why_you_apply_desc" => "I want to blabla qqqq bbbb"
            ], true],
            [[
                "name" => "Septian Hari Nugroho",
                "email" => "septianhari@gmail.com",
                "phone" => "085123123123",
                "why_you_apply_desc" => "I want to blabla qqqq bbbb"
            ], true]
        ];
    }

    /**
     * @return array
     */
    private function invalidInput(): array
    {
        return [
            [[
                "name" => "~~~ ```",
                "email" => "ammarfaizi2@gmail.com",
                "phone" => "085867152777",
                "why_you_apply_desc" => "I want to blabla qqqq bbbb"
            ], false, "/Field \`name\` must be a valid person/"],
            [[
                "name" => "Septian Hari Nugroho",
                "email" => "sep@tianhari@gmail.com",
                "phone" => "085123123123",
                "why_you_apply_desc" => "I want to blabla qqqq bbbb"
            ], false, "/is not a valid email address/"],
            [[
                "name" => "Septian Hari Nugroho",
                "email" => "septianhari@gmail.com",
                "phone" => "123123",
                "why_you_apply_desc" => "I want to blabla qqqq bbbb"
            ], false, "/Invalid phone number/"],
            [[
                "name" => "Septian Hari Nugroho",
                "email" => "septianhari@gmail.com",
                "phone" => "085123123123",
                "why_you_apply_desc" => "I want to nganu"
            ], false, "/\`why_you_apply_desc\` is too short\./"],
            [[
                "name" => "Septian Hari Nugroho",
                "email" => "septianhari@gmail.com",
                "phone" => "085123123123",
                "why_you_apply_desc" => "I want to nganu ".str_repeat("blabla", 1024)
            ], false, "/\`why_you_apply_desc\` is too long\./"],
            [[
                "name" => "Septian Hari Nugroho",
                "email" => "septianhari@gmail.com",
                "phone" => "085123123123",
                "city" => "*** 111",
                "why_you_apply_desc" => "I want to blabla qqqq bbbb"
            ], false, "/Field `city` must be a valid city name/"],
            [[
                "name" => "Ammar Faizi",
                "email" => "ammarfaizi2@gmail.com",
                "phone" => "085867152777",
                "city" => "Solo",
                "ig_link" => "~~~",
                "fb_link" => "https://www.facebook.com/ammarfaizi2",
                "why_you_apply_desc" => "I want to blabla qqqq bbbb"
            ], false, "/`fb_link` must be a valid URL/"],
            [[
                "name" => "Ammar Faizi",
                "email" => "ammarfaizi2@gmail.com",
                "phone" => "085867152777",
                "city" => "Solo",
                "ig_link" => "https://www.instagram.com/ammarfaizi12",
                "fb_link" => "~~~~~",
                "why_you_apply_desc" => "I want to blabla qqqq bbbb"
            ], false, "/`ig_link` must be a valid URL/"],
        ];
    }

    /**
     * @return array
     */
    public function listOfParticipants(): array
    {
        return array_merge([], $this->validInput(), $this->invalidInput());
    }

    /**
     * @dataProvider listOfParticipants
     * @param array  $form
     * @param bool   $isValid
     * @param string $mustMatch
     * @return void
     */
    public function testSubmit(array $form, bool $isValid, string $mustMatch = null): void
    {
        $o = $this->submit($form);

        $this->assertTrue(isset($o["info"]["http_code"]));
        $this->assertEquals($o["info"]["http_code"], ($isValid ? 200 : 400));

        if (!is_null($mustMatch)) {
            $this->assertTrue((bool)preg_match($mustMatch, $o["out"]));
        }
    }

    /**
     * @return array
     */
    private function submit(array $form): array
    {
        global $testToken;
        $me = json_decode(dencrypt($testToken, APP_KEY), true);
        $form["captcha"] = $me["code"];
        $opt = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($form),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$testToken}",
                "Content-Type: application/json"
            ]
        ];
        return $this->curl("http://localhost:8080/volunteer_register.php?action=submit", $opt);
    }

    /**
     * @return void
     */
    public function testClose(): void
    {
        $this->assertTrue(file_exists($f = BASEPATH."/php_server.pid"));
    }
}
