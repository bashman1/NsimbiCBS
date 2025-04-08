<?php

$request = $_SERVER['REQUEST_URI'];
// print_r(substr($request, 14));


switch ($request) {
    case '/' :
        require __DIR__ . '/index.php';
        break;
    case '' :
        require __DIR__ . '/index.php';
        break;
        case '/login' :
            require __DIR__ . '/login.php';
            break;
    case '/clients' :
        require __DIR__ . '/all_clients.php';
        break;
        case '/clients/new/1' :
            require __DIR__ . '/add_client.php';
            break;
            case '/clients/new/2' :
                require __DIR__ . '/add_client.php';
                break;
        

    default:
        // http_response_code(404);
        require __DIR__ . '/404.php';
        break;
}