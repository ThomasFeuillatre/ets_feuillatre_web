<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views;
require '../vendor/autoload.php';
require_once '../includes/dbOperation.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer('../templates/');

$app->get('/index', function (Request $request, Response $response)
{
    return $this->view->render($response, 'index.phtml');

});



$app->get('/getPoint',function (Request $request, Response $response)
{
    $db = new DbOperation();
    $points = $db->getListPoint();
    $response->getBody()->write(json_encode(array('points' => $points)),JSON_PRETTY_PRINT);
});

$app->post('/login',function (Request $request, Response $response)
{
    if (isTheseParametersAvailable(array('name', 'password'))) {
        $requestData = $request->getParsedBody();
        $name = $requestData['name'];
        $password = $requestData['password'];

        $db = new DbOperation();
        $responseData = array();
        $id_worker = $db->userLogin($name, $password);
        if ($id_worker != 0) {
            $responseData['error'] = false;
            $responseData['id_worker'] = $id_worker;
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Invalid login or password';
        }
        $response->getBody()->write(json_encode($responseData));
    }
});

$app->post("/getDataClockPoint", function (Request $request, Response $response)
{
   if (isTheseParametersAvailable(array('day_time', 'id_worker')))
   {
       $requestData = $request->getParsedBody();
       $date = $requestData['day_time'];
       $id_worker = $requestData['id_worker'];

       $db = new DbOperation();
       $responseData = [];
       if($result = $db->getDataClockPoint($id_worker, $date))
       {
           $responseData['error'] = false;
           $responseData['am_start'] = $result['am_start'];
           $responseData['am_end'] = $result['am_end'];
           $responseData['pm_start'] = $result['pm_start'];
           $responseData['pm_end'] = $result['pm_end'];
           $responseData['place'] = $result['place'];
           $responseData['observation'] = $result['observation'];
       }
       $response->getBody()->write(json_encode($responseData));
   }
});

$app->post('/saveTimeClock',function (Request $request, Response $response)
{
   if(isTheseParametersAvailable(array('id_worker', 'day_time', 'am_start', 'am_end', 'pm_start', 'pm_end', 'place', 'statut')))
   {
       $requestData = $request->getParsedBody();
       $id_worker = $requestData['id_worker'];
       $day_time = $requestData['day_time'];
       $am_start = $requestData['am_start'] ;
       $am_end = $requestData['am_end'];
       $pm_start = $requestData['pm_start'];
       $pm_end = $requestData['pm_end'];
       $place = $requestData['place'];
       $statut = $requestData['statut'];
       $observation = $requestData['observation'];

       $db = new DbOperation();
       $responseData = array();
       if($db->saveTimeClock($id_worker, $day_time, $am_start, $am_end, $pm_start, $pm_end, $place, $statut, $observation))
       {
           $responseData['error'] = false;
       }
       else {
           $responseData['error'] = true;
           $responseData['message'] = 'error';
       }
       $response->getBody()->write(json_encode($responseData));
   }
});

$app->post("/checkStatut",function (Request $request, Response $response)
{
   if(isTheseParametersAvailable(array('id_worker', 'day_time')))
   {
       $requestData = $request->getParsedBody();
       $id_worker = $requestData['id_worker'];
       $day_time = $requestData['day_time'];

       $db = new DbOperation();
       $responseData = [];
       $statut = $db->checkStatut($id_worker, $day_time);
       if ($statut != 0)
       {
           $responseData['error'] = false;
           $responseData['statut'] = $statut;
       }
       else
       {
           $responseData['error'] = true;
           $responseData['message'] = 'error';
           $responseData['statut'] = $statut;
       }
       $response->getBody()->write(json_encode($responseData));
   }
});

//function to check parameters
function isTheseParametersAvailable($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echo json_encode($response);
        return false;
    }
    return true;
}

try {
    $app->run();
} catch (\Slim\Exception\MethodNotAllowedException $e) {
} catch (\Slim\Exception\NotFoundException $e) {
} catch (Exception $e) {
}
