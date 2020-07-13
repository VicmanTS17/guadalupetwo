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

  public function getTicket_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $variable=$validation_token['data'];
    $id=$variable->id;
      if(!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $query = $this->db->query("SELECT ticketheader.imgTicket, ticketheader.datos,ticketheader.fecha,ticketheader.estado,ticketheader.publicTicket, usuarios.nombre
                                    FROM ticketheader
                                    INNER JOIN usuarios ON ticketheader.idCliente = usuarios.publicId");
        $res= $query->result_array();
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function createTicket_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $variable=$validation_token['data'];
    $id=$variable->id;
      if(!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if(isset($listaPOST) && !empty($listaPOST)){
          $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
          $fecha = date('d/').$meses[date('n')-1].date('/y');
          $date = date('d/').$meses[date('n')-1].date('/y H:i');
          $request = json_decode($listaPOST);

          $titu =$request->titulo;
          $detal = $request->detalles;
          $uId = uniqid('id_',TRUE);

          $titulo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $titu);
          $detalles = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $detal);

          $query = "INSERT INTO ticketheader(idCliente,datos,fecha,estado,publicTicket) VALUES ( ?, ? , ? ,?, ?)";
          $this->db->query($query,array($id,$titulo,$fecha,1,$uId));
          $dId = uniqid('id_',TRUE);
          $query2 = "INSERT INTO ticketbody(idPublic,mensaje,fecha,publicHead,tipo,visto,publicUser) VALUES ( ? , ? , ? , ? , ? , ? , ? )";
          $this->db->query($query2,array($dId,$detalles,$date,$uId,'1','0',$id));
          $res = "Registro agregado!";
        }else{
          $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function getTicketUser_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $variable=$validation_token['data'];
    $id=$variable->id;
      if(!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $query = $this->db->query("SELECT ticketheader.imgTicket, ticketheader.idCliente, ticketheader.datos,ticketheader.fecha,ticketheader.estado,ticketheader.publicTicket, usuarios.nombre FROM ticketheader INNER JOIN usuarios ON ticketheader.idCliente = usuarios.publicId WHERE ticketheader.idCliente = '$id' AND ticketheader.estado=1");
        $res= $query->result_array();
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function mostrarDatosTicket_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $variable=$validation_token['data'];
    $id=$variable->id;
    if(!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if(isset($listaPOST) && !empty($listaPOST)){

          $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $listaPOST);

          $cons = "SELECT ticketbody.* FROM ticketheader INNER JOIN ticketbody ON ticketbody.publichead = ticketheader.publicTicket WHERE ticketheader.idCliente= ? AND ticketheader.publicTicket= ? ";
          $query = $this->db->query($cons,array($id,$slistaPOST));
          $res= $query->result_array();
        }else{
          $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
        }
      }else{
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
  $this->response($res);
  }

  public function getDatosTicket_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $variable=$validation_token['data'];
    $id=$variable->id;
    if(!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if(isset($listaPOST) && !empty($listaPOST)){

          $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $listaPOST);

          $cons = "SELECT ticketbody.*, usuarios.nombre FROM ticketheader
                      INNER JOIN ticketbody ON ticketbody.publichead = ticketheader.publicTicket
                      LEFT JOIN usuarios ON ticketbody.publicUser = usuarios.publicId
                      WHERE ticketheader.publicTicket='$listaPOST' ";

          $query = $this->db->query($cons,array($slistaPOST));
          $res= $query->result_array();
        }else{
          $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
        }
      }else{
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
  $this->response($res);
  }

  public function sendMSN_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $variable=$validation_token['data'];
    $id=$variable->id;
      if(!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if(isset($listaPOST) && !empty($listaPOST)){
          $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
          $fecha = date('d/').$meses[date('n')-1].date('/y');
          $date = date('d/').$meses[date('n')-1].date('/y H:i');
          $detal = $request->data;
          $head= $request->publicHead;

          $uId = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $head);
          $detalles = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $detal);

          $query2 = "INSERT INTO ticketbody(idPublic,mensaje,fecha,publicHead,tipo,visto,publicUser) VALUES ( ? , ? , ? , ? , ? , ? , ? )";
          $this->db->query($query2,array($id,$detalles,$date,$uId,'1','0',$id));
          $res = "Registro agregado!";
        }else{
          $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function createTicketImg_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        $preparing = $validation_token['data'];
        $id = $preparing->id;
        if (!empty($validation_token) && $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $fecha = date('d/').$meses[date('n')-1].date('/y');
            $date = date('d/').$meses[date('n')-1].date('/y H:i');

            $titu = $request->titulo;
            $detal = $request->detalles;
            $img = $request->img;
            $base64 = $img->base64;
            $file_name = $img->filename;
            $file_size = $img->filesize;
            $file_type = $img->filetype;
            $uId = uniqid('id_',TRUE);

            $titulo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $titu);
            $detalles = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $detal);

            $prepath = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/';
            $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$id.'/ticket/';
            $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$id.'/ticket/'.$file_name;
            $pathNewImg = $id.'/ticket/'.$file_name;
            if (file_exists($path)) {
                if($file_type == 'image/png' || $file_type == 'image/PNG' || $file_type == 'image/jpg' || $file_type == 'image/JPG' || $file_type =='image/jpeg' || $file_type =='image/JPEG'){
                   if($file_size < 3030477 ) {
                      if(file_put_contents($pathImg, base64_decode($base64))){
                        $query = "INSERT INTO ticketheader(idCliente,datos,fecha,estado,publicTicket,imgTicket) VALUES (? , ? , ? , ? , ? , ?)";
                        $this->db->query($query,array($id,$titulo,$fecha,1,$uId,$pathNewImg));
                        $dId = uniqid('id_',TRUE);
                        $query2 = "INSERT INTO ticketbody(idPublic,mensaje,fecha,publicHead,tipo,visto,publicUser) VALUES ( ? , ? , ? , ? , ? , ? , ? )";
                        $this->db->query($query2,array($dId,$detalles,$date,$uId,'1','0',$id));

                        $res =  "Archivo cargado y guardado";
                      }else {
                        $res = "No se pudó guardar la imagen";
                      }
                    }else {
                      $res = "Archivo demasido grande, tamaño requerido '2MB'";
                    }
                  }else {
                    $res =  "Tipo de archivo no permitido, tipo recomendao 'PNG', 'JPG' o 'JPEG'";
                  }
            }else{
                  mkdir($path, 0777, true);
                  if($file_type == 'image/png' || $file_type == 'image/PNG' || $file_type == 'image/jpg' || $file_type == 'image/JPG' || $file_type =='image/jpeg' || $file_type =='image/JPEG'){
                   if($file_size < 3030477 ) {
                      if(file_put_contents($pathImg, base64_decode($base64))){
                        $query = "INSERT INTO ticketheader(idCliente,datos,fecha,estado,publicTicket,imgTicket) VALUES (? , ? , ? , ? , ? , ?)";
                        $this->db->query($query,array($id,$titulo,$fecha,1,$uId,$pathNewImg));
                        $dId = uniqid('id_',TRUE);
                        $query2 = "INSERT INTO ticketbody(idPublic,mensaje,fecha,publicHead,tipo,visto,publicUser) VALUES ( ? , ? , ? , ? , ? , ? , ? )";
                        $this->db->query($query2,array($dId,$detalles,$date,$uId,'1','0',$id));
                        $res =  "Archivo cargado y guardado";
                      }else {
                        $res = "No se pudó guardar la imagen";
                      }
                    }else {
                      $res = "Archivo demasido grande, tamaño requerido '2MB'";
                    }
                  }else {
                    $res =  "Tipo de archivo no permitido, tipo recomendao 'PNG', 'JPG' o 'JPEG'";
                  }
            }
          }else{
            $res = "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
      $this->response($res);
    }

  public function cerrarTicket_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $variable=$validation_token['data'];
    $id=$variable->id;
      if(!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if(isset($listaPOST) && !empty($listaPOST)){

          $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $listaPOST);

          $query = "UPDATE ticketheader SET estado=0 WHERE publicTicket= ? ";
          $this->db->query($query,array($slistaPOST));
          $res = "cerrado con exitos" ;
        }else{
          $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

}
