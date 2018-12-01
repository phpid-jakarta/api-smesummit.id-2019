<?php

namespace tests\Encryption;

use Encryption\Cencrypt;
use PHPUnit\Framework\TestCase;

/**
 * Class ini digunakan untuk mengetest integritas data pada encryption.
 *
 * @link https://en.wikipedia.org/wiki/Singleton_pattern
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \tests\Encryption
 */
class CencryptIntegrityTest extends TestCase
{
	public function testMain(): void
	{
		$this->t01();
		$this->t02();
	}

	/**
	 * @return void
	 */
	public function t01(): void
	{
		for ($i=1; $i <= 3; $i++) {
			for ($k=1; $k <= 2000; $k++) { 
				$str = rstr($k);
				$key = rstr(72 * $i);
				$encrypted = Cencrypt::encrypt($str, $key);
				$this->assertEquals(
					Cencrypt::decrypt($encrypted, $key),
					$str
				);
			}
		}
	}

	/**
	 * @return void
	 */
	public function t02(): void
	{
		for ($i=1; $i <= 3; $i++) {
			for ($k=1; $k <= 2000; $k++) { 
				$str = rstr($k);
				$key = rstr(72 * $i);
				$encrypted = Cencrypt::encrypt($str, $key);
				$this->assertTrue(
					Cencrypt::decrypt($encrypted, $key2 = rstr(32)) !==
					$str
				);
			}
		}
	}
}
