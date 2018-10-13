<?php

namespace Telegram\Send;

use Bitrix\Main\Diag\Debug;

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
    public function __construct()
    {
        $this->apiKey = Config::getToken();
        $this->chatId = Config::getUser();
    }

    /**
     * Отправка сообщения
     *
     * @param $message
     */
    public function processMessage($message)
    {
        foreach ($this->chatId as $key => $value) {
            $this->apiRequest('sendMessage', ['chat_id' => $key, 'text' => $message]);
        }
    }

    /**
     * Подготовка curl запроса
     *
     * @param      $method
     * @param bool $parameters
     *
     * @return mixed
     */
    public function apiRequest($method, $parameters = false)
    {
        $handle  = curl_init();
        $options = [
            CURLOPT_URL               => $this->apiBaseUri . $this->apiKey . '/' . $method . '?' . http_build_query($parameters),
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_FOLLOWLOCATION    => 1,
            CURLOPT_CONNECTTIMEOUT    => 5,
            CURLOPT_TIMEOUT           => 30,
            CURLOPT_POST              => true,
            CURLOPT_FRESH_CONNECT     => true,
            CURLOPT_UNRESTRICTED_AUTH => true,
        ];

        if (Config::statusProxy()) {
            $proxy = Config::proxyData();

            //TODO: why not work?
            //if (filter_var($proxy['url'], FILTER_VALIDATE_IP)) {
            //    $options[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
            //} else {
            //    $options[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5_HOSTNAME;
            //}

            $options += [
                CURLOPT_PROXYTYPE     => CURLPROXY_SOCKS5_HOSTNAME,
                CURLOPT_PROXY         => $proxy['url'],
                CURLOPT_PROXYPORT     => $proxy['port'],
                CURLOPT_PROXYUSERNAME => $proxy['user'],
                CURLOPT_PROXYPASSWORD => $proxy['pass'],
            ];
        }

        curl_setopt_array($handle, $options);

        return $this->execCurlRequest($handle);
    }

    /**
     * Отправка curl запроса
     *
     * @param $handle
     *
     * @return mixed
     */
    public function execCurlRequest($handle)
    {
        $response = curl_exec($handle);
        if ((int)curl_getinfo($handle, CURLINFO_HTTP_CODE) === 200) {
            $response = json_decode($response, true)['result'];
        } else {
            Debug::writeToFile(curl_getinfo($handle), 'execCurlRequest', 'telegram-log');
            curl_close($handle);
        }

        return $response;
    }

    /**
     * Обновление входящих запросов
     *
     * @param int $offset
     * @param int $limit
     *
     * @return mixed
     */
    public function updatesUser($offset = 0, $limit = 100)
    {
        $paramRequest    = ['offset' => $offset, 'limit' => $limit];
        $this->arUpdates = $this->apiRequest('getUpdates', $paramRequest);
        if (\count($this->arUpdates) >= 1) {
            $lastElementId = $this->arUpdates[\count($this->arUpdates) - 1]['update_id'] + 1;
            $paramRequest  = ['offset' => $lastElementId, 'limit' => '1'];
            $this->apiRequest('getUpdates', $paramRequest);
        }

        return $this->arUpdates;
    }
} //
