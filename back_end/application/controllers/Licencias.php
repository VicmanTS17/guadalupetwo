<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Licencias extends REST_Controller {
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

  /*get  license solicitante*/
  public function get_licencias_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;
      
      $query = "SELECT licencias.id_licencia, licencias.folio, licencias.id_tramite, licencias.is_der, licencias.is_ara, licencias.is_per,
        licencias.ultima_act, licencias.estatus, licencias.estatus_col, licencias.id_usuario, 
        licencias.total_arancel AS 'total_ara', licencias.arancel_col,
        licencias.path_lic_firma, licencias.public_id, lista_tramites.tramite AS 'tramite',
        lic_datos_predio.calle,  lic_datos_predio.colonia,
        lic_licencia_para.obra_nueva, lic_licencia_para.ampliacion, lic_licencia_para.modificacion, 
        lic_licencia_para.reparacion, lic_licencia_para.regularizacion,
        lic_licencia_para.obra_minima, lic_licencia_para.demolicion,
        lic_licencia_para.renovacion, lic_licencia_para.bardeo,
        r_licencias_derechos.total_gral AS 'total_der'
        FROM licencias
        INNER JOIN lista_tramites       ON licencias.id_tramite = lista_tramites.public_id
        LEFT JOIN lic_datos_predio      ON licencias.public_id = lic_datos_predio.id_licencia
        LEFT JOIN r_licencias_derechos  ON licencias.public_id = r_licencias_derechos.id_licencia
        LEFT JOIN lic_licencia_para     ON licencias.public_id = lic_licencia_para.id_licencia
        WHERE licencias.estatus != ? AND licencias.id_usuario = ?
        GROUP BY licencias.id_licencia ORDER BY licencias.id_licencia DESC";
      $query = $this->db->query($query,array('0', $uid_token));
      $res   = ["data" => $query->result_array(), "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  /*get tramites solicitante*/
  public function get_tramites_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $query = "SELECT tramite, tipo_usuario, arancel, public_id FROM lista_tramites WHERE estatus = ?";
      if ($utype_token == 'id_5ebe11ce542b89.25367119') {//particular
        $query .= "AND arancel = ?";
        $query = $this->db->query($query,array('1','0'));
      }else{//resto
        $query = $this->db->query($query,array('1'));
      }
     
      $res   = ["data" => $query->result_array(), "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  // gettramites master
  public function get_tramites_mas_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $query = "SELECT tramite, public_id FROM lista_tramites";
      $query = $this->db->query($query,array('1'));
      $res   = ["data" => $query->result_array(), "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  // gettramites SERVIDOR
  public function get_tramites_ser_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $query = "SELECT id_tramite FROM r_tramite_usuarios WHERE id_usuario = ? AND estatus != ?";
      $query = $this->db->query($query,array($uid_token, '0'));
      $lic_result   = $query->result_array();
      //lics result array
      $result_query_lic  = [];
      foreach ($lic_result as $key => $value) {
        $id_tramite = $value;
        $query      = "SELECT tramite, public_id FROM lista_tramites WHERE public_id = ?";
        $query      = $this->db->query($query,array($id_tramite));

        $result = $query->result_array();

        $result_query_lic = array_merge($result_query_lic,$query->result_array());
      }

      $res   = ["data" => $result_query_lic, "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }




  //grupos especificos para tramites
  public function get_tramite_group_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){

        $data_request = json_decode($data_post);
        $public_id    = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->public_id);

        $query = "SELECT grupos_licencias.nombre,  grupos_licencias.public_id, r_tramite_grupo.flag_view, r_tramite_grupo.public_id AS 'plid_rtg' FROM grupos_licencias
          LEFT JOIN r_tramite_grupo 
          ON grupos_licencias.public_id = r_tramite_grupo.id_grupo
          WHERE r_tramite_grupo.id_tramite = ? AND r_tramite_grupo.estatus = ? ORDER BY grupos_licencias.id_grupo DESC";
        $query  = $this->db->query($query,array($public_id, 1));
        $result = $result = $query->result_array();
        if (isset($result) && !empty($result)) {
          $res = [ "status" => TRUE, "data" => $result ];
        }else {
          $res = [ "status" => FALSE, "data" => $result ];
        }
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //campos especificos para grupos
public function get_form_group_lic_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);
      $plid_rtg = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->public_id);

      $query = "SELECT campos.*,
                radiovalues.radiovalue1, radiovalues.radiovalue2, radiovalues.radiovalue3,
                r_grupo_campo.estatus, r_grupo_campo.public_id AS 'plid_rgf', r_grupo_campo.id_grupo AS 'id_grupo'
                FROM campos
                LEFT JOIN radiovalues
                ON campos.public_id = radiovalues.public_campo
                LEFT JOIN r_grupo_campo
                ON campos.public_id = r_grupo_campo.id_campo
                WHERE r_grupo_campo.id_grupo = ? AND r_grupo_campo.estatus = ? ORDER BY campos.id ASC";
      $query  = $this->db->query($query,array($plid_rtg,1));
      $result = $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = [ "status" => TRUE, "data" => $result ];
      }else {
        $res = [ "status" => FALSE, "data" => $result ];
      }
    }else{
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

//guardar licencia
public function save_lic_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);
      $id_lic_para = '';

      $traid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->traid);
      // lic_id
      $public_id = uniqid('id_',TRUE);
      $data      = $data_request->data;
      $location  = $data_request->location;
      $lat       = $location->lat;
      $lng       = $location->lng;
      $base64    = $data_request->base64;
      $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
      $mes = $meses[date('n')-1];
      $ano=date('Y');
      $dia=date('d');
      $fecha = $dia.'/'.$mes.'/'.$ano;
      //define si es perito o particular
      if ($utype_token === 'id_5ebe10d0b4db29.48120814') { $is_per = 1; }
      if ($utype_token === 'id_5ebe11ce542b89.25367119') { $is_per = 0; }
      $estatus = 'Solicitado'; $estatus_col = 'En proceso';
      # P R I M E R   I  N C E R S I O N   D E   L A   L  I C E N C  I A
      $query = "INSERT INTO licencias (fecha_solicitud,is_per,estatus,estatus_col,id_tramite,lat,lng,id_usuario,public_id) VALUES (?,?,?,?,?,?,?,?,?)";
      $query = $this->db->query($query, array($fecha,$is_per,$estatus,$estatus_col,$traid,$lat,$lng,$uid_token,$public_id));

      $path_lic = '/home1/francisco/guadalupe.inmueblesenmexico.net/public_files/user_assets/'.$uid_token.'/licencias/'.$public_id;
      mkdir($path_lic, 0777, true);
      ///home1/francisco/guadalupe.inmueblesenmexico.net/back_end/application/
      //id licencia
      if(file_put_contents($path_lic.'/mapa.png', base64_decode($base64))){
        $path = $uid_token.'/licencias/'.$public_id.'/mapa.png';
        $query = "UPDATE licencias SET path_mapa = ? WHERE public_id = ? ";
        $this->db->query($query,array($path, $public_id));

        $res = [ "status" => TRUE, "data" => 0 ];
      }else { $res = [ "status" => TRUE, "data" => 100 ]; }
      
      $lenght_data = count($data);
      for ($i=0; $i < $lenght_data; $i++) {
        $pre_data     = $data[$i];
        $lenght_data_ = count($pre_data);
        $campo_db     = ""; $signos = ''; $array_datos = [];
        for ($j=0; $j < $lenght_data_-1; $j++) {

          $id_grupo = $pre_data[$j]->id_grupo; #definicion del grupo
          if($id_grupo === 'id_5ea3457c2e1668.57636641')       { $tabla_query = 'lic_datos_propietario'; }
          else if($id_grupo === 'id_5ea3457c2e33e3.36891982')  { $tabla_query = 'lic_datos_predio'; }
          else if($id_grupo === 'id_5ea352cc935558.63022662')  { $tabla_query = 'lic_uso_de_suelo'; }
          else if($id_grupo === 'id_5ea352cc935558.63022447')  { $tabla_query = 'lic_constancia_servicios'; }
          else if($id_grupo === 'id_5eb6cd94031ea2.25736133')  { $tabla_query = 'lic_const_estru'; }
          else if($id_grupo === 'id_5eb6cd94031ea2.257447514') { $tabla_query = 'lic_constancia_construccion'; }
          else if($id_grupo === 'id_5ee7d14e6e4a79.0155874572'){ $tabla_query = 'lic_autorizacion_ocupacion'; }
          else if($id_grupo === 'id_5ea3457c2e3ba5.81867615')  { $tabla_query = 'lic_descripcion_obra'; }
          else if($id_grupo === 'id_5ee7d14e6e4a79.01524533')  { $tabla_query = 'lic_licencia_para'; }
          else if($id_grupo === 'id_5ee7d14e6e4a79.01524005')  { $tabla_query = 'lic_permiso_para'; }
          else if($id_grupo === 'id_5ea3457c2e3ba5.818335578')  { $tabla_query = 'lic_antecedentes'; }
          else if($id_grupo === 'id_5ea3457c2e3ba5.818335475')  { $tabla_query = 'lic_vigencia'; }
          else{ /*grupo no  compatible*/}
          
          
          if($j < $lenght_data_-1){
            //id de grupo para requisitos
            if($id_grupo === 'id_5ee7d14e6e4a79.01524533' && $pre_data[$j]->data === "true")  {  $id_lic_para = $pre_data[$j]->public_id; }
            //eistencia del dato
            if (isset($pre_data[$j]->data) && !empty($pre_data[$j]->data)){ $array_datos[$j] = $pre_data[$j]->data; }
            else{ $array_datos[$j] = ""; }//dato null remplazado por string vacio
            
            if($j < $lenght_data_-2){ //mientras sea menor a la ultima posicion se concatena una coma
              $campo_db  .= $pre_data[$j]->campo_db.','; $signos .= '?,'; 
            }else if($j == $lenght_data_-2){ //si esta en la ultima posicion no se concatena la coma
              $campo_db  .= $pre_data[$j]->campo_db; $signos .= '?';  
            }else{ #se paso 
            }
          }
        }
        $public_id_r = uniqid('id_',TRUE);
        array_push($array_datos,$public_id,$public_id_r);
        #I N S E R C I O N   D E   D A T O S   D E   L I C E N C I A
        $query = "INSERT INTO $tabla_query ($campo_db,id_licencia,public_id) VALUES ($signos,?,?)";
        $query = $this->db->query($query, $array_datos);
      }

      
      
      // obtencion de requisitos para insercion en trabla de relacion
      if ($id_lic_para == 'id_5eb6cd94031ea2.25744661') { $traid =  'id_5ee7d28d26dbb4.24158883'; }
      else if($id_lic_para == 'id_5eb6cd94031ea2.25744662'){ $traid =  'id_5ee7d2eee1e980.01844763';}
      else if($id_lic_para == 'id_5eb6cd94031ea2.25744663'){ $traid =  'id_5ee7d474aca371.37345202';}
      else if($id_lic_para == 'id_5eb6cd94031ea2.25744664'){ $traid =  'id_5ee7d594e98050.06985997';}
      else{ }
      
      $query_slt = "SELECT requisitos.*, r_tramite_requisito.estatus AS 'asignado', r_tramite_requisito.public_id AS 'plid_rtr'
              FROM r_tramite_requisito LEFT JOIN requisitos ON requisitos.public_id = r_tramite_requisito.id_requi
              WHERE r_tramite_requisito.id_tramite = ? AND r_tramite_requisito.estatus = ?";
      $query_slt  = $this->db->query($query_slt,array($traid, '1'));
      $result_slt = $query_slt->result_array();

      //path config
      $path = 'public_files/user_assets/'.$uid_token.'/licencias/'.$public_id;
      $lenght_result_slt = count($result_slt);
      $res = $result_slt;
      for ($i=0; $i < $lenght_result_slt; $i++) {
        $r_public_id = uniqid('id_',TRUE);
        $predata     = $result_slt[$i];

        $id_requisito = $predata['public_id'];
        //insercion en trabla de relacion DE REQUISITOS
        $query = "INSERT INTO r_requisito_tramite_docs (id_licencia,id_tramite,id_requisito,estatus,validado,public_id) VALUES (?,?,?,?,?,?)";
        $query = $this->db->query($query, array($public_id,$traid,$id_requisito,'Sin Carga',0,$r_public_id));

      }
      //////////////////////////////////////////////////////////////////////////////
      $flag_ara = $data_request->flag_ara; //aranceles perito
      if ($flag_ara === true || $flag_ara === 'true'){
        $ara        = $data_request->ara;
        $total_gral = $ara[count($ara)-1];
        $lenght_ara = count($ara);
        for ($i=0; $i < $lenght_ara-1; $i++) {
          $ra_public_id = uniqid('id_',TRUE);

          $id_ara   = $ara[$i]->idar2;
          $unidad   = $ara[$i]->unidad;
          $cantidad = (float) $ara[$i]->cantidad;
          $total    = (float) $ara[$i]->total;
          //insercion en trabla de relacion ARANCELES
          $query = "INSERT INTO r_licencias_arancel (id_licencia,id_tramite,cantidad,total,id_arancel,public_id) VALUES (?,?,?,?,?,?)";
          $query = $this->db->query($query, array($public_id,$traid,$cantidad,$total,$id_ara,$ra_public_id));
        }
        /////////////////////////////////////////////////////////
        $porcentaje = 0;//obtener porcentaje - id colegio
        $query_col = "SELECT id_colegio FROM r_datos_peritos WHERE id_usuario = ?";
        $query_col  = $this->db->query($query_col,array($uid_token));
        $result_col = $query_col->result_array();

        if (isset($result_col[0]) && !empty($result_col[0])){
          $preparing_col = $result_col[0];
          $id_colegio    = (string)$preparing_col['id_colegio'];

          $query_por  = "SELECT porcentaje FROM r_datos_colegio WHERE id_colegio = ?";
          $query_por  = $this->db->query($query_por,array($id_colegio));
          $result_por = $query_por->result_array();

          if (isset($result_por[0]) && !empty($result_por[0])){
            $preparing_por = $result_por[0];
            $porcentaje = (float)$preparing_por['porcentaje'];
          }
        }
        $arancel_col = ($total_gral * $porcentaje) / 100;
        $query = "UPDATE licencias SET is_ara = ?, arancel_col = ?, total_arancel = ? WHERE public_id = ? "; //actualizar porcentaje
        $this->db->query($query,array(1,$arancel_col, $total_gral, $public_id));
      }
      ////////////////////////////////////////////////////////////
      $flag_ara_cor = $data_request->flag_ara_cor; // A R A N C E L E S   D E   C O R R E S P O N S A B L E S
      if ($flag_ara_cor === true || $flag_ara_cor === 'true'){
        $ara_cor = $data_request->ara_cor;
        $lenght_ara_cor = count($ara_cor);
        for ($i=0; $i < $lenght_ara_cor; $i++) {
          $cor_id = $ara_cor[$i]->user_id;
          $esp_id = $ara_cor[$i]->id_especialidad;
          
          $ara_cor_data   = $ara_cor[$i]->aranceles;
          $total_gral_cor = $ara_cor_data[count($ara_cor_data)-1];
          unset($ara_cor_data[count($ara_cor_data)-1]);

          $lenght_ara_cor_data = count($ara_cor_data);
          for ($j=0; $j < $lenght_ara_cor_data; $j++) {
            $ra_public_id = uniqid('id_',TRUE);

            $id_ara    = $ara_cor_data[$j]->idar2;
            $cantidad  = (float) $ara_cor_data[$j]->cantidad;
            $total     = (float) $ara_cor_data[$j]->total;
            //insercion en trabla de relacion
            $query = "INSERT INTO r_licencias_arancel_corres(id_licencia,id_tramite,cantidad,total,id_arancel,public_id) VALUES (?,?,?,?,?,?)";
            $query = $this->db->query($query, array($public_id,$traid,$cantidad,$total,$id_ara,$ra_public_id)); 
          }
          $rlc_public_id = uniqid('id_',TRUE);

          #aranceles de colegio para corresponsables
          $porcentaje = 0;//obtener porcentaje - id colegio
          $query_col = "SELECT id_colegio FROM r_datos_peritos WHERE id_usuario = ?";
          $query_col  = $this->db->query($query_col,array($cor_id));
          $result_col = $query_col->result_array();

          if (isset($result_col[0]) && !empty($result_col[0])){
            $preparing_col = $result_col[0];
            $id_colegio    = (string)$preparing_col['id_colegio'];

            $query_por  = "SELECT porcentaje FROM r_datos_colegio WHERE id_colegio = ?";
            $query_por  = $this->db->query($query_por,array($id_colegio));
            $result_por = $query_por->result_array();

            if (isset($result_por[0]) && !empty($result_por[0])){
              $preparing_por = $result_por[0];
              $porcentaje    = (float)$preparing_por['porcentaje'];
            }
          }
          $arancel_col_cor = ($total_gral_cor * $porcentaje) / 100;

          $query = "INSERT INTO r_licencias_corres(id_licencia,id_tramite,id_especialidad,total_arancel,arancel_col,id_usuario,public_id) VALUES (?,?,?,?,?,?,?)";
          $query = $this->db->query($query, array($public_id,$traid,$esp_id,$total_gral_cor,$arancel_col_cor,$cor_id,$rlc_public_id)); 
        }
      }

      $res = true;
    }else{
      $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
 $this->response($res);
}


