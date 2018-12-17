<?php

if (!defined("__INIT")) {
	define("__INIT", 1);

	require __DIR__."/../config/init.php";

	/**
	 * @param string $class
	 * @return void
	 */
	function myClassAutoloader(string $class): void
	{
		$class = str_replace("\\", "/", $class);
		if (file_exists($f = BASEPATH."/src/classes/{$class}.php")) {
			require $f;
		}
	}

	spl_autoload_register("myClassAutoloader");

	require BASEPATH."/src/helpers.php";
	header("Content-Type: application/json");
	header("Access-Control-Allow-Origin: https://www.smesummit.id");
	header("Access-Control-Allow-Headers: authorization,content-type");

	if (isset($_SERVER["REQUEST_METHOD"]) && in_array($_SERVER["REQUEST_METHOD"], ["OPTIONS", "HEAD"])) {
		http_response_code(200);
		exit;
	}
}
