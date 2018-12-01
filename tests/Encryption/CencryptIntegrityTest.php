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
 * @package \Encryption
 */
class CencryptIntegrityTest extends TestCase
{
	/**
	 * @return void
	 */
	public function test1(): void
	{
		for ($i=1; $i <= 10000; $i++) { 
			$str = rstr($i);
			$key = rstr(72);

			$encrypted = Cencrypt::encrypt($str, $key);

			$this->assertEquals(
				Cencrypt::decrypt($encrypted, $key),
				$str
			);
		}
	}
}
