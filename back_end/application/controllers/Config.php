<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;


class Config extends REST_Controller {

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

  public function changeEmail_post(){
    $listaPOST= file_get_contents("php://input");
    if (isset($listaPOST) && !empty($listaPOST)) {
      $correo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $listaPOST);

      $cons = "SELECT correo,nombre,publicId,usuario FROM usuarios WHERE correo = ? ";
      $query = $this->db->query($cons,array($correo));
      if(!empty($query->result_array())){
        $data = $query->row();
        $pi = $data->publicId;
        $usuario = $data->usuario;
        $nombre = $data->nombre;
        $newcontra = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        $vcontra = sha1($newcontra);
        $cons2 = "UPDATE usuarios SET contra = ? WHERE publicId = ?";
        $this->db->query($cons2,array($vcontra,$pi));

        $email_to = "$correo";
        $email_subject = "Cambio de Credenciales en Ventanilla Virtual Guanajuato";
       	$headers = "From: " . strip_tags('soporte@ventanillavirtualguanajuato.net') . "\r\n";
       	$headers .= "MIME-Version: 1.0\r\n";
       	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
       	$email_message = "<html><body>";
       	$email_message .= "Estimado <strong>".$nombre."</strong> ha modificado sus credenciales satisfactoriamente en el sistema <a href='https://ventanillavirtualguanajuato.net'>ventanillavirtualguanajuato.net</a><br>". "\n";
     	  $email_message .= "<br> Se anexan las nuevas credenciales\n \n <br>.";
        $email_message .= "<table rules='all' style='border-color: #666;'' cellpadding='4'>";
        $email_message .= "<tr style='background: #eee;'><td>Usuario: </td><td><strong>" . $usuario . "</strong></td></tr>\n";
        $email_message .= "<tr ><td>Nueva Contraseña: </td><td><strong>" . $newcontra . "</strong></td></tr>\n";
        $email_message .= "</table>";
        $email_message .= "<br> \n";
        $email_message .= "<br> \n";
        $email_message .= "Saludos Cordiales". "<br>\n";
        $email_message .= "<br> \n";
        $email_message .= "<strong>Ventanilla Virtual Guanajuato</strong>". "<br>\n";
        $email_message .= "</body></html>";
        @mail($email_to, $email_subject, $email_message, $headers);

        $email_to2 = "Imagenurbana@guanajuatocapital.gob.mx";
        $email_subject2 = "Notificación Cambio de Credenciales en Ventanilla Virtual Guanajuato";
        $headers2 = "From: " . strip_tags('soporte@ventanillavirtualguanajuato.net') . "\r\n";
        $headers2 .= "MIME-Version: 1.0\r\n";
        $headers2 .= "Content-Type: text/html; charset=UTF-8\r\n";
        $email_message2 = "<html><body>";
        $email_message2 .= "Estimado Ing. Luis Eduardo Martinez se le notifica que el usuario  con nombre <strong>".$nombre."</strong> y Correo <strong>".$correo."</strong> ha modificado sus credenciales satisfactoriamente en el sistema <br>". "\n";
        $email_message2 .= "<br> <a href='https://ventanillavirtualguanajuato.net'>ventanillavirtualguanajuato.net</a>\n \n <br>.";
        $email_message2 .= "<br> \n";
        $email_message2 .= "<br> \n";
        $email_message2 .= "Saludos Cordiales". "<br>\n";
        $email_message2 .= "<br> \n";
        $email_message2 .= "Soporte Ventanilla Virtual Guanajuato". "<br>\n";
        $email_message2 .= "</body></html>";

        @mail($email_to2, $email_subject2, $email_message2, $headers2);

        $res = 'SE TE ENVIO UN CORREO CON LA CONTRASEÑA';
      }else{
        $res = 'CORREO ERRONEO';
      }

    }else{
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
    $this->response($res);
  }

