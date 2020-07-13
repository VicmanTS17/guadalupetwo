<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Ticket extends REST_Controller {
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

  public function createTicket_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $data_post   = file_get_contents("php://input");
      if (isset($data_post) && !empty($data_post)){

        $data_request = json_decode($data_post);

        $asunto      = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->asunto);
        $descripcion = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->descripcion);
        $fecha       = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->fecha);

        $ticketHid   = uniqid('id_',TRUE);
        $query = "INSERT INTO ticket_asunto(id_usuario, asunto, estado, fecha, public_id) VALUES (?,?,?,?,?)";
        $this->db->query($query,array($uid_token,$asunto,1,$fecha,$ticketHid));

        $ticketMsjid = uniqid('id_',TRUE);
        $query = "INSERT INTO ticket_msjs(mensaje, tipo, fecha, id_tasunto, user_id, public_id) VALUES (?,?,?,?,?,?)";
        $this->db->query($query,array($descripcion,'0',$fecha,$ticketHid,$uid_token,$ticketMsjid));

          if (isset($data_request->data_img) && !empty($data_request->data_img)) {
            $data_img = $data_request->data_img;
            $base64 = $data_img->base64;
            $file_name = $data_img->filename;
            $file_size = $data_img->filesize;
            $file_type = $data_img->filetype;

            //i m a g e n
            $path_lic = '/home1/francisco/guadalupe.inmueblesenmexico.net/public_files/user_assets/'.$uid_token.'/tickets/';
            if (!file_exists($path_lic)) {
              mkdir($path_lic, 0777, true);
            }
            $path_newimg = $uid_token.'/tickets/'.$file_name;
            if($file_type == 'image/png' || $file_type == 'image/PNG' || $file_type == 'image/jpg' || $file_type == 'image/JPG' ||
               $file_type =='image/jpeg' || $file_type =='image/JPEG'){
              if($file_size < 3030477 ) {
                if(file_put_contents($path_lic.'/'.$file_name, base64_decode($base64))){
                  $query = "UPDATE ticket_asunto SET path_imagen = ? WHERE public_id = ? ";
                  $this->db->query($query,array($path_newimg, $ticketHid));

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
            $res = [ "status" => TRUE, "data" => 2 ];
          }

      }else{
        $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //get ticket master
  public function getMasTicket_get(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if ($utype_token === 'id_5ebafdbc81b415.25700817' && $uid_token === 'id_5ebc38f0d0be05.26294606') {
        $query = $this->db->query("SELECT ticket_asunto.path_imagen, ticket_asunto.asunto, ticket_asunto.fecha, ticket_asunto.estado, ticket_asunto.public_id,
            usuarios.nombre
            FROM ticket_asunto
            INNER JOIN usuarios ON ticket_asunto.id_usuario = usuarios.public_id
          ORDER BY ticket_asunto.fecha DESC");
        $res= $query->result_array();
      }else{
          $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
      }

    }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //get ticket user
  public function getUsrTicket_get(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $query = "SELECT ticket_asunto.path_imagen, ticket_asunto.asunto, ticket_asunto.fecha, ticket_asunto.estado, ticket_asunto.public_id,
          usuarios.nombre
          FROM ticket_asunto
          INNER JOIN usuarios ON ticket_asunto.id_usuario = usuarios.public_id
          WHERE ticket_asunto.id_usuario = ? AND ticket_asunto.estado = ?
          ORDER BY ticket_asunto.id_asunto DESC";

      $query  = $this->db->query($query, array($uid_token,1));
      $res = $query->result_array();

    }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function get_dataticket_get($ticket_head){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

        $query = "SELECT usuarios.nombre, usuarios.tipo_usuario,
          ticket_msjs.mensaje, ticket_msjs.fecha, ticket_msjs.tipo, ticket_msjs.public_id
          FROM ticket_msjs INNER JOIN usuarios
          ON ticket_msjs.user_id = usuarios.public_id
          WHERE ticket_msjs.id_tasunto= ? ";
        $query = $this->db->query($query, array($ticket_head));
        $res = $query->result_array();
      }else{
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function sendmsn_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $ticketMsjid = uniqid('id_',TRUE);

      $listaPOST= file_get_contents("php://input");
      if(isset($listaPOST) && !empty($listaPOST)){
        $data_request = json_decode($listaPOST);
        $msj          = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->msj);
        $ticket_head  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->ticket_head);
        $fecha        = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->fecha);

        $query = "INSERT INTO ticket_msjs(mensaje,fecha,id_tasunto,user_id,public_id) VALUES (?,?,?,?,?)";
        $this->db->query($query,array($msj,$fecha,$ticket_head,$uid_token,$ticketMsjid));
        $res = "se registro mensaje";
      }else{
        $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
      }
    }else{
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function closeticket_delete($ticketHid){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $query = "UPDATE ticket_asunto SET estado = ? WHERE public_id = ? ";
      $this->db->query($query,array(0, $ticketHid));

      $res = "Ticket cerrado";

    }else{
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

}