//campos especificos para grupos
public function get_re_docs_lic_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);
      $plid_lic = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->public_id);

      $query = "SELECT r_requisito_tramite_docs.*, requisitos.requisito
                FROM r_requisito_tramite_docs
                INNER JOIN requisitos
                ON r_requisito_tramite_docs.id_requisito = requisitos.public_id
                WHERE r_requisito_tramite_docs.id_licencia = ?
                ORDER BY r_requisito_tramite_docs.id_relacion ASC";
      $query  = $this->db->query($query,array($plid_lic));
      $result = $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = [ "status" => TRUE, "data" => $result ];
      }else {
        $res = [ "status" => FALSE, "data" => $result ];
      }
    }else{
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

//agregar archivos
public function add_doc_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);

      $data         = $data_request->data;
      $id_licencia  = $data_request->traid;
      $id_requisito = $data->id_relacion;


      $file      = $data->file;
      $base_64   = $file->base64;
      $filename  = $file->filename;
      $file_size = $file->filesize;
      $file_type = $file->filetype;

      if ($utype_token === 'id_5ebafe3b361083.91981512') {
        $uid_token = $data_request->solid;
      }

      //real path
      $path_lic = '/home1/francisco/guadalupe.inmueblesenmexico.net/public_files/user_assets/'.$uid_token.'/licencias/'.$id_licencia;
      //database
      $path = 'public_files/user_assets/'.$uid_token.'/licencias/'.$id_licencia.'/'.$filename;

      if ($file_type == 'image/jpeg' || $file_type == 'image/JPEG' || $file_type == 'image/jpg' || $file_type == 'image/JPG'
          || $file_type == 'image/png'  || $file_type == 'image/PNG' || $file_type == 'application/pdf'  || $file_type == 'application/PDF'
          || $file_type == 'application/zip'  || $file_type == 'application/ZIP'
          || $file_type == 'application/dwg' || $file_type == 'application/dwg') {
        if ($file_size <= 5242880 ) {
          if(file_put_contents($path_lic.'/'.$filename, base64_decode($base_64))){
            // $data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
            $query = "UPDATE r_requisito_tramite_docs SET path_file = ?, nombre = ?, estatus = ?, bandera_carga = ? WHERE id_relacion = ?";
            $query = $this->db->query($query, array($path,$filename,'Archivo Cargado',1,$id_requisito));
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
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

//agregar archivos
public function add_lic_au_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);

      $data         = $data_request->data;
      $id_licencia  = $data_request->traid;

      $file      = $data->file;
      $base_64   = $file->base64;
      $filename  = $file->filename;
      $file_size = $file->filesize;
      $file_type = $file->filetype;

      if ($utype_token === 'id_5ebafe3b361083.91981512') {
        $uid_token = $data_request->solid;
      }

      //real path
      $path_lic = '/home1/francisco/guadalupe.inmueblesenmexico.net/public_files/user_assets/'.$uid_token.'/licencias/'.$id_licencia;
      //database
      $path = 'public_files/user_assets/'.$uid_token.'/licencias/'.$id_licencia.'/'.$filename;

      if ($file_type == 'image/jpeg' || $file_type == 'image/JPEG' || $file_type == 'image/jpg' || $file_type == 'image/JPG'
          || $file_type == 'image/png'  || $file_type == 'image/PNG' || $file_type == 'application/pdf'  || $file_type == 'application/PDF'
          || $file_type == 'application/zip'  || $file_type == 'application/ZIP'
          || $file_type == 'application/dwg' || $file_type == 'application/dwg') {
        if ($file_size <= 5242880 ) {
          if(file_put_contents($path_lic.'/'.$filename, base64_decode($base_64))){
            // $data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
            $query = "UPDATE licencias SET path_lic_firma = ? WHERE public_id = ?";
            $query = $this->db->query($query, array($path,$id_licencia));
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
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

//agregar archivos
public function save_est_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);
      
      $flag         = $data_request->flag;
      $id_licencia  = $data_request->traid;

      $fecha = "";
      if($flag == 'Licencia para Firma'){
        $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $mes = $meses[date('n')-1];
        $ano=date('Y');
        $dia=date('d');
        $fecha = $dia.'/'.$mes.'/'.$ano;
      }

      $query = "UPDATE licencias SET estatus = ?,  fecha_autorizacion = ? WHERE public_id = ?";
      $query = $this->db->query($query, array($flag,$fecha,$id_licencia));
      $res = [ "status" => TRUE];
    }else{
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

 /*get  licenses servidor*/
public function get_licencias_ser_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $query = "SELECT id_tramite FROM r_tramite_usuarios WHERE id_usuario = ? AND estatus != ?";
      $query = $this->db->query($query,array($uid_token, '0'));

      $lic_result   = $query->result_array();
      //lics result array
      $result_query_lic  = [];
      foreach ($lic_result as $key => $value) {
        $id_tramite = $value;
        
        $query = "SELECT licencias.id_licencia, licencias.folio, licencias.id_tramite,
        licencias.ultima_act, licencias.estatus, licencias.id_usuario, licencias.lat, licencias.lng,
        licencias.total_arancel AS 'total_ara', licencias.is_der, 
        licencias.path_lic_firma, licencias.public_id, lista_tramites.tramite AS 'tramite',
        usuarios.nombre AS 'solicitante', usuarios.public_id AS 'id_solicitante',
        lic_datos_predio.calle,  lic_datos_predio.colonia,
        r_licencias_derechos.total_gral AS 'total_der',
        lic_licencia_para.obra_nueva, lic_licencia_para.ampliacion, lic_licencia_para.modificacion, 
        lic_licencia_para.reparacion, lic_licencia_para.regularizacion,
        lic_licencia_para.obra_minima, lic_licencia_para.demolicion,
        lic_licencia_para.renovacion, lic_licencia_para.bardeo
        FROM licencias
        LEFT JOIN lic_datos_predio     ON licencias.public_id  = lic_datos_predio.id_licencia
        INNER JOIN lista_tramites      ON licencias.id_tramite = lista_tramites.public_id
        INNER JOIN usuarios            ON licencias.id_usuario = usuarios.public_id
        LEFT JOIN r_licencias_derechos ON licencias.public_id  = r_licencias_derechos.id_licencia
        LEFT JOIN lic_licencia_para    ON licencias.public_id  = lic_licencia_para.id_licencia
        WHERE licencias.estatus != ? AND licencias.id_tramite  = ?
        GROUP BY licencias.id_licencia ORDER BY licencias.id_licencia DESC";
        $query = $this->db->query($query,array('0',$id_tramite));
        $result = $query->result_array();

        $result_query_lic = array_merge($result_query_lic,$result);
      }
      $res   = ["data" => $result_query_lic, "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

   /*get  license colegio*/
public function get_licencias_col_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      $query  = "SELECT r_datos_peritos.id_usuario  FROM r_datos_peritos WHERE r_datos_peritos.id_colegio = ?";
      $query  = $this->db->query($query,array($uid_token));
      $result = $query->result_array();

      $result_query_lic = [];
      foreach ($result as $key => $value) {
        $query = "SELECT licencias.id_licencia, licencias.folio, licencias.id_tramite, licencias.is_der, licencias.is_per,
        licencias.ultima_act, licencias.estatus, licencias.estatus_col, licencias.id_usuario, 
        licencias.total_arancel AS 'total_ara', licencias.arancel_col,
        licencias.public_id, lista_tramites.tramite AS 'tramite',
        usuarios.nombre AS 'solicitante', usuarios.public_id AS 'id_solicitante',
        lic_datos_predio.calle,  lic_datos_predio.colonia,
        lic_licencia_para.obra_nueva, lic_licencia_para.ampliacion, lic_licencia_para.modificacion, 
        lic_licencia_para.reparacion, lic_licencia_para.regularizacion,
        lic_descripcion_obra.suma_sup
        FROM licencias
        INNER JOIN lista_tramites       ON licencias.id_tramite = lista_tramites.public_id
        LEFT JOIN lic_datos_predio      ON licencias.public_id = lic_datos_predio.id_licencia
        LEFT JOIN lic_descripcion_obra  ON licencias.public_id = lic_descripcion_obra.id_licencia
        LEFT JOIN lic_licencia_para     ON licencias.public_id = lic_licencia_para.id_licencia
        INNER JOIN usuarios             ON licencias.id_usuario = usuarios.public_id
        WHERE licencias.estatus != ? AND licencias.is_ara = ? AND licencias.id_usuario = ?
        GROUP BY licencias.id_licencia ORDER BY licencias.id_licencia DESC";
        $query = $this->db->query($query,array('0','1',$value));
        $result = $query->result_array();

        $result_query_lic= array_merge($result_query_lic,$result);
      }


      $res   = ["data" => $result_query_lic, "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //agregar archivos
public function add_comen_doc_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);

      $comentario   = $data_request->comentario;
      $id_requisito = $data_request->public_id;

      $query = "UPDATE r_requisito_tramite_docs SET comentario = ? WHERE public_id = ?";
      $query = $this->db->query($query, array($comentario,$id_requisito));
      $res = [ "status" => TRUE];
    }else{
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

public function arancel_pago_get($id){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {

    $query = "SELECT campos.nombre AS 'campo', r_licencia_datos.dato
              FROM licencias
              INNER JOIN lista_tramites ON licencias.id_tramite = lista_tramites.public_id
              INNER JOIN r_licencia_datos  ON licencias.public_id = r_licencia_datos.id_licencia
              INNER JOIN campos ON r_licencia_datos.id_dato = campos.public_id
              INNER JOIN grupos_licencias ON r_licencia_datos.id_grupo = grupos_licencias.public_id
              WHERE licencias.public_id = ? ";
    $query = $this->db->query($query, array($id));

    $query2 = "SELECT licencias.estatus, r_licencias_arancel.arancel,
              r_licencias_arancel.precio, r_licencias_arancel.corresponsal, r_licencias_arancel.unidad,
              r_licencias_arancel.cantidad, r_licencias_arancel.total, r_licencias_arancel.total_gral
              FROM licencias INNER JOIN r_licencias_arancel ON licencias.public_id = r_licencias_arancel.id_licencia
              WHERE licencias.public_id = ? ";
    $query2 = $this->db->query($query2, array($id));
    session_start();
    $_SESSION['sujeto'] = $query->result_array();
    $_SESSION['arancel'] = $query2->result_array();

    $res = $id;
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

 // rlacion de tramite usuarios
public function get_admin_tra_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);

      $public_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->public_id);

      if ($utype_token === 'id_5ebafdbc81b415.25700817') { $tipu_query = 'id_5ebafe3b361083.91981512'; }
      if ($utype_token === 'id_5ebafe3b361083.91981512') { $tipu_query = 'id_5ebc623f267603.74886889'; }

      $query = "SELECT usuarios.nombre, usuarios.tipo_usuario, usuarios.estado, usuarios.public_id,
        r_tramite_usuarios.estatus, r_tramite_usuarios.accion_u AS 'editar', r_tramite_usuarios.accion_d AS 'eliminar', r_tramite_usuarios.public_id AS 'plid_rta',
        r_datos_usuarios.departamento
        FROM r_tramite_usuarios
        LEFT JOIN usuarios ON r_tramite_usuarios.id_usuario = usuarios.public_id
        LEFT JOIN r_datos_usuarios ON r_tramite_usuarios.id_usuario = r_datos_usuarios.id_usuario
        WHERE r_tramite_usuarios.id_tramite = ? AND r_tramite_usuarios.tipo_usuario = ?";
      $query  = $this->db->query($query,array($public_id, $tipu_query));
      $result = $query->result_array();
      if (isset($result) && !empty($result)) {
        $res = [ "status" => TRUE, "data" => $result ];
      }else {
        $res = [ "status" => FALSE, "data" => $result ];
      }
    }else{
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

//guardar relacion de requisitos
  public function save_r_tad_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);

      $traid    = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->traid);
      $data     = $data_request->data;
      $detalles = $data_request->detalles;

      $lenght     = count($data);
      $lenght_det = count($detalles);

      for ($i=0; $i < $lenght; $i++) {
        //unicid
        $relid = uniqid('id_',TRUE);
        //id del admin
        $admid = $data[$i]->public_id;
        //tipo id
        $tipid = $data[$i]->tipo_usuario;
        //valor de checked
        $checked = $data[$i]->checked;
        if ($checked === true || $checked === 'true') { $checked = 1; }
        if ($checked === false || $checked === 'false') { $checked = 0; }
        //id relacion
        $plid_rta = $data[$i]->plid_rta;

        if ($checked === 0) {
          $query = "UPDATE r_tramite_usuarios SET  estatus = ? WHERE public_id = ? ";
          $this->db->query($query,array($checked,$plid_rta));
        }
        // detalles
        for ($j=0; $j < $lenght_det; $j++) {
          if ($detalles[$j]->uid === $admid ) {
            if ($plid_rta !== 'non') {
              $query = "UPDATE r_tramite_usuarios SET  estatus = ?, accion_u = ?, accion_d = ? WHERE public_id = ? ";
              $this->db->query($query,array($checked,$detalles[$j]->update,$detalles[$j]->delete,$plid_rta));
            }else {
              $query = "INSERT INTO r_tramite_usuarios (estatus,id_tramite,accion_u,accion_d,id_usuario,tipo_usuario,public_id) VALUES (?,?,?,?,?,?,?)";
              $query = $this->db->query($query, array($checked,$traid,$detalles[$j]->update,$detalles[$j]->delete,$admid,$tipid,$relid));
            }
          }
        }
      }
      $res = [ "status" => TRUE ];
    }else{
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

 // rlacion de tramite usuarios
public function get_detalles_lic_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);

      $public_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->public_id);

      $query2 = "SELECT * FROM licencias WHERE public_id = ?";
      $query2  = $this->db->query($query2,array($public_id));
      $result2 = $query2->result_array();

      $lic = $result2[0];
      if($lic['id_tramite'] == 'id_5ee7c29c1b0899.56267163'){
        //No tiene grupos
        $result = "";
      }else{
        $query = "SELECT grupos_licencias.nombre, grupos_licencias.public_id 
                  FROM licencias INNER JOIN lista_tramites ON licencias.id_tramite = lista_tramites.public_id 
                  LEFT JOIN r_tramite_grupo ON lista_tramites.public_id = r_tramite_grupo.id_tramite 
                  INNER JOIN grupos_licencias ON r_tramite_grupo.id_grupo = grupos_licencias.public_id 
                  WHERE licencias.public_id = ?";
        $query  = $this->db->query($query,array($public_id));
        $result = $query->result_array();
      }

      if (isset($result) && !empty($result) && isset($result2) && !empty($result2)) {
        $res = [ "status" => TRUE, "data" => $result, "lic" => $result2 ];
      }else if(isset($result2) && !empty($result2)){
        $res = [ "status" => TRUE, "data" => $result, "lic" => $result2 ];
      }else {
        $res = [ "status" => FALSE, "data" => $result ];
      }
    }else{
      $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
  $this->response($res);
}

