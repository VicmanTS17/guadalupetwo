<?php
require("../../../lib/mpdf60/mpdf.php");
session_start();
$licencia = $_SESSION["licencia"];
$dro = $_SESSION["DRO"];
$prop = $_SESSION["id_5ea3457c2e1668.57636641"];
$data_prop = $prop[0];
$ubic = $_SESSION["id_5ea3457c2e33e3.36891982"];
$data_predio = $ubic[0];
$des_obra = $_SESSION["id_5ea3457c2e3ba5.81867615"];
$data_desc= $des_obra[0];
$perm_para = $_SESSION["id_5ee7d14e6e4a79.01524533"];
$data_para = $perm_para[0];
$antec = $_SESSION["id_5ea3457c2e3ba5.818335578"];
$data_antec = $antec[0];
$vig = $_SESSION["id_5ea3457c2e3ba5.818335475"];
$data_vig = $vig[0];
$lic_para = $_SESSION['id_5ee7d14e6e4a79.01524005'];
$data_lic_par = $lic_para[0];

$mov_escombro    = "../../images_pdfs/vacio.png";
$mov_materiales  = "../../images_pdfs/vacio.png";
$inst_drenaje    = "../../images_pdfs/vacio.png";
$inversion_calle = "../../images_pdfs/vacio.png";

if($data_para['obra_nueva'] == 'true'){
  $tipo = 'Obra Nueva';
}else if($data_para['ampliacion'] == 'true'){
  $tipo = 'Ampliacion';
}else if($data_para['modificacion'] == 'true'){
  $tipo = 'Modificacion';
}else if($data_para['reparacion'] == 'true'){
  $tipo = 'Reparacion';
}else if($data_para['regularizacion'] == 'true'){
  $tipo = 'Regularizacion';
}else if($data_para['obra_minima'] == 'true'){
  $tipo = 'Obra Minima';
}else if($data_para['demolicion'] == 'true'){
  $tipo = 'Demolicion';
}else if($data_para['renovacion'] == 'true'){
  $tipo = 'Renovacion';
}else if($data_para['bardeo'] == 'true'){
  $tipo = 'Dardeo';
}else{
  $tipo = "";
}

if($data_lic_par['mov_escombro']    == '1'){
  $mov_escombro    = "../../images_pdfs/palomita.png";
}
if($data_lic_par['mov_materiales']  == '1'){
  $mov_materiales  = "../../images_pdfs/palomita.png";
}
if($data_lic_par['inst_drenaje']    == '1'){
  $inst_drenaje    = "../../images_pdfs/palomita.png";
}
if($data_lic_par['inversion_calle'] == '1'){
  $inversion_calle = "../../images_pdfs/palomita.png";
}
$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
$mes = $meses[date('n')-1];
$ano=date('Y');
$dia=date('d');
$fecha = $dia.'/'.$mes.'/'.$ano;
$folio = $licencia['id_licencia'];
$html='
<div class="row">
  <div class="col s6 left">
    <div class="mm">
      <img src="https://guadalupe.licenciaszac.net/imagenes/LOGO1.png" width="250px">
    </div>
  </div>
  <div class="col s6 center">
    <div class="row medio">
      DESARROLLO URBANO ECOLOGIA<br>Y  AMBIENTE
    </div>
    <div class="row border">
      <div class="s6 center">
        FOLIO
      </div>
      <div class="s6 center">
        <b>No. '.$folio.'</b>
      </div>
    </div>
    <div class="row border">
      <div class="row">
        <div class="s6 center">
        FECHA
        </div>
        <div class="s6 center">
          <div class="row">
            <b>'.$fecha.'</b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row center">
  <h2>L I C E N C I A</h2>
</div>
<div class="row">
  <div class="s3">
    LICENCIA PARA:
  </div>
  <div class="s5">
    <b>'.$licencia["tramite"].'</b>
  </div>
</div>
<br>
';
if($tipo == ""){
$html .='
<div class="row">
  <div class="s3">
    TIPO DE LICENCIA:
  </div>
  <div class="s5">
    <b>'.$tipo.'</b>
  </div>
</div>
<br>'
;
}
$html .= '<div class="row">
  <div class="s2 center ">
    VIGENCIA
  </div>
  <div class="s4">
    Fecha de Inicio: <b>'.$data_vig['fecha_inicio'].'</b>
  </div>
  <div class="s4">
    Fecha de Expiración: <b>'.$data_vig['fecha_expira'].'</b>
  </div>
  <div class="s2">
    Dias: <b>'.$data_vig['vigencia'].'</b>
  </div>
