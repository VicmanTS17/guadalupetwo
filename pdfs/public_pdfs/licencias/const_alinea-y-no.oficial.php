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
<div class="row center">
<b><font face="Times New Roman" size="2">“2019, CENTENARIO DE LA PROMULGACIÓN DE LA LEY ORGÁNICA DEL MUNICIPIO LIBRE DEL ESTADO”</font></b>
</div>

<div class="row right">
<br>
<font size="2">OFICIO NÚMERO:<b>'.$folio.'</b><br>
EXPEDIENTE: PLC/DDUEMA<br>
FECHA: <b>'.$fecha.'</b><br></font>
<br><br>
ASUNTO: CONSTANCIA
DE NÚMERO OFICIAL
Y ALINEAMIENTO.
</div>
<br><br>
<div class="row">
  <br>
    A QUIEN CORRESPONDA:
</div>
<br>
<div class="row sangria justify">
  Previa verificación física a la nomenclatura en el Plano Catastral de esta Ciudad, y el Plan de Desarrollo Urbano, en el Departamento de Permisos y Licencias de Construcción, de la Dirección de Desarrollo Urbano, Ecología y Medio Ambiente del Municipio, por medio de la presente hace: 
</div>
  <br><br>
  <br>
  <div class="row center"><font size="5"> C O N S T A R </div></font>
  <br>
  <br>
<div class="row sangria justify">  
  Que el predio propiedad <u><b>“'.$data_prop['nombre'].'”</b></u>, ubicado en <u><b>“'.$data_predio['calle'].'”</b></u>, Lote  <u><b>“'.$data_predio['lote'].'”</b></u>,  Manzana <u><b>“'.$data_predio['manzana'].'”</b></u> de la Colonia y/o Fraccionamiento <u><b>“'.$data_predio['colonia'].'”</b></u>,
  de esta ciudad de Guadalupe, Zac., con superficie  total  de  terreno  de <u><b>'.number_format(intval($data_predio['superficie_terreno']), 2, '.', ',').' m²</b></u>, que se encuentra perfectamente alineado de acuerdo al Plan de Desarrollo Urbano Guadalupe-Zacatecas. <br><br>
</div>
  <br>
  <br>
<div class="row sangria justify">
  Se extiende la presente a solicitud de la parte interesada para los usos y fines que al mismo convenga.<br><br>
</div>
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
