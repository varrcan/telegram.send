<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;

/**
 * Class telegram_send
 */
Class telegram_send extends CModule
{
    const MODULE_ID = 'telegram.send';
    public $MODULE_ID;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    private $errors;

    /**
     * telegram_send constructor.
     */
    public function __construct()
    {
        $arModuleVersion = [];
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_NAME = "Телеграм";
        $this->MODULE_DESCRIPTION = "Отправка почтовых сообщений в телеграм";
        $this->MODULE_ID = 'telegram.send';
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = 'Varrcan';
        $this->PARTNER_URI = 'https://varrcan.me';
    }

    /**
     * Действия при установке модуля
     * @return bool|void
     */
    public function doInstall()
    {
        global $USER;
        if ($USER->IsAdmin()) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallEvents();
            $this->InstallFiles();
        }
    }

    /**
     * Регистрация событий
     * @return bool|void
     */
    public function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            'main',
            'OnBeforeEventSend',
            $this->MODULE_ID,
            'Telegram\\Send\\Main',
            'getEventSend'
        );
    }

    /**
     * Копирование файлов
     *
     * @param array $arParams
     */
    public function InstallFiles($arParams = [])
    {
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . self::MODULE_ID)) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . self::MODULE_ID);
        }
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin',
            true,
            true
        );
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/js/',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/',
            true,
            true
        );
    }

    /**
     * Действия при удалении модуля
     */
    public function doUninstall()
    {
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UnInstallDB();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    /**
     * Удаление событий
     * @return bool|void
     */
    public function UnInstallEvents()
    {

        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnBeforeEventSend',
            $this->MODULE_ID,
            'Telegram\\Send\\Main',
            'getEventSend'
        );
    }

    /**
     * Удаление файлов
     * @return bool|void
     */
    public function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/admin/telegram_main.php");
        DeleteDirFilesEx("/bitrix/js/telegram.send/");
    }

    /**
     * Удаление таблицы настроек
     *
     * @param array $arParams
     *
     * @return bool|void
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function UnInstallDB($arParams = [])
    {
        Application::getConnection()->query("DELETE FROM b_option WHERE `MODULE_ID`='{$this->MODULE_ID}'");
    }
}
