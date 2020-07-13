<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Derechos_old extends REST_Controller {

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

  public function get_derechos_get(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $query = $this->db->query("SELECT idderecho,clave,clave2,concepto,unidad,costo FROM derechos");
        $res = $query->result_array();
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
    $this->response($res);
  }

  public function addder_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  $preparing = $validation_token['data'];
  $uid = $preparing->id;
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $listaPOST= file_get_contents("php://input");
    if (isset($listaPOST) && !empty($listaPOST)) {
        $request = json_decode($listaPOST);

        $concep = $request->concepto;
        $cla = $request->clave;
        $unid = $request->unidad;
        $cos = $request->costo;

        $concepto = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $concep);
        $clave = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $cla);
        $unidad = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $unid);
        $costo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $cos);

        $query = "INSERT INTO derechos (clave,concepto,unidad,costo) VALUES ( ? , ? , ? , ? )";
        $query = $this->db->query($query,array($clave,$concepto,$unidad,$costo));

        $res = true;
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function editder_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
          $request = json_decode($listaPOST);



          $concepto = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $request->concepto);
          $clave = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $request->clave);
          $unidad = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $request->unidad);
          $costo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $request->costo);
          $idderec = $request->idderecho;


          $query = "UPDATE derechos SET clave = ? ,  unidad = ? , costo = ? , concepto = ? WHERE idderecho = ? ";
          $this->db->query($query,array($clave,$unidad,$costo,$concepto,$idderec));

          $res= true;
        }else{
          $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
        }
      }else {
        $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
      }
      $this->response($res);
    }

  public function delder_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {
            $request = json_decode($listaPOST);

            $idderec = $request->idderecho;


            $query = "DELETE FROM derechos WHERE idderecho = ? ";
            $this->db->query($query,array($idderec));

            $res= true;
          }else{
            $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
  }

  public function save_derechos_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {

          $request = json_decode($listaPOST);
          $lenght = count($request);
          $lic_data = $request[$lenght-2];
          $license_i = $lic_data->publicId;

          $license_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $license_i);

          if (isset($lic_data->idLicencia)) {
            $idLicenci = $lic_data->idLicencia;
            $idLicencia = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $idLicenci);
          }else {
            $idLicenci = $lic_data->folio;
            $idLicencia = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $idLicenci);
          }

          if($uid == 'id_5d28e8960d8949.05929619'){
            $area = "Dirección de Imagen Urbana y Gestión del Centro Histórico";
            $avb = "DIUGCH";
          }else{
            $area = "Dirección de Administración Urbana";
            $avb = "DAU";
          }


          //$total_gral = $request->total_gral;
          $total_gral = $request{$lenght-1};
          array_splice($request, $lenght-2, 1);
          $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
          $mes = $meses[date('n')-1];
          $ano=date('Y');
          $dia=date('d');
          $date = $dia.'/'.$mes.'/'.$ano;
          $derecho = json_encode($request, JSON_UNESCAPED_UNICODE);
          $gral_Id = uniqid('id_',TRUE);

          $c = "INSERT INTO derecho_licencia
                  (derecho, fecha, id_servidor, id_licencia, public_id ,area)
                VALUES
                  (? , ? , ? , ? , ? , ? )";
          $query_gral = $this->db->query($c,array($derecho, $date,$uid,$license_id,$gral_Id, $area));

          $ultimoId = $this->db->insert_id();
          $folio = 'OC-'.$avb.'-'.$ultimoId;

          $cons = "UPDATE derecho_licencia SET folio = ? WHERE public_id = ? ";
          $this->db->query($cons,array($folio,$gral_Id));


          $queryU = "SELECT * FROM licencias WHERE publicId = ? ";
          $query_lic = $this->db->query($queryU,array($license_id));

          $d = $query_lic->result_array();
          if (isset($d) && !empty($d)) {
            $table = "licencias";
            $estatus ="Orden Generada";
          }else {
            $table = "licencias_nodro";
            $estatus ="Orden Generada";
          }

          $queryUL = "UPDATE $table SET estatus = ?, orden_pago = ?  WHERE publicId = ? ";
          $this->db->query($queryUL,array($estatus,1,$license_id));

          $query_init_ad= $this->db->query("SELECT publicId FROM licencias WHERE publicId = '$license_id'");
          $res_init_ad =  $query_init_ad->result_array();

          if (isset($res_init_ad) && !empty($res_init_ad)) {
            $table = "licencias";
            $soli = "idDRO";
          }else{
            $table = "licencias_nodro";
            $soli = "publicPart";
          }
          $query = "SELECT fecha,tipo,apMatS,apPatS,callePred,clavePred,correoS,cuentaPred,
                                          domicilioS,drenajePred,energElecPred,lotePred,mznPred,nRegiS,noRegPer,
                                          noOficialPred,nombresPM,nombresS,propPred,rfc,serBasPred,telefonoS,zonaPred,
                                          cp,supTotalPre,supTotalCon,calleS,coloniaS,ciudadS,estadoS,noextS,nointS,cpS,
                                          callePredio,capInv,capacidad,denomCom,descPred,enFunc,estaCap,metros,nivSolc,
                                          noEmpleos,puntosRef,supOcu,usoSol,apMatPer,apMatVen,apPatPer,apPatVen,cantAnun,
                                          colcAnun,conAccDivPred,construido,correoPer,domicPer,donacion,metrosAnun,medAnun,
                                          metCLCons,metrosCons,metrosDivPred,noFctOpus,nombres,nombresPer,otherNiv,pa,pb,
                                          superDivPred,telefonoPer,tipoAnun,totalMetCons,usoProp,venta,nombreMapa,nombreCroquis,
                                          ubicacion_croquis,nombre_estado,ubicacion_estado,arancel,vigencia,folio, publicId, $soli
                                          FROM $table WHERE publicId = ?";
          $query = $this->db->query($query,array($license_id));

          $result_id = $query->result_array();
          $result = $result_id[0];

          foreach ($result as $key => $value) {
            if ($key == 'tipo') {
              $type = $value;
            }
          }
          $query_sus = $this->db->query("SELECT * FROM sust_legal WHERE tipo = '$type' ");
          $query_g = $this->db->query("SELECT derecho_licencia.derecho,derecho_licencia.fecha,derecho_licencia.folio,derecho_licencia.area,usuarios.nombre
                                       FROM derecho_licencia
                                       INNER JOIN usuarios ON derecho_licencia.id_servidor = usuarios.publicId
                                       WHERE id_licencia = '$license_id'");

          session_start();
            #$croquis = $_SESSION['croquis_'.$uid];
            /*$pre_data = $query_gral->result_array();
            $post_data = $pre_data[0];
            $after_data = $post_data['derecho'];
            $data = json_decode($after_data);
            $_SESSION ['derechos'] = $data;
            */
            $_SESSION ['licencia'] = $query->result_array();
            $_SESSION ['derechos'] = $query_g->result_array();
            $_SESSION ['sustento'] = $query_sus->result_array();

            if ($l == 'A') {
              $query_gral = $this->db->query("UPDATE $table_prorroga
                                             SET orden_pago = 1
                                             WHERE publicId = '$license_id'");
              $res = [
                  "status" => TRUE,
                  "path" => '#!prorrogas'
                ];
            }
            if ($l == 'T') {
              $query_gral = $this->db->query("UPDATE $table_terminacion
                                             SET orden_pago = 1
                                             WHERE publicId = '$license_id'");
              $res = [
                  "status" => TRUE,
                  "path" => '#!terminaciones'
                ];
            }
            if ($l != 'T' && $l != 'A') {
              $query_gral = $this->db->query("UPDATE $table
                                             SET orden_pago = 1
                                             WHERE publicId = '$license_id'");
              $res = [
                  "status" => TRUE,
                  "path" => '#!wellcome'
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

  public function deleteorden_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    $preparing = $validation_token['data'];
    $uid = $preparing->id;
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $listaPOST= file_get_contents("php://input");
      if (isset($listaPOST) && !empty($listaPOST)) {
        $cons = "SELECT * FROM licencias WHERE publicId = ? ";
        $result = $this->db->query($cons,array($listaPOST));
        $data = $result->result_array();
        if (empty($data)) {
          $table = 'licencias_nodro';
          $this->db->query("UPDATE $table SET orden_pago = 0 WHERE publicId = '$listaPOST'");
          $this->db->query("DELETE FROM derecho_licencia WHERE id_licencia = '$listaPOST' ");
          $res = "eliminado con exito";
        }else{
          $table = 'licencias';
          $this->db->query("UPDATE $table SET orden_pago = 0 WHERE publicId = '$listaPOST'");
          $this->db->query("DELETE FROM derecho_licencia WHERE id_licencia = '$listaPOST' ");
          $res = "eliminado con exito";
        }
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  public function get_pdf_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");
        if (isset($listaPOST) && !empty($listaPOST)) {

          $request = json_decode($listaPOST);
          $license_id = $request->publicId;

          $query_gral = $this->db->query("SELECT derecho_licencia.derecho,derecho_licencia.fecha,derecho_licencia.folio,derecho_licencia.area,usuarios.nombre
                                       FROM derecho_licencia
                                       INNER JOIN usuarios ON derecho_licencia.id_servidor = usuarios.publicId
                                       WHERE id_licencia = '$license_id'");

          $query_init_ad= $this->db->query("SELECT publicId FROM licencias WHERE publicId = '$license_id'");
          $res_init_ad =  $query_init_ad->result_array();

          if (isset($res_init_ad) && !empty($res_init_ad)) {
            $table = "licencias";
            $soli = "idDRO";
          }else{
            $table = "licencias_nodro";
            $soli = "publicPart";
          }
          $query = "SELECT fecha,tipo,apMatS,apPatS,callePred,clavePred,correoS,cuentaPred,
                                          domicilioS,drenajePred,energElecPred,lotePred,mznPred,nRegiS,noRegPer,
                                          noOficialPred,nombresPM,nombresS,propPred,rfc,serBasPred,telefonoS,zonaPred,
                                          cp,supTotalPre,supTotalCon,calleS,coloniaS,ciudadS,estadoS,noextS,nointS,cpS,
                                          callePredio,capInv,capacidad,denomCom,descPred,enFunc,estaCap,metros,nivSolc,
                                          noEmpleos,puntosRef,supOcu,usoSol,apMatPer,apMatVen,apPatPer,apPatVen,cantAnun,
                                          colcAnun,conAccDivPred,construido,correoPer,domicPer,donacion,metrosAnun,medAnun,
                                          metCLCons,metrosCons,metrosDivPred,noFctOpus,nombres,nombresPer,otherNiv,pa,pb,
                                          superDivPred,telefonoPer,tipoAnun,totalMetCons,usoProp,venta,nombreMapa,nombreCroquis,
                                          ubicacion_croquis,nombre_estado,ubicacion_estado,arancel,vigencia,folio, publicId, $soli
                                          FROM $table WHERE publicId = ?";
          $query = $this->db->query($query,array($license_id));

          $result_id = $query->result_array();
          $result = $result_id[0];

          foreach ($result as $key => $value) {
            if ($key == 'tipo') {
              $type = $value;
            }
          }
          $query_sus = $this->db->query("SELECT * FROM sust_legal WHERE tipo = '$type' ");

          session_start();
            #$croquis = $_SESSION['croquis_'.$uid];
            /*$pre_data = $query_gral->result_array();
            $post_data = $pre_data[0];
            $after_data = $post_data['derecho'];
            $data = json_decode($after_data);
            $_SESSION ['derechos'] = $data;
            */
            $_SESSION ['licencia'] = $query->result_array();
            $_SESSION ['derechos'] = $query_gral->result_array();
            $_SESSION ['sustento'] = $query_sus->result_array();
            $res =  TRUE;
          }else{
            $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
          }
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
  }

  public function get_fundamets_post(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");

          $query_sus = $this->db->query("SELECT * FROM sust_legal");

          $res = $query_sus->result_array();

        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
  }

  public function get_alloc_get(){
      $this->load->library('Authorization_Token');
      $validation_token = $this->authorization_token->validateToken();
      $preparing = $validation_token['data'];
      $uid = $preparing->id;
      if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $listaPOST= file_get_contents("php://input");

          $query_sus = $this->db->query("SELECT derecho_licencia.folio, derecho_licencia.fecha, derecho_licencia.area, derecho_licencia.derecho,
                                          licencias.idLicencia AS 'idLicencia', licencias.tipo AS 'tipo',
                                          licencias.publicId AS 'publicId', licencias_nodro.idLicencia AS 'idLicencia',
                                          licencias_nodro.tipo AS 'tipo', licencias_nodro.publicId AS 'publicId',
                                          usuarios.nombre
                                          FROM derecho_licencia
                                          LEFT JOIN licencias ON derecho_licencia.id_licencia = licencias.publicId
                                          LEFT JOIN licencias_nodro ON derecho_licencia.id_licencia = licencias_nodro.publicId
                                          INNER JOIN usuarios ON derecho_licencia.id_servidor = usuarios.publicId");

          $res = $query_sus->result_array();

        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
  }

}
