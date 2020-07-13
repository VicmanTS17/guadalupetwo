<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;


class Common extends REST_Controller {

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, Authorization, auth");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();
     $this->load->helper(array('form', 'url'));

  }

  public function index_get(){
    $this->response();
  }

  public function query_email_post(){
    $data_post= file_get_contents("php://input");
    if (isset($data_post) && !empty($data_post)) {
      $data_request = json_decode($data_post);
      $email = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->email);

      //validacion del email
      $consulta = "SELECT public_id FROM usuarios WHERE correo = ?";
      $query    = $this->db->query($consulta , array($email));
      $result   = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = true;
      }else{
        $res = false;
      }
    }else{
          $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
    }
    $this->response($res);
  }

  public function login_post(){
    $data_post= file_get_contents("php://input");
    if (isset($data_post) && !empty($data_post)) {
      $data_request = json_decode($data_post);

      $email = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->email);
      $pass = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->pass);
      $enc_pass = sha1($pass);

      $query = "SELECT public_id, tipo_usuario, estado FROM usuarios WHERE correo = ? AND contra = ?";
      $query = $this->db->query($query, array($email,$enc_pass));
      $result = $query->result_array();

      if (isset($result[0]) && !empty($result[0])) {
        $preparing = $result[0];

        $uid   = (string)$preparing['public_id'];
        $utype = (string)$preparing['tipo_usuario'];
        $estado  = (string)$preparing['estado'];

        $data["id"] = $uid;
        $data["time"] = time();
        $data["utype"] = $utype;


        $this->load->library('Authorization_Token');
        $token = $this->authorization_token->generateToken($data);

        $res = [ "status" => TRUE, "token" => $token];

        if($estado === 1 || $estado === '1'){
          // S E R V I D O R
          if ($utype === "id_5ebafe3b361083.91981512" || $utype === "id_5ebc623f267603.74886889") {
            if ($utype === "id_5ebafe3b361083.91981512") {  $res['jdflag']  = '1'; }
            else  {  $res['jdflag']  = '0'; }
            $path = 'servidor.html';
          }
          // C O L E G I O  Y  C A D R O 
          if ($utype === "id_5ebc667ccb01b7.90108736" || $utype === "id_5ebe11ce542b89.25366485" ) {
            if ($utype === "id_5ebe11ce542b89.25366485") {  $res['caflag']  = '1'; }
            else  {  $res['caflag']  = '0'; }
            $path = 'colegio.html';
          }
          //P E R I T O   Y    P A R T I C U L A R
          if ($utype === "id_5ebe10d0b4db29.48120814" || $utype === "id_5ebe11ce542b89.25367119") {
            $path = 'solicitante.html'; $res['estado']  = $estado;
          }
        }else {
          //P E R I T O   Y    P A R T I C U L A R   D E S H A B I L I T A D O S 
          if ($utype === "id_5ebe10d0b4db29.48120814" || $utype === "id_5ebe11ce542b89.25367119") {
            $path = 'solicitante.html'; $res['estado']  = $estado; $res['path']  = $path;
          }else {
            $res = [ "status" => FALSE, "data" => 10];
          }
        }

        // M A S T E R
        if ($utype === "id_5ebafdbc81b415.25700817") {
          $path = 'imda_index.html';
        }
        $res['path']  = $path;
        // $res = [ "status" => TRUE, "token" => $token, "path" => $path];
      }else{
        $res = [ "status" => FALSE, "data" => 0];
      }

    }else {
      $res = [  "status" => FALSE, "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST ];
    }
    $this->response($res);
  }

  //get colle
  public function get_publicol_post(){
    $query = "SELECT nombre, public_id FROM usuarios WHERE tipo_usuario = ? AND estado = ?";
    $query = $this->db->query($query,array('id_5ebc667ccb01b7.90108736', 1));
    $res   = $query->result_array();
    $this->response($res);
  }

  //get colle
  public function get_publicesp_post(){
    $query = "SELECT descripcion, public_id FROM especialidades_perito";
    $query = $this->db->query($query);
    $res   = $query->result_array();
    $this->response($res);
  }

  //get public dros
  public function get_plcdros_post(){
    $query = "SELECT nombre AS 'dro', public_id FROM usuarios WHERE usuarios.tipo_usuario = ?";
    $query  = $this->db->query($query, array('id_5ebe10d0b4db29.48120814'));
    $result = $query->result_array();
    $this->response($result);
  }
}
