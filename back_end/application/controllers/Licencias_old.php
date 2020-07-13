<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Licencias_old extends REST_Controller {
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

  function vrfytkn($nfrm, $frmtkn, $tkn) {
   //unic-id form
   session_start();
   $secret = $_SESSION["scrt"];
   $token_frm = sha1($secret.$nfrm.$tkn);
   return ($token_frm == $frmtkn);
  }

  public function correousuario($correo,$correoS,$nombre,$tipo_des,$mensaje,$folio){
    $para = $correoS;
    $titulo = 'Estado de Licencia Ventanilla Virtual Guanajuato';
    $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $cabeceras .= 'To: servidor publico <'.$correoS.'>' . "\r\n";
    $cabeceras .= 'From: '.$nombre.'<'.$correo.'>' . "\r\n";

    $mensaje = '
      <html>
        <head>
        </head>
        <body>
          Estimado(a) <strong> servidor(a) público</strong>
          el usuario <strong>'.$nombre.'</strong> '.$mensaje.'
          <br><br>
          Sobre el folio con número '.$folio.', el cual es una solicitud de tramite de '.$tipo_des.'<br><br>
          Acceder a la plataforma <a href="https://ventanillavirtualguanajuato.net">ventanillavirtualguanajuato.net</a>
          <br><br>
          <strong>Ventanilla Virtual Guanajuato</strong>
        </body>
      </html>';
    mail($para, $titulo, $mensaje, $cabeceras);
  }

  public function correoservidor($correo,$correoS,$nombre,$tipo_des,$mensaje,$folio){
    $para = $correo;
    $titulo = 'Estado de Licencia Ventanilla Virtual Guanajuato';
    $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $cabeceras .= 'To: '.$nombre.' <'.$correo.'>' . "\r\n";
    $cabeceras .= 'From: servidor publico <'.$correoS.'>' . "\r\n";

    $mensaje = '
      <html>
        <head>
        </head>
        <body>
          Estimado/a <strong>'.$nombre.'</strong> '.$mensaje.'
          <br><br>
          Sobre el folio con número '.$folio.', Solicitud de tramite de '.$tipo_des.'<br><br>
          Acceder a la plataforma <a href="https://ventanillavirtualguanajuato.net">ventanillavirtualguanajuato.net</a>
          <br><br>
          <strong>Ventanilla Virtual Guanajuato</strong>
        </body>
      </html>';
    mail($para, $titulo, $mensaje, $cabeceras);
  }

  /*Insert Licencia for User*/
  public function add_license_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {

        $request = json_decode($listaPOST);
        $data_0 =  $request[0];
        $data_1 =  $request[1];
        $data_2 =  $request[2];
        $data_3 =  $request[3];
        $data_4 =  $request[4];

        $ara_corres =  $request[5];
        $type = $data_0->type;
        $stype = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $type);
        $map = $data_1->map;
        //$scketch = $data_2->scketch;

        $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $mes = $meses[date('n')-1];
        $ano=date('Y');
        $dia=date('d');
        $date = $dia.'/'.$mes.'/'.$ano;

        $l_Id = uniqid('id_',TRUE);

        //if (self::vrfytkn($frm, $frmtkn, $tkn)) {

            $query = $this->db->query("INSERT INTO licencias
                                         (fecha, tipo, idDRO, estatus,publicId)
                                         VALUES
                                         ('$date','$stype','$uid', 'Solicitado','$l_Id')");

            foreach ($data_0 as $key => $value) {
              if ($key == 'a' || $key == 'b' || $key == 'c' || $key == 'd' || $key == 'e' || $key == 'f' || $key == 'g' || $key == 'h'
                  || $key == 'i' || $key == 'j' || $key == 'k' || $key == 'l' || $key == 'm' || $key == 'n' || $key == 'o' || $key == 'p'
                  || $key == 'q' || $key == 'r' || $key == 'type' || $key == 'renovacion') {
              }else {
                //sanear key && value
                $skey = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $key);
                $svalue = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $value);
                $queryU = $this->db->query("UPDATE licencias
                                               SET $skey = '$svalue'
                                               WHERE publicId = '$l_Id'");
              }
            }

            foreach ($data_1 as $key => $value) {
              if ($key == 'map'  || $key == 'type') {
              }else {
                $skey = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $key);
                $svalue = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $value);
                $queryU = $this->db->query("UPDATE licencias
                                               SET $skey = '$svalue'
                                               WHERE publicId = '$l_Id'");
              }
            }



            foreach ($data_2 as $key => $value) {
               if ($key == 'type') {
               }else {
              $skey = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $key);
              $svalue = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $value);
              $queryU = $this->db->query("UPDATE licencias
                                               SET $skey = '$svalue'
                                               WHERE publicId = '$l_Id'");
              }
            }

            //obra mayor // renovacion 78,79,80,81,82,83
            $j = array(35,/*36,*/37,38,39,40,41,42,43,44,45,46,75,76,77/*,78,79,80,81,82,83*/);
            //construccion especial
          $k = array(/*34,*/35,36,37,38,39,40,41,42,43,44,/*45,*/46,84,85,86,87,88,89,90,91/*,92*/);
            //Construcción para centro Historico
            $l = array(110,38,39,40,41,42,43,44,45,46,50,51,52);


            if($type == 'j'){
              for ($i=0; $i < count($j); $i++) {
                $RPlanLId = uniqid('id_',TRUE);
                $data = $j[$i];
                $queryRPLanL = $this->db->query("INSERT INTO r_planl
                                              (idLicencia, idPlano,publicId)
                                              VALUES
                                              ('$l_Id','$data','$RPlanLId')");
              }
            }
            if($type == 'k'){
              for ($i=0; $i < count($k); $i++) {
                $RPlanLId = uniqid('id_',TRUE);
                $data = $k[$i];
                $queryRPLanL = $this->db->query("INSERT INTO r_planl
                                              (idLicencia, idPlano,publicId)
                                              VALUES
                                              ('$l_Id','$data','$RPlanLId')");
              }
            }
            if($type == 'l'){
              for ($i=0; $i < count($l); $i++) {
                $RPlanLId = uniqid('id_',TRUE);
                $data = $l[$i];
                $queryRPLanL = $this->db->query("INSERT INTO r_planl
                                              (idLicencia, idPlano,publicId)
                                              VALUES
                                              ('$l_Id','$data','$RPlanLId')");
              }
            }if ($type == 'm') {
              for ($i=0; $i < count($m); $i++) {
                $RPlanLId = uniqid('id_',TRUE);
                $data = $m[$i];
                $queryRPLanL = $this->db->query("INSERT INTO r_planl
                                              (idLicencia, idPlano,publicId)
                                              VALUES
                                              ('$l_Id','$data','$RPlanLId')");
              }
            }
            if ($type == 'n') {
              for ($i=0; $i < count($n); $i++) {
                $RPlanLId = uniqid('id_',TRUE);
                $data = $n[$i];
                $queryRPLanL = $this->db->query("INSERT INTO r_planl
                                              (idLicencia, idPlano,publicId)
                                              VALUES
                                              ('$l_Id','$data','$RPlanLId')");
              }
            }
            if ($type == 'q') {
              for ($i=0; $i < count($q); $i++) {
                $RPlanLId = uniqid('id_',TRUE);
                $data = $q[$i];
                $queryRPLanL = $this->db->query("INSERT INTO r_planl
                                              (idLicencia, idPlano,publicId)
                                              VALUES
                                              ('$l_Id','$data','$RPlanLId')");
              }
            }
            if ($type == 's') {
              for ($i=0; $i < count($q); $i++) {
                $RPlanLId = uniqid('id_',TRUE);
                $data = $s[$i];
                $queryRPLanL = $this->db->query("INSERT INTO r_planl
                                              (idLicencia, idPlano,publicId)
                                              VALUES
                                              ('$l_Id','$data','$RPlanLId')");
              }
            }
            if ($type == 't') {
              for ($i=0; $i < count($q); $i++) {
                $RPlanLId = uniqid('id_',TRUE);
                $data = $t[$i];
                $queryRPLanL = $this->db->query("INSERT INTO r_planl
                                              (idLicencia, idPlano,publicId)
                                              VALUES
                                              ('$l_Id','$data','$RPlanLId')");
              }
            }


           $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$l_Id;
            mkdir($path, 0777, true);

            session_start();
            if ($map == true) {
              $mapa = $_SESSION['mapa_'.$uid];
              $query = $this->db->query("UPDATE licencias
                                              SET nombreMapa = '$mapa'
                                              WHERE publicId = '$l_Id'");
            }

            // if ($scketch == true) {
            //   $croquis = $_SESSION['croquis_'.$uid];
            //   $base64 = $croquis->base64;
            //   $file_name = $croquis->filename;
            //   $file_size = $croquis->filesize;
            //   $file_type = $croquis->filetype;
            //   $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$l_Id.'/'.$file_name;
            //   $pathNewImg = $uid.'/licenses/'.$l_Id.'/'.$file_name;
            //
            //   if ($file_size < 3030477 ) {
            //     $queryS = $this->db->query("SELECT nombreImagen FROM usuarios WHERE publicId = '$uid'");
            //     $profile = $queryS->result_array();
            //        foreach ($profile as $perfil) {
            //           $name_Profile = $perfil['nombreImagen'];
            //        }
            //
            //      if(file_put_contents($pathImg, base64_decode($base64))){
            //        $queryU = $this->db->query("UPDATE licencias
            //                                       SET nombreCroquis = '$pathNewImg',
            //                                       ubicacion_croquis = '$pathNewImg'
            //                                       WHERE publicId = '$l_Id'");
            //        $res =  "Archivo cargado y guardado";
            //      }else {
            //        $res = "No se pudó guardar la imagen";
            //      }
            //    }else {
            //      $res = "Archivo demasido grande, tamaño requerido '2MB'";
            //    }
            // }

            $arancel_gral = json_encode($data_3);
            $arancel_col = json_encode($data_4);
            $gral_Id = uniqid('id_',TRUE);
            $query_gral = $this->db->query("INSERT INTO arancel_licencia
                                         (arancel, id_licencia, public_id)
                                         VALUES
                                         ('$arancel_gral','$l_Id','$gral_Id')");

            $arancel_lenght = count($data_3);
            $arancel = floatval($data_3[$arancel_lenght-1]);
            $queryU = $this->db->query("UPDATE licencias
                                                      SET arancel = '$arancel'
                                                      WHERE publicId = '$l_Id'");

             $col_Id = uniqid('id_',TRUE);
             if ($ara_corres == true) {
             $query_col = $this->db->query("INSERT INTO arancel_licencia
                                          (arancel, id_licencia, public_id)
                                          VALUES
                                          ('$arancel_col','$l_Id','$col_Id')");
             }

              //unset($_SESSION["mapa"]);
              //unset($_SESSION["croquis"]);
              //session_unset($_SESSION ['mapa_'.$uid]);
              //session_unset($_SESSION ['croquis_'.$uid]);
              $res = [
                'res' => 'Registro Realizado',
                'data' => $l_Id
              ];

        /*}
        else {
          $res =  "form invalidate".' '.REST_Controller::HTTP_BAD_REQUEST;
        }*/
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function insert_col_post(){
            $this->load->library('Authorization_Token');
            $validation_token = $this->authorization_token->validateToken();
            $preparing = $validation_token['data'];
            $id = $preparing->id;
            if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $listaPOST= file_get_contents("php://input");
              if(isset($listaPOST) && !empty($listaPOST)){
                $request = json_decode($listaPOST);
                $lic_id = $request->lic_id;
                $publicId = $request->publicId;

                $slic_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $lic_id);
                $spublicId = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $publicId);

                if ($id == "id_5c0960c4e733d9.86325844") {
                  $query_dep = $this->db->query("SELECT idDepa
                                             FROM r_afd
                                             WHERE idFuncionario = '$id'");
                }else {
                  $query_dep = $this->db->query("SELECT publicId
                                             FROM departamentos
                                             WHERE idAdministrador = '$id'");
                }


                  $result_id = $query_dep->result_array();
                  $pre_id = $result_id[0];
                  foreach ($pre_id as $nombre => $valor) {
                    $dep_id =$valor;
                  }
                // if ($dep_id == 'id_5c0035b1a13246.02873181') {
                //   $query = $this->db->query("UPDATE licencias
                //                                  SET uid_licencias = '$publicId'
                //                                  WHERE publicId = '$lic_id'");
                // }
                //
                // if ($dep_id == 'id_5c65dd2bc42440.77678870') {#1
                //
                //   $query = $this->db->query("UPDATE licencias
                //
                //                                  SET uid_juridico = '$publicId'
                //
                //                                  WHERE publicId = '$lic_id'");
                // }
                //
                // if ($dep_id == 'id_5c65dd90655da3.81713553') {#2
                //   $query = $this->db->query("UPDATE licencias
                //                                  SET uid_analista = '$publicId'
                //                                  WHERE publicId = '$lic_id'");
                //   }

                if ($dep_id == 'id_5c744d2aabbb00.26626272') {#2
                  $query = $this->db->query("UPDATE licencias_nodro
                                                 SET publicCol = '$spublicId'
                                                 WHERE publicId = '$slic_id'");
                  }
                  else {
                    $query = $this->db->query("UPDATE licencias
                                                   SET uid_licencias = '$spublicId'
                                                   WHERE publicId = '$slic_id'");
                    $query = $this->db->query("UPDATE licencias_nodro
                                                    SET publicCol = '$spublicId'
                                                    WHERE publicId = '$slic_id'");
                  }
                $res = 'Asignación Realizada';
              }else{
                $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
              }
            }else{
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
            }
          $this->response($res);
        }

  public function add_scketch_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) && $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);
        session_start();
        $_SESSION ['croquis_'.$uid] = $request;
        $res =  'Croquis Agregado';
      }else{
        $res = "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);

  }

  public function get_croquis_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
              session_start();
              //$licencia = $_SESSION ['licencia'];
               $res = $_SESSION ['croquis_'.$uid];
            }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
            }
            $this->response($res);
  }
  /*Insert map for license*/
  public function add_map_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          session_start();
          $_SESSION ['mapa_'.$uid] = $listaPOST;
          $res =  'Mapa Agregado';
          $res =  'Ubicación Agregada';
          }else{
            $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
  }

  public function add_map2_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $img = $request->data;
          $pi = $request->publicId;
          $spi = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $pi);


          $query = $this->db->query("UPDATE licencias
                                          SET nombreMapa = '$img'
                                          WHERE publicId = '$spi'");

          $res =  'Ubicación Agregada';
          }else{
            $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
  }

  public function get_map_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
              session_start();
              //$licencia = $_SESSION ['licencia'];
               $res = $_SESSION ['mapa_'.$uid];
            }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
            }
            $this->response($res);
    }

  public function del_map_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
               session_start();
               session_unset($_SESSION ['mapa_'.$uid]);
               $res = 'Ubicación eliminada';
            }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
            }
            $this->response($res);
  }

  public function save_lic_post(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
            if (isset($listaPOST) && !empty($listaPOST)) {
              $request = json_decode($listaPOST);
              $id = $request->publicId;
              $flag = $request->flag;

              $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);
              $sflag = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $flag);

              $query_dep = $this->db->query("SELECT publicId
                                     FROM licencias
                                     WHERE publicId = '$id'");
              $result_id = $query_dep->result_array();

              if (isset($result_id) && !empty($result_id)) {
                $table = "licencias";
              }else {
                $table = "licencias_nodro";
              }

                foreach ($request as $key => $value) {  //nombreCroq = '$file_name',      pathCroquis = '$pathNewImg'
                  if ($key != 'flag' && $key != 'publicPart' && $key != 'publicCol' && $key != 'file' && $key != 'nombreCroq' && $key != 'pathCroquis' && $key !='nombreMapa') {
                    $skey = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $key);
                    $svalue = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $value);
                    if ($skey == 'condFAgua'){
                      if($svalue){$svalue = 'true';
                      }else{$svalue = 'false';}
                    }
                    if ($skey == 'condFEnergia'){
                      if($svalue){$svalue = 'true';
                      }else{$svalue = 'false';}
                    }
                    if ($skey == 'condFarbs'){
                      if($svalue){$svalue = 'true';
                      }else{$svalue = 'false';}
                    }
                    if ($skey == 'condFarroy'){
                      if($svalue){$svalue = 'true';
                      }else{$svalue = 'false';}
                    }
                    if ($skey == 'condFdrena'){
                      if($svalue){$svalue = 'true';
                      }else{$svalue = 'false';}
                    }
                    if ($skey == 'condFnin'){
                      if($svalue){$svalue = 'true';
                      }else{$svalue = 'false';}
                    }
                    $query = $this->db->query("UPDATE $table
                                                   SET $skey = '$svalue'
                                                   WHERE publicId = '$sid'");
                  }
                }
                if ($flag == 1 || $flag == '1') {
                  $query = $this->db->query("UPDATE licencias
                                                 SET estatus = 'Autorizado'
                                                 WHERE publicId = '$sid'");
                }
                $res = "actualizado";

              }else{
                $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
              }
            }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
            }
        $this->response($res);
  }

  public function sol_cancel_lic_post(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
            if (isset($listaPOST) && !empty($listaPOST)) {
              $request = json_decode($listaPOST);
              $id = $request->publicId;
              $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);

                  $query_dep = $this->db->query("SELECT publicId
                                         FROM licencias
                                         WHERE publicId = '$sid'");
                  $result_id = $query_dep->result_array();

                  if (isset($result_id) && !empty($result_id)) {
                    $table = "licencias";
                  }else {
                    $table = "licencias_nodro";
                  }

                  $query = $this->db->query("UPDATE $table
                                                 SET sol_cancelacion = 1,
                                                 estatus = 'Cancelación Solicitada'
                                                 WHERE publicId = '$sid'");
                  $res = "Cancelación solicitada";
              }else{
                $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
              }
            }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
            }
            $this->response($res);
  }

  public function cancel_lic_post(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
            if (isset($listaPOST) && !empty($listaPOST)) {
                  $request = json_decode($listaPOST);
                  $id = $request->publicId;
                  $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);

                  $query_dep = $this->db->query("SELECT publicId
                                         FROM licencias
                                         WHERE publicId = '$sid'");

                  $result_id = $query_dep->result_array();

                  if (isset($result_id) && !empty($result_id)) {
                    $table = "licencias";
                  }else {
                    $table = "licencias_nodro";
                  }

                  $query = $this->db->query("UPDATE $table
                                                 SET estatus = 0
                                                 WHERE publicId = '$sid'");
                  $res = "Folio cancelado";
              }else{
                $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
              }
            }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
            }
            $this->response($res);
  }

  public function cancel_pro_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        $preparing = $validation_token['data'];
        $uid = $preparing->id;
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $id = $request->publicId;
            $id_origen = $request->id_origen;

            $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);
            $sid_origen = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id_origen);



                $query = $this->db->query("UPDATE licencias_prorroga
                                               SET estatus = 0
                                               WHERE publicId = '$sid'");
                $query = $this->db->query("UPDATE licencias
                                              SET prorroga = 0
                                              WHERE publicId = '$sid_origen'");
                $res = "Folio cancelado";
            }else{
              $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
            }
          }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
          $this->response($res);
        }

  public function cancel_ter_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        $preparing = $validation_token['data'];
        $uid = $preparing->id;
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $id = $request->publicId;
            $id_origen = $request->id_origen;

            $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);
            $sid_origen = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id_origen);

                $query = $this->db->query("UPDATE licencias_terminacion
                                               SET estatus = 0
                                               WHERE publicId = '$sid'");
                $query = $this->db->query("UPDATE licencias
                                              SET terminacion = 0
                                              WHERE publicId = '$sid_origen'");
                $res = "Folio cancelado";
            }else{
              $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
            }
          }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
          $this->response($res);
  }

  public function add_estado_post(){
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
          $public_id = $request->public_id;
          $id_dro = $request->id_dro;

          $spublic_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $public_id);
          $sid_dro = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id_dro);

          $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$sid_dro.'/licenses/'.$spublic_id.'/'.$file_name;
          $pathNewImg = $sid_dro.'/licenses/'.$spublic_id.'/'.$file_name;

          if ($file_size < 3030477 ) {
             if(file_put_contents($pathImg, base64_decode($base64))){
               $queryU = $this->db->query("UPDATE licencias
                                              SET nombre_estado = '$file_name',
                                              ubicacion_estado = '$pathNewImg'
                                              WHERE publicId = '$spublic_id'");
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
  /*get  license*/
  public function get_licenses_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $query_lic = $this->db->query("SELECT licencias.idLicencia,licencias.docfinal, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.zonaPred, licencias.noOficialPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, licencias.arancel, licencias.fechaCarga
                                             FROM licencias
                                             WHERE licencias.estatus != '0' AND licencias.idDRO = '$uid' ORDER BY licencias.idLicencia DESC");

              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.zonaPred, licencias_nodro.noOficialPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, licencias_nodro.fechaCarga
                                              FROM licencias_nodro
                                              WHERE licencias_nodro.estatus != '0' AND licencias_nodro.publicPart = '$uid' ORDER BY licencias_nodro.idLicencia DESC ");

            $res = array_merge($query_lic->result_array(), $query_licno->result_array());
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
        $this->response($res);
      }
  /*get  license*/
  public function get_prorrogas_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {

              $query_user = $this->db->query("SELECT tipoUsuario
                                         FROM usuarios
                                         WHERE publicId = '$uid'");

              $result_id = $query_user->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
              //idDep Admin
                $type_id = $valor;
              }
              //DRO
              if ($type_id == 'id_5bcaa54a0a0cf') {
                $table = 'licencias_prorroga';
                $row_ow = 'idDRO';
              }
              //Particular
              if ($type_id == 'id_5bcac531d1758') {
                $table = 'licencias_nodro_prorrogas';
                $row_ow = 'publicPart';
              }

              $query = $this->db->query("SELECT id_prorroga, folio, fecha, nRegiS, publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion
                                             FROM $table
                                             WHERE estatus != '0' AND $row_ow = '$uid'");
              $res =  $query->result_array();
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
            $this->response($res);
      }

  public function get_prorrogas_a_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            //determina si el usuario es administrador
            $query_dep = $this->db->query("SELECT publicId
                                       FROM departamentos
                                       WHERE idAdministrador = '$uid'");
            if($query_dep->result_array()==null){
              //determina de cual departamento es colaborador
              $query_dep_col = $this->db->query("SELECT idDepa
                                           FROM r_afd
                                           WHERE idFuncionario = '$uid'");
              $result_id = $query_dep_col->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                // idDep col
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = false;
            }else {
              $result_id = $query_dep->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                //idDep Admin
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = true;
            }
            //licencias y permisos
            if ($dep_id == 'id_5d28eca5113db5.08356751') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }
              else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }

               $query_lic = $this->db->query("SELECT id_prorroga, folio, fecha, nRegiS, licencias_prorroga.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                 tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                              FROM licencias_prorroga
                                              INNER JOIN usuarios ON licencias_prorroga.idDRO = usuarios.publicId
                                              WHERE estatus != '0' AND (licencias_prorroga.tipo = 'h' OR licencias_prorroga.tipo = 'i' OR licencias_prorroga.tipo = 'j'  OR licencias_prorroga.tipo = 'k' OR licencias_prorroga.tipo = 'l' OR licencias_prorroga.tipo = 'm' OR licencias_prorroga.tipo = 'n'
                                                OR licencias_prorroga.tipo = 'o' OR licencias_prorroga.tipo = 'p' OR licencias_prorroga.tipo = 'q' OR licencias_prorroga.tipo = 'r') $whereSen");

               $query_licno = $this->db->query("SELECT id_prorroga, folio, fecha, nRegiS, licencias_nodro_prorrogas.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                 tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                              FROM licencias_nodro_prorrogas
                                              INNER JOIN usuarios ON licencias_nodro_prorrogas.publicPart = usuarios.publicId
                                              WHERE estatus != '0'  AND (licencias_nodro_prorrogas.tipo = 'h' OR licencias_nodro_prorrogas.tipo = 'i' OR licencias_nodro_prorrogas.tipo = 'j'  OR licencias_nodro_prorrogas.tipo = 'k' OR licencias_nodro_prorrogas.tipo = 'l' OR licencias_nodro_prorrogas.tipo = 'm' OR licencias_nodro_prorrogas.tipo = 'n'
                                                OR licencias_nodro_prorrogas.tipo = 'o' OR licencias_nodro_prorrogas.tipo = 'p' OR licencias_nodro_prorrogas.tipo = 'q' OR licencias_nodro_prorrogas.tipo = 'r') $whereSenND");

              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
            //fraccionamientos
            if ($dep_id == 'id_5d28ecbd4be105.89329374') {
                if ($is_adm == true) { $whereSen = ""; }
                else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT id_prorroga, folio, fecha, nRegiS, licencias_nodro_prorrogas.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                  tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                           FROM licencias_nodro_prorrogas
                                           LEFT JOIN usuarios ON licencias_nodro_prorrogas.publicCol = usuarios.publicId
                                           WHERE estatus != '0' AND (licencias_nodro_prorrogas.tipo = 'g') $whereSen");
                $res = $query->result_array();
              }
            //centro Historico
            if ($dep_id == 'id_5d28ecdfe81f79.51014751') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }
              else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }
               $query_lic = $this->db->query("SELECT id_prorroga, folio, fecha, nRegiS, licencias_prorroga.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                 tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                              FROM licencias_prorroga
                                              INNER JOIN usuarios ON licencias_prorroga.idDRO = usuarios.publicId
                                              WHERE estatus != '0' AND (licencias_prorroga.tipo = 'l') $whereSen");
               $query_licno = $this->db->query("SELECT id_prorroga, folio, fecha, nRegiS, licencias_nodro_prorrogas.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                 tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                              FROM licencias_nodro_prorrogas
                                              INNER JOIN usuarios ON licencias_nodro_prorrogas.publicPart = usuarios.publicId
                                              WHERE estatus != '0'  AND (licencias_nodro_prorrogas.tipo = 'l') $whereSenND");

                $res = array_merge($query_lic->result_array(), $query_licno->result_array());
              }
            //uso de suelo
            if ($dep_id == 'id_5d3b39327fd566.84237899') {#2  NO DRO
                if ($is_adm == true) { $whereSen = ""; }
                else{ $whereSen = "AND publicCol = '$uid'"; }
                $query_licno = $this->db->query("SELECT id_prorroga, folio, fecha, nRegiS, licencias_nodro_prorrogas.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                  tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                               FROM licencias_nodro_prorrogas
                                               INNER JOIN usuarios ON licencias_nodro_prorrogas.publicPart = usuarios.publicId
                                           WHERE estatus != '0' AND (licencias_nodro_prorrogas.tipo = 'a' OR licencias_nodro_prorrogas.tipo = 'b' OR licencias_nodro_prorrogas.tipo = 'c'  OR licencias_nodro_prorrogas.tipo = 'd') $whereSen");
                $res = $query_licno->result_array();
              }
            //alineamiento y no oficial
            if ($dep_id == 'id_5d3b394e02a514.10059639') {#2  NO DRO
                if ($is_adm == true) { $whereSen = ""; }
                else{ $whereSen = "AND publicCol = '$uid'"; }
                $query_licno = $this->db->query("SELECT id_prorroga, folio, fecha, nRegiS, licencias_nodro_prorrogas.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                  tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                               FROM licencias_nodro_prorrogas
                                               INNER JOIN usuarios ON licencias_nodro_prorrogas.publicPart = usuarios.publicId
                                           WHERE estatus != '0' AND (licencias_nodro_prorrogas.tipo = 'e' OR licencias_nodro_prorrogas.tipo = 'f') $whereSen");
                $res = $query_licno->result_array();
              }

            //$res = array_merge($query_lic->result_array(), $query_licno->result_array());
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
            $this->response($res);
      }
  /*get  license*/
  public function get_terminaciones_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $query_user = $this->db->query("SELECT tipoUsuario
                                       FROM usuarios
                                       WHERE publicId = '$uid'");

            $result_id = $query_user->result_array();
            $pre_id = $result_id[0];
            foreach ($pre_id as $nombre => $valor) {
            //idDep Admin
              $type_id = $valor;
            }
            //DRO
            if ($type_id == 'id_5bcaa54a0a0cf') {
              $table = 'licencias_terminacion';
              $row_ow = 'idDRO';
            }
            //Particular
            if ($type_id == 'id_5bcac531d1758') {
              $table = 'licencias_nodro_terminacion';
              $row_ow = 'publicPart';
            }

            $query = $this->db->query("SELECT id_terminacion, folio, fecha, nRegiS, publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                              tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion
                                           FROM $table
                                           WHERE estatus != '0' AND $row_ow = '$uid'");
            $res =  $query->result_array();
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
            $this->response($res);
      }

  public function get_terminaciones_a_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            //determina si el usuario es administrador
            $query_dep = $this->db->query("SELECT publicId
                                       FROM departamentos
                                       WHERE idAdministrador = '$uid'");
            if($query_dep->result_array()==null){
              //determina de cual departamento es colaborador
              $query_dep_col = $this->db->query("SELECT idDepa
                                           FROM r_afd
                                           WHERE idFuncionario = '$uid'");
              $result_id = $query_dep_col->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                // idDep col
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = false;
            }else {
              $result_id = $query_dep->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                //idDep Admin
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = true;
            }
            //licencias y permisos
            if ($dep_id == 'id_5d28eca5113db5.08356751') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }
              else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }

               $query_lic = $this->db->query("SELECT id_terminacion, folio, fecha, nRegiS, licencias_terminacion.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                 tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                              FROM licencias_terminacion
                                              INNER JOIN usuarios ON licencias_terminacion.idDRO = usuarios.publicId
                                              WHERE estatus != '0' AND (licencias_terminacion.tipo = 'h' OR licencias_terminacion.tipo = 'i' OR licencias_terminacion.tipo = 'j'  OR licencias_terminacion.tipo = 'k' OR licencias_terminacion.tipo = 'l' OR licencias_terminacion.tipo = 'm' OR licencias_terminacion.tipo = 'n'
                                                OR licencias_terminacion.tipo = 'o' OR licencias_terminacion.tipo = 'p' OR licencias_terminacion.tipo = 'q' OR licencias_terminacion.tipo = 'r') $whereSen");
               $query_licno = $this->db->query("SELECT id_terminacion, folio, fecha, nRegiS, licencias_nodro_terminacion.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                 tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                              FROM licencias_nodro_terminacion
                                              INNER JOIN usuarios ON licencias_nodro_terminacion.publicPart = usuarios.publicId
                                              WHERE estatus != '0'  AND (licencias_nodro_terminacion.tipo = 'h' OR licencias_nodro_terminacion.tipo = 'i' OR licencias_nodro_terminacion.tipo = 'j'  OR licencias_nodro_terminacion.tipo = 'k' OR licencias_nodro_terminacion.tipo = 'l' OR licencias_nodro_terminacion.tipo = 'm' OR licencias_nodro_terminacion.tipo = 'n'
                                                OR licencias_nodro_terminacion.tipo = 'o' OR licencias_nodro_terminacion.tipo = 'p' OR licencias_nodro_terminacion.tipo = 'q' OR licencias_nodro_terminacion.tipo = 'r') $whereSenND");

              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
            //fraccionamientos
            if ($dep_id == 'id_5d28ecbd4be105.89329374') {
                if ($is_adm == true) { $whereSen = ""; }
                else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT id_terminacion, folio, fecha, nRegiS, licencias_nodro_terminacion.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                  tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                           FROM licencias_nodro_terminacion
                                           LEFT JOIN usuarios ON licencias_nodro_terminacion.publicCol = usuarios.publicId
                                           WHERE estatus != '0' AND (licencias_nodro_terminacion.tipo = 'g') $whereSen");
                $res = $query->result_array();
              }
            //centro Historico
            if ($dep_id == 'id_5d28ecdfe81f79.51014751') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }
              else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }
               $query_lic = $this->db->query("SELECT id_terminacion, folio, fecha, nRegiS, licencias_terminacion.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                 tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                              FROM licencias_terminacion
                                              INNER JOIN usuarios ON licencias_terminacion.idDRO = usuarios.publicId
                                              WHERE estatus != '0' AND (licencias_terminacion.tipo = 'l') $whereSen");
               $query_licno = $this->db->query("SELECT id_terminacion, folio, fecha, nRegiS, licencias_nodro_terminacion.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                 tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                              FROM licencias_nodro_terminacion
                                              INNER JOIN usuarios ON licencias_nodro_terminacion.publicPart = usuarios.publicId
                                              WHERE estatus != '0'  AND (licencias_nodro_terminacion.tipo = 'l') $whereSenND");

                $res = array_merge($query_lic->result_array(), $query_licno->result_array());
              }
            //uso de suelo
            if ($dep_id == 'id_5d3b39327fd566.84237899') {#2  NO DRO
                if ($is_adm == true) { $whereSen = ""; }
                else{ $whereSen = "AND publicCol = '$uid'"; }
                $query_licno = $this->db->query("SELECT id_terminacion, folio, fecha, nRegiS, licencias_nodro_terminacion.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                  tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                               FROM licencias_nodro_terminacion
                                               INNER JOIN usuarios ON licencias_nodro_terminacion.publicPart = usuarios.publicId
                                           WHERE estatus != '0' AND (licencias_nodro_terminacion.tipo = 'a' OR licencias_nodro_terminacion.tipo = 'b' OR licencias_nodro_terminacion.tipo = 'c'  OR licencias_nodro_terminacion.tipo = 'd') $whereSen");
                $res = $query_licno->result_array();
              }
            //alineamiento y no oficial
            if ($dep_id == 'id_5d3b394e02a514.10059639') {#2  NO DRO
                if ($is_adm == true) { $whereSen = ""; }
                else{ $whereSen = "AND publicCol = '$uid'"; }
                $query_licno = $this->db->query("SELECT id_terminacion, folio, fecha, nRegiS, licencias_nodro_terminacion.publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                  tipo, vigencia, arancel, prorroga, terminacion, orden_pago, sol_cancelacion, usuarios.nombre
                                               FROM licencias_nodro_terminacion
                                               INNER JOIN usuarios ON licencias_nodro_terminacion.publicPart = usuarios.publicId
                                           WHERE estatus != '0' AND (licencias_nodro_terminacion.tipo = 'e' OR licencias_nodro_terminacion.tipo = 'f') $whereSen");
                $res = $query_licno->result_array();
              }

          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
            $this->response($res);
      }
  /*get  license*/
  public function get_cancelaciones_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $query_dep = $this->db->query("SELECT tipoUsuario
                                     FROM usuarios
                                     WHERE publicId = '$uid'");
              $result_id = $query_dep->result_array();

              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                $dep_id = $valor;
              }
              if ($dep_id == 'id_5bcac531d1758') {
                $table = "licencias_nodro";
                $where = 'publicPart';
              }else{
                $table = "licencias";
                $where = 'idDRO';
              }

              $query = $this->db->query("SELECT idLicencia, fecha, nRegiS, publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                                tipo, prorroga, terminacion, orden_pago, sol_cancelacion
                                         FROM $table
                                         WHERE estatus = '0' AND $where = '$uid'");
              $res = $query->result_array();
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
            $this->response($res);
      }

  public function get_cancelaciones_a_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {

              //determina si el usuario es administrador
              $query_dep = $this->db->query("SELECT publicId
                                         FROM departamentos
                                         WHERE idAdministrador = '$uid'");
              if($query_dep->result_array()==null){
                //determina de cual departamento es colaborador
                $query_dep_col = $this->db->query("SELECT idDepa
                                             FROM r_afd
                                             WHERE idFuncionario = '$uid'");
                $result_id = $query_dep_col->result_array();
                $pre_id = $result_id[0];
                foreach ($pre_id as $nombre => $valor) {
                  // idDep col
                  $dep_id =$valor;
                }
                //where adicional para colaboradores
                $is_adm = false;
              }else {
                $result_id = $query_dep->result_array();
                $pre_id = $result_id[0];
                foreach ($pre_id as $nombre => $valor) {
                  //idDep Admin
                  $dep_id =$valor;
                }
                //where adicional para colaboradores
                $is_adm = true;
              }
              //licencias y permisos
              if ($dep_id == 'id_5d28eca5113db5.08356751') {
                if ($is_adm == true) {
                  $whereSen = "";
                  $whereSenND = "";
                }
                else{
                  $whereSen = "AND uid_licencias = '$uid'";
                  $whereSenND = "AND publicCol = '$uid'";
                 }
                $query_lic = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                  licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre AS 'dro', i_c.nombre AS 'col'
                                            FROM licencias
                                            INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            LEFT JOIN usuarios i_c ON licencias.uid_licencias = i_c.publicId
                                            WHERE licencias.estatus = '0' AND (licencias.tipo = 'h' OR licencias.tipo = 'i' OR licencias.tipo = 'j'  OR licencias.tipo = 'k' OR licencias.tipo = 'l' OR licencias.tipo = 'm' OR licencias.tipo = 'n'
                                              OR licencias.tipo = 'o' OR licencias.tipo = 'p' OR licencias.tipo = 'q' OR licencias.tipo = 'r') $whereSen");
                $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col'
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE estatus = '0' AND (licencias_nodro.tipo = 'h' OR licencias_nodro.tipo = 'i' OR licencias_nodro.tipo = 'j'  OR licencias_nodro.tipo = 'k' OR licencias_nodro.tipo = 'l' OR licencias_nodro.tipo = 'm' OR licencias_nodro.tipo = 'n'
                                             OR licencias_nodro.tipo = 'o' OR licencias_nodro.tipo = 'p' OR licencias_nodro.tipo = 'q' OR licencias_nodro.tipo = 'r') $whereSenND");

                $res = array_merge($query_lic->result_array(), $query_licno->result_array());
              }
              //fraccionamientos
              if ($dep_id == 'id_5d28ecbd4be105.89329374') {
                  if ($is_adm == true) { $whereSen = ""; }
                  else{ $whereSen = "AND publicCol = '$uid'"; }
                  $query = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                    licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre
                                             FROM licencias_nodro
                                             LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                             WHERE estatus = '0' AND (licencias_nodro.tipo = 'g') $whereSen");
                  $res = $query->result_array();
                }
              //centro Historico
              if ($dep_id == 'id_5d28ecdfe81f79.51014751') {
                if ($is_adm == true) {
                  $whereSen = "";
                  $whereSenND = "";
                }
                else{
                  $whereSen = "AND uid_licencias = '$uid'";
                  $whereSenND = "AND publicCol = '$uid'";
                 }
                 $query_lic = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                   licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col'
                                            FROM licencias
                                            LEFT JOIN usuarios ON licencias.publicCol = usuarios.publicId
                                            LEFT JOIN usuarios i_c ON licencias.publicCol = i_c.publicId
                                            WHERE estatus = '0' AND (licencias.tipo = 'l') $whereSen");

                  $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                    licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col'
                                             FROM licencias_nodro
                                             LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                             LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                             WHERE estatus = '0' AND (licencias_nodro.tipo = 'l') $whereSenND");
                  $res = array_merge($query_lic->result_array(), $query_licno->result_array());
                }
              //uso de suelo
              if ($dep_id == 'id_5d3b39327fd566.84237899') {#2  NO DRO
                  if ($is_adm == true) { $whereSen = ""; }
                  else{ $whereSen = "AND publicCol = '$uid'"; }
                  $query = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                    licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col'
                                             FROM licencias_nodro
                                             LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                             LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                             WHERE estatus = '0' AND (licencias_nodro.tipo = 'a' OR licencias_nodro.tipo = 'b' OR licencias_nodro.tipo = 'c'  OR licencias_nodro.tipo = 'd') $whereSen");
                  $res = $query->result_array();
                }
              //alineamiento y no oficial
              if ($dep_id == 'id_5d3b394e02a514.10059639') {#2  NO DRO
                  if ($is_adm == true) { $whereSen = ""; }
                  else{ $whereSen = "AND publicCol = '$uid'"; }
                  $query = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                    licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre
                                             FROM licencias_nodro
                                             LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                             WHERE estatus = '0' AND (licencias_nodro.tipo = 'e' OR licencias_nodro.tipo = 'f') $whereSen");
                  $res = $query->result_array();
                }
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
            $this->response($res);
      }
  /*get  license College*/
  public function get_licensesColl_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          //  $uid =
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $query = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nombre_gestor,
                                                 licencias.nombre_proyecto, licencias.publicId, licencias.calle, licencias.fraccionamiento, licencias.lote,
                                                 licencias.nombre_prop, licencias.estatus, licencias.idDRO, usuarios.nombre
                                          FROM licencias
                                          INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          INNER JOIN r_dc ON licencias.idDRO = r_dc.idDRO
                                          WHERE r_dc.idColegio =  '$uid' ORDER BY licencias.idLicencia DESC");
              $result = $query->result_array();
              $res =  $result;
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
            $this->response($res);

      }
  /*get  license CADRO*/
  public function get_alllicensesCA_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $query = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nombre_gestor,
                                                licencias.nombre_proyecto, licencias.publicId, licencias.calle, licencias.fraccionamiento, licencias.lote,
                                                licencias.nombre_prop, licencias.estatus, licencias.idDRO, usuarios.nombre
                                          FROM licencias
                                          INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId ORDER BY licencias.idLicencia DESC");
              $result = $query->result_array();
              $res =  $result;
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
            $this->response($res);
      }
  /*get  license ADMIN FUNC*/
  public function get_alllicenses_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            //determina si el usuario es administrador
            $query_dep = $this->db->query("SELECT publicId
                                       FROM departamentos
                                       WHERE idAdministrador = '$uid'");
            $qudep = $query_dep->result_array();
            if(empty($qudep)){
              //determina de cual departamento es colaborador
              $query_dep_col = $this->db->query("SELECT idDepa
                                           FROM r_afd
                                           WHERE idFuncionario = '$uid'");
              $result_id = $query_dep_col->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                // idDep col
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = false;
            }else {
              $result_id = $query_dep->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                //idDep Admin
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = true;
            }
            ///Departamento de  Direccion de Imagen Urbana
            if ($dep_id == 'id_5d28eca5113db5.08356751') {
              if ($is_adm == true || $uid == 'id_5d8909e208b3a0.63783821') {
                $whereSen = "";
                $whereSenND = "";
              }else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }
              $query_lic = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre AS 'dro', i_c.nombre AS 'col', licencias.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                          FROM licencias
                                          INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.uid_licencias = i_c.publicId
                                          WHERE licencias.estatus != '0' AND licencias.estatus != 'NEGATIVA' AND licencias.estatus != 'CONCLUIDO' AND (licencias.tipo = 'h' OR licencias.tipo = 'i' OR licencias.tipo = 'j'  OR licencias.tipo = 'k' OR licencias.tipo = 'l' OR licencias.tipo = 'm' OR licencias.tipo = 'n'
                                            OR licencias.tipo = 'o' OR licencias.tipo = 'p' OR licencias.tipo = 'q' OR licencias.tipo = 'r' OR licencias.tipo = 's' OR licencias.tipo = 't') $whereSen ORDER BY licencias.idLicencia DESC");
              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                         FROM licencias_nodro
                                         LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                         LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                         WHERE licencias_nodro.estatus != '0' AND licencias_nodro.estatus != 'NEGATIVA' AND licencias_nodro.estatus != 'CONCLUIDO' AND (licencias_nodro.tipo = 'h' OR licencias_nodro.tipo = 'i' OR licencias_nodro.tipo = 'j'  OR licencias_nodro.tipo = 'k' OR licencias_nodro.tipo = 'l' OR licencias_nodro.tipo = 'm' OR licencias_nodro.tipo = 'n'
                                           OR licencias_nodro.tipo = 'o' OR licencias_nodro.tipo = 'p' OR licencias_nodro.tipo = 'q' OR licencias_nodro.tipo = 'r' OR licencias_nodro.tipo = 's' OR licencias_nodro.tipo = 't') $whereSenND ORDER BY licencias_nodro.idLicencia DESC");

              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
            //licencias y permisos
            if ($dep_id == 'id_5de579d0d51277.03219745') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }
              $query_lic = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre AS 'dro', i_c.nombre AS 'col', licencias.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                          FROM licencias
                                          INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.uid_licencias = i_c.publicId
                                          WHERE licencias.estatus != '0' AND licencias.estatus != 'NEGATIVA' AND licencias.estatus != 'CONCLUIDO' AND (licencias.tipo = 'h' OR licencias.tipo = 'i' OR licencias.tipo = 'j'  OR licencias.tipo = 'k'  OR licencias.tipo = 'm' OR licencias.tipo = 'n'
                                            OR licencias.tipo = 'o' OR licencias.tipo = 'p' OR licencias.tipo = 'q' OR licencias.tipo = 'r') $whereSen ORDER BY licencias.idLicencia DESC");
              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                         FROM licencias_nodro
                                         LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                         LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                         WHERE licencias_nodro.estatus != '0' AND licencias_nodro.estatus != 'NEGATIVA' AND licencias_nodro.estatus != 'CONCLUIDO' AND (licencias_nodro.tipo = 'h' OR licencias_nodro.tipo = 'i' OR licencias_nodro.tipo = 'j'  OR licencias_nodro.tipo = 'k' OR licencias_nodro.tipo = 'm' OR licencias_nodro.tipo = 'n'
                                           OR licencias_nodro.tipo = 'o' OR licencias_nodro.tipo = 'p' OR licencias_nodro.tipo = 'q' OR licencias_nodro.tipo = 'r') $whereSenND ORDER BY licencias_nodro.idLicencia DESC");

              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }

            //centro Historico
            if ($dep_id == 'id_5d28ecdfe81f79.51014751') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }
              else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
              }
              //licencias_nodro.fechaCarga,
              $query_lic = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                 licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                          FROM licencias
                                          LEFT JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.idDRO = i_c.publicId
                                          WHERE estatus != '0' AND licencias.estatus != 'NEGATIVA' AND licencias.estatus != 'CONCLUIDO' AND (licencias.tipo = 'l') $whereSen ORDER BY licencias.idLicencia DESC");
              //licencias_nodro.fechaCarga,
              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col',  if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE licencias_nodro.estatus != '0' AND licencias_nodro.estatus != 'NEGATIVA' AND licencias_nodro.estatus != 'CONCLUIDO' AND (licencias_nodro.tipo = 'l') $whereSenND ORDER BY licencias_nodro.idLicencia DESC");
              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
            ///Departamento de  Direccion de Administracion Urbana
            if ($dep_id == 'id_5d3b39327fd566.84237899') {#2  NO DRO
              if ($is_adm == true || $uid == 'id_5d55af0caf3418.20186038') { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE licencias_nodro.estatus != '0' AND licencias_nodro.estatus != 'NEGATIVA' AND licencias_nodro.estatus != 'CONCLUIDO' AND (licencias_nodro.tipo = 'a' OR licencias_nodro.tipo = 'b' OR licencias_nodro.tipo = 'c'  OR licencias_nodro.tipo = 'd' OR licencias_nodro.tipo = 'e' OR licencias_nodro.tipo = 'f' OR licencias_nodro.tipo = 'g' ) $whereSen
                                           ORDER BY licencias_nodro.idLicencia DESC ");
                                           ////estaba hasta d
              $res = $query->result_array();
            }
            ////////////////////
            //uso de suelo
            if ($dep_id == 'id_5de568da0f9fb6.35498590') {#2  NO DRO
              if ($is_adm == true) { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE licencias_nodro.estatus != '0' AND licencias_nodro.estatus != 'NEGATIVA' AND licencias_nodro.estatus != 'CONCLUIDO' AND (licencias_nodro.tipo = 'a' OR licencias_nodro.tipo = 'b' OR licencias_nodro.tipo = 'c'  OR licencias_nodro.tipo = 'd') $whereSen
                                           ORDER BY licencias_nodro.idLicencia DESC ");
                                           ////estaba hasta d
              $res = $query->result_array();
            }
            //alineamiento y no oficial
            if ($dep_id == 'id_5d3b394e02a514.10059639') {#2  NO DRO
              if ($is_adm == true) { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
              $query = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                         FROM licencias_nodro
                                         LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                         LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                         WHERE licencias_nodro.estatus != '0' AND licencias_nodro.estatus != 'NEGATIVA' AND licencias_nodro.estatus != 'CONCLUIDO' AND (licencias_nodro.tipo = 'f' OR licencias_nodro.tipo = 'e') $whereSen
                                         ORDER BY licencias_nodro.idLicencia DESC ");
              $res = $query->result_array();
            }
            //fraccionamientos
            if ($dep_id == 'id_5d28ecbd4be105.89329374') {
              if ($is_adm == true) { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                           WHERE licencias_nodro.estatus != '0' AND licencias_nodro.estatus != 'NEGATIVA' AND licencias_nodro.estatus != 'CONCLUIDO' AND (licencias_nodro.tipo = 'g') $whereSen ORDER BY licencias_nodro.idLicencia DESC");
              $res = $query->result_array();
            }

            //Director General///
            if ($uid == 'id_5d81225d10ce74.06107846') {
              $query_lic = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                   licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                            FROM licencias
                                            LEFT JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            LEFT JOIN usuarios i_c ON licencias.idDRO = i_c.publicId
                                            WHERE licencias.estatus != '0' AND licencias.estatus != 'NEGATIVA' AND licencias.estatus != 'CONCLUIDO' ORDER BY licencias.idLicencia DESC");

              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                    licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                             FROM licencias_nodro
                                             LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                             LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                             WHERE licencias_nodro.estatus != '0' AND licencias_nodro.estatus != 'NEGATIVA' AND licencias_nodro.estatus != 'CONCLUIDO' ORDER BY licencias_nodro.idLicencia DESC");
              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
        $this->response($res);
      }

  public function get_allnegativas_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            //determina si el usuario es administrador
            $query_dep = $this->db->query("SELECT publicId
                                       FROM departamentos
                                       WHERE idAdministrador = '$uid'");
            $qudep = $query_dep->result_array();
            if(empty($qudep)){
              //determina de cual departamento es colaborador
              $query_dep_col = $this->db->query("SELECT idDepa
                                           FROM r_afd
                                           WHERE idFuncionario = '$uid'");
              $result_id = $query_dep_col->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                // idDep col
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = false;
            }else {
              $result_id = $query_dep->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                //idDep Admin
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = true;
            }
            ///Departamento de  Direccion de Imagen Urbana
            if ($dep_id == 'id_5d28eca5113db5.08356751') {
              if ($is_adm == true || $uid == 'id_5d8909e208b3a0.63783821') {
                $whereSen = "";
                $whereSenND = "";
              }else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }
              $query_lic = $this->db->query("SELECT licencias.idLicencia,licencias.docfinal, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre AS 'dro', i_c.nombre AS 'col', licencias.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                          FROM licencias
                                          INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.uid_licencias = i_c.publicId
                                          WHERE licencias.estatus = 'NEGATIVA' AND (licencias.tipo = 'h' OR licencias.tipo = 'i' OR licencias.tipo = 'j'  OR licencias.tipo = 'k' OR licencias.tipo = 'l' OR licencias.tipo = 'm' OR licencias.tipo = 'n'
                                            OR licencias.tipo = 'o' OR licencias.tipo = 'p' OR licencias.tipo = 'q' OR licencias.tipo = 'r') $whereSen ORDER BY licencias.idLicencia DESC");
              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                         FROM licencias_nodro
                                         LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                         LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                         WHERE estatus = 'NEGATIVA' AND (licencias_nodro.tipo = 'h' OR licencias_nodro.tipo = 'i' OR licencias_nodro.tipo = 'j'  OR licencias_nodro.tipo = 'k' OR licencias_nodro.tipo = 'l' OR licencias_nodro.tipo = 'm' OR licencias_nodro.tipo = 'n'
                                           OR licencias_nodro.tipo = 'o' OR licencias_nodro.tipo = 'p' OR licencias_nodro.tipo = 'q' OR licencias_nodro.tipo = 'r') $whereSenND ORDER BY licencias_nodro.idLicencia DESC");

              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
            //licencias y permisos
            if ($dep_id == 'id_5de579d0d51277.03219745') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }
              $query_lic = $this->db->query("SELECT licencias.idLicencia,licencias.docfinal, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre AS 'dro', i_c.nombre AS 'col', licencias.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                          FROM licencias
                                          INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.uid_licencias = i_c.publicId
                                          WHERE licencias.estatus = 'NEGATIVA' AND (licencias.tipo = 'h' OR licencias.tipo = 'i' OR licencias.tipo = 'j'  OR licencias.tipo = 'k'  OR licencias.tipo = 'm' OR licencias.tipo = 'n'
                                            OR licencias.tipo = 'o' OR licencias.tipo = 'p' OR licencias.tipo = 'q' OR licencias.tipo = 'r') $whereSen ORDER BY licencias.idLicencia DESC");
              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                         FROM licencias_nodro
                                         LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                         LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                         WHERE estatus = 'NEGATIVA' AND (licencias_nodro.tipo = 'h' OR licencias_nodro.tipo = 'i' OR licencias_nodro.tipo = 'j'  OR licencias_nodro.tipo = 'k' OR licencias_nodro.tipo = 'm' OR licencias_nodro.tipo = 'n'
                                           OR licencias_nodro.tipo = 'o' OR licencias_nodro.tipo = 'p' OR licencias_nodro.tipo = 'q' OR licencias_nodro.tipo = 'r') $whereSenND ORDER BY licencias_nodro.idLicencia DESC");

              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }

            //centro Historico
            if ($dep_id == 'id_5d28ecdfe81f79.51014751') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }
              else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
              }
              //licencias_nodro.fechaCarga,
              $query_lic = $this->db->query("SELECT licencias.idLicencia,licencias.docfinal, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                 licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                          FROM licencias
                                          LEFT JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.idDRO = i_c.publicId
                                          WHERE estatus = 'NEGATIVA' AND (licencias.tipo = 'l') $whereSen ORDER BY licencias.idLicencia DESC");
              //licencias_nodro.fechaCarga,
              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col',  if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE estatus = 'NEGATIVA' AND (licencias_nodro.tipo = 'l') $whereSenND ORDER BY licencias_nodro.idLicencia DESC");
              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
            ///Departamento de  Direccion de Administracion Urbana
            if ($dep_id == 'id_5d3b39327fd566.84237899') {#2  NO DRO
              if ($is_adm == true || $uid == 'id_5d55af0caf3418.20186038') { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE estatus = 'NEGATIVA' AND (licencias_nodro.tipo = 'a' OR licencias_nodro.tipo = 'b' OR licencias_nodro.tipo = 'c'  OR licencias_nodro.tipo = 'd' OR licencias_nodro.tipo = 'e' OR licencias_nodro.tipo = 'f' OR licencias_nodro.tipo = 'g' ) $whereSen
                                           ORDER BY licencias_nodro.idLicencia DESC ");
                                           ////estaba hasta d
              $res = $query->result_array();
            }
            ////////////////////
            //uso de suelo
            if ($dep_id == 'id_5de568da0f9fb6.35498590') {#2  NO DRO
              if ($is_adm == true) { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE estatus = 'NEGATIVA' AND (licencias_nodro.tipo = 'a' OR licencias_nodro.tipo = 'b' OR licencias_nodro.tipo = 'c'  OR licencias_nodro.tipo = 'd') $whereSen
                                           ORDER BY licencias_nodro.idLicencia DESC ");
                                           ////estaba hasta d
              $res = $query->result_array();
            }
            //alineamiento y no oficial
            if ($dep_id == 'id_5d3b394e02a514.10059639') {#2  NO DRO
              if ($is_adm == true) { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                           WHERE estatus = 'NEGATIVA' AND (licencias_nodro.tipo = 'e' OR licencias_nodro.tipo = 'f') $whereSen ORDER BY licencias_nodro.idLicencia DESC");
              $res = $query->result_array();
            }
            //fraccionamientos
            if ($dep_id == 'id_5d28ecbd4be105.89329374') {
              if ($is_adm == true) { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                           WHERE estatus = 'NEGATIVA' AND (licencias_nodro.tipo = 'g') $whereSen ORDER BY licencias_nodro.idLicencia DESC");
              $res = $query->result_array();
            }

            //Director General///
            if ($uid == 'id_5d81225d10ce74.06107846') {
              $query_lic = $this->db->query("SELECT licencias.idLicencia,licencias.docfinal, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                   licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                            FROM licencias
                                            LEFT JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            LEFT JOIN usuarios i_c ON licencias.idDRO = i_c.publicId
                                            WHERE estatus = 'NEGATIVA' ORDER BY licencias.idLicencia DESC");

              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                    licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                             FROM licencias_nodro
                                             LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                             LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                             WHERE estatus = 'NEGATIVA' ORDER BY licencias_nodro.idLicencia DESC");
              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
        $this->response($res);
      }

  public function get_allconcluidas_get(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            //determina si el usuario es administrador
            $query_dep = $this->db->query("SELECT publicId
                                       FROM departamentos
                                       WHERE idAdministrador = '$uid'");
            $qudep = $query_dep->result_array();
            if(empty($qudep)){
              //determina de cual departamento es colaborador
              $query_dep_col = $this->db->query("SELECT idDepa
                                           FROM r_afd
                                           WHERE idFuncionario = '$uid'");
              $result_id = $query_dep_col->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                // idDep col
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = false;
            }else {
              $result_id = $query_dep->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                //idDep Admin
                $dep_id =$valor;
              }
              //where adicional para colaboradores
              $is_adm = true;
            }
            ///Departamento de  Direccion de Imagen Urbana
            if ($dep_id == 'id_5d28eca5113db5.08356751') {
              if ($is_adm == true || $uid == 'id_5d8909e208b3a0.63783821') {
                $whereSen = "";
                $whereSenND = "";
              }else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }
              $query_lic = $this->db->query("SELECT licencias.idLicencia,licencias.docfinal, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre AS 'dro', i_c.nombre AS 'col', licencias.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                          FROM licencias
                                          INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.uid_licencias = i_c.publicId
                                          WHERE licencias.estatus = 'CONCLUIDO' AND (licencias.tipo = 'h' OR licencias.tipo = 'i' OR licencias.tipo = 'j'  OR licencias.tipo = 'k' OR licencias.tipo = 'l' OR licencias.tipo = 'm' OR licencias.tipo = 'n'
                                            OR licencias.tipo = 'o' OR licencias.tipo = 'p' OR licencias.tipo = 'q' OR licencias.tipo = 'r') $whereSen ORDER BY licencias.idLicencia DESC");
              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                         FROM licencias_nodro
                                         LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                         LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                         WHERE licencias_nodro.estatus = 'CONCLUIDO' AND (licencias_nodro.tipo = 'h' OR licencias_nodro.tipo = 'i' OR licencias_nodro.tipo = 'j'  OR licencias_nodro.tipo = 'k' OR licencias_nodro.tipo = 'l' OR licencias_nodro.tipo = 'm' OR licencias_nodro.tipo = 'n'
                                           OR licencias_nodro.tipo = 'o' OR licencias_nodro.tipo = 'p' OR licencias_nodro.tipo = 'q' OR licencias_nodro.tipo = 'r') $whereSenND ORDER BY licencias_nodro.idLicencia DESC");

              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
            //licencias y permisos
            if ($dep_id == 'id_5de579d0d51277.03219745') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
               }
              $query_lic = $this->db->query("SELECT licencias.idLicencia,licencias.docfinal, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre AS 'dro', i_c.nombre AS 'col', licencias.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                          FROM licencias
                                          INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.uid_licencias = i_c.publicId
                                          WHERE licencias.estatus = 'CONCLUIDO' AND (licencias.tipo = 'h' OR licencias.tipo = 'i' OR licencias.tipo = 'j'  OR licencias.tipo = 'k'  OR licencias.tipo = 'm' OR licencias.tipo = 'n'
                                            OR licencias.tipo = 'o' OR licencias.tipo = 'p' OR licencias.tipo = 'q' OR licencias.tipo = 'r') $whereSen ORDER BY licencias.idLicencia DESC");
              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                         FROM licencias_nodro
                                         LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                         LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                         WHERE estatus = 'CONCLUIDO' AND (licencias_nodro.tipo = 'h' OR licencias_nodro.tipo = 'i' OR licencias_nodro.tipo = 'j'  OR licencias_nodro.tipo = 'k' OR licencias_nodro.tipo = 'm' OR licencias_nodro.tipo = 'n'
                                           OR licencias_nodro.tipo = 'o' OR licencias_nodro.tipo = 'p' OR licencias_nodro.tipo = 'q' OR licencias_nodro.tipo = 'r') $whereSenND ORDER BY licencias_nodro.idLicencia DESC");

              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }

            //centro Historico
            if ($dep_id == 'id_5d28ecdfe81f79.51014751') {
              if ($is_adm == true) {
                $whereSen = "";
                $whereSenND = "";
              }
              else{
                $whereSen = "AND uid_licencias = '$uid'";
                $whereSenND = "AND publicCol = '$uid'";
              }
              //licencias_nodro.fechaCarga,
              $query_lic = $this->db->query("SELECT licencias.idLicencia,licencias.docfinal, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                 licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                          FROM licencias
                                          LEFT JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.idDRO = i_c.publicId
                                          WHERE estatus = 'CONCLUIDO' AND (licencias.tipo = 'l') $whereSen ORDER BY licencias.idLicencia DESC");
              //licencias_nodro.fechaCarga,
              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col',  if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE estatus = 'CONCLUIDO' AND (licencias_nodro.tipo = 'l') $whereSenND ORDER BY licencias_nodro.idLicencia DESC");
              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
            ///Departamento de  Direccion de Administracion Urbana
            if ($dep_id == 'id_5d3b39327fd566.84237899') {#2  NO DRO
              if ($is_adm == true || $uid == 'id_5d55af0caf3418.20186038') { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE estatus = 'CONCLUIDO' AND (licencias_nodro.tipo = 'a' OR licencias_nodro.tipo = 'b' OR licencias_nodro.tipo = 'c'  OR licencias_nodro.tipo = 'd' OR licencias_nodro.tipo = 'e' OR licencias_nodro.tipo = 'f' OR licencias_nodro.tipo = 'g' ) $whereSen
                                           ORDER BY licencias_nodro.idLicencia DESC ");
                                           ////estaba hasta d
              $res = $query->result_array();
            }
            ////////////////////
            //uso de suelo
            if ($dep_id == 'id_5de568da0f9fb6.35498590') {#2  NO DRO
              if ($is_adm == true) { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId
                                           LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                           WHERE estatus = 'CONCLUIDO' AND (licencias_nodro.tipo = 'a' OR licencias_nodro.tipo = 'b' OR licencias_nodro.tipo = 'c'  OR licencias_nodro.tipo = 'd') $whereSen
                                           ORDER BY licencias_nodro.idLicencia DESC ");
                                           ////estaba hasta d
              $res = $query->result_array();
            }
            //alineamiento y no oficial
            if ($dep_id == 'id_5d3b394e02a514.10059639') {#2  NO DRO
              if ($is_adm == true) { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                           WHERE estatus = 'CONCLUIDO' AND (licencias_nodro.tipo = 'e' OR licencias_nodro.tipo = 'f') $whereSen ORDER BY licencias_nodro.idLicencia DESC");
              $res = $query->result_array();
            }
            //fraccionamientos
            if ($dep_id == 'id_5d28ecbd4be105.89329374') {
              if ($is_adm == true) { $whereSen = ""; }
              else{ $whereSen = "AND publicCol = '$uid'"; }
                $query = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                  licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                           FROM licencias_nodro
                                           LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                           WHERE estatus = 'CONCLUIDO' AND (licencias_nodro.tipo = 'g') $whereSen ORDER BY licencias_nodro.idLicencia DESC");
              $res = $query->result_array();
            }

            //Director General///
            if ($uid == 'id_5d81225d10ce74.06107846') {
              $query_lic = $this->db->query("SELECT licencias.idLicencia,licencias.docfinal, licencias.fecha, licencias.nRegiS, licencias.publicId, licencias.callePred,licencias.mznPred, licencias.clavePred, licencias.propPred, licencias.estatus, licencias.clavePred,
                                                   licencias.zonaPred, licencias.tipo, licencias.vigencia, licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                            FROM licencias
                                            LEFT JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            LEFT JOIN usuarios i_c ON licencias.idDRO = i_c.publicId
                                            WHERE estatus = 'CONCLUIDO' ORDER BY licencias.idLicencia DESC");

              $query_licno = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal, licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred,licencias_nodro.mznPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                                    licencias_nodro.zonaPred, licencias_nodro.tipo, licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, usuarios.nombre, i_c.nombre AS 'col', licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                             FROM licencias_nodro
                                             LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                             LEFT JOIN usuarios i_c ON licencias_nodro.publicCol = i_c.publicId
                                             WHERE estatus = 'CONCLUIDO' ORDER BY licencias_nodro.idLicencia DESC");
              $res = array_merge($query_lic->result_array(), $query_licno->result_array());
            }
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
        $this->response($res);
      }
  /*get  license ADMIN FUNC*/
  public function Licencias_all_post(){
         $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $uid = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {

            $query_dep = $this->db->query("SELECT publicId
                                       FROM departamentos
                                       WHERE idAdministrador = '$uid'");
            if($query_dep->result_array()==null){
              $query_dep_col = $this->db->query("SELECT idDepa
                                           FROM r_afd
                                           WHERE idFuncionario = '$uid'");
              $result_id = $query_dep_col->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                $dep_id =$valor;
              }
              $dep_id = 'id_5c0035b1a13246.02873181';
              if ($dep_id == 'id_5c0035b1a13246.02873181') {
                $query = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nombre_gestor, licencias.publicId, licencias.calle, licencias.fraccionamiento, licencias.lote,licencias.nombre_propietario,
                                            licencias.estatus, licencias.idDRO, usuarios.nombre AS 'dro', licencias.fechaCarga
                                            FROM licencias
                                            INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            WHERE licencias.uid_licencias = '$uid'");
              }
              if ($dep_id == 'id_5c65dd2bc42440.77678870') {#1
                $query = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nombre_gestor, licencias.publicId, licencias.calle, licencias.fraccionamiento, licencias.lote,licencias.nombre_propietario,
                                            licencias.estatus, licencias.idDRO, usuarios.nombre AS 'dro', licencias.fechaCarga
                                            FROM licencias
                                            INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            WHERE licencias.uid_juridico = '$uid'");

              }
              if ($dep_id == 'id_5c65dd90655da3.81713553') {#2
                $query = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nombre_gestor, licencias.publicId, licencias.calle, licencias.fraccionamiento, licencias.lote,licencias.nombre_propietario,
                                            licencias.estatus, licencias.idDRO, usuarios.nombre AS 'dro', licencias.fechaCarga
                                            FROM licencias
                                            INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            WHERE licencias.uid_analista = '$uid'");
                  }
              }else {
              $result_id = $query_dep->result_array();
              $pre_id = $result_id[0];
              foreach ($pre_id as $nombre => $valor) {
                $dep_id =$valor;
              }
              if ($dep_id == 'id_5c0035b1a13246.02873181') {
                $query = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nombre_gestor, licencias.publicId, licencias.calle, licencias.fraccionamiento, licencias.lote,licencias.nombre_propietario,
                                            licencias.estatus, licencias.idDRO, usuarios.nombre AS 'dro', i_c.nombre AS 'col', licencias.fechaCarga
                                            FROM licencias
                                            INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            LEFT JOIN usuarios i_c ON licencias.uid_licencias = i_c.publicId");
              }
              if ($dep_id == 'id_5c65dd2bc42440.77678870') {#1
                $query = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nombre_gestor, licencias.publicId, licencias.calle, licencias.fraccionamiento, licencias.lote,licencias.nombre_propietario,
                                            licencias.estatus, licencias.idDRO, usuarios.nombre AS 'dro', i_c.nombre AS 'col', licencias.fechaCarga
                                            FROM licencias
                                            INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            LEFT JOIN usuarios i_c ON licencias.uid_juridico = i_c.publicId");
              }
              if ($dep_id == 'id_5c65dd90655da3.81713553') {#2
                $query = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nombre_gestor, licencias.publicId, licencias.calle, licencias.fraccionamiento, licencias.lote,licencias.nombre_propietario,
                                            licencias.estatus, licencias.idDRO, usuarios.nombre AS 'dro', i_c.nombre AS 'col', licencias.fechaCarga
                                            FROM licencias
                                            INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                            LEFT JOIN usuarios i_c ON licencias.uid_analista = i_c.publicId");
              }
            }

              /*$query = $this->db->query("SELECT licencias.idLicencia, licencias.fecha, licencias.nombre_gestor, licencias.publicId, licencias.calle, licencias.fraccionamiento, licencias.lote,licencias.nombre_propietario,
                                          licencias.estatus, licencias.idDRO, usuarios.nombre AS 'dro', i_c.nombre AS 'col'
                                          FROM licencias
                                          INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId
                                          LEFT JOIN usuarios i_c ON licencias.uid_licencias = i_c.publicId");*/
              $result = $query->result_array();
              $res =  $result;
          }else {
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
            $this->response($res);
    }
  /*get  license Test*/
  public function Licencias_all_test_get(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {

          $query = $this->db->query("SELECT r_dc.nombre_colle, licencias.idLicencia, licencias.fecha, licencias.nRegiS, licencias.publicId,
                                            licencias.callePred,licencias.zonaPred, licencias.noOficialPred, licencias.clavePred,
                                            licencias.propPred, licencias.estatus, licencias.clavePred, licencias.tipo, licencias.vigencia,
                                            licencias.prorroga, licencias.terminacion, licencias.orden_pago, licencias.sol_cancelacion,
                                            licencias.arancel, licencias.fechaCarga, usuarios.nombre AS 'dro',
                                            pagos_col.ubicacion, pagos_col.nombre AS 'nm_pago'
                                            FROM r_dc
                                            INNER JOIN licencias ON r_dc.idDRO = licencias.idDRO
                                            INNER JOIN usuarios ON r_dc.idDRO = usuarios.publicId
                                            LEFT JOIN pagos_col ON licencias.publicId = pagos_col.id_licencia
                                            WHERE r_dc.idColegio='$uid' AND licencias.estatus != '0'");

          $res = $query->result_array();
      }else{
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }
  /*get  license to notif*/
  public function get_licenseNotif_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        //$preparing = $validation_token['data'];
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $id_licence = $request->id_licencia;
            $public_id = $request->public_id;

            $sid_licence = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id_licence);
            $spublic_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $public_id);

            $consulta = "SELECT idLicencia, fecha, nombre_gestor, nombre_proyecto, publicId, calle, lote, nombre_prop, estatus,idDRO
                                           FROM licencias
                                           WHERE publicId = ? ";
            $query = $this->db->query($consulta,array($sid_licence));

            $res = $query->result_array();
            }else{
              $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
            }
          }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
          $this->response($res);
      }
  #SOLISITUD DE PRORROGA Y TERMINACION
  public function set_requ_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        $preparing = $validation_token['data'];
        $uid = $preparing->id;
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $from = $request->from;
            $publicId = $request->publicId;
            $idLicencia = $request->idLicencia;
            $req = $request->req;

            $sreq = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $req);
            $sfrom = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $from);
            $spublicId = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $publicId);
            $sidLicencia = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $idLicencia);

            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
            $mes = $meses[date('n')-1];
            $ano=date('Y');
            $dia=date('d');
            $date = $dia.'/'.$mes.'/'.$ano;

            $l_Id = uniqid('id_',TRUE);

            //Prorroga
            if ($sreq == 0) {
              $vigencia = $request->new_vigencia;
              $row_pro = "prorroga";
              //DRO
              if ($sfrom == 0) {
                $table = "licencias_prorroga";
                $table_select = "licencias";
                $row_ow = "idDRO";
                $row_col = "uid_licencias";
              }
              //Particular
              if ($sfrom == 1) {
                  $table = "licencias_nodro_prorrogas";
                  $table_select = "licencias_nodro";
                  $row_ow = "publicPart";
                  $row_col = "publicCol";
              }
            }
            //Terminacion
            if ($sreq == 1) {
              $row_pro = "terminacion";
              //DRO
              if ($sfrom == 0) {
                $table = "licencias_terminacion";
                $table_select = "licencias";
                $row_ow = "idDRO";
                $row_col = "uid_licencias";
              }
              //Particular
              if ($sfrom == 1) {
                  $table = "licencias_nodro_terminacion";
                  $table_select = "licencias_nodro";
                  $row_ow = "publicPart";
                  $row_col = "publicCol";
              }
            }

            //Folio de nueva licencia
            $queryMax = $this->db->query("SELECT COUNT(folio)
                                          FROM $table
                                          WHERE id_origen = '$publicId'");

            $result_id = $queryMax->result_array();
            $pre_id = $result_id[0];
            foreach ($pre_id as $nombre => $valor) {
               $d_tf =$valor+1;
            }

            //creacion del folio
            if ($req == 0) { $folio = 'A'.$d_tf.'-'.$sidLicencia; }
            if ($req == 1) { $folio = 'T1-'.$sidLicencia; }

              //Generar la nueva solicitud
              $consulta = $this->db->query("INSERT INTO $table
                                           (fecha, $row_ow, estatus, id_origen, publicId)
                                           VALUES
                                           ( ? , ? , ? , ? , ? )");
              $this->db->query($consulta ,array($date,$uid, 'Solicitada', $spublicId, $l_Id));

            //obtiene la informacion para nuevo registro
              $query_s = $this->db->query("SELECT tipo, aguaPred, apMatS, apPatS, callePred, clavePred, correoS, cuentaPred, domicilioS,
                drenajePred, energElecPred, lotePred, mznPred, nRegiS, noRegPer, noOficialPred, nombresPM, nombresS, propPred, rfc,
                serBasPred, telefonoS, zonaPred, cp, supTotalPre, supTotalCon, callePredio, capInv, capacidad, denomCom, descPred,
                enFunc, paAper, estaCap, metros, nivSolc, noEmpleos, puntosRef, supOcu, usoSol, apMatPer, apMatVen, apPatPer, apPatVen,
                cantAnun, colcAnun, conAccDivPred, construido, correoPer, domicPer, donacion, metrosAnun, medAnun, medAnun, metrosCons,
                metrosDivPred, noFctOpus, nombres, nombresPer, otherNiv, pa, pb, superDivPred, telefonoPer, tipoAnun, totalMetCons,
                usoProp, venta, nombreMapa, nombreCroquis, ubicacion_croquis, nombre_estado, ubicacion_estado, arancel, vigencia, folio,
                prorroga, terminacion, orden_pago, sol_cancelacion, uid_licencias, uid_juridico, uid_analista, $row_pro, $row_col
                FROM $table_select
                WHERE publicId = '$spublicId'");

              //insercion de los datos en la solicitud
              $result_s = $query_s->result_array();
              foreach ($result_s[0] as $key => $value) {
                $skey = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $key);
                $svalue = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $value);
                $queryU = $this->db->query("UPDATE $table
                                              SET $skey = '$svalue'
                                              WHERE publicId = '$l_Id'");
              }
              if ($req == 0) {
                $queryU = $this->db->query("UPDATE $table
                                               SET vigencia = '$vigencia',
                                               folio = '$folio'
                                               WHERE publicId = '$l_Id'");
              }
              if ($req == 1) {
                $queryU = $this->db->query("UPDATE $table
                                               SET folio = '$folio'
                                               WHERE publicId = '$l_Id'");
              }


              $queryU = $this->db->query("UPDATE $table_select
                                            SET $row_pro = 1
                                            WHERE publicId = '$spublicId'");

              $res = 'Solicitud Realizada';
            }else{
              $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
            }
          }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
          $this->response($res);
      }
  /*get  license to notif*/
  public function get_dat_lic_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        //$preparing = $validation_token['data'];
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if (isset($listaPOST) && !empty($listaPOST)) {
            //$request = json_decode($listaPOST);
            //$id_license = $request->id_licencia;
            //$public_id = $request->public_id;
            #,
            $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);
            $consulta = "SELECT fecha, calle, calle_post, clave_ca, cp, entre_calles, fraccionamiento, lote, manzana, numero, superficie,
                coduenos, domicilio_propietario, email_propietario, nombre_propietario, telefono_propietario, ampliacion, cajones_existentes, cajones_propuestos,
                descripcion, domicilio_gestor, email_gestor, nombre_gestor, obra_nueva, preexistencia, remodelacion, telefono_gestor, uso_actual, uso_anterior,
                nombreCroquis, ubicacion_croquis, ubicacion_estado, nombre_estado, vigencia, folio, tipo, estatus, publicId, idDRO
              FROM licencias WHERE publicId = ? ";
            $query = $this->db->query($consulta,array($slistaPOST));

            $cons2 = "SELECT nombreMapa FROM licencias WHERE publicId = ? ";
            $query_map = $this->db->query($cons2,array($slistaPOST));
              $res = [
                'res' => $query->result_array(),
                'data' => $query_map->result_array()
              ];
            }else{
              $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
            }
          }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
          $this->response($res);
      }
  /*get planos para licencia*/
  public function get_planLic_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        $preparing = $validation_token['data'];
        $uid = $preparing->id;
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $license_public_id = $request->license_public_id;
            $slicense_public_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $license_public_id);

            // $query = $this->db->query("SELECT r_planl.path,r_planl.publicId, r_planl.comentario, r_planl.validado, r_planl.nombre, documentos_tramite.doctramite,
            //                                   pagos.nombre AS 'nombre_pago', pagos.ubicacion AS 'ubicacion_pago', pagos.fecha AS 'fecha_pago',
            //                                   pagos.fecha_validado AS 'fecha_validado_pago'
            //                              FROM r_planl
            //                              INNER JOIN documentos_tramite
            //                              INNER JOIN pagos
            //                              ON r_planl.idPlano = documentos_tramite.iddoc
            //                              WHERE r_planl.idLicencia = '$license_public_id' AND pagos.id_licencia = '$license_public_id'");
            $consulta = "SELECT r_planl.path,r_planl.publicId, r_planl.comentario, r_planl.validado, r_planl.nombre, documentos_tramite.doctramite

                                         FROM r_planl
                                         INNER JOIN documentos_tramite
                                         ON r_planl.idPlano = documentos_tramite.iddoc
                                         WHERE r_planl.idLicencia = ? ";
            $query = $this->db->query($consulta,array($slicense_public_id));

            if ($query->result_array() !=null) {
              $res = $query->result_array();
            }else{
              /*$query = $this->db->query("SELECT r_docnodro.path,r_docnodro.publicId, r_docnodro.comentario, r_docnodro.validado, r_docnodro.nombre, documentos_tramite.doctramite,
                                                pagos.nombre AS 'nombre_pago', pagos.ubicacion AS 'ubicacion_pago', pagos.fecha AS 'fecha_pago',
                                                pagos.fecha_validado AS 'fecha_validado_pago'
                                           FROM r_docnodro
                                           INNER JOIN documentos_tramite
                                           INNER JOIN pagos
                                           ON r_docnodro.idPlano = documentos_tramite.iddoc
                                           WHERE r_docnodro.idLicencia = '$license_public_id' AND pagos.id_licencia = '$license_public_id'");*/
              $consulta2 = "SELECT r_docnodro.path,r_docnodro.publicId, r_docnodro.comentario, r_docnodro.validado, r_docnodro.nombre, documentos_tramite.doctramite
                                           FROM r_docnodro
                                           INNER JOIN documentos_tramite
                                           ON r_docnodro.idPlano = documentos_tramite.iddoc
                                           WHERE r_docnodro.idLicencia = ? ";

              $query = $this->db->query($consulta2,array($slicense_public_id));
              $res = $query->result_array();
            }

            }else{
              $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
            }
          }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
          $this->response($res);
        }
  /*get planos para licencia*/
  public function get_planLic_dro_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $license_public_id = $request->license_public_id;
          $slicense_public_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $license_public_id);

          $cons = "SELECT r_planl.path,r_planl.publicId, r_planl.comentario, r_planl.validado, r_planl.nombre, documentos_tramite.doctramite, documentos_tramite.departamento
                                       FROM r_planl
                                       INNER JOIN documentos_tramite
                                       ON r_planl.idPlano = documentos_tramite.iddoc
                                       WHERE r_planl.idLicencia = ? ";

          $query = $this->db->query($cons,array($slicense_public_id));

          $data = $query->result_array();
          if(isset($data) && !empty($data)){
            $res = $query->result_array();
          }else{
            $cons = "SELECT r_docnodro.path,r_docnodro.publicId, r_docnodro.comentario, r_docnodro.validado, r_docnodro.nombre, documentos_tramite.doctramite
                                          FROM r_docnodro
                                          INNER JOIN documentos_tramite
                                          ON r_docnodro.idPlano = documentos_tramite.iddoc
                                          WHERE r_docnodro.idLicencia = ? ";
            $query = $this->db->query($cons,array($slicense_public_id));
            $res = $query->result_array();
          }



        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
    }

  public function get_pagos_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $license_public_id = $request->license_public_id;
          $slicense_public_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $license_public_id);

          $cons = "SELECT nombre, ubicacion, fecha, validado, public_id FROM pagos WHERE id_licencia = ? ";

          $query = $this->db->query($cons,array($slicense_public_id));

          $res = $query->result_array();
        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
    }

  public function licenseToPdf_post(){
        session_start();
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $id = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
            if(isset($listaPOST) && !empty($listaPOST)){
              $request = json_decode($listaPOST);
              $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);

              $cons = "SELECT * FROM licencias
                INNER JOIN   r_tfl ON r_tfl.idLicencia = licencias.publicId
                INNER JOIN r_tol  ON r_tol.idLicencia = licencias.publicId
                INNER JOIN usuarios ON usuarios.publicId = licencias.idDRO
                INNER JOIN r_dc ON r_dc.idDRO=usuarios.publicId
                        WHERE licencias.publicId= ? ";

              $query = $this->db->query($cons,array($slistaPOST));

              $res = $query->result_array();
              $_SESSION['licencia'] = $res;
            }else{
              $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
            }
          }else{
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
        $this->response($res);
      }

  public function addDocLic_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        $preparing = $validation_token['data'];
        $uid = $preparing->id;
        if (!empty($validation_token) && $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $publicId = $request->publicId; #id relacion
            $license_public_id = $request->license_public_id; #id licencia
            $file = $request->file;
            $base64 = $file->base64;
            $file_name = $file->filename;
            $file_size = $file->filesize;
            $file_type = $file->filetype;

            $spublicId = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $publicId);
            $slicense_public_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $license_public_id);

            $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$slicense_public_id;
            $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$slicense_public_id.'/'.$file_name;
            $pathNewImg = $uid.'/licenses/'.$slicense_public_id.'/'.$file_name;
            if(file_exists($path)){}
            else{mkdir($path, 0777, true);}

            if ($file_type == 'application/pdf' || $file_type == 'application/PDF' || $file_type == 'application/dwg' || $file_type == 'application/DWG' || $file_type == '' || $file_type == 'image/JPEG' || $file_type == 'image/JPG' || $file_type == 'image/PNG' || $file_type == 'image/jpg' || $file_type == 'image/jpeg' || $file_type == 'image/png' || $file_type == 'application/x-zip-compressed') {
               if ($file_size < 52428800 ) {
                  $queryS = $this->db->query("SELECT path FROM r_planl WHERE publicId = '$publicId'");
                  $data = $queryS->result_array();
                  if(isset($data) && !empty($data)){
                    if(file_put_contents($pathImg, base64_decode($base64))){
                      $queryU = "UPDATE r_planl
                                  SET path = '$pathNewImg',
                                  nombre = '$file_name'
                                  WHERE publicId = ? ";
                      $this->db->query($queryU,array($spublicId));

                      $res =  "Archivo cargado y guardado";
                    }else {
                      $res = "No se pudó guardar la imagen";
                    }
                  }else{
                    if(file_put_contents($pathImg, base64_decode($base64))){
                      $queryU = "UPDATE r_docnodro
                                  SET path = '$pathNewImg',
                                  nombre = '$file_name'
                                  WHERE publicId = ? ";
                      $this->db->query($queryU,array($spublicId));

                      $res =  "Archivo cargado y guardado";
                    }else {
                      $res = "No se pudó guardar la imagen";
                    }
                  }
                }else {
                  $res = "Archivo demasido grande, tamaño requerido '50MB'";
                }
              }else {
                $res =  "Tipo de archivo no permitido, tipo recomendao 'PDF'";
              }
          }else{
            $res = "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;

        }
        $this->response($res);
      }

  public function addDocLicAdm_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        $preparing = $validation_token['data'];
        $uid = $preparing->id;
        if (!empty($validation_token) && $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);
            $publicId = $request->publicId; #id relacion
            $license_public_id = $request->license_public_id; #id licencia
            $file = $request->file;
            $base64 = $file->base64;
            $file_name = $file->filename;
            $file_size = $file->filesize;
            $file_type = $file->filetype;

            $spublicId = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $publicId);
            $slicense_public_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $license_public_id);

            $queryS = $this->db->query("SELECT idDRO FROM licencias WHERE publicId = '$license_public_id'");
            $data = $queryS->result_array();
               foreach ($data as $id) {
                  $uId = $id['idDRO'];
               }

               $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$slicense_public_id;
               $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$slicense_public_id.'/'.$file_name;
               $pathNewImg = $uid.'/licenses/'.$slicense_public_id.'/'.$file_name;
               if(file_exists($path)){}
               else{mkdir($path, 0777, true);}

               if ($file_type == 'application/pdf' || $file_type == 'application/PDF' || $file_type == 'application/dwg' || $file_type == 'application/DWG' || $file_type == '' || $file_type == 'image/JPEG' || $file_type == 'image/JPG' || $file_type == 'image/PNG' || $file_type == 'image/jpg' || $file_type == 'image/jpeg' || $file_type == 'image/png' || $file_type == 'application/x-zip-compressed') {
               if ($file_size < 52428800 ) {
                 $queryS = $this->db->query("SELECT path FROM r_planl WHERE publicId = '$publicId'");
                 $data = $queryS->result_array();
                 if(isset($data) && !empty($data)){
                   if(file_put_contents($pathImg, base64_decode($base64))){
                     $queryU = "UPDATE r_planl
                                 SET path = '$pathNewImg',
                                 nombre = '$file_name'
                                 WHERE publicId = ? ";
                     $this->db->query($queryU,array($spublicId));

                     $res =  "Archivo cargado y guardado";
                   }else {
                     $res = "No se pudó guardar la imagen";
                   }
                 }else{
                   if(file_put_contents($pathImg, base64_decode($base64))){
                     $queryU = "UPDATE r_docnodro
                                 SET path = '$pathNewImg',
                                 nombre = '$file_name'
                                 WHERE publicId = ? ";
                     $this->db->query($queryU,array($spublicId));

                     $res =  "Archivo cargado y guardado";
                   }else {
                     $res = "No se pudó guardar la imagen";
                   }
                 }
               }else {
                  $res = "Archivo demasido grande, tamaño requerido '50MB'";
                }
              }else {
                $res =  "Tipo de archivo no permitido, tipo recomendao 'PDF'";
              }
          }else{
            $res = "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;

        }
        $this->response($res);
      }

  public function add_IMG_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $id = $preparing->id;
      if (!empty($validation_token) && $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $base64 = $request->base64;
          $file_name = $request->filename;
          $file_size = $request->filesize;
          $file_type = $request->filetype;
          $idLice = $request->publicId;

          $sidLice = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $idLice);

          $uId = uniqid('id_',TRUE);
          $prepath = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/';
          $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$id.'/EstadoObra/';
          $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$id.'/EstadoObra/'.$file_name;
          $pathNewImg = $id.'/EstadoObra/'.$file_name;
          $date = date('d/M/Y H:i');
          if (file_exists($path)) {
            if($file_type == 'image/png' || $file_type == 'image/PNG' || $file_type == 'image/jpg' || $file_type == 'image/JPG' || $file_type =='image/jpeg' || $file_type =='image/JPEG'){
              if($file_size < 3030477 ) {
                if(file_put_contents($pathImg, base64_decode($base64))){
                  $cons = "SELECT * FROM r_imgsl WHERE idLicencia= ? ";
                  $query0 = $this->db->query($cons,array($sidLice));
                  if($query0->result_array()==null){
                    $cons = "INSERT INTO r_imgsl(idServidor,fecha,idLicencia,newImagen,publicId) VALUES ( ? , ? , ? , ? , ? )";
                    $this->db->query($cons,array($id,$date,$sidLice,$pathNewImg,$uId));
                    $res =  "Archivo cargado y guardado";
                  }else{
                    $query = "UPDATE r_imgsl SET newImagen= ? WHERE idLicencia = ? ";
                    $this->db->query($query,array($pathNewImg,$sidLice));
                    $res =  "Archivo Actualizado y guardado";
                  }
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
                  if($query0->result_array()==null){
                    $cons = "INSERT INTO r_imgsl(idServidor,fecha,idLicencia,newImagen,publicId) VALUES ( ? , ? , ? , ? , ? )";
                    $this->db->query($cons,array($id,$date,$sidLice,$pathNewImg,$uId));
                    $res =  "Archivo cargado y guardado";
                  }else{
                    $query = "UPDATE r_imgsl SET newImagen= ? WHERE idLicencia = ? ";
                    $this->db->query($query,array($pathNewImg,$sidLice));
                    $res =  "Archivo Actualizado y guardado";
                  }
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

  public function dataForPDF_post(){
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $id = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
            if(isset($listaPOST) && !empty($listaPOST)){

              $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);

              $cons = "SELECT * FROM licencias
                INNER JOIN   r_tfl ON r_tfl.idLicencia = licencias.publicId
                INNER JOIN r_tol  ON r_tol.idLicencia = licencias.publicId
                INNER JOIN usuarios ON usuarios.publicId = licencias.idDRO
                INNER JOIN r_dc ON r_dc.idDRO=usuarios.publicId
                INNER JOIN r_imgsl ON r_imgsl.idLicencia = licencias.publicId
                        WHERE licencias.publicId= ? ";

              $query = $this->db->query($cons, array($slistaPOST));

              $res = $query->result_array();
            }else{
              $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
            }
          }else{
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
        $this->response($res);
      }

  public function licenseToPdfWIMG_post(){
        session_start();
          $this->load->library('Authorization_Token');
          $validation_token = $this->authorization_token->validateToken();
          $preparing = $validation_token['data'];
          $id = $preparing->id;
          if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $listaPOST= file_get_contents("php://input");
            if(isset($listaPOST) && !empty($listaPOST)){

              $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);

              $cons = "SELECT * FROM licencias
                INNER JOIN   r_tfl ON r_tfl.idLicencia = licencias.publicId
                INNER JOIN r_tol  ON r_tol.idLicencia = licencias.publicId
                INNER JOIN usuarios ON usuarios.publicId = licencias.idDRO
                INNER JOIN r_dc ON r_dc.idDRO=usuarios.publicId
                INNER JOIN r_imgsl ON r_imgsl.idLicencia = licencias.publicId
                        WHERE licencias.publicId= ? ";

                $query = $this->db->query($cons, array($slistaPOST));

              $res = $query->result_array();
              $_SESSION['licencia'] = $res;
            }else{
              $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
            }
          }else{
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
          }
        $this->response($res);
      }

  public function comment_post(){
            $this->load->library('Authorization_Token');
            $validation_token = $this->authorization_token->validateToken();
            $preparing = $validation_token['data'];
            $id = $preparing->id;
            if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $listaPOST= file_get_contents("php://input");
              if(isset($listaPOST) && !empty($listaPOST)){
                $request = json_decode($listaPOST);
                $comentario = $request->comentario;
                $public_id = $request->publicId;
                $licence_id = $request->licence_id;
                // #optención del departamento
                $scomentario = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $comentario);
                $spublic_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $public_id);
                $slicence_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $licence_id);

                $cons = "SELECT publicId FROM licencias WHERE publicId = ? ";
                $query_init_ad = $this->db->query($cons, array($slicence_id));
                $res_init_ad =  $query_init_ad->result_array();
                #si es dministrador
                if (isset($res_init_ad) && !empty($res_init_ad)) {
                  $table = "r_planl";
                }else{
                  $table = "r_docnodro";
                }
                $queryU = "UPDATE $table SET comentario = ? WHERE publicId = ? ";
                $query_init_ad = $this->db->query($queryU, array($scomentario,$spublic_id));
                $res = "Datos guardados";

              }else{
                $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
              }
            }else{
              $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
            }
          $this->response($res);
        }

  public function validate_post(){
              $this->load->library('Authorization_Token');
              $validation_token = $this->authorization_token->validateToken();
              $preparing = $validation_token['data'];
              $id = $preparing->id;
              if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
                $listaPOST= file_get_contents("php://input");
                if(isset($listaPOST) && !empty($listaPOST)){
                  $request = json_decode($listaPOST);
                  $status = $request->status;
                  $public_id = $request->publicId;
                  $licence_id = $request->licence_id;

                  $sstatus = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $status);
                  $spublic_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $public_id);
                  $slicence_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $licence_id);

                  $cons = "SELECT publicId FROM departamentos WHERE idAdministrador = ? ";
                  $query_init_ad= $this->db->query($cons,array($id));
                  $res_init_ad =  $query_init_ad->result_array();
                  #si es dministrador
                  if (isset($res_init_ad) && !empty($res_init_ad)) {
                    $pre_data = $res_init_ad[0];
                    $data = $pre_data['publicId'];
                    //uso de suelo
                    if ($data == "id_5d3b39327fd566.84237899") {
                      $campo = 'estatus';
                      $info = '1';
                    }
                    //licencias y permiso
                    if ($data == "id_5d28eca5113db5.08356751") {
                      $campo = 'estatus';
                      $info = '';
                    }
                    if ($data == "id_5c0035b1a13246.02873181") {
                      $campo = 'estatus';
                      $info = 'En revisión';
                    }


                  }
                  else{
                  #si es colaborador
                  $cons = "SELECT idDepa FROM r_afd WHERE idFuncionario = ? ";
                  $query_init= $this->db->query($cons,array($id));
                    $res_init =  $query_init->result_array();
                    if (isset($res_init) && !empty($res_init)) {
                      $pre_data = $res_init[0];
                      $data = $pre_data['idDepa'];
                      if ($data == "id_5c65dd90655da3.81713553") {
                        $campo = 'estatus_a';
                        $info = 'Planos Revisados';
                      }
                      if ($data == "id_5c65dd2bc42440.77678870") {
                        $campo = 'estatus_j';
                        $info = 'Docs Revisados';
                      }
                      if ($data == "id_5c0035b1a13246.02873181") {
                        $campo = 'estatus';
                        $info = 'En revisión';
                      }
                    }
                  }

                  $cons2 = "SELECT * FROM r_planl WHERE publicId= ? ";
                  $queryv = $this->db->query($cons2, array($spublic_id));

                  if(!empty($queryv->result_array()) ){
                    $cons3 ="UPDATE r_planl SET validado = ? , servidor_id = ? WHERE publicId = ?";
                    $this->db->query($cons3,array($sstatus,$id,$spublic_id));
                    // $queryUL = $this->db->query("UPDATE licencias
                    //  SET $campo = '$info'
                    //  WHERE publicId = '$licence_id'");
                    $res ='Documento Validado';
                  }else{
                    $cons3 = "UPDATE r_docnodro SET validado = ? , servidor_id = ? WHERE publicId = ? ";
                    $this->db->query($cons3,array($sstatus,$id,$spublic_id));
                    // $queryUL = $this->db->query("UPDATE licencias_nodro
                    //  SET $campo = '$info'
                    //  WHERE publicId = '$licence_id'");
                    $res ='Documento Validado';
                  }

                }else{
                  $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
                }
              }else{
                $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
              }
            $this->response($res);
          }

  //validacion o revision de documentos
  public function validate_all_post(){
                $this->load->library('Authorization_Token');
                $validation_token = $this->authorization_token->validateToken();
                $preparing = $validation_token['data'];
                $id = $preparing->id;
                if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
                  $listaPOST= file_get_contents("php://input");
                  if(isset($listaPOST) && !empty($listaPOST)){
                    $request = json_decode($listaPOST);
                    #$status = $request->status;
                    $type = $request->type;
                    $licence_id = $request->license_public_id;

                    $stype = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $type);
                    $slicence_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $licence_id);

                    $cons = $this->db->query("SELECT publicId FROM licencias WHERE publicId = ");
                    $query_init_ad= $this->db->query($cons, array($slicence_id));
                    $res_init_ad =  $query_init_ad->result_array();

                    if (isset($res_init_ad) && !empty($res_init_ad)) {
                      $table = "licencias";
                    }else{
                      $table = "licencias_nodro";
                    }
                    if ($stype == 0) {$data = 'Doc. Validados';}
                    else{ $data = 'Doc. Revisados'; }
                    $cons2 = "UPDATE $table SET estatus = ?  WHERE publicId = ? ";
                    $this->db->query($cons2, array($data,$slicence_id));
                    $res ="Datos guardados";
                  }else{
                    $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
                  }
                }else{
                  $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
                }
              $this->response($res);
            }

  public function validate_all2_post(){
                  $this->load->library('Authorization_Token');
                  $validation_token = $this->authorization_token->validateToken();
                  $preparing = $validation_token['data'];
                  $id = $preparing->id;
                  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
                    $listaPOST= file_get_contents("php://input");
                    if(isset($listaPOST) && !empty($listaPOST)){
                      //$request = json_decode($listaPOST);
                      #$status = $request->status;
                      //$type = $request->type;
                      $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);
                      $licence_id = $slistaPOST;

                      $cons = "SELECT publicId, idDRO FROM licencias WHERE publicId = ? ";
                      $query_init_ad = $this->db->query($cons,array($licence_id));
                      $res_init_ad =  $query_init_ad->result_array();

                      if (isset($res_init_ad) && !empty($res_init_ad)) {
                        foreach ($res_init_ad[0] as $key => $value) {
                          if ($key == 'idDRO') {
                            $dro = $value;
                          }
                        }
                        $table = "licencias";
                        $soli = "idDRO";
                        $camps = "tipAuto,usoPred,inahPred,altmaxPred,pusPred,consFPred,cofusoPred,cofSuPred,aprotrazPred,otrosPred,autosPred,espPred,motoPred,bicPred,obsePred,intervenciones,pathCroquis,nombreCroq,giro,areacusPred,areacosPred,colPred,";
                        $queryD = $this->db->query("SELECT usuarios.nombre,usuarios.correo,r_dc.domicilio,r_dc.celular,r_dc.rop,r_dc.cedula FROM usuarios INNER JOIN r_dc ON usuarios.publicId = r_dc.idDRO WHERE usuarios.publicId = '$dro' ");
                        $query_colab = $this->db->query("SELECT usuarios.nombre FROM licencias LEFT JOIN usuarios ON licencias.uid_licencias = usuarios.publicId WHERE licencias.publicId='$licence_id' ");
                      }else{
                        $table = "licencias_nodro";
                        $soli = "publicPart";
                        $camps = "";
                      }
                      /*if ($type == 0) {$data = 'Doc. Validados';}
                      else{ $data = 'Doc. Revisados'; }*/

                      $meses = array("ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SEP","OCT","NOV","DIC");
                      $mes = $meses[date('n')-1];
                      $ano=date('Y');
                      $dia=date('d');
                      $date = $dia.'/'.$mes.'/'.$ano;

                      $queryUL = $this->db->query("UPDATE $table
                       SET estatus = 'Para Firma' , fechaAuto = '$date'
                       WHERE publicId = '$licence_id'");
                      session_start();

                      if($table == "licencias"){
                        $_SESSION ['dro'] = $queryD->result_array();
                        $_SESSION ['colab'] = $query_colab->result_array();
                      }else {
                        $_SESSION ['dro'] = "";
                      }

                      $cons2 = "SELECT sujeto,puesto,texto1,texto2, fecha,tipo,aguaPred,apMatS,apPatS,callePred,clavePred,correoS,cuentaPred,fechaAuto,
                        domicilioS,drenajePred,energElecPred,lotePred,mznPred,nRegiS,noRegPer,$camps
                        noOficialPred,nombresPM,nombresS,propPred,rfc,serBasPred,telefonoS,zonaPred,
                        cp,supTotalPre,supTotalCon,calleS,coloniaS,ciudadS,estadoS,noextS,nointS,cpS,
                        callePredio,capInv,capacidad,denomCom,descPred,enFunc,estaCap,metros,nivSolc,
                        noEmpleos,puntosRef,supOcu,usoSol,apMatPer,apMatVen,apPatPer,apPatVen,cantAnun,
                        colcAnun,conAccDivPred,construido,correoPer,domicPer,donacion,metrosAnun,medAnun,
                        metCLCons,metrosCons,metrosDivPred,noFctOpus,nombres,nombresPer,otherNiv,pa,pb,
                        superDivPred,telefonoPer,tipoAnun,totalMetCons,usoProp,venta,nombreMapa,nombreCroquis,
                        ubicacion_croquis,nombre_estado,ubicacion_estado,arancel,vigencia,idLicencia AS folio, publicId, $soli
                        FROM $table WHERE publicId = ? ";

                      $query = $this->db->query($cons2,array($slistaPOST));

                      $cons3 = "SELECT * FROM derecho_licencia WHERE id_licencia = ? ";
                      $query_der = $this->db->query($cons3,array($slistaPOST));

                      $cons4 = "SELECT * FROM $table WHERE publicId = ? ";
                      $query = $this->db->query($cons4,array($slistaPOST));

                        $_SESSION ['licencia'] = $query->result_array();
                        $_SESSION ['derechos'] = $query_der->result_array();
                        $dt = $query->result_array();
                        foreach ($dt[0] as $key => $value) {
                          if ($key == 'tipo') {
                            $tipo = $value;
                          }
                        }
                        $res = array('tipo' => $tipo,'flag' => true );
                    }else{
                      $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
                    }
                  }else{
                    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
                  }
                $this->response($res);
              }

  public function validate_all3_post(){
                    $this->load->library('Authorization_Token');
                    $validation_token = $this->authorization_token->validateToken();
                    $preparing = $validation_token['data'];
                    $id = $preparing->id;
                    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
                      $listaPOST= file_get_contents("php://input");
                      if(isset($listaPOST) && !empty($listaPOST)){
                        //$request = json_decode($listaPOST);
                        #$status = $request->status;
                        //$type = $request->type;
                        $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);
                        $licence_id = $slistaPOST;

                        $query = "SELECT publicId, idDRO FROM licencias WHERE publicId = ? ";
                        $query_init_ad = $this->db->query($query,array($licence_id));
                        $res_init_ad =  $query_init_ad->result_array();

                        if (isset($res_init_ad) && !empty($res_init_ad)) {
                          foreach ($res_init_ad[0] as $key => $value) {
                            if ($key == 'idDRO') {
                              $dro = $value;
                            }
                          }
                          $table = "licencias";
                          $soli = "idDRO";
                          $camps = "acta_circu,tipAuto,usoPred,inahPred,altmaxPred,pusPred,consFPred,cofusoPred,cofSuPred,aprotrazPred,otrosPred,autosPred,espPred,motoPred,bicPred,obsePred,intervenciones,pathCroquis,nombreCroq,giro,areacusPred,areacosPred,colPred,";
                          $queryD = $this->db->query("SELECT usuarios.nombre,usuarios.correo,r_dc.domicilio,r_dc.celular,r_dc.rop,r_dc.cedula FROM usuarios INNER JOIN r_dc ON usuarios.publicId = r_dc.idDRO WHERE usuarios.publicId = '$dro' ");
                          $query_colab = $this->db->query("SELECT usuarios.nombre AS colab FROM licencias LEFT JOIN usuarios ON licencias.uid_licencias = usuarios.publicId WHERE licencias.publicId='$licence_id' ");

                        }else{
                          $table = "licencias_nodro";
                          $soli = "publicPart";
                          $camps = "";
                        }
                        /*if ($type == 0) {$data = 'Doc. Validados';}
                        else{ $data = 'Doc. Revisados'; }*/
                        /*$queryUL = $this->db->query("UPDATE $table
                         SET estatus = 'Autorizado'
                         WHERE publicId = '$licence_id'");*/
                        session_start();

                        if($table == "licencias"){
                          $_SESSION ['dro'] = $queryD->result_array();
                          $_SESSION ['colab'] = $query_colab->result_array();
                        }else {
                          $_SESSION ['dro'] = "";
                        }

                        $cons2 = "SELECT * FROM $table WHERE publicId = ? ";

                        $query = $this->db->query($cons2,array($slistaPOST));

                        $cons3 = "SELECT * FROM derecho_licencia WHERE id_licencia = ? ";
                        $query_der = $this->db->query($cons3,array($slistaPOST));

                          $_SESSION ['licencia'] = $query->result_array();
                          $_SESSION ['derechos'] = $query_der->result_array();
                          $dt = $query->result_array();
                          foreach ($dt[0] as $key => $value) {
                            if ($key == 'tipo') {
                              $tipo = $value;
                            }
                          }
                          $res = array('tipo' => $tipo,'flag' => true );
                      }else{
                        $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
                      }
                    }else{
                      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
                    }
                  $this->response($res);
                }
  /*get  license to notif*/
  public function downPDF_post(){
            $this->load->library('Authorization_Token');
            $validation_token = $this->authorization_token->validateToken();
            //$preparing = $validation_token['data'];
            if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $listaPOST= file_get_contents("php://input");
              if (isset($listaPOST) && !empty($listaPOST)) {
                $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);
                session_start();
                $cons = "SELECT fecha, calle, calle_post, clave_ca, cp, entre_calles, fraccionamiento, lote, manzana, numero, superficie,
                    coduenos, domicilio_propietario, email_propietario, nombre_propietario, telefono_propietario, ampliacion, cajones_existentes, cajones_propuestos,
                    descripcion, domicilio_gestor, email_gestor, nombre_gestor, obra_nueva, preexistencia, remodelacion, telefono_gestor, uso_actual, uso_anterior,
                    nombreCroquis, ubicacion_croquis, ubicacion_estado, nombre_estado, tipo, estatus,nombreMapa, publicId, idDRO
                  FROM licencias WHERE publicId = ? ";
                $query_der = $this->db->query($cons,array($slistaPOST));
                $_SESSION ['licencia'] = $query->result_array();
                  $res = 'Data Ready';
                }else{
                  $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
                }
              }else {
                $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
              }
              $this->response($res);
          }

  public function downPdf_sol_post(){
            $this->load->library('Authorization_Token');
            $validation_token = $this->authorization_token->validateToken();
            //$preparing = $validation_token['data'];
            if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $listaPOST= file_get_contents("php://input");
              if (isset($listaPOST) && !empty($listaPOST)) {
                $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);

                session_start();

                  $cons = "SELECT fecha,tipo,aguaPred,apMatS,apPatS,callePred,clavePred,correoS,cuentaPred,
                  domicilioS,drenajePred,energElecPred,lotePred,mznPred,nRegiS,noRegPer,
                  noOficialPred,nombresPM,nombresS,propPred,rfc,serBasPred,telefonoS,zonaPred,
                  cp,supTotalPre,supTotalCon,calleS,coloniaS,ciudadS,estadoS,noextS,nointS,cpS,
                  callePredio,capInv,capacidad,denomCom,descPred,enFunc,estaCap,metros,nivSolc,
                  noEmpleos,puntosRef,supOcu,usoSol,apMatPer,apMatVen,apPatPer,apPatVen,cantAnun,
                  colcAnun,conAccDivPred,construido,correoPer,domicPer,donacion,metrosAnun,medAnun,
                  metCLCons,metrosCons,metrosDivPred,noFctOpus,nombres,nombresPer,otherNiv,pa,pb,
                  superDivPred,telefonoPer,tipoAnun,totalMetCons,usoProp,venta,nombreMapa,nombreCroquis,giro,
                  ubicacion_croquis,nombre_estado,ubicacion_estado,arancel,vigencia,folio, publicId, idDRO, idLicencia AS folio
                  FROM licencias WHERE publicId = ? ";

                  $query = $this->db->query($cons,array($slistaPOST));

                  if($query->result_array()!=null){
                    $_SESSION ['licencia'] = $query->result_array();
                    $res_init_ad = $query->result_array();
                    foreach ($res_init_ad[0] as $key => $value) {
                      if ($key == 'idDRO') {
                        $dro = $value;
                      }
                    }
                    $queryD = $this->db->query("SELECT usuarios.nombre,usuarios.correo,r_dc.domicilio,r_dc.celular,r_dc.rop,r_dc.cedula FROM usuarios INNER JOIN r_dc ON usuarios.publicId = r_dc.idDRO WHERE usuarios.publicId = '$dro' ");
                    $_SESSION ['dro'] = $queryD->result_array();
                    $res = true;
                  }else{
                      $cons = "SELECT fecha,tipo,aguaPred,apMatS,apPatS,callePred,clavePred,correoS,cuentaPred,
                      domicilioS,drenajePred,energElecPred,lotePred,mznPred,nRegiS,noRegPer,
                      noOficialPred,nombresPM,nombresS,propPred,rfc,serBasPred,telefonoS,zonaPred,
                      cp,supTotalPre,supTotalCon,calleS,coloniaS,ciudadS,estadoS,noextS,nointS,cpS,
                      callePredio,capInv,capacidad,denomCom,descPred,enFunc,estaCap,metros,nivSolc,
                      noEmpleos,puntosRef,supOcu,usoSol,apMatPer,apMatVen,apPatPer,apPatVen,cantAnun,
                      colcAnun,conAccDivPred,construido,correoPer,domicPer,donacion,metrosAnun,medAnun,
                      metCLCons,metrosCons,metrosDivPred,noFctOpus,nombres,nombresPer,otherNiv,pa,pb,
                      superDivPred,telefonoPer,tipoAnun,totalMetCons,usoProp,venta,nombreMapa,nombreCroquis,
                      ubicacion_croquis,nombre_estado,ubicacion_estado,arancel,vigencia,folio, publicId, publicPart, idLicencia AS folio
                      FROM licencias_nodro WHERE publicId = ? ";

                      $query = $this->db->query($cons,array($slistaPOST));

                      if($query->result_array()!=null){
                        $_SESSION ['licencia'] = $query->result_array();
                        $_SESSION ['dro'] = '';
                        $res = true;
                      }
                  }



                }else{
                  $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
                }
              }else {
                $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
              }
              $this->response($res);
          }

  public function downPdf_lona_post(){
            $this->load->library('Authorization_Token');
            $validation_token = $this->authorization_token->validateToken();
            //$preparing = $validation_token['data'];
            if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $listaPOST= file_get_contents("php://input");
              if (isset($listaPOST) && !empty($listaPOST)) {
                $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);

                session_start();

                  $cons = "SELECT fecha,tipo,aguaPred,apMatS,apPatS,callePred,clavePred,correoS,cuentaPred,
                  domicilioS,drenajePred,energElecPred,lotePred,mznPred,nRegiS,noRegPer,
                  noOficialPred,nombresPM,nombresS,propPred,rfc,serBasPred,telefonoS,zonaPred,
                  cp,supTotalPre,supTotalCon,calleS,coloniaS,ciudadS,estadoS,noextS,nointS,cpS,
                  callePredio,capInv,capacidad,denomCom,descPred,enFunc,estaCap,metros,nivSolc,
                  noEmpleos,puntosRef,supOcu,usoSol,apMatPer,apMatVen,apPatPer,apPatVen,cantAnun,
                  colcAnun,conAccDivPred,construido,correoPer,domicPer,donacion,metrosAnun,medAnun,
                  metCLCons,metrosCons,metrosDivPred,noFctOpus,nombres,nombresPer,otherNiv,pa,pb,
                  superDivPred,telefonoPer,tipoAnun,totalMetCons,usoProp,venta,nombreMapa,nombreCroquis,giro,fechaAuto,
                  ubicacion_croquis,nombre_estado,ubicacion_estado,arancel,vigencia,folio, publicId, idDRO, idLicencia AS folio
                  FROM licencias WHERE publicId = ? ";

                  $query = $this->db->query($cons,array($slistaPOST));

                  if($query->result_array()!=null){
                    $_SESSION ['licencia'] = $query->result_array();
                    $res_init_ad = $query->result_array();
                    foreach ($res_init_ad[0] as $key => $value) {
                      if ($key == 'idDRO') {
                        $dro = $value;
                      }
                    }
                    $queryD = $this->db->query("SELECT usuarios.nombre,usuarios.correo,r_dc.domicilio,r_dc.celular,r_dc.rop,r_dc.cedula FROM usuarios INNER JOIN r_dc ON usuarios.publicId = r_dc.idDRO WHERE usuarios.publicId = '$dro' ");
                    $_SESSION ['dro'] = $queryD->result_array();
                    $res = true;
                  }else{
                      $cons = "SELECT fecha,tipo,aguaPred,apMatS,apPatS,callePred,clavePred,correoS,cuentaPred,
                      domicilioS,drenajePred,energElecPred,lotePred,mznPred,nRegiS,noRegPer,
                      noOficialPred,nombresPM,nombresS,propPred,rfc,serBasPred,telefonoS,zonaPred,
                      cp,supTotalPre,supTotalCon,calleS,coloniaS,ciudadS,estadoS,noextS,nointS,cpS,
                      callePredio,capInv,capacidad,denomCom,descPred,enFunc,estaCap,metros,nivSolc,
                      noEmpleos,puntosRef,supOcu,usoSol,apMatPer,apMatVen,apPatPer,apPatVen,cantAnun,
                      colcAnun,conAccDivPred,construido,correoPer,domicPer,donacion,metrosAnun,medAnun,
                      metCLCons,metrosCons,metrosDivPred,noFctOpus,nombres,nombresPer,otherNiv,pa,pb,
                      superDivPred,telefonoPer,tipoAnun,totalMetCons,usoProp,venta,nombreMapa,nombreCroquis,fechaAuto,
                      ubicacion_croquis,nombre_estado,ubicacion_estado,arancel,vigencia,folio, publicId, publicPart, idLicencia AS folio
                      FROM licencias_nodro WHERE publicId = ? ";

                      $query = $this->db->query($cons,array($slistaPOST));

                      if($query->result_array()!=null){
                        $_SESSION ['licencia'] = $query->result_array();
                        $_SESSION ['dro'] = '';
                        $res = true;
                      }
                  }



                }else{
                  $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
                }
              }else {
                $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
              }
              $this->response($res);
          }

