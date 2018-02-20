<?php

namespace Telegram\Send;

/**
 * Class Sending
 * @package Telegram\Send
 */
class Sending
{

	private $apiKey;
	private $chatId;
	private $arUpdates;
	private $apiBaseUri = 'https://api.telegram.org/bot';

	/**
	 * Sending constructor.
	 */
	public function __construct() {
		$this->apiKey = Config::getToken();
		$this->chatId = Config::getUser();
	}

	/**
	 * Отправка сообщения
	 * @param $message
	 */
	public function processMessage($message) {
		foreach ($this->chatId as $key => $value) {
			$this->apiRequest('sendMessage', ['chat_id' => $key, 'text' => $message]);
		}
	}

	/**
	 * Обновление входящих запросов
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public function updatesUser($offset = 0, $limit = 100) {
		$paramRequest = ['offset' => $offset, 'limit' => $limit];
		$this->arUpdates = $this->apiRequest('getUpdates', $paramRequest);

		if (count($this->arUpdates) >= 1) {
			$lastElementId = $this->arUpdates[count($this->arUpdates) - 1]['update_id'] + 1;
			$paramRequest = ['offset' => $lastElementId, 'limit' => '1'];
			$this->apiRequest('getUpdates', $paramRequest);
		}

		return $this->arUpdates;
	}

	/**
	 * Подготовка curl запроса
	 * @param      $method
	 * @param bool $parameters
	 *
	 * @return mixed
	 */
	public function apiRequest($method, $parameters = false) {
		$handle = curl_init();
		$options = [
			CURLOPT_URL            => $this->apiBaseUri . $this->apiKey . '/' . $method . '?' . http_build_query($parameters),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_POST           => true,
		];
		curl_setopt_array($handle, $options);

		return $this->execCurlRequest($handle);
	}

	/**
	 * Отправка curl запроса
	 * @param $handle
	 *
	 * @return mixed
	 */
	public function execCurlRequest($handle) {
		$response = curl_exec($handle);

		if (intval(curl_getinfo($handle, CURLINFO_HTTP_CODE)) == 200) {
			$response = json_decode($response, true)['result'];
		} else {
			curl_close($handle);
		}

		return $response;
	}
} //
