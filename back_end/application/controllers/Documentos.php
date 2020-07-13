<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Documentos extends REST_Controller {

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

    public function get_docs_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $data_post   = file_get_contents("php://input");
            $data_token  = $validation_token['data'];
            $utype_token = $data_token->utype;
            $uid_token   = $data_token->id;
            if (isset($data_post) && !empty($data_post)){
                $data_request = json_decode($data_post);
                
              if ($utype_token == 'id_5ebe10d0b4db29.48120814') {//dro
                //obtencion  de id del colegio
                $query_col     = "SELECT id_colegio FROM r_datos_peritos WHERE id_usuario = ?";
                $query_col     = $this->db->query($query_col,array($uid_token));
                $result_col    = $query_col->result_array();
                $preparing_col = $result_col[0];
                $id_colegio    = (string)$preparing_col['id_colegio'];

                $query = "SELECT doc_para, path_file, nombre, comentario, estatus, tipo_usuario, public_id 
                FROM listado_docs WHERE doc_para = ? OR ( doc_para = ? AND id_usuario = ?) AND estatus = ?";
                $query = $this->db->query($query,array(0,0,$id_colegio,1));  
              }else if ($utype_token == 'id_5ebe11ce542b89.25367119') {//particular
                  $query = "SELECT doc_para, path_file, nombre, comentario, estatus, tipo_usuario, public_id 
                  FROM listado_docs WHERE doc_para = ? AND estatus = ?";
                  $query = $this->db->query($query,array(1,1));  
              }else if ($utype_token == 'id_5ebafe3b361083.91981512' || $utype_token == 'id_5ebafe3b361083.91981512'){//municipio
                $query = "SELECT doc_para, path_file, nombre, comentario, estatus, public_id 
                FROM listado_docs WHERE tipo_usuario = ? OR tipo_usuario = ?";
                $query = $this->db->query($query,array('id_5ebafe3b361083.91981512','id_5ebafe3b361083.91981512'));
              }else if ($utype_token == 'id_5ebc667ccb01b7.90108736' || $utype_token == 'id_5ebe11ce542b89.25366485'){//colegio cadro
                $query = "SELECT doc_para, path_file, nombre, comentario, estatus, public_id 
                FROM listado_docs WHERE tipo_usuario = ? AND id_usuario = ?";
                $query = $this->db->query($query,array($utype_token,$uid_token));
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
public function add_doc_post(){
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
        $doc_para     = $data_request->doc_para;
  
        $file      = $data_request->file;
        $base_64   = $file->base64;
        $filename  = $file->filename;
        $file_size = $file->filesize;
        $file_type = $file->filetype;
        
        if ($utype_token == 'id_5ebafdbc81b415.25700817') {//master
            $dir = 'master_docs';
        }else if ($utype_token == 'id_5ebafe3b361083.91981512' || $utype_token == 'id_5ebafe3b361083.91981512') {//servidor colaborador
            $dir = 'municipio_docs';
        }else if ($utype_token == 'id_5ebe11ce542b89.25366485') {//CADRO
            $dir = 'cadro_docs';
        }else if ($utype_token == 'id_5ebc667ccb01b7.90108736') {//colegio
            $dir = 'colegios_docs';
        }
        //real path
        $path_lic = '/home1/francisco/guadalupe.inmueblesenmexico.net/public_files/'.$dir;
        //database
        $path = 'public_files/'.$dir.'/'.$filename;
  
        if ($file_type == 'image/jpeg' || $file_type == 'image/JPEG' || $file_type == 'image/jpg' || $file_type == 'image/JPG'
            || $file_type == 'image/png'  || $file_type == 'image/PNG' || $file_type == 'application/pdf'  || $file_type == 'application/PDF') {
          if ($file_size <= 5242880 ) {
            if(file_put_contents($path_lic.'/'.$filename, base64_decode($base_64))){
              $public_id = uniqid('id_',TRUE);
              $query = "INSERT INTO listado_docs (nombre,path_file,comentario,doc_para,estatus,tipo_usuario,id_usuario,public_id) VALUES (?,?,?,?,?,?,?,?)";
              $query = $this->db->query($query, array($filename,$path,$comentario,$doc_para,1,$utype_token,$uid_token,$public_id)); 
              
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
  
        $comentario = $data_request->comentario;
        $public_id  = $data_request->public_id;
  
        $query = "UPDATE listado_docs SET comentario = ? WHERE public_id = ?";
        $query = $this->db->query($query, array($comentario,$public_id));
        $res = [ "status" => TRUE];
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //suspender usuario
  public function estado_doc_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){

        $data_request = json_decode($data_post);

        $user_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->public_id);
        $estado = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->estatus);

        if ($estado === '1') { $data_upquery = 0; }
        if ($estado === '0') { $data_upquery = 1; }
        //actualizacion del usuario
        $query = "UPDATE listado_docs SET estatus = ? WHERE public_id = ?";
        $query = $this->db->query($query, array($data_upquery,$user_id));
        $res = [ "status" => true ];

      }else{
          $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }
  
}