</div>
<br>
<div class="row">
  <div class="s8">
    <div class="row ">
      <div class="s12  center">
        U B I C A C I Ó N
        <hr>
      </div>
      <div class="row">
        <div class="s4">
          CALLE
        </div>
        <div class="s7">
        <b>'.$data_predio["calle"].'</b>
        </div>
      </div>
      <div class="row">
        <div class="s4">
          CLAVE CATASTRAL
        </div>
        <div class="s7">
          <b>'.$data_predio["clave_catastral"].'</b>
        </div>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="s12  center">
        P R O P I E T A R I O
      </div>
      <div class="row">
        <div class="s4">
          NOMBRE
        </div>
        <div class="s7">
        <b>'.$data_prop['nombre'].'</b>
        </div>
      </div>
      <div class="row">
        <div class="s4">
          DOMICILIO
        </div>
        <div class="s7">
        <b>'.$data_prop['calle'].'</b>
        </div>
      </div>
      <div class="row">
        <div class="s4">
          COLONIA
        </div>
        <div class="s7">
        <b>'.$data_prop['colonia'].'</b>
        </div>
      </div>
      <div class="row">
        <div class="s4">
          R.F.C.
        </div>
        <div class="s7">
        <b>'.$data_prop['rfc'].'</b>
        </div>
      </div>
    </div>
    <br>';
