<?php

namespace tests\API;

use tests\Curl;
use PHPUnit\Framework\TestCase;

static $testToken = NULL;

/**
 * Class ini digunakan untuk mengetest API /coacher_register.php
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \test\API
 */
class CoacherRegisterTest extends TestCase
{	
	use Curl;

	/**
	 * @return void
	 */
	public function testGetToken(): void
	{
		global $testToken;
		$o = $this->curl("http://localhost:8080/coacher_register.php?action=get_token");
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
				"photo" => "https://photo.com/aqweqwe.jpg",
				"experience" => "I have been developed a messenger chat platform with high encryption.",
				"topic" => "High encryption in messenger chat",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"last_education" => "Chemical engineering ITB",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"sponsor_type" => "gold"
			], true],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"photo" => "https://photo.com/aqweqwe.jpg",
				"experience" => "I have been developed a messenger chat platform with high encryption.",
				"topic" => "High encryption in messenger chat",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"last_education" => "Chemical engineering ITB",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"sponsor_type" => "silver"
			], true],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"photo" => "https://photo.com/aqweqwe.jpg",
				"experience" => "I have been developed a messenger chat platform with high encryption.",
				"topic" => "High encryption in messenger chat",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"last_education" => "Chemical engineering ITB",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"sponsor_type" => "media_partner"
			], true],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"photo" => "https://photo.com/aqweqwe.jpg",
				"experience" => "I have been developed a messenger chat platform with high encryption.",
				"topic" => "High encryption in messenger chat",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"last_education" => "Chemical engineering ITB",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"sponsor_type" => "platinum"
			], true],
		];
	}

	/**
	 * @return array
	 */
	private function invalidInput(): array
	{
		return [
			[[
				"name" => "Ammar Faizi",
				"company_name" => "~~Tea Inside",
				"photo" => "https://photo.com/aqweqwe.jpg",
				"experience" => "I have been developed a messenger chat platform with high encryption.",
				"topic" => "High encryption in messenger chat",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"last_education" => "Chemical engineering ITB",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"sponsor_type" => "platinum"
			], false, "/Field \`company_name\` must be a valid company/"],
			[[
				"name" => "~~Ammar Faizi",
				"company_name" => "Tea Inside",
				"photo" => "https://photo.com/aqweqwe.jpg",
				"experience" => "I have been developed a messenger chat platform with high encryption.",
				"topic" => "High encryption in messenger chat",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"last_education" => "Chemical engineering ITB",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"sponsor_type" => "platinum"
			], false, "/Field \`name\` must be a valid person/"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"photo" => "htt~ps://~phot~o.com/aqweqwe.jpg",
				"experience" => "I have been developed a messenger chat platform with high encryption.",
				"topic" => "High encryption in messenger chat",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"last_education" => "Chemical engineering ITB",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"sponsor_type" => "platinum"
			], false, "/\`photo\` must be a valid URL/"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "~~Tea Inside",
				"photo" => "https://photo.com/aqweqwe.jpg",
				"experience" => "I have been developed a messenger chat platform with high encryption.",
				"topic" => "High encryption in messenger chat",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"last_education" => "Chemical engineering ITB",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"sponsor_type" => "platinum"
			], false, "/Field \`company_name\` must be a valid company/"],
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
		return $this->curl("http://localhost:8080/coacher_register.php?action=submit", $opt);
	}

	/**
	 * @return void
	 */
	public function testClose(): void
	{
		$this->assertTrue(file_exists($f = BASEPATH."/php_server.pid"));
	}
}
