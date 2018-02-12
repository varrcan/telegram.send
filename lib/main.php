<?php

namespace Telegram\Send;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\SiteTable;

/**
 * Class Main
 * @package Telegram\Send
 */
class Main {

	/**
	 * Действие после отправки письма
	 *
	 * @param $arFields
	 * @param $arTemplate
	 */
	static function getEventSend(&$arFields, &$arTemplate) {
		if ($arFields && $arTemplate && Config::statusModule() == 1) {
			if (in_array($arTemplate['EVENT_NAME'], Config::getMail())) {
				try {
					$message = self::delTags($arTemplate['MESSAGE']);
					foreach ($arFields + self::getSiteParam() as $key => $field) {
						$message = preg_replace('/#' . $key . '#/', $field, $message);
					}
					(new Sending)->processMessage($message);
				} catch (\Exception $e) {
					Debug::writeToFile($e->getMessage(), 'getEventSend', 'telegram-log');
				}
			}
		}
	}

	/**
	 * Удаление html тегов из письма
	 * @param $text
	 *
	 * @return mixed
	 */
	static function delTags($text) {
		return str_replace('&nbsp;', ' ', preg_replace('/\s{2,}/', "\n", strip_tags($text)));
	}

	/**
	 * Получение стандартных полей письма
	 *
	 * @return array
	 */
	static function getSiteParam() {
		$defaultParam = [];
		$getParam = SiteTable::getList([
			'select' => ['EMAIL', 'NAME', 'SERVER_NAME'],
			'filter' => ['ACTIVE' => 'Y']
		]);
		$siteParam = $getParam->fetch();
		if ($siteParam) {
			$defaultParam = [
				'DEFAULT_EMAIL_FROM' => $siteParam['EMAIL'],
				'SITE_NAME'          => $siteParam['NAME'],
				'SERVER_NAME'        => $siteParam['SERVER_NAME']
			];
		}

		return $defaultParam;
	}

} //
