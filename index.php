<?php

$url = $_SERVER['REQUEST_URI'];


$url = trim($url, '/');
var_dump($url);

if(str_contains ($url, '/')){
    $url = explode('/', $url);
}

else if ($url !== '')  {
    $url = [$url];
}

$controller = $segments[0] ?? 'home';
$action     = $segments[1] ?? 'index';
$id         = $segments[2] ?? null;
$file = $controller . '_' . $action . '.php';

echo "Controller: $controller" . PHP_EOL;
echo "Action: $action" . PHP_EOL;
echo "ID: $id" . PHP_EOL;
echo "include file: $file";

if(file_exists($file)){
    require $file;
}
else {
    require '404.php';
}



?>