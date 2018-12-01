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
		shell_exec("sh -c {$arg}");
	}

	/**
	 * @return void
	 */
	public function testGetToken(): void
	{
		$this->curl("http://localhost:");
	}
}
