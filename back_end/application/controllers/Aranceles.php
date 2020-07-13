<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;


class Aranceles extends REST_Controller {

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, Authorization");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();
     $this->load->helper(array('form', 'url'));

  }

  public function index_get(){

  }

  public function get_ara_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);
        if (isset($data_request->aid) && !empty($data_request->aid)){
          $aid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->aid);
          $query = "SELECT descripcion AS 'nomas',  precio, tipo, unidad, public_id AS 'idar2' FROM lista_aranceles_espe WHERE id_especialidad = ?";
        }else {
          $aid = '';
          $query = "SELECT idar2, nomas, precio, tipo, unidad  FROM aranceles";
        }
        $query = $this->db->query($query,array($aid));
        $res   = ["data" => $query->result_array(), "status" => true];
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function add_ar_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);

        $concepto = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->concepto);
        $clave    = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->clave);
        $unidad   = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->unidad);
        $costo    = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->costo);
        // $corres    = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->corres);

        $flag = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->flag);

        if ($flag === '0') {
          $query = "INSERT INTO aranceles (nomas, precio, tipo, unidad) VALUES ( ?, ?, ?, ? )";
          $query = $this->db->query($query,array($concepto, $costo, $clave, $corres, $unidad));
        }
        if ($flag === '1') {
          $esid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->esid);

          $pid = uniqid('id_',TRUE);
          $query = "INSERT INTO lista_aranceles_espe (descripcion, precio, tipo, unidad, id_especialidad, public_id) VALUES ( ?, ?, ?, ?, ?, ? ) ";
          $query = $this->db->query($query,array($concepto, $costo, $clave, $unidad, $esid, $pid));
        }




        $res   = [ "status" => true ];
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function up_ar_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);

        $concepto   = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->concepto);
        $clave      = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->clave);
        $unidad     = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->unidad);
        $costo      = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->costo);
        // $corres     = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->corres);
        $id_derecho = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->id_arancel);

        $flag = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->flag);

        if ($flag === '0') { $query = "UPDATE aranceles SET tipo = ?, unidad = ?, precio = ?, nomas = ? WHERE idar2 = ? "; }
        if ($flag === '1') { $query = "UPDATE lista_aranceles_espe SET tipo = ?, unidad = ?, precio = ?, descripcion = ? WHERE public_id = ? "; }

        $this->db->query($query,array($clave,$unidad,$costo,$concepto,$id_derecho));

        $res  = [ "status" => true ];
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function del_ar_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);
        if (isset($data_request->esid) && !empty($data_request->esid)){
          $query = "DELETE FROM lista_aranceles_espe WHERE public_id = ? ";
        }else {
          $query = "DELETE FROM aranceles WHERE idar2 = ? ";
        }
        $id_derecho = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->idar2);
        $this->db->query($query,array($id_derecho));

        $res  = [ "status" => true ];
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }
}
