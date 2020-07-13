<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;


class Pdf extends REST_Controller {

    public function __construct(){
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, Authorization");
        header("Access-Control-Allow-Origin: *");
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('form', 'url'));

    }

    public function index_get(){
    }

    public function solicitud_get($id){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            //consulta para saber el numero de grupos 
            $query = "SELECT grupos_licencias.nombre, grupos_licencias.public_id
                    FROM licencias INNER JOIN lista_tramites ON licencias.id_tramite = lista_tramites.public_id 
                    LEFT JOIN r_tramite_grupo ON lista_tramites.public_id = r_tramite_grupo.id_tramite 
                    INNER JOIN grupos_licencias ON r_tramite_grupo.id_grupo = grupos_licencias.public_id
                    WHERE licencias.public_id = ?";
            $query  = $this->db->query($query,array($id));
            $result = $query->result_array();
            $num_groups = count($result);
            session_start();
            if($num_groups > 0){
                for($i = 0; $i < $num_groups ; $i++){
                    $iter = $result[$i];
                    $id_grupo = $iter["public_id"];
                    if($id_grupo === 'id_5ea3457c2e1668.57636641') {
                        $query = "SELECT * FROM lic_datos_propietario WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array();
                    }else if($id_grupo === 'id_5ea3457c2e33e3.36891982')  {
                        $query = "SELECT * FROM lic_datos_predio WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array();
                    }else if($id_grupo === 'id_5ea352cc935558.63022662')  {
                        $query = "SELECT * FROM lic_uso_de_suelo WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array();
                    }else if($id_grupo === 'id_5ea352cc935558.63022447')  {
                        $query = "SELECT * FROM lic_constancia_servicios WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array();
                    }else if($id_grupo === 'id_5eb6cd94031ea2.25736133')  {
                        $query = "SELECT * FROM lic_const_estru WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array();
                    }else if($id_grupo === 'id_5eb6cd94031ea2.257447514') {
                        $query = "SELECT * FROM lic_constancia_construccion WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array();
                    }else if($id_grupo === 'id_5ee7d14e6e4a79.0155874572'){ 
                        $query = "SELECT * FROM lic_autorizacion_ocupacion WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array();
                    }else if($id_grupo === 'id_5ea3457c2e3ba5.81867615')  {
                        $query = "SELECT * FROM lic_descripcion_obra WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array(); 
                    }else if($id_grupo === 'id_5ee7d14e6e4a79.01524533')  {   
                        $query = "SELECT * FROM lic_licencia_para WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array();
                    }else if($id_grupo === 'id_5ee7d14e6e4a79.01524005')  {
                        $query = "SELECT * FROM lic_permiso_para WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        $_SESSION[$id_grupo] = $query->result_array();
                    }else if($id_grupo === 'id_5ea3457c2e3ba5.818335475')  {
                        $query = "SELECT * FROM lic_vigencia WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        //$_SESSION[$id_grupo] = $query->result_array();
                        $_SESSION[$id_grupo] = $this->setting_dates($query->result_array());
                    }else if($id_grupo === 'id_5ea3457c2e3ba5.818335578')  {
                        $query = "SELECT * FROM lic_antecedentes WHERE id_licencia = ?";
                        $query  = $this->db->query($query,array($id));
                        //$_SESSION[$id_grupo] = $query->result_array();
                        $_SESSION[$id_grupo] = $this->setting_dates($query->result_array());
                        
                    }else {
                        $res = "no anexado o incorrecto";
                    }
                }
            }
            $query3 = "SELECT licencias.*, lista_tramites.tramite
                        FROM  licencias
                        INNER JOIN lista_tramites ON licencias.id_tramite = lista_tramites.public_id
                        WHERE licencias.public_id = ?";
            $query3  = $this->db->query($query3,array($id));
            $result = $query3->result_array();
            $datos_lic = $result[0];
            $_SESSION['licencia'] = $datos_lic;

            $tipo_lic = $datos_lic['id_tramite'];
            $w_dro = $datos_lic['is_per'];
            if($w_dro == 1){
                $user = $datos_lic['id_usuario'];
                $dro_query = "SELECT usuarios.nombre, r_datos_peritos.no_registro, r_datos_usuarios.calle, 
                r_datos_usuarios.colonia, r_datos_usuarios.celular, colegio.nombre AS colegio
                FROM usuarios 
                INNER JOIN r_datos_peritos ON usuarios.public_id = r_datos_peritos.id_usuario
                INNER JOIN r_datos_usuarios ON usuarios.public_id = r_datos_usuarios.id_usuario
                LEFT JOIN usuarios colegio ON r_datos_peritos.id_colegio = colegio.public_id
                WHERE usuarios.public_id = ?";
                $dro_query  = $this->db->query($dro_query,array($user));
                $dro_result = $dro_query->result_array();
                $_SESSION['DRO'] = $dro_result[0];
            }else{
                $_SESSION['DRO'] = "";
            }

            if($tipo_lic == 'id_5ee7cfb91bd489.36231307' || $tipo_lic == 'id_5eb6d2b728d801.27882313' || $tipo_lic == 'id_5eb6cd94032df1.01532919'){
                $path = "solicitudes/solicitud.php";
            }else if($tipo_lic == 'id_5ee7c29c1b0899.56267163'){//Acreditación de Director Responsable de Obra
                $path = "solicitudes/solcitud_acred.php";
            }else if($tipo_lic == 'id_5ee7c29c1b0899.56267163'){//CONSTANCIA DE COMPATIBILIDAD URBANISTÍCA Y/O USO DE SUELO
                $path = "solicitudes/solcitud_uso_suelo.php";
            }else if($tipo_lic == 'id_5eb6cd940325e2.95920769'){
                $path = "solicitudes/solicitud_seg_estructural.php";
            }else{
                $path = "solicitudes/solicitud_pdf.php";
            }
            $res = $path;
        }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
    }

    public function licencia_get($id){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            //consulta para saber el numero de grupos 
            $query = "SELECT grupos_licencias.nombre, grupos_licencias.public_id
                    FROM licencias INNER JOIN lista_tramites ON licencias.id_tramite = lista_tramites.public_id 
                    LEFT JOIN r_tramite_grupo ON lista_tramites.public_id = r_tramite_grupo.id_tramite 
                    INNER JOIN grupos_licencias ON r_tramite_grupo.id_grupo = grupos_licencias.public_id
                    WHERE licencias.public_id = ?";
            $query  = $this->db->query($query,array($id));
            $result = $query->result_array();
            $num_groups = count($result);
            
            session_start();
            for($i = 0; $i < $num_groups ; $i++){
                $iter = $result[$i];
                $id_grupo = $iter["public_id"];
                if($id_grupo === 'id_5ea3457c2e1668.57636641') {
                    $query = "SELECT * FROM lic_datos_propietario WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea3457c2e33e3.36891982')  {
                    $query = "SELECT * FROM lic_datos_predio WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea352cc935558.63022662')  {
                    $query = "SELECT * FROM lic_uso_de_suelo WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea352cc935558.63022447')  {
                    $query = "SELECT * FROM lic_constancia_servicios WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5eb6cd94031ea2.25736133')  {
                    $query = "SELECT * FROM lic_const_estru WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5eb6cd94031ea2.257447514') {
                    $query = "SELECT * FROM lic_constancia_construccion WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ee7d14e6e4a79.0155874572'){ 
                    $query = "SELECT * FROM lic_autorizacion_ocupacion WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea3457c2e3ba5.81867615')  {
                    $query = "SELECT * FROM lic_descripcion_obra WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array(); 
                }else if($id_grupo === 'id_5ee7d14e6e4a79.01524533')  {
                    $query = "SELECT * FROM lic_licencia_para WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ee7d14e6e4a79.01524005')  {
                    $query = "SELECT * FROM lic_permiso_para WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea3457c2e3ba5.818335475')  {
                    $query  = "SELECT * FROM lic_vigencia WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    //$_SESSION[$id_grupo] = $query->result_array();
                    $_SESSION[$id_grupo] = $this->setting_dates($query->result_array());
                }else if($id_grupo === 'id_5ea3457c2e3ba5.818335578')  {
                    $query  = "SELECT * FROM lic_antecedentes WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    //$_SESSION[$id_grupo] = $query->result_array();
                    $_SESSION[$id_grupo] = $this->setting_dates($query->result_array());
                }else {
                    $res = "no anexado o incorrecto";
                }
            }
            $query3 = "SELECT licencias.*, lista_tramites.tramite
                        FROM  licencias
                        LEFT JOIN lista_tramites ON licencias.id_tramite = lista_tramites.public_id
                        WHERE licencias.public_id = ?";
            $query3  = $this->db->query($query3,array($id));
            $result = $query3->result_array();
            $datos_lic = $result[0];
            $_SESSION['licencia'] = $datos_lic;

            $tipo_lic = $datos_lic['id_tramite'];
            $w_dro = $datos_lic['is_per'];
            if($w_dro == 1){
                $user = $datos_lic['id_usuario'];
                $dro_query = "SELECT usuarios.nombre, r_datos_peritos.no_registro, r_datos_usuarios.calle, 
                r_datos_usuarios.colonia, r_datos_usuarios.celular, colegio.nombre AS colegio
                FROM usuarios 
                INNER JOIN r_datos_peritos ON usuarios.public_id = r_datos_peritos.id_usuario
                INNER JOIN r_datos_usuarios ON usuarios.public_id = r_datos_usuarios.id_usuario
                LEFT JOIN usuarios colegio ON r_datos_peritos.id_colegio = colegio.public_id
                WHERE usuarios.public_id = ?";
                $dro_query  = $this->db->query($dro_query,array($user));
                $dro_result = $dro_query->result_array();
                $_SESSION['DRO'] = $dro_result[0];
            }else{
                $_SESSION['DRO'] = "";
            }

            if($tipo_lic == 'id_5ee7cfb91bd489.36231307'){//Bardeo perimetral hasta 2.50 m de altura.
                $path = "licencia_construccion.php";
            }else if($tipo_lic == 'id_5ee7d474aca371.37345202'){//Renovación de licencia construcción.
                $path = "licencia_construccion.php";
            }else if($tipo_lic == 'id_5ee7d2eee1e980.01844763'){//Demolición/Tantos C 60 m2
                $path = "licencia_construccion.php";
            }else if($tipo_lic == 'id_5ee7cfb91bd489.36231307'){//Licencia de Construcción mayor a 45m2
                $path = "licencia_construccion.php";
            }else if($tipo_lic == 'id_5ee7d28d26dbb4.24158883'){//Obra mínima / Movimiento de Tierras
                $path = "licencia_construccion.php";
            }else if($tipo_lic == 'id_5ee7d1da6f9739.17289761'){//Constancia de Alineamiento
                $path = "const_alineamiento.php"
            }else if($tipo_lic == 'id_5ee7d14e6e4a79.01709418'){//Autorización de Uso y Ocupación
                $path = "autorizacion_uso-y-ocupacion.php";
            }else if($tipo_lic == 'id_5ee7cd148ad1c6.54903447'){//Permiso de Excavación para toma domiciliaria. C/5m
                $path = "perm_excavacion-dom.php";//falta este o la otra excavacion
            }else if($tipo_lic == 'id_5ee7c4d7970c87.11819772'){//Alineamiento y Número Oficial
                $path = "const_alinea-y-no.oficial.php";
            }else if($tipo_lic == 'id_5ee7c29c1b0899.56267163'){//Acreditación de Director Responsable de Obra
                $path = "acreditacion_DRO.php";
            }else if($tipo_lic == 'id_5ee7c0459e3f29.73510478'){//Compatibilidad Urbanística (Uso de suelo)
                $path = "const_uso_suelo.php";
            }else if($tipo_lic == 'id_5eb6cd94031173.90385454'){//Constancia de Construcción
                $path = "const_construccion.php";
            }else if($tipo_lic == 'id_5eb6cd94031ea2.25736147'){//Constancia de Servicios
                $path = "const_servicios.php";
            }else if($tipo_lic == 'id_5eb6cd940325e2.95920769'){//Constancia de Seguridad Estructural
                $path = "const_seguridad-estruct.php";
            }else if($tipo_lic == 'id_5eb6cd94032df1.01532919'){//Permiso de Excavación
                $path = "licencia_construccion.php";
            }else if($tipo_lic == 'id_5eb6cd94033509.04158568'){//Constancia de Número Oficial
                $path = "const_numero-oficial.php";
            }else if($tipo_lic == 'id_5eb6d2b728d801.27882313'){//Licencia de Construcción menor a 45m2
                $path = "licencia_construccion.php";
            }else if($tipo_lic == 'id_5eb6e201276057.49768157'){//Terminación de Obra
                $path = "terminacion_obra.php";
            }else{  
            }
            $res = $path;
        }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
    }

    public function orden_pago_get($id){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
            $query0 = "SELECT *
                    FROM licencias
                    WHERE public_id = ? ";
            $query0 = $this->db->query($query0, array($id));
            $query = "SELECT *
                    FROM lic_datos_propietario
                    WHERE id_licencia = ? ";
            $query = $this->db->query($query, array($id));
            $query1 = "SELECT *
                    FROM lic_datos_predio
                    WHERE id_licencia = ? ";
            $query1 = $this->db->query($query1, array($id));
            $query2 = "SELECT licencias.estatus, r_licencias_derechos.derecho, r_licencias_derechos.precio,
                        r_licencias_derechos.unidad, r_licencias_derechos.cantidad, r_licencias_derechos.total,
                        r_licencias_derechos.total_gral
                        FROM licencias INNER JOIN r_licencias_derechos ON licencias.public_id = r_licencias_derechos.id_licencia
                        WHERE licencias.public_id = ? ";
            $query2 = $this->db->query($query2, array($id));
            session_start();
            $_SESSION['licencia'] = $query0->result_array();
            $_SESSION['predio'] = $query1->result_array();
            $_SESSION['propietario'] = $query->result_array();
            $_SESSION['derechos'] = $query2->result_array();
            $res = $id;
        }else {
          $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
    }

    public function arancel_pago_get($id){
        $this->load->library('Authorization_Token');
        $validation_token = $this->authorization_token->validateToken();
        if (!empty($validation_token) AND $validation_token['status'] === TRUE) {
                        //consulta para saber el numero de grupos 
            $query = "SELECT grupos_licencias.nombre, grupos_licencias.public_id
                        FROM licencias INNER JOIN lista_tramites ON licencias.id_tramite = lista_tramites.public_id 
                        LEFT JOIN r_tramite_grupo ON lista_tramites.public_id = r_tramite_grupo.id_tramite 
                        INNER JOIN grupos_licencias ON r_tramite_grupo.id_grupo = grupos_licencias.public_id
                        WHERE licencias.public_id = ?";
            $query  = $this->db->query($query,array($id));
            $result = $query->result_array();
            $num_groups = count($result);
                
            session_start();
            for($i = 0; $i < $num_groups ; $i++){
                $iter = $result[$i];
                $id_grupo = $iter["public_id"];
                if($id_grupo === 'id_5ea3457c2e1668.57636641') {
                    $query = "SELECT * FROM lic_datos_propietario WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea3457c2e33e3.36891982')  {
                    $query = "SELECT * FROM lic_datos_predio WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea352cc935558.63022662')  {
                    $query = "SELECT * FROM lic_uso_de_suelo WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea352cc935558.63022447')  {
                    $query = "SELECT * FROM lic_constancia_servicios WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5eb6cd94031ea2.25736133')  {
                    $query = "SELECT * FROM lic_const_estru WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5eb6cd94031ea2.257447514') {
                    $query = "SELECT * FROM lic_constancia_construccion WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ee7d14e6e4a79.0155874572'){ 
                    $query = "SELECT * FROM lic_autorizacion_ocupacion WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea3457c2e3ba5.81867615')  {
                    $query = "SELECT * FROM lic_descripcion_obra WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array(); 
                }else if($id_grupo === 'id_5ee7d14e6e4a79.01524533')  {
                    $query = "SELECT * FROM lic_licencia_para WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ee7d14e6e4a79.01524005')  {
                    $query = "SELECT * FROM lic_permiso_para WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    $_SESSION[$id_grupo] = $query->result_array();
                }else if($id_grupo === 'id_5ea3457c2e3ba5.818335475')  {
                    $query  = "SELECT * FROM lic_vigencia WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    //$_SESSION[$id_grupo] = $query->result_array();
                    $_SESSION[$id_grupo] = $this->setting_dates($query->result_array());
                }else if($id_grupo === 'id_5ea3457c2e3ba5.818335578')  {
                    $query  = "SELECT * FROM lic_antecedentes WHERE id_licencia = ?";
                    $query  = $this->db->query($query,array($id));
                    //$_SESSION[$id_grupo] = $query->result_array();
                    $_SESSION[$id_grupo] = $this->setting_dates($query->result_array());
                }else {
                    $res = "no anexado o incorrecto";
                }
            }
            $query3 = "SELECT licencias.*, lista_tramites.tramite
                        FROM  licencias
                        LEFT JOIN lista_tramites ON licencias.id_tramite = lista_tramites.public_id
                        WHERE licencias.public_id = ?";
            $query3  = $this->db->query($query3,array($id));
            $result = $query3->result_array();
            $datos_lic = $result[0];

            $tipo_lic = $datos_lic['id_tramite'];
            $w_dro = $datos_lic['is_per'];
            if($w_dro == 1){
                $user = $datos_lic['id_usuario'];
                $dro_query = "SELECT usuarios.nombre, r_datos_peritos.no_registro, r_datos_usuarios.calle, 
                r_datos_usuarios.colonia, r_datos_usuarios.celular, colegio.nombre AS colegio
                FROM usuarios 
                INNER JOIN r_datos_peritos ON usuarios.public_id = r_datos_peritos.id_usuario
                INNER JOIN r_datos_usuarios ON usuarios.public_id = r_datos_usuarios.id_usuario
                LEFT JOIN usuarios colegio ON r_datos_peritos.id_colegio = colegio.public_id
                WHERE usuarios.public_id = ?";
                $dro_query  = $this->db->query($dro_query,array($user));
                $dro_result = $dro_query->result_array();
                $_SESSION['DRO'] = $dro_result[0];
            }else{
                $_SESSION['DRO'] = "";
            }

            $query = "SELECT * FROM lic_datos_propietario WHERE id_licencia = ?";
            $query  = $this->db->query($query,array($id));
            $prop = $query->result_array();

            $query3 = "SELECT * FROM lic_datos_predio WHERE id_licencia = ?";
            $query3 = $this->db->query($query3,array($id));
            $predio = $query3->result_array();

            $query2 = "SELECT licencias.id_licencia, licencias.estatus, aranceles.nomas AS arancel,aranceles.unidad, aranceles.precio,
                            r_licencias_arancel.total, r_licencias_arancel.cantidad
                        FROM licencias 
                        INNER JOIN r_licencias_arancel ON licencias.public_id = r_licencias_arancel.id_licencia 
                        INNER JOIN aranceles ON r_licencias_arancel.id_arancel = aranceles.idar2
                        WHERE licencias.public_id = ? 
                        GROUP BY r_licencias_arancel.id_arancel";
            $query2 = $this->db->query($query2, array($id));

            $query_corres = "SELECT r_licencias_arancel_corres.cantidad, r_licencias_arancel_corres.total, usuarios.nombre, lista_aranceles_espe.*
                FROM r_licencias_arancel_corres
                INNER JOIN r_licencias_corres ON r_licencias_arancel_corres.id_licencia = r_licencias_corres.id_licencia
                INNER JOIN usuarios ON r_licencias_corres.id_usuario = usuarios.public_id
                INNER JOIN lista_aranceles_espe ON r_licencias_arancel_corres.id_arancel = lista_aranceles_espe.public_id
                WHERE r_licencias_arancel_corres.id_licencia = ? ";
            $query_corres = $this->db->query($query_corres, array($id));
            $dro_query = "SELECT usuarios.nombre, r_datos_peritos.no_registro, r_datos_usuarios.calle, 
                r_datos_usuarios.colonia, r_datos_usuarios.celular, colegio.nombre AS colegio
                FROM usuarios 
                INNER JOIN r_datos_peritos ON usuarios.public_id = r_datos_peritos.id_usuario
                INNER JOIN r_datos_usuarios ON usuarios.public_id = r_datos_usuarios.id_usuario
                LEFT JOIN usuarios colegio ON r_datos_peritos.id_colegio = colegio.public_id
                WHERE usuarios.public_id = ?";
            $dro_query  = $this->db->query($dro_query,array($user));
            $dro_result = $dro_query->result_array();
            
            $_SESSION['licencia'] = $datos_lic;
            $_SESSION['PROPIETARIO'] = $prop[0];
            $_SESSION['PREDIO'] = $predio[0];
            $_SESSION['ARANCEL'] = $query2->result_array();
            $_SESSION['CORRES'] = $query_corres->result_array();
            $res = $id;
        }else {
            $res = $validation_token['message'].', Error : '. REST_Controller::HTTP_NOT_FOUND;
        }
        $this->response($res);
    }

    public function setting_dates($array){
        if (isset($array[0]) && !empty($array[0])){
            foreach ($array[0] as $key => $value) {
                if (strpos($key, 'fecha') !== false) {
                    $predate   = strtotime($value);
                    $newformat = date('d-M-Y', $predate);
                    $array[0][$key] = $newformat;
                }
            }
            return $array;
        }else {
            return '';
        }
        
    }
}