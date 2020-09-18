<?
declare(strict_types=1);

use \Bitrix\Main\ModuleManager;

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
        //$arModuleVersion = array();
        $this->MODULE_VERSION = '0.0.1';
        $this->MODULE_VERSION_DATE = '15.09.2020 09:00';
        $this->MODULE_NAME = 'Тестовый модуль управления IP покупателей';
        $this->MODULE_DESCRIPTION = 'Тестовый модуль для интернет-магазина Афоня по управлению IP покупателей';
    }

    function DoInstall()
    {
        $this->InstallDB();
        $this->InstallEvents();
        $this->RegisterDependences();
        $this->RegisterAgent();
        \Bitrix\Main\ModuleManager::RegisterModule('afonya.customersip');
        return true;
    }

    function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnRegisterDependences();
        $this->UnRegisterAgent();
        \Bitrix\Main\ModuleManager::UnRegisterModule('afonya.customersip');
        return true;
    }

    function InstallDB()
    {
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/local/modules/afonya.customersip/install/db/install.sql');
        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
    }

    function UnInstallDB()
    {
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/local/modules/afonya.customersip/install/db/uninstall.sql');
        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
    }

    function InstallEvents()
    {
        $et = new CEventType;
        $et->Add([
            "LID"           => SITE_ID,
            "EVENT_NAME"    => 'NEW_IP_INFORM',
            "NAME"          => 'Добавлена информация по IP нового пользователя',
            "DESCRIPTION"   => ''
        ]);


        $rsSites = \Bitrix\Main\SiteTable::getList(['select' => ['LID'], 'filter' => ['ACTIVE' => 'Y']]);
        $arSites = $rsSites->fetchAll();
        $sitesList = [];

        foreach ($arSites as $site) {
            $sitesList[] = $site['LID'];
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
            'LID' => $sitesList,
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
        $et = new CEventType;
        $et->Delete('NEW_IP_INFORM');

        $by = 'NEW_IP_INFORM';
        $order = 'desc';
        $arfilter = ['TYPE_ID' => 'NEW_IP_INFORM'];
        $rsEMessages = CEventMessage::GetList($by, $order, $arfilter);

        $em = new CEventMessage;

        while($arEMessage = $rsEMessages->GetNext())
        {
            $em->Delete($arEMessage['ID']);
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