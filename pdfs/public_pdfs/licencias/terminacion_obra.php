<?php
require("../../../lib/mpdf60/mpdf.php");
session_start();
$licencia = $_SESSION["licencia"];
$dro = $_SESSION["DRO"];
$prop = $_SESSION["id_5ea3457c2e1668.57636641"];
$data_prop = $prop[0];
$ubic = $_SESSION["id_5ea3457c2e33e3.36891982"];
$data_predio = $ubic[0];
$antec = $_SESSION["id_5ea3457c2e3ba5.818335578"];
$data_antec = $antec[0];
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
  <div class="col s6 right">
    <div class="mm ">
      <img src="https://guadalupe.licenciaszac.net/imagenes/logoDU.png" width="250px">
    </div>
  </div>
</div>

<div class="row right">
<br>
<font size="2">OFICIO NÚMERO: <b>'.$folio.'</b><br>
EXPEDIENTE: PLC/DDUEMA<br>
FECHA: <b>'.$fecha.'</b><br></font>
<br>
<b> ASUNTO:</b> TERMINACIÓN DE OBRA
</div>

<div class="row">
  <br>
  A QUIEN CORRESPONDA:
</div>

<div class="row">
Por este conducto y en relación al permiso de construcción con número de folio <b>'.$data_antec['licencia_anterior'].'</b> propiedad de 
<b>'.$data_prop['nombre'].'</b>, para casa habitación de 2 niveles de 94.40 M2, 
ubicada en la <b>'.$data_predio['calle'].'</b>, de la Colonia y/o Fraccionamiento <b>“'.$data_predio['colonia'].'”</b>, 
en este Municipio de Guadalupe, Zacatecas.<br><br>
<br>
<br>

Este H. Ayuntamiento le comunica a usted, que de acuerdo a la inspección realizada por parte de esta Dirección se verificó que la construcción de la casa habitación está terminada al 100 %, por lo que se otorga la presente <b>CONSTANCIA DE TERMINACIÓN DE OBRA</b> de acuerdo a lo solicitado en el Artículo 115 del Reglamento General de la Ley de Construcción para el Estado y Municipio de Zacatecas.<br><br>
<br>
<br>

Se extiende la presente solicitud de la parte interesada para los usos y fines que a la misma convenga.<br><br>
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
