<?
declare(strict_types=1);

use Bitrix\Main\ModuleManager;
use Bitrix\Main\SiteTable;

Class Afonya_CustomersIP extends CModule
{
    var $MODULE_ID = 'afonya.customersip';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $errors;

    function __construct()
    {
        $this->MODULE_VERSION = '0.0.1';
        $this->MODULE_VERSION_DATE = '15.09.2020 09:00:00';
        $this->MODULE_NAME = 'Тестовый модуль управления IP покупателей';
        $this->MODULE_DESCRIPTION = 'Тестовый модуль для интернет-магазина Афоня по управлению IP покупателей';
    }

    function DoInstall()
    {
        $this->InstallDB();
        $this->InstallEvents();
        $this->RegisterDependences();
        $this->RegisterAgent();
        ModuleManager::RegisterModule('afonya.customersip');

        return true;
    }

    function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnRegisterDependences();
        $this->UnRegisterAgent();
        ModuleManager::UnRegisterModule('afonya.customersip');

        return true;
    }

    function InstallDB()
    {
        global $DB;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/local/modules/afonya.customersip/install/db/install.sql');

        return $this->errors ?? true;
    }

    function UnInstallDB()
    {
        global $DB;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/local/modules/afonya.customersip/install/db/uninstall.sql');

        return $this->errors ?? true;
    }

    function InstallEvents()
    {
        CEventType::Add([
            'LID'           => SITE_ID,
            'EVENT_NAME'    => 'NEW_IP_INFORM',
            'NAME'          => 'Добавлена информация по IP нового пользователя',
            'DESCRIPTION'   => ''
        ]);

        $sites = SiteTable::getList(['select' => ['LID'], 'filter' => ['ACTIVE' => 'Y']])->fetchAll();
        $siteList = [];

        foreach ($sites as $site) {
            $siteList[] = $site['LID'];
        }

        $textMessage = "<!doctype html>
<html lang='ru'>
<head>
  <meta charset='utf-8'>
  <title>Получена информация о новом IP покупателя</title>
</head>
<body>
<h2>Добрый день!</h2>
 
<p>Получена информация о новом IP покупателя</p>

<p>Номер заказа - #ORDER_ID# </p>
<p>IP покупателя #IP# </p>
 
#IP_INFORM#
 
<p>Письмо сформировано автоматически.</p>
</body>
</html>";

        $fields = [
            'ACTIVE' => 'Y',
            'EVENT_NAME' => 'NEW_IP_INFORM',
            'LID' => $siteList,
            'EMAIL_FROM' => "#DEFAULT_EMAIL_FROM#",
            'EMAIL_TO' => "#EMAIL_TO#",
            'BCC' => '',
            'SUBJECT' => 'Получена информация о новом IP покупателя',
            'BODY_TYPE' => 'html',
            'MESSAGE' => $textMessage
        ];

        $em = new CEventMEssage;
        $em->Add($fields);

        return true;
    }

    function UnInstallEvents()
    {
        CEventType::delete('NEW_IP_INFORM');

        $by = 'NEW_IP_INFORM';
        $order = 'desc';
        $filter = ['TYPE_ID' => 'NEW_IP_INFORM'];
        $eMessages = CEventMessage::GetList($by, $order, $filter);

        while($eMessage = $eMessages->fetch())
        {
            CEventMessage::Delete($eMessage['ID']);
        }

        return true;
    }

    function RegisterAgent()
    {
        \CAgent::AddAgent(
            "\\Afonya\\CustomersIP\\Agents::checkIPTableAgent();",
            'afonya.customersip',
            'N',
            60,
            '',
            'Y'
        );
    }

    function UnRegisterAgent()
    {
        \CAgent::RemoveAgent(
            "\\Afonya\\CustomersIP\\Agents::checkIPTableAgent();",
            'afonya.customersip'
        );
    }

    function RegisterDependences()
    {
        RegisterModuleDependences(
            'sale',
            'OnSaleOrderSaved',
            $this->MODULE_ID,
            '\Afonya\CustomersIP\EventHandlers',
            'onSaleOrderSavedHandler'
        );
        RegisterModuleDependences(
            'afonya.customersip',
            'OnIPInformGet',
            $this->MODULE_ID,
            '\Afonya\CustomersIP\EventHandlers',
            'onIPInformGetHandler'
        );
    }

    function UnRegisterDependences()
    {
        UnRegisterModuleDependences(
            'sale',
            'OnOrderAdd',
            $this->MODULE_ID,
            '\Afonya\CustomersIP\EventHandlers',
            'onOrderAddHandler'
        );
        UnRegisterModuleDependences(
            'afonya.customersip',
            'OnIPInformGet',
            $this->MODULE_ID,
            '\Afonya\CustomersIP\EventHandlers',
            'onIPInformGetHandler'
        );
    }
}