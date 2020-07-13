<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Pagos extends REST_Controller {

    public function __construct(){
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, Authorization");
        header("Access-Control-Allow-Origin: *");
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('form', 'url'));

    }

    public function index_get(){
        $this->response();
    }

    public function get_comprobante_pago_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $data_post   = file_get_contents("php://input");
            $data_token  = $validation_token['data'];
            $utype_token = $data_token->utype;
            $uid_token   = $data_token->id;
            if (isset($data_post) && !empty($data_post)){
                $data_request = json_decode($data_post);
                
                #consulta para perito / solicitante
                if ($utype_token == 'id_5ebe10d0b4db29.48120814' || $utype_token == 'id_5ebe11ce542b89.25367119') {
                  $query = "SELECT pago_para, path_file, nombre, comentario, estatus, validado, fecha, public_id 
                  FROM r_licencia_pagos WHERE id_licencia = ?";
                  $query = $this->db->query($query,array($data_request->public_id));
                }else if ($utype_token == 'id_5ebafe3b361083.91981512') { #consulta para servidor
                  $query = "SELECT pago_para, path_file, nombre, comentario, estatus, validado, fecha, id_licencia, public_id 
                  FROM r_licencia_pagos WHERE id_licencia = ? AND pago_para = ?";
                  $query = $this->db->query($query,array($data_request->public_id, 1));
                }else if ($utype_token == 'id_5ebc667ccb01b7.90108736') { #consulta para colegio
                  $query = "SELECT pago_para, path_file, nombre, comentario, estatus, validado, fecha, id_licencia, public_id 
                  FROM r_licencia_pagos WHERE id_licencia = ? AND pago_para = ?";
                  $query = $this->db->query($query,array($data_request->public_id, 0));
                }
                
                $res   = ["data" => $query->result_array(), "status" => true];
                
            }else{
                $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
            }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
    }

    //agregar archivos
public function add_pago_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;
  
      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);
        
        $data          = $data_request->data;
        $id_licencia   = $data_request->traid;
        $pago_para     = $data->pago_para;
  
        $file      = $data_request->file;
        $base_64   = $file->base64;
        $filename  = $file->filename;
        $file_size = $file->filesize;
        $file_type = $file->filetype;
  
        //real path
        $path_lic = '/home1/francisco/guadalupe.inmueblesenmexico.net/public_files/user_assets/'.$uid_token.'/licencias/'.$id_licencia;
        //database
        $path = 'public_files/user_assets/'.$uid_token.'/licencias/'.$id_licencia.'/'.$filename;
  
        if ($file_type == 'image/jpeg' || $file_type == 'image/JPEG' || $file_type == 'image/jpg' || $file_type == 'image/JPG'
            || $file_type == 'image/png'  || $file_type == 'image/PNG' || $file_type == 'application/pdf'  || $file_type == 'application/PDF') {
          if ($file_size <= 5242880 ) {
            if(file_put_contents($path_lic.'/'.$filename, base64_decode($base_64))){
                $query_slt  = "SELECT public_id FROM r_licencia_pagos WHERE id_licencia = ? AND pago_para = ?";
                $query_slt  = $this->db->query($query_slt,array($id_licencia, $pago_para));
                $result     = $query_slt->result_array();
                if (isset($result[0]) && !empty($result[0])){
                  $query = "UPDATE r_licencia_pagos SET path_file = ?, nombre = ?, estatus = ? WHERE id_licencia = ? AND pago_para = ?";
                  $query = $this->db->query($query, array($path,$filename,'Pago Corregido',$id_licencia,$pago_para));
                  
                  if ($pago_para) {#correccion a pago derechos
                    $query = "UPDATE licencias SET estatus = ? WHERE public_id = ?";
                    $query = $this->db->query($query, array('Pago Corregido',$id_licencia));
                  }
                }else {
                  $public_id = uniqid('id_',TRUE);
                  $query = "INSERT INTO r_licencia_pagos 
                      (id_licencia,pago_para,path_file,nombre,estatus,validado,public_id) 
                      VALUES (?,?,?,?,?,?,?)";
                  $query = $this->db->query($query, array($id_licencia,$pago_para,$path,$filename,'Archivo Cargado',0,$public_id)); 
                  
                  if ($pago_para) {#primer pago derechos
                    $query = "UPDATE licencias SET estatus = ? WHERE public_id = ?";
                    $query = $this->db->query($query, array('Pagado',$id_licencia));
                  }
                }
              
              $res = [ "status" => TRUE, "data" => 0 ];
            }else {
              $res = [ "status" => TRUE, "data" => 100 ];
            }
          }else {
            $res = [ "status" => TRUE, "data" => 10 ];
          }
        }else {
          $res = [ "status" => TRUE, "data" => 1 ];
        }
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function add_comen_doc_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;
  
      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);
  
        $comentario   = $data_request->comentario;
        $id_requisito = $data_request->public_id;
        $id_licencia  = $data_request->id_licencia;
  
        $query = "UPDATE r_licencia_pagos SET comentario = ?, estatus = ? WHERE public_id = ?";
        $query = $this->db->query($query, array($comentario,'Pago con Observaciones',$id_requisito));

        $query = "UPDATE licencias SET estatus = ? WHERE public_id = ?";
        $query = $this->db->query($query, array('Pago con Observaciones',$id_licencia));
        $res = [ "status" => TRUE];
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }
  
}