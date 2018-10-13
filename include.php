<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

if (file_exists(__DIR__ . '/reinstalljs')) {
    CopyDirFiles(
        $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/telegram.send/install/js/',
        $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/',
        true,
        true
    );

    unlink(__DIR__ . '/reinstalljs');
}
