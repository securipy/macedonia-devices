<?php
namespace App\Model;

use App\Lib\Response,
App\Lib\Auth;

class DevicesModel extends MasterModel
{
    private $db;
    private $response;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->response = new Response();
        parent::__construct($db,$this->response);

    }


    public function getDataByIdDeviceUser($id_user,$audit,$id_device)
    {

        $st = $this->db->prepare("SELECT ip_domain,port,status,extra FROM devices as d,devices_ports as dp, audit as a WHERE dp.id_device = d.id AND d.id_audit = a.id AND a.id = :audit AND d.id=:id_device AND a.id_user=:id_user");

        $st->bindParam(':id_user',$id_user);
        $st->bindParam(':audit',$audit);
        $st->bindParam(':id_device',$id_device);

        if($st->execute()){
          $this->response->result = $st->fetchAll();
          return $this->response->SetResponse(true,"List devices");
        }else{
          return $this->response->SetResponse(false,'Error get devices');
        }
    }




    public function getDevicesByIdUserAudit($id_user,$id_audit)
    {

        
        $st = $this->db->prepare("SELECT d.id as id_device, d.ip_domain, sw.date_execute,sw.date_finish FROM devices as d,audit as a,script_work as sw WHERE a.id=d.id_audit AND a.id=:id_audit AND  sw.id_user=:id_user AND sw.id_info_work=d.id");

        $st->bindParam(':id_user',$id_user);
        $st->bindParam(':id_audit',$id_audit);

        if($st->execute()){
          $this->response->result = $st->fetchAll();
          return $this->response->SetResponse(true,"List devices");
        }else{
          return $this->response->SetResponse(false,'Error get devices');
        }
    }

    public function setScanDevices($id_audit,$ip_domain)
    {
        $st = $this->db->prepare("INSERT INTO devices (id_audit,ip_domain)VALUES(:id_audit,:ip_domain)");

        $st->bindParam(':ip_domain',$ip_domain);
        $st->bindParam(':id_audit',$id_audit);

        if($st->execute()){
          $this->response->result = $this->db->lastInsertId();
          return $this->response->SetResponse(true,"Insert new devices");
        }else{
          return $this->response->SetResponse(false,'Error insert Scan Device');
        }
    }
    
    public function setScriptServer($id_user,$id_info_work,$id_script_server,$date_execute)
    {
        $st = $this->db->prepare("INSERT INTO script_work (id_user,id_script_server,id_info_work,date_execute) VALUES (:id_user,:id_script_server,:id_info_work,:date_execute)");

        $st->bindParam(':id_user',$id_user);
        $st->bindParam(':id_info_work',$id_info_work);
        $st->bindParam(':id_script_server',$id_script_server);
        $st->bindParam(':date_execute',$date_execute);

        if($st->execute()){
            $this->response->result = $this->db->lastInsertId();
            return $this->response->SetResponse(true,"Insert new devices");
        }else{
          return $this->response->SetResponse(false,'Error al consultar el email');
        }
    }



}