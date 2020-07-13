<?php
require("../../../lib/mpdf60/mpdf.php");
session_start();
$licencia = $_SESSION["licencia"];
$dro = $_SESSION["DRO"];
$prop = $_SESSION["DATOS DEL PROPIETARIO"];
$data_prop = $prop[0];
$ubic = $_SESSION["UBICACIÓN DEL PREDIO"];
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

<div class="row right">
    <font size="2">OFICIO NÚMERO:<b>'.$folio.'</b><br>
    FECHA: <b>'.$fecha.'</b><br></font>
    ASUNTO: Constancia de compatibilidad<br> 
    urbanistíca y/o uso de suelo.
</div>

<div class="row">
  <br>
    <b>C. '.$data_prop['nombre'].'<br>
    P R E S E N T E.-</b>
</div>
<br>
<div class="row justify">
    En atención a su solicitud de fecha <b>20 de Marzo de 2020</b> y con fundamento en el <b>Art. 125 del Código Urbano del 
    Estado de Zacatecas</b>, se expide la Constancia de Compatibilidad Urbanística para un predio 
    ubicado en <b>Calle Fátima No. 4</b> Fraccionamiento <b>Las Quintas</b> del Municipio de Guadalupe, Zacatecas, 
    que de acuerdo al Programa de Desarrollo Urbano Zacatecas – Guadalupe   2016 - 2040, se encuentra localizado en 
    una zona donde el uso de suelo es <b>Habitacional densidad alta;  Compatible con vivienda densidad alta, 
    vivienda densidad media, vivienda densidad baja</b>. 
    Condicionado con vivienda campestre, industria ligera, oficinas, educación, cultura, salud, asistencia, 
    comercio departamental o especializado, comercio al detalle, comunicación, transporte, mantenimiento, seguridad, 
    recreación y deporte, preparación y venta de alimentos, turismo, servicios de aseo y limpieza, culto, investigación, 
    infraestructura y elementos ornamentales.
    Prohibido con industria pesada, industria media, agroindustria, abasto, trabajo zootécnico, convivencia y 
    espectáculos, reclusión, especial, inhumación, cremación, actividades extractivas, depósito de desechos, 
    agropecuario.
    Por lo que su proyecto es condicionado.
</div>
<br><br>
<div class="row justify">
    <b>TRÁMITES Y/O  PERMISOS:</b> Para efecto de ocupación, construcción, remodelación o ampliación debe de obtener constancia 
    de compatibilidad urbanística, alineamiento, número oficial y permiso de construcción emitida por  la Dirección de 
    Desarrollo Urbano, Ecología y Medio Ambiente Municipal, factibilidad de dotación de servicios de agua potable y 
    alcantarillado ante la JIAPAZ, y dotación de los servicios de Energía Electica ante la C.F.E. Autorización de 
    la S.S.A. y Plan Interno de Protección Civil.
</div>
<br><br>
<div class="row justify">
    <b>Debe contar con trampa para desechos propios del establecimiento, la cual será supervisada periódicamente por la JIAPAZ.</b>
    <br><br>
    <b>CONDICIONADO:</b> Deberá contar con un cajón de estacionamiento, dentro de su propiedad por cada 30 m² de superficie construida y 
    no se afectará el flujo vehicular de las vialidades circundantes.
    <br><br>
    <b>NOTA:</b> El presente documento tiene de vigencia un año.<br>
    No es Constancia de Propiedad, ni Licencia de Construcción.<br>
    El uso de suelo propuesto estará sujeto al cumplimiento de las restricciones que en su caso se mencionan.
    <br><br>

    Sin más por el momento reciba un cordial saludo.
    <br><br>
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
$mpdf->SetWatermarkImage('https://guadalupe.licenciaszac.net/imagenes/LOGO1.png', 0.1, 'F');
$mpdf->showWatermarkImage = true;
$mpdf->SetHTMLFooter($footer);
$mpdf->OutPut($licencia["tramite"]."_".$folio.".pdf","I");
?>
