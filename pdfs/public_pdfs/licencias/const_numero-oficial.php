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
$ano = date('Y');
$dia = date('d');
$fecha = $dia.'/'.$mes.'/'.$ano;
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
FECHA: '.$fecha.'<br></font>
<br>
<b> ASUNTO:</b> CONSTANCIA DE NÚMERO OFICIAL
</div>

<div class="row">
  <br>
  A QUIEN CORRESPONDA:
</div>
<br><br>
<div class="row">
Previa verificación física a la nomenclatura en el Plano Catastral de esta Ciudad y el Plan de Desarrollo 
Urbano, en el Departamento de Permisos y Licencias de Construcción, adscrito a la Dirección de Desarrollo 
Urbano, Ecología y Medio Ambiente del Municipio, por medio de la presente se hace:   <br><br>
<br>
<div class="row center"><font size="5"> <b> C O N S T A R </b></div></font>
<br>
Que el predio propiedad <u><b>“C. '.$data_prop['nombre'].'”</b></u>, 
ubicado en Calle <u><b>“'.$data_predio['calle'].'”</b></u>, de la Colonia y/o Fraccionamiento <u><b>“'.$data_predio['colonia'].'”</b></u>, 
en Guadalupe, Zac., Lote <u><b>“'.$data_predio['lote'].'”</b></u>, Manzana <u><b>“'.$data_predio['manzana'].'”</b></u>.
<u>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   </u>
Se extiende la presente a solicitud de la parte interesada para los usos y fines que al mismo convenga, 
con la aclaración de que la presente <b>no ampara la propiedad</b>, únicamente el número oficial de la finca.<br><br>
<br>


  <div class="center">
    <b>A T E N T A M E N T E</b><br>
    <font size="2">Hagamos Historia</font>
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
<br>
';

$footer = '
<div class="rowfoot">
  <div class="s12">
    <div class="s4 left"> <font color="">
      Departamento de Permisos<br>
      Y Licencias para Construcción
    </div> </font>
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
$mpdf->SetWatermarkImage('https://guadalupe.licenciaszac.net/imagenes/LOGO1.png', 0.1, 'F');
$mpdf->showWatermarkImage = true;
$mpdf->SetHTMLFooter($footer);
$mpdf->OutPut($licencia["tramite"]."_".$folio.".pdf","I");
?>
