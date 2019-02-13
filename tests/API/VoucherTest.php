<?php

namespace tests\API;

use tests\Curl;
use PHPUnit\Framework\TestCase;

static $testToken = null;

/**
 * Class ini digunakan untuk mengetest API /volunteer_register.php
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \test\API
 */
class VoucherTest extends TestCase
{
    use Curl;

	/**
	 * @return void
	 */
	public function testGetToken(): void
	{
		global $testToken;
		$o = $this->curl("http://localhost:8080/participant_register.php?action=get_token");
		$o = json_decode($o["out"], true);
		$this->assertTrue(
			isset(
				$o["status"],
				$o["data"],
				$o["data"]["token"],
				$o["data"]["expired"]
			)
		);
		$this->assertEquals($o["status"], "success");
		$testToken = $o["data"]["token"];
	}

	/**
	 * @return array
	 */
	private function validInput(): array
	{
		return [
			[
				[
					"voucher" => "PHPID20"
				],
				true,
				"/discount 20\%/"
			]
		];
	}

	/**
	 * @return array
	 */
	private function invalidInput(): array
	{
		return [
			[
				[
					"voucher" => "QWEQWE"
				],
				false,
				"/Invalid voucher/"
			]
		];
	}
	
	/**
	 * @return array
	 */
	public function listOfParticipants(): array
	{
		return array_merge([], $this->validInput(), $this->invalidInput());
	}

	/**
	 * @dataProvider listOfParticipants
	 * @param array  $form
	 * @param bool   $isValid
	 * @param string $mustMatch
	 * @return void
	 */
	public function testSubmit(array $form, bool $isValid, string $mustMatch = null): void
	{
		$o = $this->submit($form);

		$this->assertTrue(isset($o["info"]["http_code"]));
		$this->assertEquals($o["info"]["http_code"], ($isValid ? 200 : 400));

		if (!is_null($mustMatch)) {
			$this->assertTrue((bool)preg_match($mustMatch, $o["out"]));
		}
	}

	/**
	 * @return array
	 */
	private function submit(array $form): array
	{
		global $testToken;
		$opt = [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($form),
			CURLOPT_HTTPHEADER => [
				"Authorization: Bearer {$testToken}",
				"Content-Type: application/json"
			]
		];
		return $this->curl("http://localhost:8080/participant_register.php?action=voucher", $opt);
	}
}
