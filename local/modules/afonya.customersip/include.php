<?php
Bitrix\Main\Loader::registerAutoloadClasses(
    'afonya.customersip',
    array(
        "Afonya\\CustomersIP\\EventHandlers" => 'lib/EventHandlers.php',
        "Afonya\\CustomersIP\\SendingEmail" => 'lib/SendingEmail.php',
        "Afonya\\CustomersIP\\Agents" => 'lib/Agents.php'
    )
);