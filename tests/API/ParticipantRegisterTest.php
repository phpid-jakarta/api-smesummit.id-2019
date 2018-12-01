<?php

namespace tests\API;

use tests\Curl;
use PHPUnit\Framework\TestCase;

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

	public function setUp(): void
	{
		$arg = escapeshellarg(PHP_BINARY." ".BASEPATH."/server.php >> /dev/null 2>&1 &");
		print shell_exec("sh -c {$arg}");
		sleep(1);
	}

	/**
	 * @return void
	 */
	public function testGetToken(): void
	{
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
	}
}
