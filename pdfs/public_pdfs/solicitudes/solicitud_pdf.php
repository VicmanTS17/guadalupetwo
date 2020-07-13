<?php
require("../../../lib/mpdf60/mpdf.php");
session_start();
$licencia = $_SESSION["licencia"];
$prop = $_SESSION["id_5ea3457c2e1668.57636641"];
$data_prop = $prop[0];
$ubic = $_SESSION["id_5ea3457c2e33e3.36891982"];
$data_predio = $ubic[0];
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
<br><br><br>
<div class="row center">
  <h2>SOLICITUD de <b>'.$licencia["tramite"].'</b></h2>
</div>
<br><br><br>
<div class="row right">
  <b>
  FECHA: '.$fecha.'
  </b>
</div>
<br><br>
<div class="row">
  <b>
    Dr.GUILLERMO GERARDO DUEÑAS GONZALEZ:
  </b>
</div>
<br><br><br><br>
<div class="row sangria justify">
  Por medio del presente solicito el trámite de <b>'.$licencia["tramite"].'</b> para el predio ubicado en <b>'.$data_predio["calle"].', Colonia o Fraccionamiento '.$data_predio["colonia"].', Guadalupe, Zac. </b> propiedad de <b>'.$data_prop["nombre"].'</b>.
</div>
<br>
<br>
<div class="row sangria justify">
  Sin otro particular, agradezco de antemano su consideración.
</div>
<div class="row">
  <br><br><br><br><br><br>
  <div class="center">
    <b>A T E N T A M E N T E</b><br><br>

    <b>'.$data_prop["nombre"].'</b>
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
      ..................................................................................................................................................................................
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
