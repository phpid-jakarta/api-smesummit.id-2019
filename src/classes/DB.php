<?php

/**
 * Load database config.
 */
require BAESPATH."/config/database.php";

/**
 * Class ini digunakan untuk melakukan koneksi ke database dengan konsep Singleton Pattern.
 *
 * @link https://en.wikipedia.org/wiki/Singleton_pattern
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \
 */
final class DB
{
	/**
	 * Digunakan untuk menyimpan instance class \DB.
	 *
	 * Instance class \DB disimpan ke dalam static property agar koneksi instance \PDO
	 * tidak hilang sehingga bisa bertahan tanpa reconnect hingga eksekusi PHP selesai.
	 *
	 * @var \DB
	 */
	private static $instance;

	/**
	 * Digunakan untuk menyimpan instance PDO.
	 *
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		defined("PDO_PARAMETERS") or 
		exit("PDO_PARAMETERS is not defined yet!\n");

		$this->pdo = new PDO(...PDO_PARAMETERS);

		/**
		 * Set error mode ke PDO::ERRMODE_EXCEPTION
		 * agar PDO melemparkan exception ketika
		 * terjadi error query.
		 */
		$this->pdo->setAttribute(
			PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION
		);
	}

	/**
	 * @return \PDO
	 */
	public static function pdo(): PDO
	{
		return self::getInstance()->pdo;
	}

	/**
	 * @return \DB
	 */
	public static function getInstance(): DB
	{
		if (!(self::$instance instanceof DB)) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
