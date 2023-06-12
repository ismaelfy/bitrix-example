<?php

// init.php
$userId = $USER->GetID();
CPullWatch::AddToStack('my_channel', array(
    'USER_ID' => $userId
));

// init.php
AddEventHandler("main", "OnBeforeUserLogout", "CerrarSesionesAnteriores");
function CerrarSesionesAnteriores(&$arParams)
{
    $userId = $USER->GetID();
    $sessionId = session_id();

    CPullStack::CloseByChannel('my_channel', $userId, $sessionId);
}

