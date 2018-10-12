<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require __DIR__.'/../../vendor/autoload.php';

$request = Request::createFromGlobals();
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/images/{id:[0-9]+}', function(Request $request, $id) {
        $redis = getRedis();
        
        $thumbUrl = sendRequest("GET", 
                                sprintf('http://storage_app:80/files/%s', 
                                        $redis->get(sprintf('thumb:%s', $id))));

        $photoUrl = sendRequest("GET", 
                                sprintf('http://storage_app:80/files/%s', 
                                        $redis->get(sprintf('full:%s', $id))));

        $storageServerUrl = "http://localhost:8004/";

        $imageUrls = array(
            "name" => $redis->get(sprintf('name:%s', $id)),
            "full" => $storageServerUrl . $photoUrl["url"],
            "thumb" => $storageServerUrl . $thumbUrl["url"],
        );

        return new JsonResponse($imageUrls);
    });
    $r->addRoute('GET', '/images/list', function(Request $request) {
        $redis = getRedis();

        $list = array(
            "ids" => $redis->lrange("list-photos", 0, -1),
        );

        return new JsonResponse($list);
    });
});

function sendRequest($method, $url) {

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
    ));

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return ['error' => [
            'url' => $url,
            'message' => $err
        ]];
    } else {
        return json_decode($response, true);
    }

}

function getRedis() {
    static $redis;
    if (null === $redis) {
        $redis = new Redis();
        $redis->connect('images_redis');
    }

    return $redis;
}

//

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $vars = (array)$vars;
        array_unshift($vars, $request);
        /** @var Response $response */
        $response = call_user_func_array($handler, $vars);
        echo $response->getContent();
        break;
}