<?php
require("../../../lib/mpdf60/mpdf.php");
session_start();
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
<font size="2">OFICIO NÚMERO:4957<br>
EXPEDIENTE: PLC/DDUEMA<br>
FECHA: 25 DE OCTUBRE DE 2019<br></font>
ASUNTO: CONSTANCIA
DE NÚMERO OFICIAL
Y ALINEAMIENTO.
</div>

<div class="row">
  <br>
    A QUIEN CORRESPONDA:
</div>
<br>
<div class="row">

  Previa verificación física a la nomenclatura en el Plano Catastral de esta Ciudad, y el Plan de Desarrollo Urbano, en el Departamento de Permisos y Licencias de Construcción, de la Dirección de Desarrollo Urbano, Ecología y Medio Ambiente del Municipio, por medio de la presente hace: <br><br>
  <br>
  <div class="row center"><font size="5"> CONSTAR </div></font>

  Que el predio propiedad <u>“ABEL SALDAÑA FRANCISCO”</u>, ubicado en la Calle <u>“PRIVADA BRISAS DEL CAMPO”</u>,  le  corresponde  el  Número  Oficial  <u>“125”</u>, Lote  <u>“64”</u>,  Manzana <u>“1”</u> de la Colonia y/o Fraccionamiento <u>“BRISAS DEL CAMPO”</u>, de esta ciudad de Guadalupe, Zac., con superficie  total  de  terreno  de <u>90.00 M²</u>, que se encuentra perfectamente alineado de acuerdo al Plan de Desarrollo Urbano Guadalupe-Zacatecas. <br><br>
  <br>
  <br>

  Se extiende la presente a solicitud de la parte interesada para los usos y fines que al mismo convenga.<br><br>
  <br>
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
';

$footer = '
<div class="row center">
<font size="3">C.c.p. Arq. Felipe  de Jesús Sandoval López.-Subdirector Técnico.
D’GGDG/A`FJSL/ C.P.lrb
</font>
</div>
';

$mpdf=new mPDF('c','Letter');
$stylesheet = file_get_contents('../css_pdfs/style.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html,2);
$mpdf->SetWatermarkImage('https://guadalupe.licenciaszac.net/imagenes/LOGO1.png', 0.1, 'F');
$mpdf->showWatermarkImage = true;
$mpdf->SetHTMLFooter($footer);
$mpdf->OutPut("Anuncios_Moviles".$folio.".pdf","I");
?>
