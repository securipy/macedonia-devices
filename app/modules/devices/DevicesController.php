<?php
namespace App\Controller;

use App\Lib\Response,
App\Lib\Auth,
\Aws\Ec2\Ec2Client;


class DevicesController extends MasterController
{
	private $response;

	public function __construct($model,$connection)
	{
		$this->response = new Response();
		$this->model = $model;
		parent::__construct($connection,$this->response);
	}

	public function getDevicesByIdUserAudit($id_user,$id_audit)
	{
		return $this->model->getDevicesByIdUserAudit($id_user,$id_audit);
	}

	public function getDataByIdDeviceUser($id_user,$audit,$id_device)
	{
		return $this->model->getDataByIdDeviceUser($id_user,$audit,$id_device);

	}

	public function setScanDevices($id,$audit,$day_scan,$ip_domain,$servers)
	{
		
		foreach ($servers as $key => $id_server) {
			$result_check_Script_server = $this->checkServerScriptByUser($id,$id_server,'1');
			if($result_check_Script_server->response == True and !empty($result_check_Script_server->result)){
				$id_script_server = $result_check_Script_server->result->id_server_script;
				$result_set_scan = $this->model->setScanDevices($audit,$ip_domain);
				if($result_set_scan->response == true && !empty($result_set_scan->result)){
					return $this->model->setScriptServer($id,$result_set_scan->result,$id_script_server,$day_scan);
				}else{
					return $result_set_scan;
				}
			}

		}
	}



}