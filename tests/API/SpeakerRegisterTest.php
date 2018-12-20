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
class SpeakerRegisterTest extends TestCase
{	
	use Curl;

	/**
	 * @return void
	 */
	public function testGetToken(): void
	{
		global $testToken;
		$o = $this->curl("http://localhost:8080/speaker_register.php?action=get_token");
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
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "085867152777",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], true],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "@ammarfaizi2",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], true],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "@ammar_fa_izi2",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], true],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "@ammar_fa_izi2",
				"sector" => "Industry/Digital Marketing",
				"topic" => "PHP Unit"
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
				"name" => "~~Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "@ammarfaizi2",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], false, "/Field \`name\` must be a valid person/"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "~~Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "@ammarfaizi2",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], false, "/Field \`company_name\` must be a valid company/"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "!!!Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "@ammarfaizi2",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], false, "/Field \`position\` must be a valid position/"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfa@izi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "@ammarfaizi2",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], false, "/is not a valid email address/"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "a short desc",
				"phone" => "@ammarfaizi2",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], false, "/is too short. Please provide a description at least 20 bytes\./"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "1234",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], false, "/Invalid phone number/"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "!@ammarfaizi2",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], false, "/Invalid telegram username: Telegram username must be started with/"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "@___aaaa",
				"sector" => "Industry",
				"topic" => "PHP Unit"
			], false, "/Invalid telegram username/"],
			[[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Owner",
				"email" => "ammarfaizi2@gmail.com",
				"photo" => "https://www.site.com/photo.jpg",
				"last_education" => "Chemical engineering ITB",
				"experience" => "I have been developed a messenger platform with high security encryption",
				"phone" => "@ammarfaizi2",
				"sector" => "~Industry",
				"topic" => "PHP Unit"
			], false, "/Field \`sector\` must be a valid sector/"],
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
		return $this->curl("http://localhost:8080/speaker_register.php?action=submit", $opt);
	}

	/**
	 * @return void
	 */
	public function testClose(): void
	{
		$this->assertTrue(file_exists($f = BASEPATH."/php_server.pid"));
	}
}
