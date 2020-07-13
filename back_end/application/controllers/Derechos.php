<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Derechos extends REST_Controller {

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

  public function get_der_ser_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $query = "SELECT concepto, unidad, costo, id_derecho FROM derechos WHERE activo = ?";
      $query = $this->db->query($query,array('1'));
      $res   = ["data" => $query->result_array(), "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //guardar licencia
public function save_lic_der_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);

      $traid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->traid);
      $licid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->licid);

      $ara = $data_request->ara;
      // lic_id
      $public_id = uniqid('id_',TRUE);

      $total_gral = $ara[count($ara)-1];
      $lenght_ara = count($ara);
      for ($i=0; $i < $lenght_ara-1; $i++) {
        $ra_public_id = uniqid('id_',TRUE);

        $derecho      = $ara[$i]->concepto;
        $precio       = (float) $ara[$i]->costo;
        $unidad       = $ara[$i]->unidad;
        $cantidad     = (float) $ara[$i]->cantidad;
        $total        = (float) $ara[$i]->total;
        //insercion en trabla de relacion
        $query = "INSERT INTO r_licencias_derechos (id_licencia,id_tramite,derecho,precio,unidad,cantidad,
          total,total_gral,public_id) VALUES (?,?,?,?,?,?,?,?,?)";
        $query = $this->db->query($query, array($licid,$traid,$derecho,$precio,$unidad,$cantidad,$total,$total_gral,$ra_public_id));  
      }
      //consulta de iniciales dl servidor
      $query_i = "SELECT iniciales FROM r_datos_servidor  WHERE id_servidor = ? ";
      $query_i = $this->db->query($query_i,array($uid_token));
      $result  = $query_i->result_array();
      $iniciales  = $result[0]['iniciales'];
      
      $query = "UPDATE licencias SET is_der = ?, iniciales_gen_op = ?, estatus = ? WHERE public_id = ?";
      $query = $this->db->query($query, array(1, $iniciales, 'Por Pagar',$licid));

      $query_f = "SELECT id_licencia, is_per, id_usuario, lista_tramites.tramite AS 'tramite' 
        FROM licencias INNER JOIN lista_tramites ON licencias.id_tramite = lista_tramites.public_id
        WHERE licencias.public_id = ? ";
      $query_f = $this->db->query($query_f,array($licid));
      $result  = $query_f->result_array();
      $is_per  = $result[0]['is_per'];

      if ($is_per === '1' || $is_per === 1) {//es perito
        //consulta de correo en tabla propietario relacion lic
        $query_c = "SELECT correo FROM lic_datos_propietario WHERE id_licencia = ? ";
        $query_c = $this->db->query($query_c,array($licid));    
      }else{ //es particular
        $id_usuario  = $result[0]['id_usuario'];
        $query_c     = "SELECT correo FROM usuarios WHERE public_id = ? ";
        $query_c     = $this->db->query($query_c,array($id_usuario));        
      }

      $result_c     = $query_c->result_array();
      $pre_email_to = $result_c[0];
      $email_to     = $pre_email_to['correo'];

      $this->correousuario($email_to, $result[0]['id_licencia'], $result[0]['tramite'], $is_per);
      
      $res = true;
    }else{
      $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
 $this->response($res);
}

  public function add_der_post(){
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

        $query = "INSERT INTO derechos (clave,concepto,unidad,costo,activo) VALUES ( ?, ?, ?, ?, ? )";
        $query = $this->db->query($query,array($clave,$concepto,$unidad,$costo,1));

        $res   = [ "status" => true ];
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function up_der_post(){
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
        $id_derecho = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->id_derecho);

        $query = "UPDATE derechos SET clave = ? ,  unidad = ? , costo = ? , concepto = ? WHERE id_derecho = ? ";
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

  public function del_der_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);

        $id_derecho = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->id_derecho);

        $query = "DELETE FROM derechos WHERE id_derecho = ? ";
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

  //email funcion
  public function correousuario($email_to, $folio, $tramite, $is_per){
    
    $email_subject = "Generación de Orden de pago";

    $headers = "From: " . strip_tags('soporte@guadalupe.licenciaszac.net') . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $email_message = "<html><body>";
    $email_message .= "Se ha generado la orden de pago para el trámite <b>$tramite</b> con folio <b>$folio</b>";
    if ($is_per === '1' || $is_per === 1) { $email_message .= "<br>Conctacte con su perito para obtener el PDF correspondiente.\n \n <br>."; }
    else { $email_message .= "<br>Revise la plataforma para obtener el PDF correspondiente\n \n <br>.";}
    
    #$email_message .= "<table rules='all' style='border-color: #666;'' cellpadding='4'>";
    #$email_message .= "<tr style='background: #eee;'><td>Usuario: </td><td><strong>" . $email_to . "</strong></td></tr>\n";
    #$email_message .= "<tr ><td>Asunto: </td><td><strong>" . $temp_pass . "</strong></td></tr>\n";
    #$email_message .= "</table>";
    #$email_message .= "<br> \n";
    $email_message .= "<br> \n";
    $email_message .= "Saludos Cordiales". "<br>\n";
    $email_message .= "<br> \n";
    $email_message .= "Desarrollo Urbano". "<br>\n";
    $email_message .= "</body></html>";

    mail($email_to, $email_subject, $email_message, $headers);
  }
}