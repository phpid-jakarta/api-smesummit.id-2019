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
		return $r;
	}
}