if(isset($dro)){
$html .='
    <div class="row">
      <div class="s12  center">
        D A T O S  D E L  P E R I T O
      </div>
      <div class="row">
        <div class="s4">
          NOMBRE
        </div>
        <div class="s8">
          <b>'.$dro['nombre'].'</b>
        </div>
      </div>
      <div class="row">
        <div class="s4">
          DOMICILIO
        </div>
        <div class="s8">
          <b>'.$dro['calle'].'</b> <b>'.$dro['colonia'].'</b>
        </div>
      </div>
      <div class="row">
        <div class="s4">
          Nº DE REG.
        </div>
        <div class="s8">
        <b>'.$dro['no_registro'].'</b>
        </div>
      </div>
      <!--div class="row">
        <div class="s4">
          R. F. C. DE D. R. O.
        </div>
        <div class="s8">
        </div>
      </div -->
      <div class="row">
        <div class="s4">
          TELEFONO
        </div>
        <div class="s8">
          <b>'.$dro['celular'].'</b>
        </div>
      </div>
    </div>';
}
$html .='<br>
    <div class="row">
      <div class="s12  center">
        DESCRIPCION DE LA OBRA
      </div>
      <div class="row">
        <b>'.$data_desc['desc_constr'].'</b>
      </div>
      <div class="row">
        <div class="s12 nota center">
          NO SE PERMITE  CONSTRUCCIÓN SOBRE MARQUESINA, FUERA DE  ALINEAMIENTO
        </div>
      </div>
      <div class="row">
        <div class="s12 nota center">
           NI MODIFICACIÓN EN BANQUETA SIN AUTORIZACION DE ESTA DIRECCIÓN.
        </div>
      </div>
      <div class="row center">
        <img src="https://guadalupe.licenciaszac.net/public_files/user_assets/'.$licencia["path_mapa"].'" height="250px">
      </div>
    </div>
  </div>
  <div class="s42 bl">
    <div class="row ml">
      <div class="s12  center">
        A N T E C E D E N T E S
        <hr>
      </div>
      <div class="s12">
        Nº LIC. ANTERIOR <b>'.$data_antec['licencia_anterior'].'</b>
      </div>
      <div class="s12">
        FECHA <b>'.$data_antec['fecha_licencia'].'</b>
      </div>
      <div class="s12">
        CONSTRUCCIÓN ACTUAL <b>'.$data_antec['construccion_actual'].'</b>
      </div>
    </div>
    <!-- div class="row ml">
      <div class="s12">
        SUP. DE TERRENO <b>'.$da24['dato'].'</b>
      </div>
      <div class="s12">
        CONSTRUIDA P. BAJA <b>'.$da20['dato'].'</b>
      </div>
      <div class="s12">
        CUBIERTA POR AMPL. <b>'.$da19['dato'].'</b>
      </div>
      <div class="s12">
        ESTACIONAMIENTO
      </div>
      <div class="s12">
        NO CONSTRUIDA
      </div>
    </div -->
    <br>
    <div class="row ml">
      <div class="s12  center">
        TIENE PERMISO PARA <b>'.$da23['dato'].'</b>
      </div>
      <div class="s6 left">
        MOV.ESCOMBRO
      </div>
      <div class="s6 left">
        <img src="'.$mov_escombro.'" height="10px">
      </div>
      <div class="s6 left">
        INST.DE DRENAJE 
      </div>
      <div class="s6 left">
        <img src="'.$inst_drenaje.'" height="10px">
      </div>
      <div class="s6 left">
        MOV.MATERIALES
      </div>
      <div class="s6 left">
        <img src="'.$mov_materiales.'" height="10px">
      </div>
      <div class="s6 left">
        INVASION CALLE
      </div>
      <div class="s6 left">
        <img src="'.$inversion_calle.'" height="10px">
      </div>
    </div>
    <br>
    <div class="row">
      <div class="s12  center">
        SUP. POR CONSTRUIR<br>
        O REGULARIZAR
      </div>
      <div class="row ml">
      <div class="s4">
        Niveles
      </div>
      <div class="s8">
        Superficie en m2
      </div>
    </div>
      <div class="row ml">
        <div class="s4">
          -3
        </div>
        <div class="s8"> 
          <b>'.number_format($data_desc['n3'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          -2
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['n2'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          -1
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['n1'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          P.B.
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['pb'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          1
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['p1'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          2
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['p2'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          3
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['p3'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          4
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['p4'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          5
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['p5'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          6
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['p6'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          7
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['p7'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          8
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['p8'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          9
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['p9'], 2, '.', ',').'</b>
        </div>
      </div>
      <div class="row ml">
        <div class="s4">
          TOTAL
        </div>
        <div class="s8">
          <b>'.number_format($data_desc['suma_sup'], 2, '.', ',').'</b>
      </div>
      <br><br>
    </div>
    <br>
  </div>
</div>
<table width="100%" class="center" style="font-size: 12px;  background-image: url(http://www.drosmexico.com/celaya/images/Fondo/fondo_footer.jpg);
background-repeat: no-repeat;">
<thead>
  <tr>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
        <br><br><br><br><p><strong><em>______________________________</em></strong></p>
        <b><?php echo $dire?></b><br>
        <em>Jefe de Departamento de Permisos y Licencias</em></p>
    </td>';
if(isset($dro)){
  $html .= '
    <td>
      <br><br><br><br><p><strong><em>______________________________</em></strong></p>
      <b>'.$dro['nombre'].'</b><br>
      <em>Director Responsable de Obra</em></p>
    </td>';
}else{
  $html .= '
    <td>
      <br><br><br><br><p><strong><em>______________________________</em></strong></p>
      <b>'.$data_prop['nombre'].'</b><br>
      <em>Propietario</em></p>
    </td>';
}
$html .='<td>
        <br><br><br><br><p><strong><em>______________________________</em></strong></p>
        <b>Arq. Guillermo Gerardo Dueñas González</b><br>
        <em>Director de Desarrollo Urbano Ecologia y Medio Ambiente</em></p>
    </td>
  </tr>
</tbody>
</table>
';

$footer = '
<div class="rowfoot">
  <div class="s12">
    <div class="s4 left">
      Departamento de Permisos<br>
      Y Licencias para Construcción
    </div>
    <div class="s8 right">
      Av. Colegio Militar 96 Oriente<br>
      Col, Centro, C.P. 98600, Guadalupe, Zacatecas.<br>
      +52 (492) 923 5492 +25 (492) 923 5493 +52 (492) 923 5494
    </div>
    <div class="s12">
      ............................................................................................................................................................................
    </div>
  </div>
</div>
';

$mpdf=new mPDF('c','Letter');
$stylesheet = file_get_contents('https://guadalupe.licenciaszac.net/pdfs/css_pdfs/style.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html,2);
$mpdf->SetWatermarkImage('https://guadalupe.licenciaszac.net//imagenes/LOGO1.png', 0.1, 'F');
$mpdf->showWatermarkImage = true;
$mpdf->SetHTMLFooter($footer);
$mpdf->OutPut($licencia["tramite"]."_".$folio.".pdf","I");
?>
