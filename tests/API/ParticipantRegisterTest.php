<?php

namespace tests\API;

use tests\Curl;
use PHPUnit\Framework\TestCase;

$arg = escapeshellarg(PHP_BINARY." ".BASEPATH."/server.php >> /dev/null 2>&1 &");
print shell_exec("sh -c {$arg}");
sleep(2);

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
			], false],
			[[
				"name" => "Septian Hari Nugroho",
				"company_name" => "~~PHP LTM Group",
				"position" => "Founder",
				"company_sector" => "Food and Drink",
				"email" => "septianhari@gmail.com",
				"phone" => "085123123123",
				"problem_desc" => "nganu abc qwe asd zxc asd qwe ert dfg cvb"
			], false]
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
	 * @param array $form
	 * @param bool  $isValid
	 * @return void
	 */
	public function testSubmit(array $form, bool $isValid): void
	{
		$o = $this->submit($form);
		var_dump($o);die;
		$this->assertTrue(isset($o["info"]["http_code"]));
		$this->assertEquals($o["info"]["http_code"], ($isValid ? 200 : 400));
	}

	/**
	 * @return array
	 */
	private function submit(array $form): array
	{
		global $testToken;
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
