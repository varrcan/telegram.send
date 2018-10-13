<?php

namespace Telegram\Send;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Mail\Internal\EventTypeTable;

/**
 * Class Config
 * @package Telegram\Send
 */
class Config
{
    public static $module_id = 'telegram.send';
    public static $request;
    public static $response = [
        'updates' => false,
        'message' => false
    ];

    /**
     * Request
     */
    public static function processRequest()
    {
        self::$request = Context::getCurrent()->getRequest();
        $funcName = self::$request->getPost('funcName');
        if ($funcName) {
            self::$funcName();
        }
    }

    /**
     * Получение всех почтовых шаблонов
     * @return array
     */
    public static function getMailTemplates():array
    {
        $getRow = EventTypeTable::getList([
            'select' => ['ID', 'EVENT_NAME', 'NAME'],
            'filter' => ['=LID' => LANGUAGE_ID],
            'order'  => ['EVENT_NAME' => 'ASC']
        ]);

        return $getRow->fetchAll();
    }

    /**
     * Получение входящих запросов
     */
    public static function getUpdates()
    {
        $arReturn = [];
        $arUpdates = (new Sending)->updatesUser()[0];
        if ($arUpdates['message']['text'] === '/start' && $arUpdates['message']['chat']['id']) {
            $arUsers = self::getUser();
            if (!array_key_exists($arUpdates['message']['chat']['id'], $arUsers)) {
                $arReturn = $arUpdates['message']['chat'];
            }
        }
        if (!$arReturn) {
            self::$response['message'] = self::setNote('Входящих запросов нет', 'ERROR');
        }
        self::$response['updates'] = $arReturn;
        self::sendResponse();
    }

    /**
     * Добавленные пользователи
     * @return mixed
     */
    public static function getUser()
    {
        return unserialize(Option::get(self::$module_id, 'user'));
    }

    /**
     * Генерация уведомления
     *
     * @param $message
     * @param $type "ERROR"|"OK"|"PROGRESS"
     *
     * @return string
     */
    public static function setNote($message, $type):string
    {
        return (new \CAdminMessage(['MESSAGE' => $message, 'TYPE' => $type]))->Show();
    }

    /**
     * Отправка json ответа
     */
    public static function sendResponse()
    {
        header('Content-Type: application/json');
        die(json_encode(self::$response));
    }

    /**
     * Добавление пользователя
     */
    public static function setUser()
    {
        $fields = self::$request->getPost('fields');
        if ($fields) {
            $savedUser = self::getUser();
            if (!array_key_exists($fields['id'], $savedUser)) {
                $newUser = [
                    $fields['id'] => [
                        'nickname' => $fields['nickname'],
                        'username' => $fields['username']
                    ]
                ];
                if ($savedUser) {
                    $newUser += $savedUser;
                }
                self::setOption('user', $newUser);
                self::$response['message'] = self::setNote('Пользователь добавлен', 'OK');
            } else {
                self::$response['message'] = self::setNote('Пользователь уже существует', 'ERROR');
            }
        }
        self::sendResponse();
    }

    /**
     * Запись данных в базу
     *
     * @param      $name
     * @param      $option
     * @param bool $serialize
     */
    public static function setOption($name, $option, $serialize = true)
    {
        Option::set(self::$module_id, $name, $serialize ? serialize($option) : $option);
    }

    /**
     * Удаление пользователя
     */
    public static function deleteUser()
    {
        $fields = self::$request->getPost('fields');
        if ($fields) {
            $savedUser = self::getUser();
            if (array_key_exists($fields['id'], $savedUser)) {
                unset($savedUser[$fields['id']]);
                self::setOption('user', $savedUser);
                self::$response['message'] = self::setNote('Пользователь удален', 'OK');
            } else {
                self::$response['message'] = self::setNote('Этот пользователь уже удален', 'ERROR');
            }
        }
        self::sendResponse();
    }

    /**
     * Сохранение настроек
     */
    public static function saveConfig()
    {
        $fields = self::$request->getPost('fields');
        if ($fields) {
            foreach ($fields as $field => $value) {
                if (\is_array($value)) {
                    self::setOption($field, $value);
                } else {
                    self::setOption($field, $value, false);
                }
            }
        }
        if ($fields['module_on'] === '1') {
            self::$response['message'] = self::setNote('Настройки сохранены', 'OK');
        } else {
            self::$response['message'] = self::setNote('Модуль отключен', 'ERROR');
        }
        self::sendResponse();
    }

    /**
     * Активность модуля
     * @return string
     */
    public static function statusModule():string
    {
        return Option::get(self::$module_id, 'module_on');
    }

    /**
     * Токен бота
     * @return string
     */
    public static function getToken():string
    {
        return Option::get(self::$module_id, 'token');
    }

    /**
     * Почтовые шаблоны
     * @return mixed
     */
    public static function getMail()
    {
        return unserialize(Option::get(self::$module_id, 'mail'));
    }

    /**
     * Активность прокси
     * @return mixed
     */
    public static function statusProxy()
    {
        return Option::get(self::$module_id, 'proxy_on');
    }

    /**
     * Данные прокси
     * @return mixed
     */
    public static function proxyData()
    {
        return unserialize(Option::get(self::$module_id, 'proxy'));
    }
} //
