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

  public function login_post(){
    $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
      $request = json_decode($listaPOST);
      $us = $request->user;
      $cont = $request->epwd;
      $flag = $request->flag;

      $usuario = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $us);
      $contra1 = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $cont);

      $contra = sha1($contra1);
      $score = $request->scr;
      $score = floatval($score);
        //if ($score > 0.5) {
          if ($flag === true) {
              $cons = "SELECT publicId,tipoUsuario FROM usuarios WHERE usuario COLLATE utf8_bin = ? AND contra = ? AND estado= ? ";
              $query = $this->db->query($cons, array($usuario,$contra,1));
              $result = $query->result_array();
              if (isset($result) && !empty($result)) {
                $preparing = $result[0];
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $mes = $meses[date('n')-1];
                $ano=date('y');
                $dia=date('d');
                $fecha = $dia.'/'.$mes.'/'.$ano;
                $uid = (string)$preparing['publicId'];
                $type = (string)$preparing['tipoUsuario'];
                $fechaSeparada = explode("/", $fecha);
                $ano1 = $fechaSeparada[2];
                $mes1 = $fechaSeparada[1];
                $dia1 = $fechaSeparada[0];

                if ($mes1 == 'Ene' || $mes1 == 'Enero' || $mes1 == 'Jan'){
                  $m = 1;
                }elseif ($mes1 == 'Feb' || $mes1 == 'Febrero'){
                  $m = 2;
                }elseif ($mes1 == 'Mar' || $mes1 == 'Marzo'){
                  $m = 3;
                }elseif ($mes1 == 'Abr' || $mes1 == 'Abril' || $mes1 == 'Apr'){
                  $m = 4;
                }elseif ($mes1 == 'May' || $mes1 == 'Mayo'){
                  $m = 5;
                }elseif ($mes1 == 'Jun' || $mes1 == 'Junio'){
                  $m = 6;
                }elseif ($mes1 == 'Jul' || $mes1 == 'Julio'){
                  $m = 7;
                }elseif ($mes1 == 'Ago' || $mes1 == 'Agosto' || $mes1 == 'Aug'){
                  $m = 8;
                }elseif ($mes1 == 'Sep' || $mes1 == 'Septiembre'){
                  $m = 9;
                }elseif ($mes1 == 'Octubre' || $mes1 == 'Oct'){
                  $m = 10;
                }elseif ($mes1 == 'Nov' || $mes1 == 'Noviembre'){
                  $m = 11;
                }elseif ($mes1 == 'Dic' || $mes1 == 'Diciembre' || $mes1 == 'Dec'){
                  $m = 12;
                }
                  if($type == 'id_5bcac531d1769'){
                    $cons2 = "SELECT vigencia FROM r_dc WHERE idDro = ? ";
                    $queryV = $this->db->query($cons2,array($uid));
                    $dV = $queryV->result_array();
                    if (isset($dV) && !empty($dV)) {
                      $vigeData = $dV[0];
                      $vigencia = $vigeData['vigencia'];
                      $fechaSeparada2 = explode("/", $vigencia);
                      $ano2 = $fechaSeparada2[2];
                      $mes2 = $fechaSeparada2[1];
                      $dia2 = $fechaSeparada2[0];
                      if ($mes2 == 'Ene' || $mes2 == 'Enero' || $mes2 == 'Jan'){
                          $m2 = 1;
                        }elseif ($mes2 == 'Feb' || $mes2 == 'Febrero'){
                          $m2 = 2;
                        }elseif ($mes2 == 'Mar' || $mes2 == 'Marzo'){
                          $m2 = 3;
                        }elseif ($mes2 == 'Abr' || $mes2 == 'Abril' || $mes2 == 'Apr'){
                          $m2 = 4;
                        }elseif ($mes2 == 'May' || $mes2 == 'Mayo'){
                          $m2 = 5;
                        }elseif ($mes2 == 'Jun' || $mes2 == 'Junio'){
                          $m2 = 6;
                        }elseif ($mes2 == 'Jul' || $mes2 == 'Julio'){
                          $m2 = 7;
                        }elseif ($mes2 == 'Ago' || $mes2 == 'Agosto' || $mes2 == 'Aug'){
                          $m2 = 8;
                        }elseif ($mes2 == 'Sep' || $mes2 == 'Septiembre'){
                          $m2 = 9;
                        }elseif ($mes2 == 'Octubre' || $mes2 == 'Oct'){
                          $m2 = 10;
                        }elseif ($mes2 == 'Nov' || $mes2 == 'Noviembre'){
                          $m2 = 11;
                        }elseif ($mes2 == 'Dic' || $mes2 == 'Diciembre' || $mes2 == 'Dec'){
                          $m2 = 12;
                        }

                        if($ano1 == $ano2){
                          if($m < $m2){
                              $data["id"] = $uid;
                              $data["time"] = time();
                              $this->load->library('Authorization_Token');
                              $token = $this->authorization_token->generateToken($data);
                              $res = [
                                  "data" => $data,
                                  "status" => TRUE,
                                  "token" => $token
                                ];
                          }elseif ($m == $m2) {
                            if($dia1 <= $dia2){
                              $data["id"] = $uid;
                              $data["time"] = time();
                              $this->load->library('Authorization_Token');
                              $token = $this->authorization_token->generateToken($data);
                              $res = [
                                  "data" => $data,
                                  "status" => TRUE,
                                  "token" => $token
                                ];
                            }else{
                              $cons3 = "UPDATE usuarios SET estado= ? WHERE publicId= ? ";
                              $this->db->query($cons3,array(0,$uid));
                              $res = [
                                "status" => FALSE,
                                "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
                              ];
                            }
                          }else{
                            $cons3 = "UPDATE usuarios SET estado= ? WHERE publicId= ? ";
                            $this->db->query($cons3,array(0,$uid));
                            $res = [
                              "status" => FALSE,
                              "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
                            ];
                          }
                        }elseif($ano1 < $ano2){
                          $data["id"] = $uid;
                          $data["time"] = time();
                          $this->load->library('Authorization_Token');
                          $token = $this->authorization_token->generateToken($data);
                          $res = [
                              "data" => $data,
                              "status" => TRUE,
                              "token" => $token
                            ];
                        }else{
                          $cons3 = "UPDATE usuarios SET estado= ? WHERE publicId= ? ";
                          $this->db->query($cons3,array(0,$uid));
                          $res = [
                            "status" => FALSE,
                            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
                          ];
                        }
                    }

                  }
                  elseif ($type == 'id_5bcaa54a0a0cf'){
                    $cons = "SELECT vigencia FROM r_dc WHERE idDro =  ? ";
                    $queryV = $this->db->query($cons, array($uid));
                    $dV = $queryV->result_array();
                    if (isset($dV) && !empty($dV)) {
                      $vigeData = $dV[0];
                      $vigencia = $vigeData['vigencia'];
                      $fechaSeparada2 = explode("/", $vigencia);
                      $ano2 = $fechaSeparada2[2];
                      $mes2 = $fechaSeparada2[1];
                      $dia2 = $fechaSeparada2[0];

                        if ($mes2 == 'Ene' || $mes2 == 'Enero' || $mes2 == 'Jan'){
                          $m2 = 1;
                        }elseif ($mes2 == 'Feb' || $mes2 == 'Febrero'){
                          $m2 = 2;
                        }elseif ($mes2 == 'Mar' || $mes2 == 'Marzo'){
                          $m2 = 3;
                        }elseif ($mes2 == 'Abr' || $mes2 == 'Abril' || $mes2 == 'Apr'){
                          $m2 = 4;
                        }elseif ($mes2 == 'May' || $mes2 == 'Mayo'){
                          $m2 = 5;
                        }elseif ($mes2 == 'Jun' || $mes2 == 'Junio'){
                          $m2 = 6;
                        }elseif ($mes2 == 'Jul' || $mes2 == 'Julio'){
                          $m2 = 7;
                        }elseif ($mes2 == 'Ago' || $mes2 == 'Agosto' || $mes2 == 'Aug'){
                          $m2 = 8;
                        }elseif ($mes2 == 'Sep' || $mes2 == 'Septiembre'){
                          $m2 = 9;
                        }elseif ($mes2 == 'Octubre' || $mes2 == 'Oct'){
                          $m2 = 10;
                        }elseif ($mes2 == 'Nov' || $mes2 == 'Noviembre'){
                          $m2 = 11;
                        }elseif ($mes2 == 'Dic' || $mes2 == 'Diciembre' || $mes2 == 'Dec'){
                          $m2 = 12;
                        }
                        if($ano1 == $ano2){
                          if($m == $m2){
                            if($dia1 <= $dia2 ){
                              $data["id"] = $uid;
                              $data["time"] = time();
                              $this->load->library('Authorization_Token');
                              $token = $this->authorization_token->generateToken($data);
                              $res = [
                                  "data" => $data,
                                  "status" => TRUE,
                                  "token" => $token,
                                  "fechas" => $fecha.' '.$vigencia.'nada'
                                ];
                            }else{
                              $cons3 = "UPDATE usuarios SET estado= ? WHERE publicId= ? ";
                              $this->db->query($cons3,array(0,$uid));
                              $data["id"] = $uid;
                              $data["time"] = time();
                              $this->load->library('Authorization_Token');
                              $token = $this->authorization_token->generateToken($data);
                              $res = [
                                  "data" => $data,
                                  "status" => TRUE,
                                  "token" => $token,
                                  "fechas" => $fecha.' '.$vigencia.' dia'
                                ];
                            }
                          }elseif($m < $m2){
                            $data["id"] = $uid;
                            $data["time"] = time();
                            $this->load->library('Authorization_Token');
                            $token = $this->authorization_token->generateToken($data);
                            $res = [
                                "data" => $data,
                                "status" => TRUE,
                                "token" => $token,
                                "fechas" => $fecha.' '.$vigencia.'nada'
                              ];
                          }else{
                            $cons3 = "UPDATE usuarios SET estado= ? WHERE publicId= ? ";
                            $this->db->query($cons3,array(0,$uid));
                            $data["id"] = $uid;
                            $data["time"] = time();
                            $this->load->library('Authorization_Token');
                            $token = $this->authorization_token->generateToken($data);
                            $res = [
                                "data" => $data,
                                "status" => TRUE,
                                "token" => $token,
                                "fechas" => $fecha.' '.$vigencia.' mes'
                              ];
                          }
                        }elseif ($ano1 < $ano2) {
                          $data["id"] = $uid;
                          $data["time"] = time();
                          $this->load->library('Authorization_Token');
                          $token = $this->authorization_token->generateToken($data);
                          $res = [
                              "data" => $data,
                              "status" => TRUE,
                              "token" => $token,
                              "fechas" => $fecha.' '.$vigencia.'nada'
                            ];
                        }else{
                          $cons3 = "UPDATE usuarios SET estado= ? WHERE publicId= ? ";
                          $this->db->query($cons3,array(0,$uid));
                          $data["id"] = $uid;
                          $data["time"] = time();
                          $this->load->library('Authorization_Token');
                          $token = $this->authorization_token->generateToken($data);
                          $res = [
                              "data" => $data,
                              "status" => TRUE,
                              "token" => $token,
                              "fechas" => $fecha.' '.$vigencia.' año'
                            ];
                        }
                    }
                  }
                  else{
                    $data["id"] = $uid;
                    $data["time"] = time();
                    $this->load->library('Authorization_Token');
                    $token = $this->authorization_token->generateToken($data);
                    $res = [
                        "data" => $data,
                        "status" => TRUE,
                        "token" => $token
                      ];
                  }
              }
              else{
                $cons = "SELECT publicId FROM usuarios WHERE usuario COLLATE utf8_bin = ? AND contra = ? AND estado= ? AND tipoUsuario= ? ";
                $query = $this->db->query($cons,array($usuario,$contra,0,'id_5bcaa54a0a0cf'));
                $result = $query->result_array();
                if (isset($result) && !empty($result)) {
                  $preparing = $result[0];
                  $uid = (string)$preparing['publicId'];
                  $data["id"] = $uid;
                  $data["time"] = time();
                  $this->load->library('Authorization_Token');
                  $token = $this->authorization_token->generateToken($data);
                  $res = [
                      "data" => $data,
                      "status" => TRUE,
                      "token" => $token
                    ];
                }else{
                  $res = [
                    "status" => FALSE,
                    "message" => "Usuario o Contraseña Incorrecta"
                  ];
                }
              }
            }else {
              $res = [
                "status" => FALSE,
                "message" => "Json not validated, Error : ".REST_Controller::HTTP_BAD_REQUEST
                ];
            }
        /*}else {
          $res = [
              "status" => FALSE,
              "message" => "Acción indebida contacte con soporte 'scr04'",
              "data" => $score
            ];
        }*/
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    $this->response($res);
  }

  public function userType_post(){

    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      $cons = "SELECT tipoUsuario FROM usuarios WHERE publicId = ? ";
      $query = $this->db->query($cons,array($uid));
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $prePath = $result[0];
        $type = $prePath['tipoUsuario'];
        if ($type == 'id_5bca9bf3d4b4b') {
          $path = [
              "path" =>'master.html',
              "idma" => FALSE
            ];
        }elseif ($type === 'id_5bcaa531d3338') {
          $path = [
              "path" =>'college.html',
              "idma" => FALSE,
              "cadro" => FALSE
            ];
        }elseif ($type == 'id_5bcac531d1756') {
          $path = [
              "path" =>'college.html',
              "idma" => TRUE,
              "check" => false,
              "cadro" => TRUE
            ];
        }elseif ($type == 'id_5bcaa54a0a0cf') {
          $q = $this->db->query("SELECT estado FROM usuarios WHERE publicId = '$uid' AND estado=1");
          $r = $q->result_array();
          if (isset($r) && !empty($r)) {
            $path = [
                "path" =>'dro.html',
                "check" => true,
                "idma" => FALSE
              ];
          }else{
            $path = [
                "path" =>'dro.html',
                "check" => false,
                "idma" => FALSE
              ];
          }
        }elseif ($type == 'id_5bca91d35a038') {
          $path = [
              "path" =>'developer.html',
              "idma" => FALSE
            ];
        }elseif ($type == 'id_5bcac531d1769') {
            $path = [
                "path" =>'dro.html',
                "check" => false,
                "idma" => FALSE,
                "dro" => false
              ];
        }elseif ($type == 'id_5bcaa4f838f9e') {
          $queryAdm = $this->db->query("SELECT publicId, nombre FROM departamentos WHERE idAdministrador = '$uid' AND nombre = 'Coordinación Administrativa Urbana'");
          $resultAdm = $queryAdm->result_array();
          if (isset($resultAdm) && !empty($resultAdm)) {
            //$path = 'administrator.html';
            $path = [
                "status" => TRUE,
                "path" =>'coordinacion.html',
                "idma" => TRUE,
                "data" => $resultAdm,
                "check" => true,
                "cadro" => FALSE
              ];
          }else {
            $queryAdm = $this->db->query("SELECT publicId, nombre FROM departamentos WHERE idAdministrador = '$uid' AND nombre = 'Finanzas'");
            $resultAdm = $queryAdm->result_array();
            if (isset($resultAdm) && !empty($resultAdm)) {
              //$path = 'administrator.html';
              $path = [
                  "status" => TRUE,
                  "path" =>'finanzas.html',
                  "idma" => TRUE,
                  "data" => $resultAdm,
                  "check" => true,
                  "cadro" => FALSE
                ];
            }else {
                $queryAdm = $this->db->query("SELECT publicId, nombre FROM departamentos WHERE idAdministrador = '$uid'");
                $resultAdm = $queryAdm->result_array();
                if (isset($resultAdm) && !empty($resultAdm)) {
                  //$path = 'administrator.html';
                  $path = [
                      "status" => TRUE,
                      "path" =>'administrator.html',
                      "idma" => TRUE,
                      "data" => $resultAdm,
                      "check" => true,
                      "cadro" => FALSE
                    ];
                }else {
                      $path = [
                          "idma" => TRUE,
                          "status" => FALSE,
                          "message" => "You have not deparment asignate, Error : " . REST_Controller::HTTP_BAD_REQUEST
                        ];
                }
            }
          }
        }elseif ($type == 'id_5bcaa5151ec8b') {
          $queryAdm = $this->db->query("SELECT r_afd.publicId, departamentos.nombre, departamentos.publicId
                                              FROM r_afd LEFT JOIN departamentos ON r_afd.idDepa = departamentos.publicId
                                              WHERE r_afd.idFuncionario = '$uid'");
          $resultAdm = $queryAdm->result_array();
          if (isset($resultAdm) && !empty($resultAdm)) {
            //$path = 'administrator.html';
            $path = [
                "status" => TRUE,
                "path" =>'administrator.html',
                "idma" => TRUE,
                "data" => $resultAdm,
                "check" => false,
                "cadro" => FALSE
              ];
          }else {
                $path = [
                    "idma" => TRUE,
                    "status" => FALSE,
                    "message" => "You have not deparment asignate, Error : " . REST_Controller::HTTP_BAD_REQUEST
                  ];
          }
        }elseif ($type == 'id_5bcac531d1758') {
          $path = [
              "path" =>'particular.html',
              "check" => false,
              "idma" => false,
              "part" => true
            ];
        }
        $data['id'] = $uid;
        $data['time'] = time();
        $this->load->library('Authorization_Token');
        $token = $this->authorization_token->generateToken($data);
        $res = [
            "status" => TRUE,
            //"token" => $token,
            "path" => $path
          ];
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    }else {
      $res = [
          "status" => FALSE,
          "JTWMSG" =>$is_valid_token['message'],
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }
}
