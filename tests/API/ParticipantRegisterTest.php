<?php

namespace tests\API;

use tests\Curl;
use PHPUnit\Framework\TestCase;

static $testToken = NULL;

/**
 * Class ini digunakan untuk mengetest API /participant_register.php
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \test\API
 */
class ParticipantRegisterTest extends TestCase
{	
	use Curl;

	/**
	 * @return void
	 */
	public function testGetToken(): void
	{
		global $testToken;
		$o = $this->curl("http://localhost:8080/participant_register.php?action=get_token");
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
				"company_name" => "Tea Inside",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"problem_desc" => "blablablah aaaa bbbb cccc dddd eeee ffff"
			], true],
			[[
				"name" => "Septian Hari Nugroho",
				"company_name" => "PHP LTM Group",
				"position" => "Founder",
				"company_sector" => "Food and Drink",
				"email" => "septianhari@gmail.com",
				"phone" => "085123123123",
				"problem_desc" => "nganu abc qwe asd zxc asd qwe ert dfg cvb"
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
				"name" => "!!!!!Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"problem_desc" => "blablablah aaaa bbbb cccc dddd eeee ffff"
			], false, "/Field `name` must be a valid person/"],
			[[
				"name" => "Septian Hari Nugroho",
				"company_name" => "~~PHP LTM Group",
				"position" => "Founder",
				"company_sector" => "Food and Drink",
				"email" => "septianhari@gmail.com",
				"phone" => "085123123123",
				"problem_desc" => "nganu abc qwe asd zxc asd qwe ert dfg cvb"
			], false, "/Field `company_name` must be a valid company/"],
			[[
				"name" => "Septian Hari Nugroho",
				"company_name" => "PHP LTM Group",
				"position" => "Founder",
				"company_sector" => "Food and Drink",
				"email" => "septianh@ari@gmail.com",
				"phone" => "085123123123",
				"problem_desc" => "nganu abc qwe asd zxc asd qwe ert dfg cvb"
			], false, "/is not a valid email address/"],
			[[
				"name" => "Septian Hari Nugroho",
				"company_name" => "PHP LTM Group",
				"position" => "Founder",
				"company_sector" => "Food and Drink",
				"email" => "septianhari@gmail.com",
				"phone" => "9999",
				"problem_desc" => "nganu abc qwe asd zxc asd qwe ert dfg cvb"
			], false, "/Invalid phone number/"],
			[[
				"name" => "Septian Hari Nugroho",
				"company_name" => "PHP LTM Group",
				"position" => "Founder",
				"company_sector" => "Food and Drink",
				"email" => "septianhari@gmail.com",
				"phone" => "avavav",
				"problem_desc" => "nganu abc qwe asd zxc asd qwe ert dfg cvb"
			], false, "/Invalid telegram username: Telegram username must be started with /"],
			[[
				"name" => "Septian Hari Nugroho",
				"company_name" => "PHP LTM Group",
				"position" => "Founder",
				"company_sector" => "Food and Drink",
				"email" => "septianhari@gmail.com",
				"phone" => "085123123123",
				"problem_desc" => "..."
			], false, "/`problem_desc` is too short\. Please provide a description at least 20 bytes\./"],
			[[
				"name" => "Septian Hari Nugroho",
				"company_name" => "PHP LTM Group",
				"position" => "Founder",
				"company_sector" => "Food and Drink",
				"email" => "septianhari@gmail.com",
				"phone" => "085123123123",
				"problem_desc" => str_repeat("q", 1025)
			], false, "/`problem_desc` is too long\. Please provide a description with size less than 1024 bytes\./"]
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
		return $this->curl("http://localhost:8080/participant_register.php?action=submit", $opt);
	}

	/**
	 * @return void
	 */
	public function testClose(): void
	{
		$this->assertTrue(file_exists($f = BASEPATH."/php_server.pid"));
	}
}
