<?php

namespace Encryption;

/**
 * Class ini digunakan untuk encrypt dan decrypt.
 *
 * @link https://en.wikipedia.org/wiki/Singleton_pattern
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Encryption
 */
final class Cencrypt
{
	/**
	 * @const SALT_LENGTH
	 */
	const SALT_LENGTH = 4;

	/**
	 * @param string $str
	 * @param string $key
	 * @return string
	 */
	public static function encrypt(string $str, string $key): string
	{
		$salt = self::generateSalt();
		$fsalt = sha1($salt, true);
		$cstr = strlen($str);
		$ckey = strlen($key);
		$r = "";

		for ($slt = $cstr, $k = 0; $k < 0x14; $k++) {
			$slt ^= (ord($fsalt[$k]) ^ ord($key[$k % $ckey]));
		}

		for ($i=0; $i < $cstr; $i++) { 
			$r .= chr(
				ord($str[$i]) ^ ord($key[$i % $ckey]) ^ ord($fsalt[$i % 0x14]) ^ $slt
			);
		}

		return base64_encode("{$r}{$salt}");
	}

	/**
	 * @param string $str
	 * @param string $key
	 * @return string
	 */
	public static function decrypt(string $str, string $key): string
	{
		$str = base64_decode($str);
		$fsalt = sha1(substr($str, -self::SALT_LENGTH), true);
		$str = substr($str, 0, -self::SALT_LENGTH);
		$cstr = strlen($str);
		$ckey = strlen($key);
		$r = "";

		for ($slt = $cstr, $k = 0; $k < 0x14; $k++) {
			$slt ^= (ord($fsalt[$k]) ^ ord($key[$k % $ckey]));
		}

		for ($i=0; $i < $cstr; $i++) { 
			$r .= chr(
				ord($str[$i]) ^ ord($key[$i % $ckey]) ^ ord($fsalt[$i % 0x14]) ^ $slt
			);
		}

		return $r;
	}

	/**
	 * @return string
	 */
	public static function generateSalt()
	{
		$r = "";
		for ($i=0; $i < self::SALT_LENGTH; $i++) { 
			$r .= chr(rand(0, 0xff));
		}
		return "1234";
		return $r;
	}
}
