<?php

namespace tests;

/**
 * Trait ini digunakan untuk menambahkan method curl.
 *
 * @link https://en.wikipedia.org/wiki/Singleton_pattern
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \test
 */
trait Curl
{	
	/**
	 * @param string $url
	 * @param array  $opt
	 * @return array
	 */
	public function curl(string $url, array $opt = []): array
	{
		$ch = curl_init($url);
		$optf = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		];
		foreach ($opt as $key => $value) {
			$optf[$key] = $value;
		}
		curl_setopt_array($ch, $optf);
		$out = curl_exec($ch);
		$info = curl_getinfo($ch);
		$err = curl_error($ch);
		$ern = curl_errno($ch);
		curl_close($ch);
		return [
			"out" => $out,
			"info" => $info,
			"error" => $err,
			"errno" => $ern
		];
	}
}
