<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Usuarios extends REST_Controller {
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

#add master-admin-colegio
  public function addMAC_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);
        $date = date('d/M/y');
          $nombre = $request->nombre;
          $correo = $request->correo;
          $usuario = $request->usuario;
          $contra = $request->contraseña;
          $tipo = $request->tipo;
          $imgName = $request->imgName;

          $snombre = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombre);
          $scorreo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $correo);
          $susuario= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $usuario);
          $scontra= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);
          $stipo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $tipo);

          $vcontra = sha1($scontra);

         $uId = uniqid('id_',TRUE);
         $pathProfile = $uId.'/profile/'.$imgName;
         //$query = $this->db->query("SELECT * FROM usuarios WHERE usuario='$usuario' OR correo='$correo'");
         $consulta = "SELECT * FROM usuarios WHERE usuario= ? ";
         $query = $this->db->query($consulta , array($susuario));
         $result = $query->result_array();
         if (isset($result) && !empty($result)) {
          $res = "Usuario ya existente";
         }else{
            $cons2 = "INSERT INTO usuarios
                                        (nombre,correo,usuario,contra,tipoUsuario,fechaRegistro,nombreImagen,publicId)
                                        VALUES
                                        ( ? , ? , ? , ? , ? , ? , ? , ?)";
            $this->db->query($cons2 , array($snombre,$scorreo,$susuario, $vcontra,$stipo,$date,$pathProfile,$uId));

            $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uId.'/profile/';
            $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/img/'.$imgName;
            mkdir($path, 0777, true);
            copy($pathImg, $path.$imgName);

            $email_to2 = $correo;
            $email_subject2 = "Registro en Ventanilla Virtual Guanajuato";

            $headers2 = "From: " . strip_tags('soporte@ventanillavirtualguanajuato.net') . "\r\n";
            $headers2 .= "MIME-Version: 1.0\r\n";
            $headers2 .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email_message2 = "<html><body>";
            $email_message2 .= "Estimado ".$snombre." Te has registrado de forma exitosa en la plataforma de <br>". "\n";
            $email_message2 .= "<br> <a href='https://ventanillavirtualguanajuato.net'>ventanillavirtualguanajuato.net</a>\n \n <br>.";
            $email_message2 .= "<br> \n";
            $email_message2 .= "<br> \n";
            $email_message2 .= "<br> Se anexan las nuevas credenciales\n \n <br>.";
            $email_message2 .= "<table rules='all' style='border-color: #666;'' cellpadding='4'>";
            $email_message2 .= "<tr style='background: #eee;'><td>Usuario: </td><td><strong>" . $susuario . "</strong></td></tr>\n";
            $email_message2 .= "<tr ><td>Nueva Contraseña: </td><td><strong>" . $scontra . "</strong></td></tr>\n";
            $email_message2 .= "</table>";
            $email_message2 .= "Saludos Cordiales". "<br>\n";
            $email_message2 .= "<br> \n";
            $email_message2 .= "Soporte Ventanilla Virtual Guanajuato". "<br>\n";
            $email_message2 .= "</body></html>";

            @mail($email_to2, $email_subject2, $email_message2, $headers2);

            $res = "Registro agregado!";
         }
      }else{
          $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  #suspender master-admin-colegio
    public function susMAC_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        $preparing = $validation_token['data'];
        $uid = $preparing->id;
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $date = date('d/M/y');

          $contra = $request->contraseña;
          $publicId = $request->publicId;

          $scontra= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);
          $spublicId= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $publicId);

          $consulta = "SELECT nombre FROM usuarios WHERE publicId = ? AND contra = ? ";
          $query = $this->db->query($consulta,array($spublicId,$scontra));

          $result = $query->result_array();
            if (isset($result) && !empty($result)) {
              $cons2 = "UPDATE usuarios SET estado = 0 WHERE publicId = ? ";
              $this->db->query($cons2 , array($spublicId));
              $res = 'Estado cambiado!';
            }else {
              $res =  "Contraseña Incorrecta, Error : " . REST_Controller::HTTP_BAD_REQUEST;
            }
        }else{
            $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
    }

    #delet master-admin-colegio
      public function delMAC_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $date = date('d/M/y');

            $contra = $request->contraseña;
            $publicId = $request->publicId;

            $scontra= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);
            $spublicId= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $publicId);

            $consulta = "SELECT nombre FROM usuarios WHERE publicId = ? AND contra = ? ";
            $query = $this->db->query($consulta,array($spublicId,$scontra));

            $result = $query->result_array();

            if (isset($result) && !empty($result)) {
               $cons2 = "DELETE FROM usuarios WHERE publicId = ? ";
               $this->db->query($cons2 , array($spublicId));
                //unlink('/home3/sanluis/public_html/public_files/'.$publicId);
                $res = 'Registro eliminado!';
             }else {
               $res =  "Contraseña Incorrecta, Error : " . REST_Controller::HTTP_BAD_REQUEST;
             }
          }else{
              $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
      }

      #edit master-admin-colegio
        public function editMAC_post(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
            $preparing = $validation_token['data'];
            $uid = $preparing->id;
            if (isset($listaPOST) && !empty($listaPOST)) {
              $request = json_decode($listaPOST);
              $date = date('d/M/y');
              $correo = $request->correo;
              $nombre = $request->nombre;
              $publicId = $request->publicId;
              //$contra = $request->pwd;
              $usuario = $request->usuario;
              $estado = $request->estatus;

              $scorreo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $correo);
              $snombre = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombre);
              $spublicId = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $publicId);
              $susuario = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $usuario);
              $sestado = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $estado);

              $consulta= "UPDATE usuarios SET correo = ? , nombre = ? , usuario = ? , estado = ? WHERE publicId = ? ";
              $this->db->query($consulta, array($scorreo,$snombre,$susuario,$sestado,$spublicId));

              $res = 'Registro actualizado!';
            }else{
                $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
            }
          }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
          $this->response($res);
        }

  #add funcionario-colaborado
    public function addF_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);

          $date = date('d/M/y');

          $nombre = $request->nombre;
          $correo = $request->correo;
          $usuario = $request->usuario;
          $contra = $request->contraseña;
          $tipo = $request->tipo;
          $idDepa = $request->idDepa;
          $imgName = $request->imgName;

          $snombre = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombre);
          $scorreo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $correo);
          $susuario= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $usuario);
          $scontra= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);
          $stipo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $tipo);
          $sdepa = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $idDepa);
          $vcontra = sha1($scontra);

          //$query = $this->db->query("SELECT * FROM usuarios WHERE usuario='$usuario' OR correo='$correo'");
          $consulta = "SELECT * FROM usuarios WHERE usuario= ? ";
          $query = $this->db->query($consulta , array($susuario));

          $result = $query->result_array();
          if (isset($result) && !empty($result)) {
           $res = "Usuario ya existente";
          }else{
            $uId = uniqid('id_',TRUE);
            $pathProfile = $uId.'/profile/'.$imgName;

            $cons2 = "INSERT INTO usuarios
                                  (nombre,correo,usuario,contra,tipoUsuario,fechaRegistro,nombreImagen,publicId)
                                  VALUES
                                  ( ? , ? , ? , ? , ? , ? , ? , ?)";
            $this->db->query($cons2 , array($snombre,$scorreo,$susuario, $vcontra,$stipo,$date,$pathProfile,$uId));


            $dId = uniqid('id_',TRUE);
            $cons3 = "INSERT INTO r_afd (idFuncionario,idDepa,publicId)
                                         VALUES  ( ? ,?, ?)";

            $this->db->query($cons3, array($uId,$idDepa,$dId));

            $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uId.'/profile/';
            $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/img/'.$imgName;
            mkdir($path, 0777, true);
            copy($pathImg, $path.$imgName);
            $res = "Registro Realizadó!";
          }
        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
    }

    #delet col
      public function delCol_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $date = date('d/M/y');

            $contra = $request->contraseña;
            $publicId = $request->publicId;

            $scontra = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);
            $spublicId = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $publicId);

            $consulta = "SELECT nombre FROM usuarios WHERE publicId = ? AND contra = ? ";
            $query = $this->db->query($consulta,array($spublicId,$scontra));

            $result = $query->result_array();
              if (isset($result) && !empty($result)) {
                $cons2 = "DELETE FROM usuarios WHERE publicId = ? ";
                $this->db->query($cons2 , array($spublicId));

                $cons3 = "DELETE FROM r_afd  WHERE idFuncionario = ? ";
                $this->db->query($cons3 , array($spublicId));
                //unlink('/home3/sanluis/public_html/public_files/'.$publicId);
                $res = 'Registro eliminado!';
             }else {
               $res =  "Contraseña Incorrecta, Error : " . REST_Controller::HTTP_BAD_REQUEST;
             }
          }else{
              $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
      }

    #add DRO externo

  public function addD_post(){
    $listaPOST= file_get_contents("php://input");
    if (isset($listaPOST) && !empty($listaPOST)) {
      $listaPOST= file_get_contents("php://input");
      $request = json_decode($listaPOST);
      $date = date('d/M/y');

      $nombre = $request->nombre;
      $correo = $request->correo;
      $usuario = $request->usuario;
      $contra = $request->contraseña;
      $tipo = $request->tipo;
      $imgName = $request->imgName;
      ##
      $cedula = $request->cedula;
      $rop = $request->rop;
      $domicilio = $request->domicilio;
      $celular = $request->celular;
      ##
      $idColegio = $request->colle_Id;
      $nombreColegio = $request->colle_Name;

      $snombre = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombre);
      $scorreo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $correo);
      $susuario= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $usuario);
      $scontra= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);
      $stipo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $tipo);

      $scedula = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $cedula);
      $srop = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $rop);
      $sdomicilio = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $domicilio);
      $scelular = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $celular);

      $sidColegio = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $idColegio);
      $snombreColegio = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombreColegio);

      $vcontra = sha1($scontra);

      $uId = uniqid('id_',TRUE);
      $pathProfile = $uId.'/profile/'.$imgName;
      //$query = $this->db->query("SELECT * FROM usuarios WHERE usuario='$usuario' OR correo='$correo'");
      $consulta = "SELECT * FROM usuarios WHERE usuario = ? ";
      $query = $this->db->query($consulta , array($susuario));

      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
       $res = "Usuario ya existente";
      }else{
        $uId = uniqid('id_',TRUE);
        $pathProfile = $uId.'/profile/'.$imgName;
        $query = "INSERT INTO usuarios
                                    (nombre,correo,usuario,contra,tipoUsuario,fechaRegistro,estado,nombreImagen,publicId)
                                    VALUES
                                    ( ? , ? , ? , ? , ? , ? , ? , ? , ? )";

        $this->db->query($query ,array($nombre,$correo,$usuario,$vcontra,$tipo,$date,0,$pathProfile,$uId));
        //$idDRO = $this->db->insert_id();
        $dId = uniqid('id_',TRUE);
        $queryR = "INSERT INTO r_dc
                    (idDRO,cedula,rop,domicilio,celular,nombre_colle,idColegio,publicId)
                    VALUES (? ,? ,? ,? ,? ,? ,? ,? )";

        $this->db->query($queryR,array($uId,$cedula,$rop,$domicilio,$celular,$nombreColegio,$idColegio,$dId));

        $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uId.'/profile/';
        $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/img/'.$imgName;
        mkdir($path, 0777, true);
        copy($pathImg, $path.$imgName);
          if (isset($request->pesp)) {
            $pesp = $request->pesp;
            $rDeId = uniqid('id_',TRUE);
            $queryRDE = "INSERT INTO r_droes (idDRO,idEspecialidad,publicId) VALUES (? ,? ,? )";
            $this->db->query($queryRDE,array($uId,$pesp,$rDeId));
          }
          if (isset($request->sesp)) {
            $sesp = $request->sesp;
            $rDeId = uniqid('id_',TRUE);
            $queryRDE = "INSERT INTO r_droes (idDRO,idEspecialidad,publicId) VALUES (? ,? ,? )";
            $this->db->query($queryRDE,array($uId,$sesp,$rDeId));
          }
          if (isset($request->tesp)) {
            $tesp = $request->tesp;
            $rDeId = uniqid('id_',TRUE);
            $queryRDE = "INSERT INTO r_droes (idDRO,idEspecialidad,publicId) VALUES (? ,? ,? )";
            $this->db->query($queryRDE,array($uId,$tesp,$rDeId));
          }
          if (isset($request->cesp)) {
            $cesp = $request->cesp;
            $rDeId = uniqid('id_',TRUE);
            $queryRDE = "INSERT INTO r_droes (idDRO,idEspecialidad,publicId) VALUES (? ,? ,? )";
            $this->db->query($queryRDE,array($uId,$cesp,$rDeId));
          }

          $email_to2 = $correo;
          $email_subject2 = "Registro en Ventanilla Virtual Guanajuato";

          $headers2 = "From: " . strip_tags('soporte@ventanillavirtualguanajuato.net') . "\r\n";
          $headers2 .= "MIME-Version: 1.0\r\n";
          $headers2 .= "Content-Type: text/html; charset=UTF-8\r\n";
          $email_message2 = "<html><body>";
          $email_message2 .= "Estimado ".$snombre." Te has registrado de forma exitosa en la plataforma de <br>". "\n";
          $email_message2 .= "<br> <a href='https://ventanillavirtualguanajuato.net'>ventanillavirtualguanajuato.net</a>\n \n <br>.";
          $email_message2 .= "<br> \n";
          $email_message2 .= "<br> \n";
          $email_message2 .= "<br> Se anexan las nuevas credenciales\n \n <br>.";
          $email_message2 .= "<table rules='all' style='border-color: #666;'' cellpadding='4'>";
          $email_message2 .= "<tr style='background: #eee;'><td>Usuario: </td><td><strong>" . $susuario . "</strong></td></tr>\n";
          $email_message2 .= "<tr ><td>Nueva Contraseña: </td><td><strong>" . $scontra . "</strong></td></tr>\n";
          $email_message2 .= "</table>";
          $email_message2 .= "Saludos Cordiales". "<br>\n";
          $email_message2 .= "<br> \n";
          $email_message2 .= "Soporte Ventanilla Virtual Guanajuato". "<br>\n";
          $email_message2 .= "</body></html>";

          @mail($email_to2, $email_subject2, $email_message2, $headers2);

          $res = 'Registro realizadó!';
        }
    }else{
      $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
    }
    $this->response($res);
  }

    #add Desarrollador externo
    public function addDe_post(){
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $listaPOST= file_get_contents("php://input");
          $request = json_decode($listaPOST);
          $date = date('d/M/y');

          $nombre = $request->nombre;
          $correo = $request->correo;
          $usuario = $request->usuario;
          $contra = $request->contraseña;
          $tipo = $request->tipo;
          $imgName = $request->imgName;
          ##
          $compania = $request->company;

          $snombre = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombre);
          $scorreo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $correo);
          $susuario= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $usuario);
          $scontra= preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);
          $stipo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $tipo);

          $vcontra = sha1($scontra);

          $scompania = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $compania);

          //$query = $this->db->query("SELECT * FROM usuarios WHERE usuario='$usuario' OR correo='$correo'");
          $consulta = "SELECT * FROM usuarios WHERE usuario= ? ";
          $query = $this->db->query($consulta , array($susuario));
          $result = $query->result_array();
          if (isset($result) && !empty($result)) {
           $res = "Usuario ya existente";
          }else{
            $uId = uniqid('id_',TRUE);
            $pathProfile = $uId.'/profile/'.$imgName;
            $consulta = "INSERT INTO usuarios
                                        (nombre,correo,usuario,contra,tipoUsuario,fechaRegistro,nombreImagen,publicId)
                                        VALUES
                                        (? ,? ,? ,? ,? ,? ,? ,? ,? )";

            $this->db->query($consulta ,array($snombre,$scorreo,$susuario,$vcontra,$stipo,$date,$pathProfile,$uId));
            //$idDRO = $this->db->insert_id();
            $rId = uniqid('id_',TRUE);
            $queryR = "INSERT INTO R_DeEm (empresa,idDesarrollador,publicId) VALUES (? ,? ,? )";
            $this->db->query($queryR,array($scompania,$uId,$rId));

            $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uId.'/profile/';
            $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/img/'.$imgName;
            mkdir($path, 0777, true);
            copy($pathImg, $path.$imgName);

            $email_to2 = $correo;
            $email_subject2 = "Registro en Ventanilla Virtual Guanajuato";

            $headers2 = "From: " . strip_tags('soporte@ventanillavirtualguanajuato.net') . "\r\n";
            $headers2 .= "MIME-Version: 1.0\r\n";
            $headers2 .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email_message2 = "<html><body>";
            $email_message2 .= "Estimado ".$snombre." Te has registrado de forma exitosa en la plataforma de <br>". "\n";
            $email_message2 .= "<br> <a href='https://ventanillavirtualguanajuato.net'>ventanillavirtualguanajuato.net</a>\n \n <br>.";
            $email_message2 .= "<br> \n";
            $email_message2 .= "<br> \n";
            $email_message2 .= "<br> Se anexan las nuevas credenciales\n \n <br>.";
            $email_message2 .= "<table rules='all' style='border-color: #666;'' cellpadding='4'>";
            $email_message2 .= "<tr style='background: #eee;'><td>Usuario: </td><td><strong>" . $susuario . "</strong></td></tr>\n";
            $email_message2 .= "<tr ><td>Nueva Contraseña: </td><td><strong>" . $scontra . "</strong></td></tr>\n";
            $email_message2 .= "</table>";
            $email_message2 .= "Saludos Cordiales". "<br>\n";
            $email_message2 .= "<br> \n";
            $email_message2 .= "Soporte Ventanilla Virtual Guanajuato". "<br>\n";
            $email_message2 .= "</body></html>";

            @mail($email_to2, $email_subject2, $email_message2, $headers2);

            $res = "Registro realizado!";
          }
        }else{
          $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
        }
      $this->response($res);
    }



