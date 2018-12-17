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
	// header("Access-Control-Allow-Origin: *");

	if ($_SERVER["REQUEST_METHOD"] === "HEAD") {
		http_response_code(200);
		exit;
	}
}
