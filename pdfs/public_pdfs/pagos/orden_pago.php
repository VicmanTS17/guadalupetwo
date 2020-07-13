<?php
require("../../../lib/mpdf60/mpdf.php");
session_start();
setlocale(LC_MONETARY, 'es_MX');
$licencia = $_SESSION["licencia"];
$derechos = $_SESSION["derechos"];
$predio = $_SESSION["predio"];
$data_pred = $predio[0];
$propietario = $_SESSION["propietario"];
$data_prop = $propietario[0];
$length = count($derechos);
$lic = $licencia[0];
$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
$mes = $meses[date('n')-1];
$ano=date('Y');
$dia=date('d');
$fecha = $dia.'/'.$mes.'/'.$ano;
$folio = $lic['id_licencia'];
$html='
<div class="container">
  <div class="row">
    <div class="s4">
      <img src="https://guadalupe.licenciaszac.net/imagenes/LOGO1.png" width="250px">
    </div>
    <div class="s4">
      <div class="titulo bold center">
        MUNICIPIO DE GUADALUPE
      </div>
      <div class="titulo bold center">
        TESORERIA MUNICIPAL
      </div>
      <div class="titulo center">
        DIRECCIÓN DE INGRESOS
      </div>
    </div>
    <div class="s4 center">
      <div class="row border">
        <div class="s6 medio center">
          FOLIO
        </div>
        <div class="s6 center">
          <b>'.$folio.'</b>
        </div>
      </div>
      <div class="row border">
        <div class="row">
          <div class="s6 medio center">
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
  <!-- div class="row ml">
    <b>AREA QUE EMITE:</b> '.$area.'
  </div -->
  <div class="row titterN center"> <b>DATOS DEL CONTRIBUYENTE</b> </div>
  <div class="row ml">
    <div class="row">
      <div class="s4"><b>NOMBRE(S) O RAZON SOCIAL:</b></div>
      <div class="s7"><b>'.$data_prop['nombre'].'</b></div>
    </div>
    <div class="row">
      <div class="s2"><b>CALLE:</b></div>
      <div class="s8"><b>'.$data_prop['calle'].'</b></div>
    </div>
    <div class="row">
      <div class="s2"><b>COLONIA:</b></div>
      <div class="s8"><b>'.$data_prop['colonia'].'</b></div>
    </div>
    <div class="row">
      <div class="s2"><b>ESTADO:</b></div>
      <div class="s8"> ZACATECAS</div>
    </div>
  </div>
  <div class="row titterN center bold"> REFERENCIA DEL PAGO </div>
  <table width="100%" class="border2">
    <thead>
      <tr>
        <td class="center tittleText border2" width="50%"> <b>CONCEPTO</b> </td>
        <td class="center tittleText border2"> <b>UNIDAD</b> </td>
        <td class="center tittleText border2"> <b>IMPORTE</b> </td>
        <td class="center tittleText border2"> <b>CANTIDAD</b> </td>
        <td class="center tittleText border2"> <b>TOTAL</b> </td>
      </tr>
    </thead><b>
    <tbody>';
    $total_gen = 0;
    for ($i=0; $i < $length; $i++) {
          $data = $derechos[$i];
          $concepto = $data['derecho'];
          $cantidad = $data['cantidad'];
          $unidad = $data['unidad'];
          $costo = $data['precio'];
          $total = $costo*$cantidad;
          $total_gen += $total;
          $html.='
                <tr>
                  <td class="left tittleText border2" width="50%"> '.$concepto.' </td>
                  <td class="center tittleText border2"> '.$unidad.' </td>
                  <td class="center tittleText border2">  '.$costo.'</td>
                  <td class="center tittleText border2"> '.$cantidad.' </td>
                  <td class="center tittleText border2">'.number_format((float)$total, 2, '.', '').' </td>
                </tr>';
    }
    $html.='

    </tbody>
  </table>
  <div class="row ml">
    <b>EN BASE A LO DISPUESTO POR:</b><br>
    '.$sus.'
  </div>
  <br>
  <div class="row ml">
    <div class="right">Total a pagar<div class="br"><b>$'.money_format('%i', $total_gen).'</b></div></div>
  </div>
  <div class="row titterN center bold"> DATOS DEL PREDIO</div>
  <div class="row">
    Propietario: <b>'.$data_prop['nombre'].'</b><br>
    Ubicación: <b>'.$data_pred['calle'].', '.$data_pred['colonia'].'</b>
  </div>
  <div class="row titterN center bold"> DATOS DE VALIDACIÓN </div>
  <div class="row">
    <div class="mismo3 border center">
      <div class="row">
        REALIZÓ<br><br>'.$nombre.'
      </div>
      <div class="row border">
        (Nombre)<br><br><br>
      </div>
    </div>
    <div class="mismo3 border center">

      <barcode code="'.$data_prop['nombre'].' '.$data_pred['calle'].'  '.$data_pred['colonia'].'  " size="1.5" type="QR" error="M" class="barcode" />
    </div>

  </div>
</div>
';

$mpdf=new mPDF('c','Letter');
$stylesheet = file_get_contents('https://guadalupe.licenciaszac.net/pdfs/css_pdfs/style.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html,2);
$mpdf->OutPut("orden_pago_".$folio.".pdf","I");
?>
