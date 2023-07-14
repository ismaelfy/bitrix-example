<?php

// Array de batchTypes
$batchTypes = array(
    'charges',
    'payments',
    'missing Info',
    'reference'
);

// Array de clients
$clients = array(
    array(
        'id' => 1,
        'name' => 'Cliente 1'
    ),
    array(
        'id' => 2,
        'name' => 'Cliente 2'
    ),
    array(
        'id' => 3,
        'name' => 'Cliente 3'
    ),
    array(
        'id' => 4,
        'name' => 'Cliente 4'
    ),
    array(
        'id' => 4,
        'name' => 'Cliente 4'
    ),
);

// Contar los aging en el array "details"
$agingCategory = array(
    '0-2 days',
    '3-7 days',
    '8-30 days',
    'over 30 days'
);


// Función para generar el array "items" simulando la obtención de datos de la base de datos
$items = array();
for ($i = 1; $i <= 100; $i++) {
    $dataFromDB = array(
        'field1' => 'Value 1'
    );

    $creationDate = date('Y-m-d', strtotime('-' . rand(1, 40) . ' days'));
    $batchType = $batchTypes[rand(0, count($batchTypes) - 1)];
    $client = $clients[rand(0, count($clients) - 1)]['id'];

    $item = array(
        'batchType' => $batchType,
        'client' => $client,
        'creation_date' => $creationDate
    );

    // Fusionar los campos obtenidos desde la base de datos
    $item = array_merge($item, $dataFromDB);

    $items[] = $item;
}



// Función para generar el array "details"
$details = array();
foreach ($items as $item) {
    $clientId = $item['client'];
    $batchType = $item['batchType'];

    $client = '';
    foreach ($clients as $clientData) {
        if ($clientData['id'] === $clientId) {
            $client = $clientData['name'];
            break;
        }
    }

    // Calcular el valor de aging en base a la diferencia de fechas
    $creationDate = strtotime($item['creation_date']);
    $today = strtotime('today');
    $aging = floor(($today - $creationDate) / (60 * 60 * 24)) + 1;

    if ($aging <= 2) {
        $agingCategoryValue = '0-2 days';
    } elseif ($aging <= 7) {
        $agingCategoryValue = '3-7 days';
    } elseif ($aging <= 30) {
        $agingCategoryValue = '8-30 days';
    } else {
        $agingCategoryValue = 'over 30 days';
    }

    $detail = array(
        'batchType' => $batchType,
        'client' => $client,
        'aging' => 1,
        'agingCategory' => $agingCategoryValue,
        'creation_date' => $item['creation_date'],
    );

    $details[] = $detail;
}


// Función para contar los aging en el array "details"
$detailsTotal = array();
foreach ($details as $detail) {
    $batchType = $detail['batchType'];
    $client = $detail['client'];
    $agingCategory = $detail['agingCategory'];
    $aging = $detail['aging'];

    if (!isset($detailsTotal[$batchType])) {
        $detailsTotal[$batchType] = array();
    }

    if (!isset($detailsTotal[$batchType][$client])) {
        $detailsTotal[$batchType][$client] = array();
    }

    if (!isset($detailsTotal[$batchType][$client][$agingCategory])) {
        $detailsTotal[$batchType][$client][$agingCategory] = 0;
    }

    $detailsTotal[$batchType][$client][$agingCategory] += $aging;
}

$dataToProcess = [];
// Función para preparar la data para procesar en la base de datos
foreach ($details as $detail) {
    $batchType = $detail['batchType'];
    $client = $detail['client'];
    $agingCategory = $detail['agingCategory'];
    $creationDate = $detail['creation_date'];
    $aging = isset($detailsTotal[$batchType][$client][$agingCategory]) ? $detailsTotal[$batchType][$client][$agingCategory] : 0;

    $dataToProcess[] = array(
        'batchType' => $batchType,
        'client' => $client,
        'aging' => $aging,
        'agingCategory' => $agingCategory,
        'creationDate' => $creationDate,
    );
}
dd($dataToProcess);
exit;

function dd(...$arg)
{
    echo "<pre>";
    echo json_encode($arg);
    echo "</pre>";
}
