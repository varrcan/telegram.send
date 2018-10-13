<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Telegram\Send\Config;

$APPLICATION->SetTitle('Телеграм');
Loader::includeModule('telegram.send');

Config::processRequest();
$mailTemplates = Config::getMailTemplates();
$registerUser  = Config::getUser();

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

CJSCore::Init(['jquery']);
Asset::getInstance()->addJs('/bitrix/js/telegram.send/telegram_send.js');
?>

<div class="telegram-response" style="display:none"></div>

<form method="POST" action="" class="telegram-form">
    <div class="adm-detail-block">
        <div class="adm-detail-tabs-block">
            <span id="setting" class="adm-detail-tab adm-detail-tab-active">Настройки</span>
            <span id="user" class="adm-detail-tab">Пользователи</span>
            <span id="proxy" class="adm-detail-tab">Настройки прокси</span>
        </div>
        <div class="adm-detail-content-wrap">
            <div id="wrap-setting" class="adm-detail-content">
                <div class="adm-detail-title">Параметры модуля</div>
                <div class="adm-detail-content-item-block">
                    <table class="adm-detail-content-table edit-table">
                        <tbody>
                        <tr>
                            <td class="adm-detail-content-cell-l">
                                Включить модуль:
                            </td>
                            <td class="adm-detail-content-cell-r">
                                <input name="module_on"
                                       value="<?=Config::statusModule();?>"
                                    <?=Config::statusModule() ? 'checked="checked"' : '';?>
                                       type="checkbox"
                                       class="module_on"
                                       title="">
                            </td>
                        </tr>
                        <tr>
                            <td class="adm-detail-content-cell-l">
                                Токен бота:
                            </td>
                            <td class="adm-detail-content-cell-r">
                                <input name="token"
                                       size="50"
                                       value="<?=Config::getToken();?>"
                                       type="text"
                                       class="token"
                                       title="">
                            </td>
                        </tr>
                        <tr>
                            <td class="adm-detail-content-cell-l">
                                Почтовые события:
                            </td>
                            <td class="adm-detail-content-cell-r">
                                <select name="mail[]" class="mail" title="" size="<?=count($mailTemplates)?>" multiple>
                                    <?php foreach ($mailTemplates as $item) : ?>
                                        <option value="<?=$item['EVENT_NAME'];?>"
                                            <?=in_array($item['EVENT_NAME'], Config::getMail(), true) ? 'selected' : '';?>>
                                            <?=$item['NAME'];?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="wrap-user" class="adm-detail-content" style="display:none">
                <div class="adm-detail-title">Пользователи</div>
                <div class="adm-detail-content-item-block" style="height: auto; overflow-y: visible;">
                    <table class="adm-detail-content-table edit-table">
                        <tbody>
                        <tr>
                            <td colspan="2">
                                <table width="100%" cellspacing="1" cellpadding="3" border="0">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input id="updates_user" name="updates_user" value="Запросить обновления" type="button">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <br>
                                <table class="internal" width="100%" cellspacing="1" cellpadding="3" border="0">
                                    <tbody>
                                    <tr>
                                        <td class="heading"><b>ID</b></td>
                                        <td class="heading"><b>Ник</b></td>
                                        <td class="heading"><b>Имя</b></td>
                                        <td class="heading"><b>Действия</b></td>
                                    </tr>
                                    <?php if ($registerUser) : ?>
                                        <tr>
                                            <td colspan="6">Зарегистрированные пользователи</td>
                                        </tr>
                                        <?php foreach ($registerUser as $id => $val) : ?>
                                            <tr class="register_user" id="<?=$id?>">
                                                <td><?=$id?></td>
                                                <td><?=$val['nickname']?></td>
                                                <td><?=$val['username']?></td>
                                                <td><input value="Удалить" type="button" onclick="delUser(<?=$id?>);"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <tr class="new_user">
                                    </tr>
                                    <tr class="telegram_user">
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="wrap-proxy" class="adm-detail-content" style="display:none">
                <div class="adm-detail-title">Настройки прокси</div>
                <div class="adm-detail-content-item-block">
                    <table class="adm-detail-content-table edit-table">
                        <tbody>
                        <tr>
                            <td class="adm-detail-content-cell-l">
                                Включить прокси:
                            </td>
                            <td class="adm-detail-content-cell-r">
                                <input name="proxy_on"
                                       value="<?=Config::statusProxy();?>"
                                    <?=Config::statusProxy() ? 'checked="checked"' : '';?>
                                       type="checkbox"
                                       class="proxy_on"
                                       title="">
                            </td>
                        </tr>
                        <tr>
                            <td class="adm-detail-content-cell-l">
                                IP или домен (без http):
                            </td>
                            <td class="adm-detail-content-cell-r">
                                <input name="proxy_url"
                                       size="50"
                                       value="<?=Config::proxyData()['url'];?>"
                                       type="text"
                                       class="proxy_url"
                                       title="">
                            </td>
                        </tr>
                        <tr>
                            <td class="adm-detail-content-cell-l">
                                Порт:
                            </td>
                            <td class="adm-detail-content-cell-r">
                                <input name="proxy_port"
                                       size="50"
                                       value="<?=Config::proxyData()['port'];?>"
                                       type="text"
                                       class="proxy_port"
                                       title="">
                            </td>
                        </tr>
                        <tr>
                            <td class="adm-detail-content-cell-l">
                                Пользователь:
                            </td>
                            <td class="adm-detail-content-cell-r">
                                <input name="proxy_user"
                                       size="50"
                                       value="<?=Config::proxyData()['user'];?>"
                                       type="text"
                                       class="proxy_user"
                                       title="">
                            </td>
                        </tr>
                        <tr>
                            <td class="adm-detail-content-cell-l">
                                Пароль:
                            </td>
                            <td class="adm-detail-content-cell-r">
                                <input name="proxy_pass"
                                       size="50"
                                       value="<?=Config::proxyData()['pass'];?>"
                                       type="text"
                                       class="proxy_pass"
                                       title="">
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="adm-detail-content-btns-wrap adm-detail-content-btns-pin">
                <div class="adm-detail-content-btns">
                    <a href="#" class="adm-btn adm-btn-save" id="save">Сохранить</a>
                </div>
            </div>

        </div>
    </div>
</form>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
?>