//guardar licencia
public function update_gral_ser_post(){
  $this->load->library('Authorization_Token');
  $validation_token = $this->authorization_token->validateToken();
  if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
    $data_post   = file_get_contents("php://input");
    $data_token  = $validation_token['data'];
    $utype_token = $data_token->utype;
    $uid_token   = $data_token->id;

    if (isset($data_post) && !empty($data_post)){
      $data_request = json_decode($data_post);

      $data  = $data_request->datos;
      $licid = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '',$data_request->id_licencia);
      $id_grupo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->groupid);

      if($id_grupo === 'id_5ea3457c2e1668.57636641')       { $tabla_query = 'lic_datos_propietario'; }
      else if($id_grupo === 'id_5ea3457c2e33e3.36891982')  { $tabla_query = 'lic_datos_predio'; }
      else if($id_grupo === 'id_5ea352cc935558.63022662')  { $tabla_query = 'lic_uso_de_suelo'; }
      else if($id_grupo === 'id_5ea352cc935558.63022447')  { $tabla_query = 'lic_constancia_servicios'; }
      else if($id_grupo === 'id_5eb6cd94031ea2.25736133')  { $tabla_query = 'lic_const_estru'; }
      else if($id_grupo === 'id_5eb6cd94031ea2.257447514') { $tabla_query = 'lic_constancia_construccion'; }
      else if($id_grupo === 'id_5ee7d14e6e4a79.0155874572'){ $tabla_query = 'lic_autorizacion_ocupacion'; }
      else if($id_grupo === 'id_5ea3457c2e3ba5.81867615')  { $tabla_query = 'lic_descripcion_obra'; }
      else if($id_grupo === 'id_5ee7d14e6e4a79.01524533')  { $tabla_query = 'lic_licencia_para'; }
      else if($id_grupo === 'id_5ee7d14e6e4a79.01524005')  { $tabla_query = 'lic_permiso_para'; }
      else if($id_grupo === 'id_5ea3457c2e3ba5.818335475')  { $tabla_query = 'lic_vigencia'; }
      else if($id_grupo === 'id_5ea3457c2e3ba5.818335578')  { $tabla_query = 'lic_antecedentes'; }
      
      $query = "SELECT public_id FROM $tabla_query WHERE id_licencia = ?";
      $query = $this->db->query($query, array($licid));
      $result = $query->result_array();


      $lenght_data_ = count($data);
      $campo_db     = ""; $signos = ''; $array_datos = [];

      if (isset($result) && !empty($result)){
        foreach ($data as $key => $value) {
          if ($key !== 'id_licencia' && $key !== 'public_id' && $key !== 'id') {
            $query_des = "UPDATE $tabla_query SET $key = ? WHERE id_licencia = ?";
            $query_des = $this->db->query($query_des, array($value, $licid));
          }
        }
      }else{
        foreach ($data as $key => $value) {
          $campo_db  .= $key.','; $signos .= '?,'; 
          array_push($array_datos,$value);
        }
        
        $public_id_r = uniqid('id_',TRUE);
        array_push($array_datos,$licid,$public_id_r);
        #I N S E R C I O N   D E   D A T O S   D E   L I C E N C I A
        $query = "INSERT INTO $tabla_query ($campo_db id_licencia,public_id) VALUES ($signos ?,?)";
        $query = $this->db->query($query, $array_datos);
      }

      $res = [ "status" => TRUE ];
    }else{
      $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
    }
  }else {
    $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
  }
 $this->response($res);
}

  public function updateMapa_put(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);

        $uid_token = $data_request->sol_id;
        $lic_id = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '',$data_request->lic_id);
        $base64 = $data_request->base64;

        $path_lic = '/home1/francisco/guadalupe.inmueblesenmexico.net/public_files/user_assets/'.$uid_token.'/licencias/'.$lic_id;

        if (file_exists($path_lic.'/updatemapa.png')) {
          unlink($path_lic.'/updatemapa.png');
        }

        if (!file_exists($path_lic)) {
          mkdir($path_lic, 0777, true);
        }

        if(file_put_contents($path_lic.'/updatemapa.png', base64_decode($base64))){
          $path = $uid_token.'/licencias/'.$lic_id.'/updatemapa.png';
          $query = "UPDATE licencias SET path_mapa = ? WHERE public_id = ? ";
          $this->db->query($query,array($path, $lic_id));

          $res = [ "status" => TRUE ];
        }else {
          $res = [ "status" => FALSE];
        }
      }else{
        $res =  "Empty Json, Error : ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //campos especificos para grupos
  public function form_data_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_post   = file_get_contents("php://input");
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;

      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);
        $id_grupo = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->grup_id);
        $id_licencia = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->lic_id);

        if($id_grupo === 'id_5ea3457c2e1668.57636641')       { $tabla_query = 'lic_datos_propietario'; }
        else if($id_grupo === 'id_5ea3457c2e33e3.36891982')  { $tabla_query = 'lic_datos_predio'; }
        else if($id_grupo === 'id_5ea352cc935558.63022662')  { $tabla_query = 'lic_uso_de_suelo'; }
        else if($id_grupo === 'id_5ea352cc935558.63022447')  { $tabla_query = 'lic_constancia_servicios'; }
        else if($id_grupo === 'id_5eb6cd94031ea2.25736133')  { $tabla_query = 'lic_const_estru'; }
        else if($id_grupo === 'id_5eb6cd94031ea2.257447514') { $tabla_query = 'lic_constancia_construccion'; }
        else if($id_grupo === 'id_5ee7d14e6e4a79.0155874572'){ $tabla_query = 'lic_autorizacion_ocupacion'; }
        else if($id_grupo === 'id_5ea3457c2e3ba5.81867615')  { $tabla_query = 'lic_descripcion_obra'; }
        else if($id_grupo === 'id_5ee7d14e6e4a79.01524533')  { $tabla_query = 'lic_licencia_para'; }
        else if($id_grupo === 'id_5ee7d14e6e4a79.01524005')  { $tabla_query = 'lic_permiso_para'; }
        else if($id_grupo === 'id_5ea3457c2e3ba5.818335475')  { $tabla_query = 'lic_vigencia'; }
        else if($id_grupo === 'id_5ea3457c2e3ba5.818335578')  { $tabla_query = 'lic_antecedentes'; }
        else{ /*grupo no  compatible*/}

        $query = "SELECT campos.*,
                  radiovalues.radiovalue1, radiovalues.radiovalue2, radiovalues.radiovalue3,
                  r_grupo_campo.estatus, r_grupo_campo.public_id AS 'plid_rgf', r_grupo_campo.id_grupo AS 'id_grupo'
                  FROM campos
                  LEFT JOIN radiovalues
                  ON campos.public_id = radiovalues.public_campo
                  LEFT JOIN r_grupo_campo
                  ON campos.public_id = r_grupo_campo.id_campo
                  WHERE r_grupo_campo.id_grupo = ? AND r_grupo_campo.estatus = ? ORDER BY campos.id ASC";
        $query  = $this->db->query($query,array($id_grupo,1));
        $result = $query->result_array();

        $query2 = "SELECT * FROM $tabla_query WHERE id_licencia = ? ";
        $query2  = $this->db->query($query2,array($id_licencia));
        $result2 =  $query2->result_array();

        if (isset($result) && !empty($result)) {
          $res = [ "status" => TRUE, "form" => $result, "data" => $result2 ];
        }else {
          $res = [ "status" => FALSE, "data" => $result ];
        }
      }else{
        $res =  "Empty Json, Error: ".REST_Controller::HTTP_BAD_REQUEST;
      }
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  // C O R R E S P O N S A B L E S
   /*get  license solicitante*/
   public function get_lic_corres_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
      $data_token  = $validation_token['data'];
      $utype_token = $data_token->utype;
      $uid_token   = $data_token->id;
      
      $query = "SELECT licencias.id_licencia, licencias.folio, licencias.id_tramite, licencias.is_der, licencias.is_per,
        licencias.ultima_act, licencias.estatus, licencias.id_usuario, 
        licencias.public_id, licencias.path_lic_firma, lista_tramites.tramite AS 'tramite',
        r_licencias_corres.total_arancel AS 'total_ara', r_licencias_corres.arancel_col,
        lic_datos_predio.calle,  lic_datos_predio.colonia,
        lic_licencia_para.obra_nueva, lic_licencia_para.ampliacion, lic_licencia_para.modificacion, 
        lic_licencia_para.reparacion, lic_licencia_para.regularizacion,
        r_licencias_derechos.total_gral AS 'total_der'
        FROM licencias
        INNER JOIN lista_tramites       ON licencias.id_tramite = lista_tramites.public_id
        LEFT JOIN lic_datos_predio      ON licencias.public_id = lic_datos_predio.id_licencia
        LEFT JOIN r_licencias_derechos  ON licencias.public_id = r_licencias_derechos.id_licencia
        LEFT JOIN lic_licencia_para     ON licencias.public_id = lic_licencia_para.id_licencia
        LEFT JOIN r_licencias_corres    ON licencias.public_id = r_licencias_corres.id_licencia
        WHERE licencias.estatus != ? AND r_licencias_corres.id_usuario = ?
        GROUP BY licencias.id_licencia ORDER BY licencias.id_licencia DESC";
      $query = $this->db->query($query,array('0', $uid_token));
      $res   = ["data" => $query->result_array(), "status" => true];
    }else {
      $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
    }
    $this->response($res);
  }

  //////// C A N C E L A C I O N E S 
  public function sol_can_post(){
    $this->load->library('Authorization_Token');
    $validation_token = $this->authorization_token->validateToken();
    if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
        $data_post   = file_get_contents("php://input");
        $data_token  = $validation_token['data'];
        $utype_token = $data_token->utype;
        $uid_token   = $data_token->id;
      if (isset($data_post) && !empty($data_post)){
        $data_request = json_decode($data_post);

        $id_usuario  = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->id_usuario);
        $id_licencia = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->id_licencia);
        $contra      = preg_replace('([^A-Za-z0-9-ñÑáéíóúÁÉÍÓÚ@.,-_/\s+/u])', '', $data_request->contra);  
        //contra encrip
        $enc_pass = sha1($contra);

        $query = "SELECT public_id FROM usuarios WHERE contra = ? AND public_id = ?";
        $query = $this->db->query($query,array($enc_pass, $id_usuario));
        $resul = $query->result_array();

        if (isset($resul[0]) && !empty($resul[0])){
          $query = "UPDATE licencias SET estatus = ? WHERE id_licencia = ? ";
          $this->db->query($query,array('Cancelado', $id_licencia));
          $res   = [ "status" => true,  "data" => null ];
        }else {
          $res   = [ "status" => true,  "data" => 0 ];
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

