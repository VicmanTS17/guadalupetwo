<?php
require("../../../lib/mpdf60/mpdf.php");
session_start();
$licencia = $_SESSION["licencia"];
$dro = $_SESSION["DRO"];
$prop = $_SESSION["id_5ea3457c2e1668.57636641"];
$data_prop = $prop[0];
$ubic = $_SESSION["id_5ea3457c2e33e3.36891982"];
$data_predio = $ubic[0];
$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
$mes = $meses[date('n')-1];
$ano=date('Y');
$dia=date('d');
if($licencia['fecha_autorizacion'] != ""){
  $fecha = $licencia['fecha_autorizacion'];
}else{
  $fecha = $dia.'/'.$mes.'/'.$ano;
}
$folio = $licencia['id_licencia'];
$html='
<div class="row">
  <div class="col s6 left">
    <div class="mm">
      <img src="https://guadalupe.licenciaszac.net/imagenes/LOGO1.png" width="250px">
    </div>
  </div>
  <div class="col s6 right">
    <div class="mm ">
      <img src="https://guadalupe.licenciaszac.net/imagenes/logoDU.png" width="250px">
    </div>
  </div>
</div>

<div class="row right">
<br>
<font size="2">OFICIO NÚMERO:<b>'.$folio.'</b><br>
EXPEDIENTE: PLC/DDUEMA<br>
FECHA: <b>'.$fecha.'</b><br></font>
<br><br>
<b> ASUNTO:</b> Permiso de Excavación
</div>
<br><br>
<div class="row">
  <br>
  <b>C. '.$data_prop['nombre'].'<br>
  P R E S E N T E.-</b>
</div>
<br><br>
<br>
<div class="row sangria justify">
  Por medio del presente este H. Ayuntamiento a través de la oficina de Permisos y Licencias, de la Dirección de Desarrollo Urbano, Ecología y Medio Ambiente de este Municipio, tiene a bien autorizar a Usted, el permiso para que se realicen trabajos de excavación para toma domiciliaria con instalación de tubería de agua potable y/o drenaje, en <b>'.$data_predio['calle'].',</b> de la Colonia <b><b>'.$data_predio['colonia'].'</b>,</b> de esta ciudad de Guadalupe, Zac. <br><br>
</div>
<br>
<div class="row sangria justify">
  Debiendo de colocar señalamientos en la excavación, hacer los trabajos en etapas para dejar el libre paso de vehículos, tener el apoyo y visto bueno de la Dirección de Tránsito y Vialidad del Estado para la libre circulación; así mismo deberá de reparar el piso dañado en las mismas condiciones al existente, con el entendido que de no acatar dicha disposición se hará acreedor a la sanción correspondiente de acuerdo a la Ley de Construcción vigente en el Estado de Zacatecas. <br><br>
</div>
<br>
<div class="row sangria justify">
  Se extiende la presente a solicitud de la parte interesada para los usos y fines que al mismo convenga.
</div>
<br><br>
<br>
<br>
<br><br>
  <div class="center">
    <b>A T E N T A M E N T E</b><br>
    <font size="2">Hagamos Historia</font>
    <br><br>
    <br>
    <br>
    <br>
    <br>
  </div>
  <div class="row center">
  <b>DR. GUILLERMO GERARDO DUEÑAS GONZÁLEZ.<br></b>
Director de Desarrollo Urbano, Ecología y Medio Ambiente.
  </div>
</div>
';

$footer = '
<div class="rowfoot">
  <div class="s12">
    <div class="s4 left" style="color:#28c028">
      <b>Departamento de Permisos<br>
      Y Licencias para Construcción</b>
    </div>
    <div class="s8 right">
      <font color="#9a9b9a">
        Av. Colegio Militar 96 Oriente<br>
        Col, Centro, C.P. 98600, Guadalupe, Zacatecas.<br>
      </font>
      <font color="#767775">
        +52 (492) 923 5492 +25 (492) 923 5493 +52 (492) 923 5494
      </font>
    </div>
    <div class="s12">
      ...................................................................................................................................................................................................................
    </div>
  </div>
</div>
';

$mpdf=new mPDF('c','Letter');
$stylesheet = file_get_contents('https://guadalupe.licenciaszac.net/pdfs/css_pdfs/style.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html,2);
$mpdf->SetWatermarkImage('https://guadalupe.licenciaszac.net/imagenes/LOGO1.png', 0.1, 'F');
$mpdf->showWatermarkImage = true;
$mpdf->SetHTMLFooter($footer);
$mpdf->OutPut($licencia["tramite"]."_".$folio.".pdf","I");
?>
