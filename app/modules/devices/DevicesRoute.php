<?php
use App\Lib\Auth,
    App\Lib\Response,
    App\Lib\GeneralFunction,
    App\Validation\devicesValidation,
    App\Middleware\AuthMiddleware,
    App\Middleware\AuditMiddleware;



$app->group('/device', function () {

$this->get('/list', function ($request, $response, $args) {
    $token = $request->getAttribute('token');
    $type_petition = $request->getAttribute('type_petition');
    $audit = $request->getAttribute('audit');
    $id= Auth::GetData($token)->id;

    $devices = $this->controller->devices->getDevicesByIdUserAudit($id,$audit);
  
    if($type_petition == "html"){
    return $this->view->render($response, 'modules/devices/templates/deviceslist.twig',[
        'devices' => $devices
      ]);

    }else{
        return $response->withHeader('Content-type', 'application/json')
                   ->write(
                     json_encode($devices)
        ); 
    }
    
 })->add(new AuditMiddleware($this))->add(new AuthMiddleware($this));

$this->get('/{id_device:[0-9]+}', function ($request, $response, $args) {
    $token = $request->getAttribute('token');
    $type_petition = $request->getAttribute('type_petition');
    $audit = $request->getAttribute('audit');
    $id= Auth::GetData($token)->id;
 
    $device = $this->controller->devices->getDataByIdDeviceUser($id,$audit,$args['id_device']);
    if($type_petition == "html"){
      return $this->view->render($response, 'modules/devices/templates/device.twig',[
        'device' => $device
      ]);
    }else{
     return $response->withHeader('Content-type', 'application/json')
     ->write(
       json_encode($device)
       );
   }
 })->add(new AuditMiddleware($this))->add(new AuthMiddleware($this));


$this->post('/new', function ($request, $response, $args) {
    $token = $request->getAttribute('token');

    $audit = $request->getAttribute('audit');
    $id= Auth::GetData($token)->id;

    $expected_fields = array('day_scan','ip_domain','servers');

    $data = GeneralFunction::createNullData($request->getParsedBody(),$expected_fields);

    $r = devicesValidation::Validate($data);


    if(!$r->response){
            return $res->withHeader('Content-type', 'application/json')
                       ->withStatus(422)
                       ->write(json_encode($r));
    }


    $devices = $this->controller->devices->setScanDevices($id,$audit,$data['day_scan'],$data['ip_domain'],$data['servers']);
  

    return $response->withHeader('Content-type', 'application/json')
            ->write(
            json_encode($devices)
    ); 
    
    
 })->add(new AuditMiddleware($this))->add(new AuthMiddleware($this));



});