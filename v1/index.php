<?php

require_once '../include/DbHandler.php';
require_once '../include/DbConnect.php';
require '../libs/Slim/Slim.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->response->headers->set('Access-Control-Allow-Origin', '*');
$app->response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS');

$app->get("/", function () {
    echo "<h1>Hello!!!!</h1>";
});

/**
 * Get all the events
 * method GET
 * url /events
 */
$app->get('/events', function() {
    $db = new DbHandler();
    $response = array();

    // fetch events
    $result = $db->getAllEvents();

    if ($result != NULL) {
        $response["error"] = false;
        $response = $result;
        echoResponse(200, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/**
 * Get a single event
 * method GET
 * url /events/id
 */
$app->get('/event/:id', function ($id) {
    $response = array();
    $db = new DbHandler();

    // fetch event
    $result = $db->getEvent($id);

    if ($result != NULL) {
        $response["error"] = false;
        $response = $result;
        echoResponse(200, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/**
 * Create a new event
 * method POST
 * url /events
 */
$app->post('/events', function() {
    // opening db connection
    $db = new DbConnect();

    $request = \Slim\Slim::getInstance()->request();

    $event = json_decode($request->getBody());

    // Language
    $language_num = count($event->language);
    $language = "";
    foreach ($event->language as $key=>$item) {
        if($key == $language_num-1){
            $language .= $item;
        } else {
            $language .= $item.",";
        }
    }

    // Age Limit
    $ageMin = $event->ageLimit[0];
    $ageMax = $event->ageLimit[1];

    $img = " ";
    if($event->type == "Sport" || $event->type == "sport"){
        $img = "http://www.damianofossa.com/joinapp/img/sport.jpg";
    } elseif($event->type == "Hanging out" || $event->type == "hanging out") {
        $img = "http://www.damianofossa.com/joinapp/img/drink2.jpg";
    } elseif($event->type == "Drinking" || $event->type == "drinking") {
        $img = "http://www.damianofossa.com/joinapp/img/drink.jpg";
    } elseif($event->type == "Other activity" || $event->type == "other activity") {
        $img = "http://www.damianofossa.com/joinapp/img/event.jpg";
    }

    $sql = "INSERT INTO event (title, location, date, time, ageMin, ageMax, groupSize, limited, maxParticipants, joining, description, img, type, language) VALUES ('".$event->title."', '".$event->location."', '".$event->date."', '".$event->time."', '".$ageMin."', '".$ageMax."', '".$event->groupSize."', '".$event->limited."', '".$event->maxParticipants."', '".$event->joining."', '".$event->description."', '".$img."', '".$event->type."', '".$language."')";

    try {
        $conn = $db->connect();
        $result = $conn->query($sql);
        $event->id = $conn->insert_id;
        echoResponse(200, $event->id);
    } catch(PDOException $e) {
        echoResponse(404, '{"error":{"text":'. $e->getMessage() .'}}');
    }
});

/**
 * Modify a single event
 * method PUT
 * url /events
 */
$app->put('/event/:id', function ($id) {
    // opening db connection
    $db = new DbConnect();
    $handler = new DbHandler();

    $request = \Slim\Slim::getInstance()->request();
    $event = json_decode($request->getBody());
    $joining = $event->joining;

    $result = $handler->getEvent($id);
    if(($joining < $result[0]["event"]["maxParticipants"]) && ($result[0]["event"]["maxParticipants"] > 0)){
        $sql = "UPDATE event SET joining=".$joining." WHERE ID=".$id;
        try {
            $conn = $db->connect();
            $query = $conn->query($sql);
            $result = $handler->getEvent($id);
            echoResponse(200, $result);
        } catch(PDOException $e) {
            echoResponse(404, '{"error":{"text":'. $e->getMessage() .'}}');
        }
    } elseif ($result[0]["event"]["maxParticipants"] == 0) {
        $sql = "UPDATE event SET joining=".$joining." WHERE ID=".$id;
        try {
            $conn = $db->connect();
            $query = $conn->query($sql);
            $result = $handler->getEvent($id);
            echoResponse(200, $result);
        } catch(PDOException $e) {
            echoResponse(404, '{"error":{"text":'. $e->getMessage() .'}}');
        }
    } else {
        echoResponse(404, '{"error":{"text": error }}');
    }
});

$app->options('/event/:id', function ($id) {});

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();

?>