  function ipmethod() {
    if (isset($_SERVER["HTTP_CLIENT_IP"]))
    {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
    {
        $ip = $_SERVER["HTTP_X_FORWARDED"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
    {
        $ip = $_SERVER["HTTP_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED"]))
    {
        $ip = $_SERVER["HTTP_FORWARDED"];
    }
    else
    {
        $ip = $_SERVER["REMOTE_ADDR"];
    }
   return ($ip);
  }

  public function getConfig_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $query = $this->db->query("SELECT titulo, logo, icono, back1, back2, back3, primaryColor, secondColor, otherColor, columna1, columna2, columna3, titulo1, titulo2, titulo3, icono1, icono2, icono3, public_id FROM config WHERE public_id = 'id_5d2e1d055420e3.11111111' ");
          $res = $query->result_array();
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function ipService_get(){

    $ip = self::ipmethod();
    $meta = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.self::ipmethod()));
    $ciudad = $meta['geoplugin_city'];
    $codPais = $meta['geoplugin_countryCode'];
    $pais = $meta['geoplugin_countryName'];
    $delay = $meta['geoplugin_delay'];
    $region = $meta['geoplugin_region'];
    $codRegion = $meta['geoplugin_regionCode'];
    $zonaHora = $meta['geoplugin_timezone'];
    $nomRegion = $meta['geoplugin_regionName'];
    $latitud = $meta['geoplugin_latitude'];
    $longitud = $meta['geoplugin_longitude'];

    $query = $this->db->query("INSERT INTO visitors
                                   (ip,ciudad,codigo_pais,pais,delay,latitud,longitud,region,codigo_region,nombre_region,zona_horario)
                                   VALUES
                                   ('$ip','$ciudad','$codPais','$pais','$delay','$latitud','$longitud','$region','$codRegion','$nomRegion','$zonaHora')");

    $this->response('');
  }

  public function getConfigI_post(){
    $query = $this->db->query("SELECT titulo, logo, icono, back1, back2, back3, primaryColor, secondColor, otherColor, columna1, columna2, columna3, titulo1, titulo2, titulo3, icono1, icono2, icono3, public_id FROM config WHERE public_id = 'id_5d2e1d055420e3.11111111' ");
    $res = $query->result_array();
    $this->response($res);
  }

  public function updateConfig_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
            foreach ($request as $key => $value) {
                $this->db->query("UPDATE config
                                    SET $key = '$value'
                                    WHERE public_id = 'id_5d2e1d055420e3.11111111'");
            }
          $res = 'actualización exitosa';
        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  function vrfytkn($nfrm, $frmtkn, $tkn) {
   //unic-id form
   session_start();
   $secret = $_SESSION["scrt"];
   $token_frm = sha1($secret.$nfrm.$tkn);
   return ($token_frm == $frmtkn);
  }

  public function srvc_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $tkn = $preparing->id;
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $preparing = $is_valid_token['data'];
        $request = json_decode($listaPOST);
        $data = $request->data;
        session_start();
        $secret = uniqid($data.'_',TRUE);
        $_SESSION["scrt"] = $secret;

        $token_frm = sha1($secret.$data.$tkn);

        $res = $token_frm;
      }else{
        $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = [
          "status" => FALSE,
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }

  public function recapchat_post(){

      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        define('SECRET_KEY', '6Lfi6r8UAAAAAM9PQbBhvW-Gd50I_ODhKkg0xLFS');
        //$response = file_get_contents('https://www.google.com/recaptcha/api/siteverify')
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array('secret' => SECRET_KEY, 'response' => $listaPOST, 'remoteip' => self::ipmethod());

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result = json_decode($result);
        if ($result === FALSE) { $result = FALSE; }
        else {
          //session_start();
          //$_SESSION["scrt"] = $result;
        }
      }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }

    $this->response($result);
  }

  public function lgre_post(){
    $listaPOST= file_get_contents("php://input");
    $request = json_decode($listaPOST);
    $user = $request->user;
    $done = $request->dv;

    $meta = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.self::ipmethod()));
    $ciudad = $meta['geoplugin_city'];
    $codPais = $meta['geoplugin_countryCode'];
    $pais = $meta['geoplugin_countryName'];
    $delay = $meta['geoplugin_delay'];
    $region = $meta['geoplugin_region'];
    $codRegion = $meta['geoplugin_regionCode'];
    $zonaHora = $meta['geoplugin_timezone'];
    $nomRegion = $meta['geoplugin_regionName'];
    $latitud = $meta['geoplugin_latitude'];
    $longitud = $meta['geoplugin_longitude'];

    $query = "INSERT INTO login (usuario, ip, ciudad, pais, latitud, longitud, zona_horario,done)
                                   VALUES(?,?,?,?,?,?,?,?)";
    $query = $this->db->query($query, array($user,self::ipmethod(),$ciudad,$pais,$latitud,$longitud,$zonaHora,$done));
    $this->response('');
  }

  public function sendqs_post(){
    $listaPOST= file_get_contents("php://input");
    $request = json_decode($listaPOST);

    $email = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $request->email);
    $direccion = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $request->direccion);
    $curp = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $request->curp);
    $nombre = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $request->nombre);
    $asunto = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $request->asunto);

    $pid = uniqid('id_',TRUE);
    $query = "INSERT INTO buzon (email, asunto, nombre, direccion, curp, publicId)
                                   VALUES(?,?,?,?,?,?)";
    $query = $this->db->query($query, array($email,$asunto,$nombre,$direccion,$curp,$pid));

    $consulta = "SELECT * FROM buzon WHERE publicId= ? ";
    $query = $this->db->query($consulta , array($pid));
    $result = $query->result_array();
    if (isset($result) && !empty($result)) {
     $res = "Mensaje enviado";
    }else{
      $res = "Mensaje no se envio";
    }
    $this->response($res);
  }

  function newfile_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) && $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);

        $base64 = $request->base64;
        $file_name = $request->filename;
        $file_size = $request->filesize;
        $file_type = $request->filetype;
        $campo = $request->campo;

        $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/plataforma/'.$file_name;
        $pathNewImg = 'plataforma/'.$file_name;

        if ($file_size < 3030477 ) {
            if(file_put_contents($pathImg, base64_decode($base64))){
              $queryU = $this->db->query("UPDATE config
                                            SET $campo = '$pathNewImg'
                                            WHERE public_id = 'id_5d2e1d055420e3.11111111' ");
              $res =  "Archivo cargado y guardado";
            }else {
              $res = "No se pudó guardar la imagen";
            }
          }else {
            $res = "Archivo demasido grande, tamaño requerido '2MB'";
          }
        }else{
          $res = "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

}