//////////////////////////////////////////////COORDINACION/////////////////////////////////////////////////////////////////////////////////////

  public function add_license_nodro_post(){
              $this->load->library('Authorization_Token');
              $validation_token = $this->authorization_token->validateToken();
              $preparing = $validation_token['data'];
              $uid = $preparing->id;
              if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
                $listaPOST= file_get_contents("php://input");
                if (isset($listaPOST) && !empty($listaPOST)) {
                  $queryU = $this->db->query("SELECT tipoUsuario FROM usuarios WHERE publicId='$uid'");
                    $tip = $queryU->result_array();
                    foreach ($tip as $tip) {
                      $t = $tip['tipoUsuario'];
                    }
                  $request = json_decode($listaPOST);
                  $data_0 =  $request[0];
                  $data_1 =  $request[1];
                  $data_2 =  $request[2];
                  $data_3 =  $request[3];

                  $type = $data_0->type;
                  $stype = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $type);

                  $map = $data_1->map;
                  // $scketch = $data_2->scketch;

                  $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
                  $mes = $meses[date('n')-1];
                  $ano=date('Y');
                  $dia=date('d');
                  $date = $dia.'/'.$mes.'/'.$ano;

                  $l_Id = uniqid('id_',TRUE);

                  if($t == 'id_5bcaa4f838f9e'){
                    $query = "INSERT INTO licencias_nodro
                                                 (fecha, tipo, estatus,publicId)
                                                 VALUES
                                                 ( ? , ? , ? , ? )";
                    $this->db->query($query , array($date,$stype,'Solicitado',$l_Id));
                  }else if($t == 'id_5bcac531d1758'){
                    $query = "INSERT INTO licencias_nodro
                                                 (fecha, tipo, estatus,publicId,publicPart)
                                                 VALUES
                                                 ( ? , ? , ? , ? , ? )";
                    $this->db->query($query , array($date,$stype,'Solicitado',$l_Id,$uid));
                  }elseif ($t == 'id_5bcaa54a0a0cf') {
                    $query = "INSERT INTO licencias_nodro
                                                 (fecha, tipo, estatus,publicId,publicPart)
                                                 VALUES
                                                 ( ? , ? , ? , ? , ? )";
                    $this->db->query($query , array($date,$stype,'Solicitado',$l_Id,$uid));
                  }


                  foreach ($data_0 as $key => $value) {
                    if ($key == 'a' || $key == 'b' || $key == 'c' || $key == 'd' || $key == 'e' || $key == 'f' || $key == 'g' || $key == 'h'
                        || $key == 'i' || $key == 'j' || $key == 'k' || $key == 'l' || $key == 'm' || $key == 'n' || $key == 'o' || $key == 'p'
                        || $key == 'q' || $key == 'r' || $key == 'type' || $key == 'colPred') {
                    }else {
                      $skey = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $key);
                      $svalue = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $value);
                      $queryU = "UPDATE licencias_nodro SET licencias_nodro.$skey  = ? WHERE publicId = ? ";
                      $this->db->query($queryU,array($svalue,$l_Id));
                    }
                  }

                  foreach ($data_1 as $key => $value) {
                    if ($key == 'map' || $key == 'colPred' || $key == 'type') {
                    }else {
                      $skey = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $key);
                      $svalue = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $value);
                      $queryU = "UPDATE licencias_nodro SET licencias_nodro.$skey = ? WHERE publicId = ? ";
                      $this->db->query($queryU,array($svalue,$l_Id));
                    }
                  }



                  foreach ($data_2 as $key => $value) {
                    if ($key == 'scketch' || $key == 'colPred' || $key == 'type') {
                    }else {
                      $skey = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $key);
                      $svalue = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $value);
                      $queryU = "UPDATE licencias_nodro SET licencias_nodro.$skey  = ? WHERE publicId = ? ";
                      $this->db->query($queryU,array($svalue,$l_Id));
                    }
                  }

                  // foreach ($data_3 as $key => $value) {
                  //   if ($key == 'scketch') {
                  //   }else {
                  //     $queryU = $this->db->query("UPDATE licencias_nodro
                  //                                    SET $key = '$value'
                  //                                    WHERE publicId = '$l_Id'");
                  //   }
                  // }
                  ///Factibilidad
                  $a = array(35,36,37,1,2,3,4);
                  //Uso de Suelo
                  $b = array(35,36,37,5,6,7,8,9,10,11,12);
                  //Constancia de verificacion de uso de suelo
                  $c = array(35,36,37,13,14,15);
                  //C.U.S.
                  $d = array(35,36,37,16,17,18,19);
                  //Constancia de Alineamiento y Numero Oficial
                  $e = array(35,36,37,20,21,22,23);
                  //Const. de verificación de Alineamiento y Numero Oficial
                  $f = array(35,36,37,24,25,69);
                  //Permiso de División de Predio
                  $g = array(35,36,37,26,27,28,29,30,31,32,33,34);
                  //Aviso de obra menor 47-49
                  //110-46 generales
                  $h = array(110,38,39,40,41,42,43,44,45,46,47,48,49);
                  //obra menor 47-49
                  $i1 = array(110,38,39,40,41,42,43,44,45,46,50,51,52);
                  //Aviso de Suspención Temporal de Obra
                  $m = array(110,53,54,55,56,57);
                  //Certificación de Terminación de Obra
                  $n = array(110,53,54,55,56,57,58,59);
                  //Anuncios y Toldos /// renovacion ,69,70,71,72,73,74
                  $o = array(110,63,64,65,66,67,68);
                  //Anuncios espectaculares mayor a 4.00 m2
                  $q = array(110,35,36,37,63,64,65,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109);
                  //Anuncios Moviles
                  $r = array(110,63,64,65);
                  //Construcción menor a 40 m2 en Centro Historico
                  $s = array(35,/*36,*/37,38,39,40,41,42,43,44,45,46,75,76,77,78,79,80,81,82,83);
                  //Aviso de obra menor en centro historico
                  $t = array(35,/*36,*/37,38,39,40,41,42,43,44,45,46,75,76,77,78,79,80,81,82,83);



                  if ($type == 'a') {
                    for ($i=0; $i < count($a); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $a[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }
                  if ($type == 'b') {
                    for ($i=0; $i < count($b); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $b[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'c') {
                    for ($i=0; $i < count($c); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $c[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'd') {
                    for ($i=0; $i < count($d); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $d[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'e') {
                    for ($i=0; $i < count($e); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $e[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'f') {
                    for ($i=0; $i < count($f); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $f[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'g') {
                    for ($i=0; $i < count($g); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $g[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'h') {
                    for ($i=0; $i < count($h); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $h[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'i') {
                    for ($i=0; $i < count($i1); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $i1[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'm') {
                    for ($i=0; $i < count($m); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $m[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }
                  if ($type == 'n') {
                    for ($i=0; $i < count($n); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $n[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }
                  if ($type == 'o') {
                    for ($i=0; $i < count($o); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $o[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'q') {
                    for ($i=0; $i < count($q); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $q[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 'r') {
                    for ($i=0; $i < count($r); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $r[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 's') {
                    for ($i=0; $i < count($s); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $s[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                  if ($type == 't') {
                    for ($i=0; $i < count($t); $i++) {
                      $RPlanLId = uniqid('id_',TRUE);
                      $data = $t[$i];
                      $queryRPLanL = "INSERT INTO r_docnodro (idLicencia, idPlano,publicId) VALUES ( ? , ? , ? )";
                      $this->db->query($queryRPLanL,array($l_Id,$data,$RPlanLId));
                    }
                  }

                 $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$l_Id;
                  mkdir($path, 0777, true);

                  session_start();
                  if ($map == true) {
                    $mapa = $_SESSION['mapa_'.$uid];
                    $this->db->query("UPDATE licencias_nodro SET nombreMapa = '$mapa' WHERE publicId = '$l_Id'");
                  }
                    $res = [
                      'res' => 'Registro Realizado',
                      'data' => $l_Id
                    ];
                  }else{
                    $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
                  }
                }else {
                  $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
                }
                $this->response($res);
          }

  public function get_licenses_nodro_get(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $queryU = $this->db->query("SELECT tipoUsuario FROM usuarios WHERE publicId='$uid'");
        $tip = $queryU->result_array();
        foreach ($tip as $tip) {
          $type = $tip['tipoUsuario'];
        }
        if($type == 'id_5bcac531d1758'){
          $query = $this->db->query("SELECT idLicencia,docfinal, fecha, nRegiS, publicId, callePred, zonaPred, propPred, estatus, noOficialPred,
                                            tipo, prorroga, terminacion, orden_pago, sol_cancelacion, fechaCarga
                                     FROM licencias_nodro
                                     WHERE estatus != '0' AND publicPart='$uid' ORDER BY idLicencia DESC");
          $res = $query->result_array();
        }else if($type == 'id_5bcaa4f838f9e'){
          $query = $this->db->query("SELECT licencias_nodro.idLicencia,licencias_nodro.docfinal,licencias_nodro.fecha, licencias_nodro.nRegiS, licencias_nodro.publicId, licencias_nodro.callePred, licencias_nodro.zonaPred, licencias.noOficialPred, licencias_nodro.clavePred, licencias_nodro.propPred, licencias_nodro.estatus, licencias_nodro.clavePred,
                                            licencias_nodro.tipo , licencias_nodro.vigencia, licencias_nodro.prorroga, licencias_nodro.terminacion, licencias_nodro.orden_pago, licencias_nodro.sol_cancelacion, licencias_nodro.tipo, usuarios.nombre, licencias_nodro.fechaCarga, if (usuarios.tipoUsuario='id_5bcac531d1758','PARTICULAR','PERITO') AS tipouser
                                     FROM licencias_nodro
                                     LEFT JOIN usuarios ON licencias_nodro.publicCol = usuarios.publicId
                                     WHERE estatus != '0' ORDER BY licencias_nodro.idLicencia DESC");
          $res = $query->result_array();
        }else{
          $res = 'invalido';
        }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
  $this->response($res);
  }
  /*get planos para licencia*/
  public function get_planLic_nodro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);
        $license_public_id = $request->license_public_id;
        $slicense_public_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $license_public_id);

        $cons = "SELECT r_docnodro.path,r_docnodro.publicId, r_docnodro.comentario, r_docnodro.validado, r_docnodro.nombre, documentos_tramite.doctramite
                                   FROM r_docnodro
                                   INNER JOIN documentos_tramite
                                   ON r_docnodro.idPlano = documentos_tramite.iddoc
                                   WHERE r_docnodro.idLicencia = ? ";
        $query = $this->db->query($cons,array($slicense_public_id));
        $res = $query->result_array();
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
  $this->response($res);
  }

  public function addDocLic_nodro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) && $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);
        $publicId = $request->publicId; #id relacion
        $license_public_id = $request->license_public_id; #id licencia
        $file = $request->file;
        $base64 = $file->base64;
        $file_name = $file->filename;
        $file_size = $file->filesize;
        $file_type = $file->filetype;

        $slicense_public_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $license_public_id);
        $spublicId = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $publicId);


        $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$slicense_public_id;
        $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$slicense_public_id.'/'.$file_name;
        $pathNewImg = $uid.'/licenses/'.$slicense_public_id.'/'.$file_name;
        if(file_exists($path)){}
        else{mkdir($path, 0777, true);}

        if ($file_type == 'application/pdf' || $file_type == 'application/PDF' || $file_type == 'application/dwg' || $file_type == 'application/DWG' || $file_type == '' || $file_type == 'image/JPEG' || $file_type == 'image/JPG' || $file_type == 'image/PNG' || $file_type == 'image/jpg' || $file_type == 'image/jpeg' || $file_type == 'image/png' || $file_type == 'application/x-zip-compressed') {
           if ($file_size < 52428800 ) {
              if(file_put_contents($pathImg, base64_decode($base64))){
                $queryU = "UPDATE r_docnodro SET path = ? , nombre = ? WHERE publicId = ? ";
                $this->db->query($queryU,array($pathNewImg,$file_name,$spublicId));
                $res =  "Archivo cargado y guardado";
              }else {
                $res = "No se pudó guardar la imagen";
              }
            }else {
              $res = "Archivo demasido grande, tamaño requerido '50MB'";
            }
          }else {
            $res =  "Tipo de archivo no permitido, tipo recomendao 'PDF'";
          }
      }else{
        $res = "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;

    }
    $this->response($res);
  }

  //cambiar estado, archivos cargados
  public function statusDocs_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) && $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);
        //public id
        $folio = $request->folio;
        $sfolio = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $folio);
        //tipo de estatus
        $type_e = $request->type_e;
        $stype_e = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $type_e);
        //consulta para ver en que tabla está
        $queryU = "SELECT * FROM licencias WHERE publicId = ? ";
        $query_lic = $this->db->query($queryU,array($sfolio));
        $data = $query_lic->result_array();
        if (isset($data) && !empty($data)) {
          $table = "licencias";
          $consulta = "SELECT usuarios.correo, usuarios.nombre, licencias.idLicencia, licencias.tipo FROM licencias INNER JOIN usuarios ON licencias.idDRO = usuarios.publicId WHERE licencias.publicId = ? ";
          $consulta_datos = $this->db->query($consulta,array($sfolio));
          $result_datos = $consulta_datos->result_array();
          $datosp = $result_datos[0];
          $tipo = $datosp['tipo'];
          $correo = $datosp['correo'];
          $nombre = $datosp['nombre'];
          $f = $datosp['idLicencia'];
        }else{
          $table = "licencias_nodro";
          $consulta = "SELECT usuarios.correo, usuarios.nombre, licencias_nodro.idLicencia, licencias_nodro.tipo FROM licencias_nodro INNER JOIN usuarios ON licencias_nodro.publicPart = usuarios.publicId WHERE licencias_nodro.publicId = ? ";
          $consulta_datos = $this->db->query($consulta,array($sfolio));
          $result_datos = $consulta_datos->result_array();
          $datosp = $result_datos[0];
          $tipo = $datosp['tipo'];
          $correo = $datosp['correo'];
          $nombre = $datosp['nombre'];
          $f = $datosp['idLicencia'];
        }

        if ($tipo == 'a') {
          $tipo_des = 'Factibilidad de Uso de Suelo';
        }
        else if ($tipo == 'b') {
          $tipo_des = 'Permiso de Uso de Suelo';
        }
        else if ($tipo == 'c') {
          $tipo_des = 'Const. de verificación de Uso de Suelo';
        }
        else if ($tipo == 'd') {
          $tipo_des = 'Constancia para  C.U.S(3er, 4to , o mas)';
        }
        else if ($tipo == 'e') {
          $tipo_des = 'Constancia de Alineamiento y Número Oficial';
        }
        else if ($tipo == 'f') {
          $tipo_des = 'Constancia de verificación de Alineamiento';
        }
        else if ($tipo == 'g') {
          $tipo_des = 'Permiso de División de Predio';
        }
        else if ($tipo == 'h') {
          $tipo_des = 'Aviso de obra menor';
        }
        else if ($tipo == 'i') {
          $tipo_des = 'Construcción menor a 40 m2';
        }
        else if ($tipo == 'j') {
          $tipo_des = 'Construcción mayor a 40 m2';
        }
        else if ($tipo == 'k') {
          $tipo_des = 'Construcción especial';
        }
        else if ($tipo == 'l') {
          $tipo_des = 'Construcción para centro Historico';
        }
        else if ($tipo == 'm') {
          $tipo_des = 'Aviso de Suspención Temporal de Obra';
        }
        else if ($tipo == 'n') {
          $tipo_des = 'Certificación de Terminación de Obra';
        }
        else if ($tipo == 'o') {
          $tipo_des = 'Anuncios y Toldos';
        }
        else if ($tipo == 'q') {
          $tipo_des = 'Anuncios espectaculares mayor a 4.00 m2';
        }
        else if ($tipo == 'r') {
          $tipo_des = 'Anuncios Moviles';
        }

        switch ($stype_e) {
          case 0:
              $res = "Haz cargado los documentos para el folio ".$f;
              if($tipo == 'a' || $tipo == 'b' || $tipo == 'c' || $tipo == 'd' || $tipo == 'e' || $tipo == 'f' || $tipo == 'g' ){
                $correoS = "admon.urbana@guanajuatocapital.gob.mx";
              }else{
                $correoS = "imagenurbana@guanajuatocapital.gob.mx";
              }
              $mensaje = "Ha cargado los documentos";
              $this->correousuario($correo,$correoS,$nombre,$tipo_des,$mensaje,$f);
              break;
          case 1:
              $res = "Ha comentado algunas observaciones sobre el folio ".$f;
              if($tipo == 'a' || $tipo == 'b' || $tipo == 'c' || $tipo == 'd' || $tipo == 'e' || $tipo == 'f' || $tipo == 'g' ){
                $correoS = "admon.urbana@guanajuatocapital.gob.mx";
              }else{
                $correoS = "imagenurbana@guanajuatocapital.gob.mx";
              }
              $mensaje = "el servidor público ha encontrado algunos detalles en la carga de documentos";
              $this->correoservidor($correo,$correoS,$nombre,$tipo_des,$mensaje,$f);
              break;
          case 2:
              $res = "Ha solicita una revisión de documentos para el folio ".$f;
              if($tipo == 'a' || $tipo == 'b' || $tipo == 'c' || $tipo == 'd' || $tipo == 'e' || $tipo == 'f' || $tipo == 'g' ){
                $correoS = "admon.urbana@guanajuatocapital.gob.mx";
              }else{
                $correoS = "imagenurbana@guanajuatocapital.gob.mx";
              }
              $mensaje = "Ha solicitado la revisión de los documentos que contaban con observaciones ";
              $this->correousuario($correo,$correoS,$nombre,$tipo_des,$mensaje,$f);
              break;
          case 3:
              $res = "Para Firma";
              break;
          case 4:
              $res = "Haz validado los documentos del folio ".$f;
              if($tipo == 'a' || $tipo == 'b' || $tipo == 'c' || $tipo == 'd' || $tipo == 'e' || $tipo == 'f' || $tipo == 'g' ){
                $correoS = "admon.urbana@guanajuatocapital.gob.mx";
              }else{
                $correoS = "imagenurbana@guanajuatocapital.gob.mx";
              }
              $mensaje = "el servidor público ha VALIDADO LOS DOCUMENTOS puedes continuar con el proceso";
              $this->correoservidor($correo,$correoS,$nombre,$tipo_des,$mensaje,$f);
              break;
        }

        if  ($stype_e == 0 || $stype_e == '0') {
          $estatus = "Doc. Cargados";
        }if ($stype_e == 1 || $stype_e == '1') {
          $estatus = "Con Observaciones";
        }if ($stype_e == 2 || $stype_e == '2') {
          $estatus = "Observaciones Sol.";
        }if ($stype_e == 3 || $stype_e == '3') {
          $estatus = "Para Firmar";
        }if ($stype_e == 4 || $stype_e == '4') {
          $estatus = "Doc. Validados";
        }

        $queryUL = "UPDATE $table SET estatus = ? WHERE publicId = ? ";
        $this->db->query($queryUL,array($estatus,$sfolio));

        $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $mes = $meses[date('n')-1];
        $ano=date('Y');
        $dia=date('d');
        $date = $dia.'/'.$mes.'/'.$ano;

        $queryUL2 = "UPDATE $table SET fechaCarga = ? WHERE publicId = ? ";
        $this->db->query($queryUL2,array($date,$sfolio));

      }else{
        $res = "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;

    }
    $this->response($res);
  }

  public function downPDFnodro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {

        $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);
        session_start();
        $cons = "SELECT idLicencia,fecha, calle, calle_post, clave_ca, cp, entre_calles, fraccionamiento, lote, manzana, numero, superficie,
            coduenos, domicilio_propietario, email_propietario, nombre_propietario, telefono_propietario, ampliacion, cajones_existentes, cajones_propuestos,
            descripcion, domicilio_gestor, email_gestor, nombre_gestor, obra_nueva, preexistencia, remodelacion, telefono_gestor, uso_actual, uso_anterior,
            nombreCroquis, ubicacion_croquis, ubicacion_estado, nombre_estado, tipo, estatus, publicId, publicPart,nombreMapa,uso_propuesto, uso_propuestom, com_propuesto,com_propuestom, folio_uso
          FROM licencias_nodro WHERE publicId = ? ";

          $query = $this->db->query($cons,array($slistaPOST));
          $_SESSION ['licencia'] = $query->result_array();
          $res = 'Data Ready';
        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
  }

  public function cancel_lic_nodro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);
        $id = $request->publicId;
        $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);

        $query = "UPDATE licencias_nodro SET estatus = 0 WHERE publicId = ? ";
        $this->db->query($query,array($sid));
        $res = "Folio cancelado";
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function comment_nodro_post(){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        $preparing = $validation_token['data'];
        $id = $preparing->id;
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
          $listaPOST= file_get_contents("php://input");
          if(isset($listaPOST) && !empty($listaPOST)){
            $request = json_decode($listaPOST);
            $comentario = $request->comentario;
            $public_id = $request->publicId;
            $licence_id = $request->licence_id;

            $scomentario = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $comentario);
            $spublic_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $public_id);
            $slicence_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $licence_id);

            $queryU = "UPDATE r_docnodro SET comentario = ? WHERE publicId = ? ";
            $this->db->query($queryU,array($scomentario,$spublic_id));

            $queryUL = "UPDATE licencias_nodro SET estatus = ? WHERE publicId = ? ";
            $this->db->query($queryUL,array('Comentario',$slicence_id));

            $res ='Comentario registrado';
          }else{
            $res =  "Empty Json".' '.REST_Controller::HTTP_BAD_REQUEST;
          }
        }else{
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
      $this->response($res);
    }

  public function get_dat_lic_nodro_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      //$preparing = $validation_token['data'];
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);

          $cons = "SELECT idLicencia,fecha, calle, calle_post, clave_ca, cp, entre_calles, fraccionamiento, lote, manzana, numero, superficie,
              coduenos, domicilio_propietario, email_propietario, nombre_propietario, telefono_propietario, ampliacion, cajones_existentes, cajones_propuestos,
              descripcion, domicilio_gestor, email_gestor, nombre_gestor, obra_nueva, preexistencia, remodelacion, telefono_gestor, uso_actual, uso_anterior,
              nombreCroquis, ubicacion_croquis, ubicacion_estado, nombre_estado, tipo, estatus, publicId, publicPart,nombreMapa,uso_propuesto, uso_propuestom, com_propuesto,com_propuestom, folio_uso
            FROM licencias_nodro WHERE publicId = ? ";
          $query = $this->db->query($cons,array($slistaPOST));

          $cons2 = "SELECT nombreMapa FROM licencias WHERE publicId = ? ";
          $query_map = $this->db->query($cons2,array($slistaPOST));

            $res = [
              'res' => $query->result_array(),
              'data' => $query_map->result_array()
            ];
          }else{
            $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
    }

  public function get_map_nodro_get(){
            $this->load->library('Authorization_Token');
            $validation_token = $this->authorization_token->validateToken();
            $preparing = $validation_token['data'];
            $uid = $preparing->id;
            if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
              $listaPOST= file_get_contents("php://input");
                session_start();
                 $res = $_SESSION ['mapa_'.$uid];
              }else {
                $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
              }
              $this->response($res);
      }

  public function save_lic_nodro_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);

        $id = $request->publicId;
        $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);

        foreach ($request as $key => $value) {
          $skey = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $key);
          $svalue = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $value);

          $cons2 = "UPDATE licencias_nodro SET licencias_nodro.$skey  = ? WHERE publicId = ? ";
          $this->db->query($cons2,array($svalue,$sid));
        }
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($id);
  }

  public function add_pago_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $id = $request->license_public_id;
          $base64 = $request->base64;
          $file_name = $request->filename;
          $file_size = $request->filesize;
          $file_type = $request->filetype;

          $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);
          $sfile_size = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $file_size);

          $meses = array("ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SEP","OCT","NOV","DIC");
          $mes = $meses[date('n')-1];
          $ano=date('y');
          $dia=date('d');
          $fecha = $dia.'/'.$mes.'/'.$ano;
          $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$id.'/'.$file_name;
          $pathNewImg = $uid.'/licenses/'.$id.'/'.$file_name;
          $cons = "SELECT * FROM pagos WHERE id_licencia= ? ";
          $query1 = $this->db->query($cons,array($sid));
          $result = $query1->result_array();
            if(!empty($result)){
              if ($sfile_size < 3030477 ) {
                if(file_put_contents($pathImg, base64_decode($base64))){
                  $cons2 = "UPDATE pagos SET nombre = ? , ubicacion = ? , fecha = ? WHERE id_licencia = ? ";
                  $this->db->query($cons2,array($file_name,$pathNewImg,$fecha,$sid));

                  $res =  "Archivo Actualizado";

                  $query_user = $this->db->query("SELECT tipoUsuario FROM usuarios WHERE publicId = '$uid'");

                  $result_id = $query_user->result_array();
                  $pre_id = $result_id[0];
                  foreach ($pre_id as $nombre => $valor) {
                  //idDep Admin
                    $type_id = $valor;
                  }
                  //DRO
                  if ($type_id == 'id_5bcaa54a0a0cf') {
                    $table = 'licencias';
                  }
                  //Particular
                  if ($type_id == 'id_5bcac531d1758') {
                    $table = 'licencias_nodro';
                  }

                  $cons3 = "UPDATE $table SET estatus = ? WHERE publicId = ? ";
                  $this->db->query($cons3,array('Pago Cargado',$sid));

                }else {
                  $res = "No se pudó guardar la imagen";
                }
              }else {
                $res = "Archivo demasido grande, tamaño requerido '2MB'";
              }
            }else{
              if ($sfile_size < 3030477 ) {
                if(file_put_contents($pathImg, base64_decode($base64))){
                  $nid = uniqid('id_',TRUE);
                  $cons2 = "INSERT INTO pagos (nombre,ubicacion,fecha,id_licencia,public_id) VALUES ( ? , ? , ? , ? , ? )";
                  $this->db->query($cons2,array($file_name,$pathNewImg,$fecha,$sid,$nid));
                  $res =  "Archivo cargado y guardado";

                  $query_user = $this->db->query("SELECT tipoUsuario FROM usuarios WHERE publicId = '$uid'");

                  $result_id = $query_user->result_array();
                  $pre_id = $result_id[0];
                  foreach ($pre_id as $nombre => $valor) {
                  //idDep Admin
                    $type_id = $valor;
                  }
                  //DRO
                  if ($type_id == 'id_5bcaa54a0a0cf') {
                    $table = 'licencias';
                  }
                  //Particular
                  if ($type_id == 'id_5bcac531d1758') {
                    $table = 'licencias_nodro';
                  }

                  $cons3 = "UPDATE $table SET estatus = ? WHERE publicId = ? ";
                  $this->db->query($cons3,array('Pago Cargado',$sid));

                }else {
                  $res = "No se pudó guardar la imagen";
                }
              }else {
                $res = "Archivo demasido grande, tamaño requerido '2MB'";
              }
            }

        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function add_pagocol_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $id = $request->license_public_id;
          $base64 = $request->base64;
          $file_name = $request->filename;
          $file_size = $request->filesize;
          $file_type = $request->filetype;

          $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);
          $sfile_size = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $file_size);

          $meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
          $mes = $meses[date('n')-1];
          $ano=date('y');
          $dia=date('d');
          $fecha = $dia.'/'.$mes.'/'.$ano;
          $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$uid.'/licenses/'.$id.'/'.$file_name;
          $pathNewImg = $uid.'/licenses/'.$id.'/'.$file_name;
          $query1 = $this->db->query("SELECT * FROM pagos_col WHERE id_licencia='$id'");
          $result = $query1->result_array();
            if(!empty($result)){
              if ($file_size < 3030477 ) {
                if(file_put_contents($pathImg, base64_decode($base64))){
                  $cons2 = "UPDATE pagos_col SET nombre = ? , ubicacion = ? , fecha = ? WHERE id_licencia = ? ";
                  $this->db->query($cons2,array($file_name,$pathNewImg,$fecha,$sid));

                  $res =  "Archivo Actualizado";
                }else {
                  $res = "No se pudó guardar la imagen";
                }
              }else {
                $res = "Archivo demasido grande, tamaño requerido '2MB'";
              }
            }else{
              if ($file_size < 3030477 ) {
                if(file_put_contents($pathImg, base64_decode($base64))){
                  $nid = uniqid('id_',TRUE);
                  $cons2 = "INSERT INTO pagos_col (nombre,ubicacion,fecha,id_licencia,public_id) VALUES ( ? , ? , ? , ? , ? )";
                  $this->db->query($cons2,array($file_name,$pathNewImg,$fecha,$sid,$nid));

                  $res =  "Archivo cargado y guardado";
                }else {
                  $res = "No se pudó guardar la imagen";
                }
              }else {
                $res = "Archivo demasido grande, tamaño requerido '2MB'";
              }
            }
        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function get_pagosc_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $license_public_id = $request->license_public_id;
          $query = $this->db->query("SELECT nombre, ubicacion, fecha, validado, public_id
                                        FROM pagos_col
                                        WHERE id_licencia = '$license_public_id'");
          $res = $query->result_array();
        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function orden_c_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);
        $id = $request->publicId;
        $orden_pago = $request->orden_pago;

        $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);
        $sorden_pago = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $orden_pago);

        $query = "UPDATE licencias SET orden_pago = ? WHERE publicId = ? ";
        $this->db->query($query,array($orden_pago,$sid));
        $res = "Dato Guardado";
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function get_license_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $slistaPOST = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $listaPOST);
          //$license_public_id = $request->license_public_id;
          $cons = "SELECT * FROM licencias WHERE publicId = ? ";
          $query_lic = $this->db->query($cons,array($slistaPOST));

          $data = $query_lic->result_array();
          if (isset($data) && !empty($data)) {
            $res = $query_lic->result_array();
          }else {
            $cons2 = "SELECT * FROM licencias_nodro WHERE publicId = ? ";
            $query_licno = $this->db->query($cons2,array($slistaPOST));
            $res = $query_licno->result_array();
          }
        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function add_croquis_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);

          $file = $request->file;
          $id = $request->public_id;
          $idDRO = $request->idDRO;
          $base64 = $file->base64;
          $file_name = $file->filename;
          $file_size = $file->filesize;
          $file_type = $file->filetype;

          $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);
          $sidDRO = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $idDRO);
          $sfile_size = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $file_size);

          $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$idDRO.'/licenses/'.$id;
          $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$idDRO.'/licenses/'.$id.'/'.$file_name;
          $pathNewImg = $idDRO.'/licenses/'.$id.'/'.$file_name;
          if(file_exists($path)){}else{
            mkdir($path, 0777, true);
          }
          if ($sfile_size < 3030477 ) {
            if(file_put_contents($pathImg, base64_decode($base64))){
              $cons = "UPDATE licencias SET nombreCroq = ? , pathCroquis = ?  WHERE publicId = ? ";
              $this->db->query($cons,array($file_name,$pathNewImg,$sid));
              $res =  "Archivo Actualizado";
            }else {
              $res = "No se pudó guardar la imagen";
            }
          }else {
            $res = "Archivo demasido grande, tamaño requerido '2MB'";
          }

        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function add_plan_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);

          $file = $request->file;
          $id = $request->public_id;
          $idDRO = $request->publicPart;
          $base64 = $file->base64;
          $file_name = $file->filename;
          $file_size = $file->filesize;
          $file_type = $file->filetype;

          $sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);
          $sidDRO = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $idDRO);
          $sfile_size = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $file_size);

          $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$idDRO.'/licenses/'.$id;
          $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$idDRO.'/licenses/'.$id.'/'.$file_name;
          $pathNewImg = $idDRO.'/licenses/'.$id.'/'.$file_name;
          if(file_exists($path)){}else{
            mkdir($path, 0777, true);
          }
          if ($sfile_size < 3030477 ) {
            if(file_put_contents($pathImg, base64_decode($base64))){
              $cons = "UPDATE licencias_nodro SET nombreCroq = ? , pathCroquis = ?  WHERE publicId = ? ";
              $this->db->query($cons,array($file_name,$pathNewImg,$sid));
              $res =  "Archivo Actualizado";
            }else {
              $res = "No se pudó guardar la imagen";
            }
          }else {
            $res = "Archivo demasido grande, tamaño requerido '2MB'";
          }

        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function add_finals_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);

          $file = $request->file;
          $base64 = $file->base64;
          $file_name = $file->filename;
          $file_size = $file->filesize;
          $file_type = $file->filetype;
          $id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $request->public_id);
          $campo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $request->campo);
          $cons = "SELECT idDRO FROM licencias WHERE publicId = ? ";
          $result = $this->db->query($cons,array($id));
          $data = $result->result_array();
          if (empty($data)) {
            $cons = "SELECT publicPart FROM licencias_nodro WHERE publicId = ? ";
            $result = $this->db->query($cons,array($id));
            $data = $result->result_array();
            $data0 = $data[0];
            $sujeto = $data0["publicPart"];
            $table = 'licencias_nodro';
            $res = $data0["publicPart"]."particular".$campo;
          }else {
            $data0 = $data[0];
            $sujeto = $data0["idDRO"];
            $table = 'licencias';
            $res = $data0["idDRO"]."dro".$campo;
          }

          $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$sujeto.'/licenses/'.$id;
          $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$sujeto.'/licenses/'.$id.'/'.$file_name;
          $pathNewImg = $sujeto.'/licenses/'.$id.'/'.$file_name;
          if(file_exists($path)){}else{
            mkdir($path, 0777, true);
          }

          if ($file_size < 3030477 ) {
            if(file_put_contents($pathImg, base64_decode($base64))){
              $cons = "UPDATE $table SET  docfinal = ?, estatus = ? WHERE publicId = ? ";
              $this->db->query($cons,array($pathNewImg,$campo,$id));
              $res =  "Archivo Actualizado";
            }else {
              $res = "No se pudó guardar la imagen";
            }
          }else {
            $res = "Archivo demasido grande, tamaño requerido '2MB'";
          }

          /*$sid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $id);
          $sidDRO = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $idDRO);
          $sfile_size = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,*-_/\s+/u])', '', $file_size);

          $path = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$idDRO.'/licenses/'.$id;
          $pathImg = '/home3/sanluis/ventanillavirtualguanajuato.net/public_files/'.$idDRO.'/licenses/'.$id.'/'.$file_name;
          $pathNewImg = $idDRO.'/licenses/'.$id.'/'.$file_name;
          if(file_exists($path)){}else{
            mkdir($path, 0777, true);
          }
          if ($sfile_size < 3030477 ) {
            if(file_put_contents($pathImg, base64_decode($base64))){
              $cons = "UPDATE licencias SET nombreCroq = ? , pathCroquis = ?  WHERE publicId = ? ";
              $this->db->query($cons,array($file_name,$pathNewImg,$sid));
              $res =  "Archivo Actualizado";
            }else {
              $res = "No se pudó guardar la imagen";
            }
          }else {
            $res = "Archivo demasido grande, tamaño requerido '2MB'";
          }*/

        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function gtmts_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);
          $public_id = $request->publicId;

            $cons2 = "SELECT supTotalPre, totalMetCons,metrosCons FROM licencias_nodro WHERE publicId = ? ";
            $query_licno = $this->db->query($cons2,array($public_id));
            $res = $query_licno->result_array();
            if (isset($res) && !empty($res)) {
              $res = $query_licno->result_array();
            }else {
              $query = "SELECT supTotalPre, totalMetCons,metrosCons FROM licencias WHERE publicId = ? ";
              $query = $this->db->query($query,array($public_id));
              $res = $query->result_array();

            }

        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

}
