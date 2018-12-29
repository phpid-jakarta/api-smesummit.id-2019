<?php

namespace tests\API;

use tests\Curl;
use PHPUnit\Framework\TestCase;

static $testToken = NULL;

/**
 * Class ini digunakan untuk mengetest API /sponsor_register.php
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \test\API
 */
class SponsorRegisterTest extends TestCase
{	
	use Curl;

	/**
	 * @return void
	 */
	public function testGetToken(): void
	{
		global $testToken;
		$o = $this->curl("http://localhost:8080/sponsor_register.php?action=get_token");
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
				"company_name" => "Tea Inside",
				"company_logo" => "https://site.com/company_logo.jpg",
				"company_sector" => "Chemistry",
				"email_pic" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"sponsor_type" => "gold"
			], true],
			[[
				"company_name" => "PHP LTM Group",
				"company_logo" => "https://site.com/company_logo.jpg",
				"company_sector" => "Food and Drink",
				"email_pic" => "septianhari@gmail.com",
				"phone" => "085123123123",
				"sponsor_type" => "silver"
			], true],
			[[
				"company_name" => "PHP LTM Group",
				"company_logo" => "https://site.com/company_logo.jpg",
				"company_sector" => "Food and Drink",
				"email_pic" => "septianhari@gmail.com",
				"phone" => "085123123123",
				"sponsor_type" => "platinum"
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
				"company_name" => "~~PHP LTM Group",
				"company_logo" => "https://site.com/company_logo.jpg",
				"company_sector" => "Food and Drink",
				"email_pic" => "septianhari@gmail.com",
				"phone" => "085123123123",
				"sponsor_type" => "gold"
			], false, "/Field `company_name` must be a valid company/"],
			[[
				"company_name" => "PHP LTM Group",
				"company_logo" => "https://site.com/company_logo.jpg",
				"company_sector" => "Food and Drink",
				"email_pic" => "septianh@ari@gmail.com",
				"phone" => "085123123123",
				"sponsor_type" => "gold"
			], false, "/is not a valid email address/"],
			[[
				"company_name" => "PHP LTM Group",
				"company_logo" => "https://site.com/company_logo.jpg",
				"company_sector" => "Food and Drink",
				"email_pic" => "septianhari@gmail.com",
				"phone" => "9999",
				"sponsor_type" => "gold"
			], false, "/Invalid phone number/"],
			[[
				"company_name" => "PHP LTM Group",
				"company_logo" => "https://site.com/company_logo.jpg",
				"company_sector" => "Food and Drink",
				"email_pic" => "septianhari@gmail.com",
				"phone" => "085123123123",
				"sponsor_type" => "qweqwe"
			], false, "/is not a valid sponsor type\!/"]
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

		var_dump($o["out"]);

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
		return $this->curl("http://localhost:8080/sponsor_register.php?action=submit", $opt);
	}

	/**
	 * @return void
	 */
	public function testClose(): void
	{
		$this->assertTrue(file_exists($f = BASEPATH."/php_server.pid"));
	}
}
