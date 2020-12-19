<?php
Bitrix\Main\Loader::registerAutoloadClasses(
    'afonya.customersip',
    [
        'Afonya\CustomersIP\EventHandlers' => 'lib/EventHandlers.php',
        'Afonya\CustomersIP\DataTable' => 'lib/DataTable.php',
        'Afonya\CustomersIP\SendingEmail' => 'lib/SendingEmail.php',
        'Afonya\CustomersIP\Agents' => 'lib/Agents.php',
    ]
);