/* Edit user DATA profile*/
public function editUserProfile_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  $preparing = $validation_token['data'];
  $uid = $preparing->id;
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $listaPOST= file_get_contents("php://input");
    if (isset($listaPOST) && !empty($listaPOST)) {
      $request = json_decode($listaPOST);

        $nombre = $request->nombre;
        $correo = $request->correo;
        $usuario = $request->usuario;
        $contra = $request->contra;

        $snombre  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombre);
        $scorreo  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $correo);
        $susuario = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $usuario);
        $scontra  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);

        $vcontra = sha1($scontra);
        if ($scontra === "") {
          $query = "UPDATE usuarios SET correo = ? , nombre = ? , usuario = ? WHERE publicId = ? ";
          $this->db->query($query,array($scorreo,$snombre,$susuario,$uid));
          $res = 'Registro actualizado!';
        }else {
          $query = "UPDATE usuarios SET correo = ? , nombre = ? , usuario = ? , contra = ? WHERE publicId = ? ";
          $this->db->query($query,array($scorreo,$snombre,$susuario,$vcontra,$uid));

          $email_to2 = "Imagenurbana@guanajuatocapital.gob.mx";
          $email_subject2 = "Notificación Cambio de Credenciales en Ventanilla Virtual Guanajuato";

          $headers2 = "From: " . strip_tags('soporte@ventanillavirtualguanajuato.net') . "\r\n";
          $headers2 .= "MIME-Version: 1.0\r\n";
          $headers2 .= "Content-Type: text/html; charset=UTF-8\r\n";
          $email_message2 = "<html><body>";
          $email_message2 .= "Estimado Ing. Luis Eduardo Martinez se le notifica que el usuario  con nombre <strong>".$snombre."</strong> y Correo <strong>".$scorreo."</strong> ha modificado sus credenciales satisfactoriamente en el sistema <br>". "\n";
          $email_message2 .= "<br> <a href='https://ventanillavirtualguanajuato.net'>ventanillavirtualguanajuato.net</a>\n \n <br>.";
          $email_message2 .= "<br> \n";
          $email_message2 .= "<br> \n";
          $email_message2 .= "Saludos Cordiales". "<br>\n";
          $email_message2 .= "<br> \n";
          $email_message2 .= "Soporte Ventanilla Virtual Guanajuato". "<br>\n";
          $email_message2 .= "</body></html>";

          @mail($email_to2, $email_subject2, $email_message2, $headers2);

          $res = 'Registro actualizado!';
        }
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);

  }

  /* Edit picture profile user*/
      public function editPictureProfile_post(){
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

            $prepath = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/';
            $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/profile/';
            $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/profile/'.$file_name;
            $pathNewImg = $uid.'/profile/'.$file_name;
            #dirname(__FILE__)
            if (file_exists($path)) {
                if ($file_type == 'image/png' || $file_type == 'image/PNG' || $file_type == 'image/jpg' || $file_type == 'image/JPG' || $file_type =='image/jpeg' || $file_type =='image/JPEG') {
                   if ($file_size < 3030477 ) {
                     $queryS = $this->db->query("SELECT nombreImagen FROM usuarios WHERE publicId = '$uid'");
                     $profile = $queryS->result_array();
                        foreach ($profile as $perfil) {
                           $name_Profile = $perfil['nombreImagen'];
                        }
                        if ($name_Profile === 'userDefault.png' || $name_Profile === 'droDefault.png' || $name_Profile === 'collegeDefault.png') {}
                        else {
                            unlink($prepath.$name_Profile);
                          }
                      #$pre_type = explode("/", $file_type);
                      if(file_put_contents($pathImg, base64_decode($base64))){
                        $queryU = $this->db->query("UPDATE usuarios
                                                       SET nombreImagen = '$pathNewImg'
                                                       WHERE publicId = '$uid'");
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
            } else {
                  mkdir($path, 0777, true);
                  if ($file_type == 'image/png' || $file_type == 'image/PNG' || $file_type == 'image/jpg' || $file_type == 'image/JPG' || $file_type =='image/jpeg' || $file_type =='image/JPEG') {
                     if ($file_size < 3030477 ) {
                       $queryS = $this->db->query("SELECT nombreImagen FROM usuarios WHERE publicId = '$uid'");
                       $profile = $queryS->result_array();
                          foreach ($profile as $perfil) {
                             $name_Profile = $perfil['nombreImagen'];
                          }
                        if ($name_Profile === 'userDefault.png' || $name_Profile === 'droDefault.png' || $name_Profile === 'collegeDefault.png') {}
                        else {
                            unlink($prepath.$name_Profile);
                          }
                        #$pre_type = explode("/", $file_type);
                        if(file_put_contents($pathImg, base64_decode($base64))){
                          $queryU = $this->db->query("UPDATE usuarios
                                                         SET nombreImagen = '$pathNewImg'
                                                         WHERE publicId = '$uid'");
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

  /* GET user DATA profile*/

  public function userDataProfile_get(){
    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) && $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      //$query = $this->db->query("SELECT nombre,correo,usuario,nombreImagen FROM usuarios WHERE publicId = '$uid'");
      $cons= "SELECT nombre,correo,usuario,nombreImagen,publicId FROM usuarios WHERE publicId = ? ";
      $query = $this->db->query($cons,array($uid));
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result[0];
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    }else {
      $res = [
          "status" => FALSE,
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }

  /* GET USER DATA init*/
  public function userData_get(){
    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      //$query = $this->db->query("SELECT nombre,nombreImagen,publicId FROM usuarios WHERE publicId = '$uid'");
      $query1 = "SELECT nombre,nombreImagen,publicId FROM usuarios WHERE publicId = ? ";
      $query = $this->db->query($query1,array($uid));
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
    }
    $this->response($res);
  }

  /* GET Administrators*/
  public function AdminsData_get(){
    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      $query = $this->db->query("SELECT nombre,correo,usuario,contra,fechaRegistro,publicId,estado,nombreImagen FROM usuarios WHERE tipoUsuario = 'id_5bcaa4f838f9e'");
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $data['id'] = $uid;
        $data['time'] = time();
        $this->load->library('Authorization_Token');
        $token = $this->authorization_token->generateToken($data);
        $res = [

            "status" => TRUE,
            "token" => $token,
            "data"=>$result
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
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }

  /* GET Colabo*/
  public function ColData_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");

      $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $listaPOST);

    if (isset($listaPOST) && !empty($listaPOST)) {
      /*$query = $this->db->query("SELECT usuarios.nombre,usuarios.correo,usuarios.usuario,usuarios.fechaRegistro,usuarios.publicId, usuarios.estado,usuarios.nombreImagen
                                        FROM usuarios
                                        INNER JOIN r_afd ON usuarios.publicId = r_afd.idFuncionario
                                        WHERE usuarios.tipoUsuario = 'id_5bcaa5151ec8b' AND r_afd.idDepa = '$listaPOST'");*/
      $consulta = "SELECT usuarios.nombre,usuarios.correo,usuarios.usuario,usuarios.fechaRegistro,usuarios.publicId, usuarios.estado,usuarios.nombreImagen
                                        FROM usuarios
                                        INNER JOIN r_afd ON usuarios.publicId = r_afd.idFuncionario
                                        WHERE usuarios.tipoUsuario = 'id_5bcaa5151ec8b' AND r_afd.idDepa = ? ";
      $query = $this->db->query($consulta,array($slistaPOST));

      $result = $query->result_array();
          if (isset($result) && !empty($result)) {
              $res = [
                "status" => TRUE,
                "data"=>$result
              ];
          }else {
            $res = [
                "status" => FALSE,
                "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
              ];
          }
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }


  /* GET Colleges*/
  public function ColleData_get(){
    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) && $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      $query = $this->db->query("SELECT nombre,correo,usuario,contra,fechaRegistro,publicId,estado,nombreImagen FROM usuarios WHERE tipoUsuario = 'id_5bcaa531d3338'");
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    }else {
      $res = [
          "status" => FALSE,
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }

  /* GET public Colleges*/
  public function Public_ColleData_get(){
      $query = $this->db->query("SELECT nombre,publicId,estado,nombreImagen FROM usuarios WHERE tipoUsuario = 'id_5bcaa531d3338' AND estado = 1");
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    $this->response($res);

  }

  /* GET public especialitations*/
  public function Public_EspData_get(){
      $query = $this->db->query("SELECT descripcion,publicId FROM especialidades");
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    $this->response($res);

  }


  /* GET all dros*/
  public function drosData_get(){
    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      $query = $this->db->query("SELECT usuarios.nombre,usuarios.correo,usuarios.usuario,usuarios.contra,usuarios.fechaRegistro,usuarios.publicId,usuarios.estado,usuarios.nombreImagen,
                                  r_dc.nombre_colle, r_dc.cedula, r_dc.rop, r_dc.celular
                                  FROM usuarios
                                  INNER JOIN r_dc ON r_dc.idDRO = usuarios.publicId
                                  WHERE usuarios.tipoUsuario = 'id_5bcaa54a0a0cf'");
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    }else {
      $res = [
          "status" => FALSE,
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }

  /* GET all dros CADRO*/
  public function drosDataCA_get(){
    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      $query = $this->db->query("SELECT usuarios.nombre,usuarios.correo,usuarios.fechaRegistro,usuarios.publicId,usuarios.estado,usuarios.nombreImagen,
                                  r_dc.nombre_colle, r_dc.cedula, r_dc.rop, r_dc.celular, r_dc.domicilio, r_dc.vigencia
                                  FROM usuarios
                                  INNER JOIN r_dc ON r_dc.idDRO = usuarios.publicId
                                  WHERE usuarios.tipoUsuario = 'id_5bcaa54a0a0cf'");
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    }else {
      $res = [
          "status" => FALSE,
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }

  /* GET all dros CollegioO*/
  public function drosDataCol_get(){
    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;

      $consulta = "SELECT usuarios.nombre,usuarios.correo,usuarios.fechaRegistro,usuarios.publicId,usuarios.estado,usuarios.nombreImagen,
                                  r_dc.nombre_colle, r_dc.cedula, r_dc.rop, r_dc.celular
                                  FROM usuarios
                                  INNER JOIN r_dc ON r_dc.idDRO = usuarios.publicId
                                  WHERE usuarios.tipoUsuario = 'id_5bcaa54a0a0cf'
                                  AND r_dc.idColegio = ? ";
      $query = $this->db->query($consulta,array($uid));

      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    }else {
      $res = [
          "status" => FALSE,
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }

  /* GET all dros PublicoO*/
  public function getDrosPublic_get(){
  /*  $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      */
      $query = $this->db->query("SELECT usuarios.nombre,usuarios.correo,usuarios.fechaRegistro,usuarios.publicId,usuarios.estado,usuarios.nombreImagen,
                                  r_dc.nombre_colle, r_dc.cedula, r_dc.rop, r_dc.celular
                                  FROM usuarios
                                  INNER JOIN r_dc ON r_dc.idDRO = usuarios.publicId");
      $result = $query->result_array();
      $res = $result;
      /*
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    }else {
      $res = [
          "status" => FALSE,
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    */
    $this->response($res);
  }


  //Activar Dro
  public function estatusDro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);

        $public_id = $request->public_id;
        $estatus = $request->estatus;

        $spublic_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $public_id);
        $sestatus = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $estatus);


        if ($estatus == 1) {
          $vigencia = $request->vigencia;
          $svigencia = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $vigencia);

          $consulta = "UPDATE r_dc SET vigencia = ? WHERE idDRO = ? ";
          $this->db->query($consulta,array($svigencia,$spublic_id));
        }
          $query = "UPDATE usuarios SET estado = ? WHERE publicId = ? ";
          $this->db->query($query,array($sestatus,$spublic_id));


        $res = "Cambio de estatus registrado";
      }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
  }

  /* GET especialidad DRO*/
  public function epecialidadData_get(){
    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) && $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      $query = $this->db->query("SELECT especialidades.descripcion, r_droes.publicId AS 'pb_idR_DE'
                                  FROM especialidades
                                  INNER JOIN r_droes ON especialidades.publicId = r_droes.idEspecialidad
                                  WHERE r_droes.idDRO = '$uid'");
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    }else {
      $res = [
          "status" => FALSE,
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }
  /* GET especialidad DRO Colegio*/
  public function epecialidadDataCol_post(){
      $this->load->library('Authorization_Token');
      $is_valid_token = $this->authorization_token->validateToken();
      if (!empty($is_valid_token) && $is_valid_token['status'] === TRUE) {
        $preparing = $is_valid_token['data'];
        $uid = $preparing->id;
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
              $request = json_decode($listaPOST);
              $publicId = $request->publicId;
              $spublicId = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $publicId);

              $cons = "SELECT especialidades.descripcion, r_droes.publicId AS 'pb_idR_DE'
                                        FROM especialidades
                                        INNER JOIN r_droes ON especialidades.publicId = r_droes.idEspecialidad
                                        WHERE r_droes.idDRO = ? ";

              $query = $this->db->query($cons,array($publicId));
              $result = $query->result_array();
            if (isset($result) && !empty($result)) {
              $res = $result;
            }else {
              $res = [
                  "status" => FALSE,
                  "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
                ];
            }
          }else{
              $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
            }
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
          ];
      }
      $this->response($res);
    }

  //Actualizar especialidad
  public function upEspDro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);

        $n_pb_id = $request->n_pb_id;
        $pb_idR_DE = $request->pb_idR_DE;

        $sn_pb_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $n_pb_id);
        $spb_idR_DE = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $pb_idR_DE);

        $query = "UPDATE r_droes SET idEspecialidad = ? WHERE publicId= ? ";
        $this->db->query($query,array($sn_pb_id,$spb_idR_DE));

        $query = "UPDATE usuarios SET estado = '0' WHERE publicId= ? ";
        $this->db->query($query,array($uid));

        $res = "Cambio registrado";
      }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
  }

  //Agregar especialidad
  public function addEspDro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $listaPOST);
      if (isset($listaPOST) && !empty($listaPOST)) {
        //$request = json_decode($listaPOST);
        #$n_pb_id = $request->n_pb_id;
        #$pb_idR_DE = $request->pb_idR_DE;
        $r_DE_Id = uniqid('id_',TRUE);

        $query = "INSERT INTO r_droes (idDRO,idEspecialidad,publicId) VALUES ( ? , ? , ? )";
        $this->db->query($query,array($uid,$slistaPOST,$r_DE_Id));

        $query = $this->db->query("UPDATE usuarios SET estado = '0' WHERE publicId='$uid'");

        $res = "Cambio registrado";
      }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
  }

  public function corresData_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        /*$request = json_decode($listaPOST);
        $public_id = $request->public_id;
        $estatus = $request->estatus;*/
        $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $listaPOST);

        $cons = "SELECT usuarios.nombre,usuarios.correo,usuarios.fechaRegistro,usuarios.publicId,usuarios.estado,usuarios.nombreImagen,
                                  r_dc.nombre_colle, r_dc.cedula, r_dc.rop, r_dc.celular, r_dc.rop, r_dc.vigencia
                                         FROM usuarios
                                         INNER JOIN r_dc ON r_dc.idDRO = usuarios.publicId
                                         INNER JOIN r_droes ON usuarios.publicId = r_droes.idDRO
                                         WHERE r_droes.idEspecialidad = ? ";
        $query = $this->db->query($cons , array($slistaPOST));

        $res = $query->result_array();
      }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
  }

  public function corresDataCol_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        /*$request = json_decode($listaPOST);
        $public_id = $request->public_id;
        $estatus = $request->estatus;*/
        $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $listaPOST);
        $cons = "SELECT usuarios.nombre,usuarios.correo,usuarios.fechaRegistro,usuarios.publicId,usuarios.estado,usuarios.nombreImagen,
                                  r_dc.nombre_colle, r_dc.cedula, r_dc.rop, r_dc.celular
                                         FROM usuarios
                                         INNER JOIN r_dc ON r_dc.idDRO = usuarios.publicId
                                         INNER JOIN r_droes ON usuarios.publicId = r_droes.idDRO
                                         WHERE r_droes.idEspecialidad = ?
                                         AND r_dc.idColegio = ? ";

        $query = $this->db->query($cons , array($slistaPOST,$uid));

        $res = $query->result_array();
      }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
  }

  public function addPart_post(){
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $listaPOST= file_get_contents("php://input");
        $request = json_decode($listaPOST);

        $date = date('d/M/y');
        $nombre = $request->nombre;
        $correo = $request->correo;
        $usuario = $request->usuario;
        $contra = $request->contra;
        $tipo = $request->tipo;
        $imgName = $request->imgName;

        //sanear variables
        $snombre = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombre);
        $scorreo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $correo);
        $susuario = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $usuario);
        $scontra = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);
        $stipo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $tipo);
        $vcontra = sha1($scontra);

        //$query = $this->db->query("SELECT * FROM usuarios WHERE usuario='$usuario' OR correo='$correo'");
        /* ejemplo
        $query = "SELECT public_id,tipoUsuario FROM usuarios WHERE usuario = ? AND contra = ? AND estado = ?";
        $query = $this->db->query($query, array($usuario, $contra, 1));
        */
        $consulta = "SELECT * FROM usuarios WHERE usuario = ?";
        $query = $this->db->query($consulta, array($susuario));

        $result = $query->result_array();
        if (isset($result) && !empty($result)) {
         $res = "Usuario ya existente";
        }else{
          $uId = uniqid('id_',TRUE);
          $pathProfile = $uId.'/profile/'.$imgName;
          $consulta2 = "INSERT INTO usuarios
                                      (nombre,correo,usuario,contra,tipoUsuario,fechaRegistro,nombreImagen,publicId)
                                      VALUES
                                      ( ? , ? , ? , ? , ? , ? , ? , ? )";

          $this->db->query($consulta2, array($snombre, $scorreo, $susuario, $vcontra, $stipo, $date, $pathProfile, $uId));

          $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uId.'/profile/';
          $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/img/'.$imgName;
          mkdir($path, 0777, true);
          copy($pathImg, $path.$imgName);
          $res = "Registro realizado!";
        }
      }else{
        $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    $this->response($res);
  }


  /* GET all dros CADRO*/
  public function getPart_get(){
    $this->load->library('Authorization_Token');
    $is_valid_token = $this->authorization_token->validateToken();
    if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
      $preparing = $is_valid_token['data'];
      $uid = $preparing->id;
      $query = $this->db->query("SELECT usuarios.nombre,usuarios.correo,usuarios.fechaRegistro,usuarios.publicId,usuarios.estado,usuarios.nombreImagen
                                  FROM usuarios
                                  WHERE usuarios.tipoUsuario = 'id_5bcac531d1758'");
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = $result;
      }else {
        $res = [
            "status" => FALSE,
            "message" => "Wrong data, Error : " . REST_Controller::HTTP_BAD_REQUEST
          ];
      }
    }else {
      $res = [
          "status" => FALSE,
          "message" => "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST
        ];
    }
    $this->response($res);
  }

  public function addCor_post(){
    $listaPOST= file_get_contents("php://input");
    if (isset($listaPOST) && !empty($listaPOST)) {
      $listaPOST= file_get_contents("php://input");
      $request = json_decode($listaPOST);
      $date = date('d/M/y');

        $nombre = $request->nombre;
        $correo = $request->correo;
        $usuario = $request->usuario;
        $contra = $request->contraseña;
        $tipo = $request->tipo;
        $imgName = $request->imgName;
        ##
        $cedula = $request->cedula;
        $rop = $request->rop;
        $domicilio = $request->domicilio;
        $celular = $request->celular;
        ##
        $idColegio = $request->colle_Id;
        $nombreColegio = $request->colle_Name;

        $snombre = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombre);
        $scorreo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $correo);
        $susuario = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $usuario);
        $scontra = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $contra);
        $stipo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $tipo);
        $vcontra = sha1($scontra);

        $scedula = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $cedula);
        $srop = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $rop);
        $sdomicilio = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $domicilio);
        $scelular = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $celular);

        $sidColegio = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $idColegio);
        $snombreColegio = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $nombreColegio);

      //$query = $this->db->query("SELECT * FROM usuarios WHERE usuario='$usuario' OR correo='$correo'");
      $consulta = "SELECT * FROM usuarios WHERE usuario = ?";
      $query = $this->db->query($consulta,array($susuario));
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = "Usuario ya existente";
      }else{
        $uId = uniqid('id_',TRUE);
        $pathProfile = $uId.'/profile/'.$imgName;
        $consulta2 = "INSERT INTO usuarios (nombre,correo,usuario,contra,tipoUsuario,fechaRegistro,estado,nombreImagen,publicId)
                      VALUES  ( ? , ? , ? , ? , ? , ? , ? , ? , ? )";

        $this->db->query($consulta2,array($snombre,$scorreo,$susuario,$vcontra,$stipo,$date,0,$pathProfile,$uId));
        //$idDRO = $this->db->insert_id();
        $dId = uniqid('id_',TRUE);

        $queryR = "INSERT INTO r_dc (idDRO,cedula,rop,domicilio,celular,nombre_colle,idColegio,publicId)
                                   VALUES ( ? , ? , ? , ? , ? , ? , ? , ? )";

        $this->db->query($queryR,array($uId,$scedula,$srop,$sdomicilio,$scelular,$snombreColegio,$sidColegio,$dId));

        $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uId.'/profile/';
        $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/img/'.$imgName;
        mkdir($path, 0777, true);
        copy($pathImg, $path.$imgName);
          if (isset($request->pesp)) {
            $pesp = $request->pesp;
            $spesp = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $pesp);
            $rDeId = uniqid('id_',TRUE);
            $queryRDE = "INSERT INTO r_droes (idDRO,idEspecialidad,publicId) VALUES ( ? , ? , ? )";
            $this->db->query($queryRDE,array($uId,$spesp,$rDeId));
          }
          if (isset($request->sesp)) {
            $sesp = $request->sesp;
            $ssesp = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $sesp);
            $rDeId = uniqid('id_',TRUE);
            $queryRDE = "INSERT INTO r_droes (idDRO,idEspecialidad,publicId) VALUES ( ? , ? , ? )";
            $this->db->query($queryRDE,array($uId,$ssesp,$rDeId));
          }
          if (isset($request->tesp)) {
            $tesp = $request->tesp;
            $stesp = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $tesp);
            $rDeId = uniqid('id_',TRUE);
            $queryRDE = "INSERT INTO r_droes (idDRO,idEspecialidad,publicId) VALUES ( ? , ? , ? )";
            $this->db->query($queryRDE,array($uId,$stesp,$rDeId));
          }
          if (isset($request->cesp)) {
            $cesp = $request->cesp;
            $scesp = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $cesp);
            $rDeId = uniqid('id_',TRUE);
            $queryRDE = "INSERT INTO r_droes (idDRO,idEspecialidad,publicId) VALUES ( ? , ? , ? )";
            $this->db->query($queryRDE,array($uId,$scesp,$rDeId));
          }
          $res = 'Registro realizadó!';
        }
    }else{
      $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
    }
    $this->response($res);
  }

}
