<?php

if (!function_exists("rstr")) {
	/**
	 * Membuat random string
	 *
	 * @param int 		$n	Panjang random string.
	 * @param string 	$e	Daftar karakter yang disiapkan untuk membuat random string. 
	 * @return string
	 */
	function rstr(int $n = 32, string $e = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890___...---"): string
	{

		// Menghindari infinite loop ketika memasukkan negative integer.
		$n = abs($n);

		for ($r = "", $c = strlen($e) - 1, $i=0; $i < $n; $i++) { 
			$r .= $e[rand(0, $c)];
		}

		return $r;
	}
}

if (!function_exists("error_api")) {
	/**
	 * @param mixed $errMsg
	 * @param int    $errCode
	 * @return void
	 */
	function error_api($errMsg, int $errCode): void
	{
		http_response_code($errCode);
		print API::json001(
			"error",
			[
				"message" => $errMsg,
				"error_code" => $errCode
			]
		);
		exit($errCode);
	}
}

if (!function_exists("cencrypt")) {
	/**
	 * @param string $str
	 * @param string $key
	 * @return string $key
	 */
	function cencrypt(string $str, string $key): string
	{
		
	}
}
