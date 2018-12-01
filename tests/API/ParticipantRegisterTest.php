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
 * @link https://en.wikipedia.org/wiki/Singleton_pattern
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
	 * @return void
	 */
	public function testSubmit(): void
	{
		$o = $this->submit(
			[
				"name" => "Ammar Faizi",
				"company_name" => "Tea Inside",
				"position" => "Founder",
				"company_sector" => "Chemistry",
				"email" => "ammarfaizi2@gmail.com",
				"phone" => "085867152777",
				"problem_desc" => "blablablah aaaa bbbb cccc dddd eeee ffff"
			]
		);
		$this->assertTrue(isset($o["info"]["http_code"]));
		$this->assertEquals($o["info"]["http_code"], 200);
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
		$pid = (int)file_get_contents(BASEPATH."/php_server.pid");
		shell_exec("kill -TERM {$pid} 2>&1");
	}
}
