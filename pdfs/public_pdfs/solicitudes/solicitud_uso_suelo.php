<?php
require("../../../lib/mpdf60/mpdf.php");
/*session_start();
$licencia = $_SESSION["licencia"];
$dro = $_SESSION["DRO"];
$prop = $_SESSION["DATOS DEL PROPIETARIO"];
$data_prop = $prop[0];
$ubic = $_SESSION["UBICACIÓN DEL PREDIO"];
$data_predio = $ubic[0];
$auto = $_SESSION["DATOS DE AUTORIZACIÓN DE USO Y OCUPACIÓN"];
$data_auto = $auto[0];
$folio = $licencia['id_licencia'];*/
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$mes = $meses[date('n')-1];
$ano=date('Y');
$dia=date('d');
$fecha = $dia.' de '.$mes.' del '.$ano;

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
<br>
<div class="row">
    <b>Con base en el Artículo 133 Código Urbano del Estado de Zacatecas, solicito se me expida la Constancia Municipal de Compatibilidad Urbanística, para el predio ubicado en:</b>
</div>
<br>
<div class="row center ">
    <b>'.$data_predio['calle'].', '.$data_predio['colonia'].', Guadalupe</b>
</div>
<br>
<div class="row">
    <b>Con las siguientes medidas y colindancias:</b>
</div>
<br>
<div class="row center ">
    <b>'.$data_predio['calle'].', '.$data_predio['colonia'].', Guadalupe</b>
</div>
<br>
<div class="row">
    <b>Uso actual del terreno:</b>
</div>
<br>
<div class="row center ">
    <b>'.$data_predio['calle'].', '.$data_predio['colonia'].', Guadalupe</b>
</div>
<br>
<div class="row">
    <b>Uso propuesto del terreno:</b>
</div>
<br>
<div class="row center ">
    <b>'.$data_predio['calle'].', '.$data_predio['colonia'].', Guadalupe</b>
</div>
<br><br>
<div class="row">
    <h3>DATOS DEL SOLICITANTE</h3>
    <div class="s6 center">
    <br><br><p><strong><b>'.$data_prop['nombre'].'</b></strong></p>
    <b>NOMBRE</b></div>
    <div class="s6 center">
    <br><br><p><strong><em>______________________________</em></strong></p>
    <b>FIRMA</b></div>
</div>
<div class="row">
    <b>DOMICILIO Y TELEFONO</b>
</div>
<br>
<div class="row center ">
    <b>'.$data_prop['calle'].', '.$data_prop['colonia'].', '.$data_prop['municipio'].'   '.$data_prop['colonia'].'</b>
</div>
<br>
<div class="row center">
    <b>'.$fecha.'</b>
</div>
<br>
<div class="row border">
    <h3>PARA USO EXCLUSIVO DE LA DIRECCIÓN</h3>
    Con fundamento en el Artículo 22, Fracción XXXVIII del Código Urbano del Estado de Zacatecas, se Expide Constancia Municipal de Compatibilidad Urbanística, para el predio descrito, cuyos:
    <br>
    <div class="row">
        <div class="s6">USO DE SUELO PERMITIDOS SON:</div>
        <div class="s6"></div>
    </div>
    <div class="row">
        <div class="s6">USO DE SUELO COMPATIBLES SON:</div>
        <div class="s6"></div>
    </div>
    <div class="row">
        <div class="s6">USO DE SUELO PROHIBÍDOS SON:</div>
        <div class="s6"></div>
    </div>
    <div class="row">
        <div class="s6">USO DE SUELO CONDICIONADOS SON:</div>
        <div class="s6"></div>
    </div>
    <div class="row">
        <div class="s6">EL USO DE SUELO PROPUESTO ES:</div>
        <div class="s6"></div>
    </div>
</div>
<div class="row">
    <div class="s3">
        <b>NOTAS:</b>
    </div>
    <div class="s9 nota">
        1.- El presente documento tiene vigencia de 1(un) año a partir de la fecha de expedición.
        <br>
        2.-No es constancia de propiedad, ni licencia de construcción y será nulo si se    carece de la parte complementaria el reverso.
        <br> 
        3.-El uso propuesto  estará sujeto, a las restricciones y observaciones que en su caso se mencionan al reverso de esta constancia. 
    </div>
</div>
';

$html2 ='
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
<br>
<div class="row">
    <b>CONSTANCIA MUNICIPAL DE COMPATIBILIDAD URBANÍSTICA No.</b> '.$data_prop['algo'].'
    <br><br>
    <div class="row center">
        <img src="https://guadalupe.licenciaszac.net/public_files/user_assets/'.$licencia["path_mapa"].'" height="250px"><br>
        <b>CROQUIS DE LOCALIZACIÓN DEL TERRENO</b>
    </div>
</div>
<br>
<div class="row center">
    <h2>PARA USO EXCLUSIVO DE LA DIRECCIÓN.</h2>
</div>
<br>
<div class="row">
    <h3>RESTRICCIONES:</h3>
</div>
<div class="row">
    <h3>OBSERVACIONES:</h3>
</div>
<br>
<div class="row center ">
    <b>
        A T E N T A M E N T E<br>
        DIRECTOR DE DESARROLLO URBANO,<br>
        ECOLOGÍA Y MEDIO AMBIENTE<br><br>
        ARQ. GUILLERMO GERARDO DUEÑAS GONZÁLEZ
        <br><br><br>
        '.$fecha.'
    </b>
</div>
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
$mpdf->SetWatermarkImage('https://guadalupe.licenciaszac.net/imagenes/LOGO1.png', 0.1, 'F');
$mpdf->showWatermarkImage = true;
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html,2);
$mpdf->WriteHTML('<pagebreak sheet-size="letter" />');
$mpdf->WriteHTML($html2);
$mpdf->SetHTMLFooter($footer);
$mpdf->OutPut($licencia["tramite"]."_".$folio.".pdf","I");
?>
