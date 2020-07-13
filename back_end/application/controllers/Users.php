<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Users extends REST_Controller {
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

  //$this->correousuario($correo,$correoS,$nombre,$tipo_des,$mensaje,$f);

  public function add_inside_user_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){

        $data_request = json_decode($data_post);

        $name  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->name);
        $email = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->email);
        //$pass  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->pass);
        $pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);


        $utype_request = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->utype);

        //master type id_5ebafdbc81b415.25700817
        //jefe dep  id_5ebafe3b361083.91981512
        //colegio   id_5ebc667ccb01b7.90108736

        //dep boss
        if ($utype_token === 'id_5ebafe3b361083.91981512') {
          //colaborador id_5ebc623f267603.74886889
          //consulta de departamento
          $query = "SELECT departamento FROM r_datos_servidor WHERE id_servidor = ?";
          $query = $this->db->query($query, array($uid_token));
          $result = $query->result_array();

          $preparing = $result[0];
          $dep = (string)$preparing['departamento'];
        }

        //contra encrip
        $enc_pass = sha1($pass);
        //user id
        $uid = uniqid('id_',TRUE);

        //validacion del email
        $consulta = "SELECT public_id FROM usuarios WHERE correo = ?";
        $query    = $this->db->query($consulta , array($email));
        $result   = $query->result_array();
        if (isset($result) && !empty($result)) {
          $res = [ "status" => true, "data" => '10' ];
        }else{
          //insercion del usuario
          $query = "INSERT INTO usuarios (nombre,correo,contra,tipo_usuario,estado,public_id) VALUES ( ?,?,?,?,?,?)";
          $this->db->query($query , array($name,$email,$enc_pass,$utype_request,1,$uid));
          //insercion del departamento
          if ($utype_request === 'id_5ebafe3b361083.91981512' || $utype_request === 'id_5ebc623f267603.74886889') {
            $inicial = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->inicial);
            $rid = uniqid('id_',TRUE);
            $query = "INSERT INTO r_datos_servidor (departamento,iniciales,id_servidor,public_id) VALUES (?,?,?,?)";
            $this->db->query($query , array($dep,$inicial,$uid,$rid));
          }
          $res = [ "status" => true, "data" => null ];
        }

      }else{
          $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //get jefe dep colegio colaborador para master y jefe dep
  public function get_admsusrs_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token   = $validation_token['data'];
      $utype_token  = $data_token->utype;

      //master type
      $query  = "SELECT usuarios.nombre, usuarios.correo, usuarios.tipo_usuario,
       usuarios.fecha_registro, usuarios.estado, usuarios.public_id, r_datos_servidor.departamento, r_datos_servidor.iniciales
       FROM usuarios
       LEFT JOIN r_datos_servidor
       ON usuarios.public_id = r_datos_servidor.id_servidor
       WHERE tipo_usuario = ?";
      if ($utype_token === 'id_5ebafdbc81b415.25700817') {
        $query .= 'OR tipo_usuario = ?';
        $query  = $this->db->query($query,array('id_5ebafe3b361083.91981512','id_5ebc667ccb01b7.90108736'));
        //dep boss
      }else{
        $query  = $this->db->query($query,array('id_5ebc623f267603.74886889'));
      }
      $res = ["data" => $query->result_array(), "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }

     $this->response($res);
  }

  public function add_outside_user_post(){
    $data_post   = file_get_contents("php://input");
    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);

      $name  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->name);
      $email = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->email);
      $pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
      $numreg  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->numreg);
      //contra encrip
      $enc_pass = sha1($pass);
      $uid = uniqid('id_',TRUE);

      $consulta_email = "SELECT public_id FROM usuarios WHERE correo = ?";//validacion de email
      $query_email    = $this->db->query($consulta_email , array($email));
      $result_email   = $query_email->result_array();

      $consulta_noreg = "SELECT no_registro FROM r_datos_peritos WHERE no_registro = ?";//validacion de no reg
      $query_noreg    = $this->db->query($consulta_noreg , array($numreg));
      $result_noreg   = $query_noreg->result_array();
      if ((isset($result_email) && !empty($result_email)) || (isset($result_noreg) && !empty($result_noreg))) {
       $res = 10;
      }else{
        $utype_request = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->utype);

        //insercion del usuario
        $query = "INSERT INTO usuarios (nombre,correo,contra,tipo_usuario,estado,public_id) VALUES ( ?,?,?,?,?,?)";
        $this->db->query($query , array($name,$email,$enc_pass,$utype_request,1,$uid));
        $this->correousuario($email,$name,"Bienvenido a la plataforma te anexamos las credenciales:",$pass);

        $street = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->street);
        $suburb = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->suburb);
        $pc     = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->pc);
        $cel    = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->cel);

        $rid = uniqid('id_',TRUE);
        $query = "INSERT INTO r_datos_usuarios (calle,colonia,cp,celular,id_usuario,public_id) VALUES (?,?,?,?,?,?)";
        $this->db->query($query , array($street,$suburb,$pc,$cel,$uid,$rid));
          //particular
        if ($utype_request === 'id_5ebe11ce542b89.25367119') {
          $curp = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->curp);
          $query = "UPDATE r_datos_usuarios SET curp = ? WHERE id_usuario = ? ";
          $this->db->query($query , array($curp,$uid));

          $res = true;
        }
        //perito
        else if ($utype_request === 'id_5ebe10d0b4db29.48120814') {
          $idcard  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->idcard);
          $numreg  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->numreg);
          $college = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->college);
          $is_cor  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->is_cor);

          $rpid = uniqid('id_',TRUE);
          $query = "INSERT INTO r_datos_peritos (cedula,no_registro,id_colegio,corresponsable,id_usuario,public_id) VALUES (?,?,?,?,?,?)";
          $this->db->query($query , array($idcard,$numreg,$college,$is_cor,$uid,$rpid));

          //especialidades
          if ($is_cor === '1' || $is_cor === 'true' || $is_cor === true) {
           $especial = $data_request->especial;
           foreach ($especial as $key => $value) {
            $especiali  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $value);
            $rpeid = uniqid('id_',TRUE);
            $query = "INSERT INTO r_especialidad_perito (id_especialidad,id_usuario,public_id) VALUES (?,?,?)";
            $this->db->query($query , array($especiali,$uid,$rpeid));
           }
          }
          $res = true;
        }
        //other invalid
        else {
          $res = false;
        }
      }
    }else{
        $res = false;
    }
    $this->response($res);
  }

  //update users master jefe dep
  public function up_user_mfd_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){

        $data_request = json_decode($data_post);

        $name  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->name);
        $email = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->email);
        $inicial = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->inicial);
        $user_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->user_id);

        //validacion del email
        $consulta = "SELECT public_id FROM usuarios WHERE correo = ?";
        $query    = $this->db->query($consulta , array($email));
        $result   = $query->result_array();
        if (isset($result) && !empty($result)) {

          $preparing     = $result[0];
          $user_id_query = (string)$preparing['public_id'];
          // mismo correo
          if ($user_id !== $user_id_query) {
            $res = [ "status" => true, "data" => '10' ];
          }else {
            //actualizacion del usuario
            $query = "UPDATE usuarios SET nombre = ? WHERE public_id = ?";
            $query = $this->db->query($query, array($name,$user_id));
            //actualización iniciales
            $query = "UPDATE r_datos_servidor SET iniciales = ? WHERE id_servidor = ?";
            $query = $this->db->query($query, array($inicial,$user_id));
            $res = [ "status" => true, "data" => null ];
          }
        }else{
          //actualizacion del usuario
          $query = "UPDATE usuarios SET nombre = ?, correo = ? WHERE public_id = ?";
          $query = $this->db->query($query, array($name,$email,$user_id));
          //actualización iniciales
          $query = "UPDATE r_datos_servidor SET iniciales = ? WHERE id_servidor = ?";
          $query = $this->db->query($query, array($inicial,$user_id));

          $res = [ "status" => true, "data" => null ];
        }

      }else{
          $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //suspender usuario
  public function estado_usr_post(){
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
        $estado = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->estado);

        if ($estado === '1') { $data_upquery = 0; }
        if ($estado === '0') { $data_upquery = 1; }
        //actualizacion del usuario
        $query = "UPDATE usuarios SET estado = ? WHERE public_id = ?";
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

  //get perfil
  public function get_pro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      // // servidor
      //if ($utype_token === '') {
      //  $con_query = '';
      //}
      if ($utype_token === 'id_5ebe11ce542b89.25367119') { // particular
        $query = "SELECT usuarios.nombre, usuarios.correo
        FROM usuarios
        WHERE usuarios.public_id = ?";
      }
      else if ($utype_token === 'id_5ebe10d0b4db29.48120814') { // perito
        $query = "SELECT usuarios.nombre, usuarios.correo,
        r_datos_usuarios.departamento, r_datos_usuarios.calle, r_datos_usuarios.colonia, r_datos_usuarios.cp, r_datos_usuarios.celular, r_datos_usuarios.curp
        FROM usuarios
        LEFT JOIN r_datos_usuarios ON usuarios.public_id = r_datos_usuarios.id_usuario
        WHERE usuarios.public_id = ?";
      }
      else if ($utype_token === 'id_5ebc667ccb01b7.90108736') { // colegio
        $query = "SELECT usuarios.nombre, usuarios.correo, r_datos_colegio.porcentaje
        FROM usuarios
        LEFT JOIN r_datos_colegio  ON usuarios.public_id = r_datos_colegio.id_colegio
        WHERE usuarios.public_id = ?";
      }
      else if ($utype_token === 'id_5ebafe3b361083.91981512' || $utype_token === 'id_5ebc623f267603.74886889') { // servidor corresponsable
        $query = "SELECT usuarios.nombre, usuarios.correo, 
        r_datos_servidor.departamento, r_datos_servidor.uma, r_datos_servidor.iniciales
        FROM usuarios
        LEFT JOIN r_datos_servidor  ON usuarios.public_id = r_datos_servidor.id_servidor
        WHERE usuarios.public_id = ?";
      }
      else if ($utype_token === 'id_5ebe11ce542b89.25366485') { // cadro
        $query = "SELECT usuarios.nombre, usuarios.correo FROM usuarios WHERE tipo_usuario = ?";
        $uid_token = 'id_5ebc667ccb01b7.90108736';
      }
      $consulta  = $this->db->query($query, array($uid_token));
      $result = $consulta->result_array();

      $res = [ "status" => true, "data" => $result ];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //get especialidad corresponsable
  public function get_esp_cor_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $query = "SELECT especialidades_perito.descripcion, r_especialidad_perito.public_id
      FROM especialidades_perito
      LEFT JOIN r_especialidad_perito ON especialidades_perito.public_id = r_especialidad_perito.id_especialidad
      WHERE r_especialidad_perito.id_usuario = ?";
      
      $consulta  = $this->db->query($query, array($uid_token));
      $result = $consulta->result_array();

      $res = [ "status" => true, "data" => $result ];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //get solicitantes ser
  public function get_sol_ser_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token   = $validation_token['data'];
      $utype_token  = $data_token->utype;

      //master type
      $query  = "SELECT usuarios.nombre, usuarios.correo, usuarios.tipo_usuario,
       usuarios.fecha_registro, usuarios.estado, usuarios.public_id, r_datos_usuarios.departamento
       FROM usuarios
       LEFT JOIN r_datos_usuarios
       ON usuarios.public_id = r_datos_usuarios.id_usuario
       WHERE tipo_usuario = ? OR tipo_usuario = ?";

      $query  = $this->db->query($query,array('id_5ebe10d0b4db29.48120814','id_5ebe11ce542b89.25367119'));
      $res = ["data" => $query->result_array(), "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
     $this->response($res);
  }

  //get peritos col
  public function get_per_col_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token   = $validation_token['data'];
      $utype_token  = $data_token->utype;
      $uid_token   = $data_token->id;

      $query  = "SELECT usuarios.nombre, usuarios.correo, usuarios.tipo_usuario, usuarios.fecha_registro, usuarios.estado,
        r_datos_peritos.id_usuario
        FROM r_datos_peritos
        INNER JOIN usuarios ON r_datos_peritos.id_usuario = usuarios.public_id
        WHERE r_datos_peritos.id_colegio != ?";
      $query  = $this->db->query($query,array($uid_token));

      $res = ["data" => $query->result_array(), "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
     $this->response($res);
  }

  //get peritos corresponsables
  public function get_corres_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);

        $esp_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->public_id);

        $query  = "SELECT usuarios.nombre, usuarios.public_id
          FROM usuarios
          INNER JOIN r_datos_peritos		 ON usuarios.public_id = r_datos_peritos.id_usuario
          INNER JOIN r_especialidad_perito ON usuarios.public_id = r_especialidad_perito.id_usuario
          WHERE r_datos_peritos.corresponsable = ? AND r_especialidad_perito.id_especialidad = ? AND r_especialidad_perito.id_usuario != ?";
        $query  = $this->db->query($query,array(1,$esp_id,$uid_token));

        $res = ["data" => $query->result_array(), "status" => true];
      }else{
          $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
     $this->response($res);
  }

  //get perfil
  public function save_pro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);

        $nombre = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->nombre);
        $email  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->correo);
        if (isset($data_request->contra) && !empty($data_request->contra)){ //contra
          $flag_conta = true;
          $contra = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->contra);        
          $enc_pass = sha1($contra); //contra encrip
        }else{ $flag_conta = false; }
        
        if ($utype_token === 'id_5ebc667ccb01b7.90108736') { //colegio
          //validaciones
          if (isset($data_request->porcentaje) && !empty($data_request->porcentaje)){ //porcentaje
            $porcentaje = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->porcentaje);
          }else{ 
            $porcentaje = ''; 
          }
          //validacion de existencia de porcentaje
          $query  = "SELECT public_id FROM r_datos_colegio WHERE id_colegio = ?";
          $query  = $this->db->query($query, array($uid_token));
          $result = $query->result_array();

          if (isset($result[0]) && !empty($result[0])){
            //$preparing = $result[0];
            //$id_relacion_por = (string)$preparing['id_relacion_por'];
            $query = "UPDATE r_datos_colegio SET porcentaje = ? WHERE id_colegio = ? "; //actulizacion
            $this->db->query($query , array($porcentaje, $uid_token));
          }else{
            //user id
            $rid = uniqid('id_',TRUE);
            $query = "INSERT INTO r_datos_colegio (porcentaje,id_colegio,public_id) VALUES (?,?,?)"; // insercion
            $this->db->query($query , array($porcentaje,$uid_token,$rid));
          }
        }
        if ($utype_token === 'id_5ebafe3b361083.91981512') { //servidor
          //validaciones
          if (isset($data_request->uma) && !empty($data_request->uma)){ //porcentaje
            $uma = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->uma);
          }else{ 
            $uma = 0; 
          }
          $iniciales = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->iniciales);
          $query = "UPDATE r_datos_servidor SET uma = ?, iniciales = ? WHERE id_servidor = ? "; //actulizacion
          $this->db->query($query , array($uma,$iniciales,$uid_token));
        }
        //bandera contra
        if ($flag_conta) { 
          $query = "UPDATE usuarios SET nombre = ?, correo = ?, contra = ? WHERE public_id = ? ";
          $this->db->query($query, array($nombre, $email, $enc_pass,$uid_token));
          $this->correousuario($email,$nombre,"Reciente mente cambiaste tus credenciales, te anexamos las nuevas credenciales:",$contra);
        }else{ 
          $query = "UPDATE usuarios SET nombre = ?, correo = ? WHERE public_id = ? ";
          $this->db->query($query, array($nombre, $email,$uid_token));
        }
                        

        $res = [ "status" => true];
      }else{
          $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //get nueva corresponsable
  public function add_esp_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        //$data_request = json_decode($data_post);
        $query = "SELECT r_especialidad_perito.public_id FROM r_especialidad_perito WHERE r_especialidad_perito.id_usuario = ? AND r_especialidad_perito.id_especialidad = ?";
        
        $consulta  = $this->db->query($query, array($uid_token, $data_post));
        $result = $consulta->result_array();

        if (isset($result) && !empty($result)){
          $res = [ "status" => true, "data" => false ];
        }else{
          $rid = uniqid('id_',TRUE);
          $query = "INSERT INTO r_especialidad_perito (id_especialidad,id_usuario,public_id) VALUES (?,?,?)"; // insercion
          $this->db->query($query , array($data_post,$uid_token,$rid));
          $res = [ "status" => true, "data" => true ];
        }
      }else{
        $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //del especialidad corresponsable
  public function del_esp_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        //$data_request = json_decode($data_post);
        $query = "DELETE FROM r_especialidad_perito WHERE public_id = ? AND id_usuario = ?";
        $this->db->query($query,array($data_post, $uid_token));
        //$result = $consulta->result_array();

        $res = [ "status" => true, "data" => true ];

      }else{
        $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function correousuario($correo,$nombre,$msn,$contra){
    $para = $correo;
    $titulo = 'Desarrollo Urbano';
    $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $cabeceras .= 'To: Desarrollo Urbano <guadalupe@hotmail.com>' . "\r\n";
    $cabeceras .= 'From: '.$nombre.'<'.$correo.'>' . "\r\n";

    $mensaje = '
      <html>
        <head>
        </head>
        <body>
          Estimado(a) <strong> '.$nombre.'</strong>
          '.$msn.' '.$contra.'
          <br><br>
          Acceder a la plataforma <a href="https://guadalupe.licenciaszac.net">guadalupe licencias</a>
          <br><br>
          <strong>Guadalupe / Licencias</strong>
        </body>
      </html>';
    mail($para, $titulo, $mensaje, $cabeceras);
  }

}
?>
