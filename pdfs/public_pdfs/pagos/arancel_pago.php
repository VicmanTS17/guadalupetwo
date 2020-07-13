<?php
require("../../../lib/mpdf60/mpdf.php");
session_start();
setlocale(LC_MONETARY, 'es_MX');
$propietario = $_SESSION["PROPIETARIO"];
$predio = $_SESSION["PREDIO"];
$derechos = $_SESSION["ARANCEL"];
$length = count($derechos);
$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
$mes = $meses[date('n')-1];
$ano=date('Y');
$dia=date('d');
$fecha = $dia.'/'.$mes.'/'.$ano;
$folio = $derechos[0];
$html='
<div class="container">
  <div class="row">
    <div class="s4">
      <img src="https://guadalupe.licenciaszac.net/imagenes/LOGO1.png" width="250px">
    </div>
    <div class="s4">
      <div class="titulo bold center">
        PAGO DE ARANCELES
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
        <b>'.$folio['id_licencia'].'</b>
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
      <div class="s7"><b>'.$propietario['nombre'].'</b></div>
    </div>
    <div class="row">
      <div class="s2"><b>CALLE:</b></div>
      <div class="s8"><b>'.$predio['calle'].'</b></div>
    </div>
    <div class="row">
      <div class="s2"><b>COLONIA:</b></div>
      <div class="s8"><b>'.$predio['colonia'].'</b></div>
    </div>
    <div class="row">
      <div class="s2"><b>ESTADO:</b></div>
      <div class="s8"> ZACATECAS</div>
    </div>
  </div>
  <div class="row titterN center bold"> REFERENCIA DEL PAGO </div>
  <table width="100%">
    <thead>
      <tr class="medio">
        <td class="center border" width="30%"> <b>ARANCEL</b> </td>
        <td class="center border"> <b>PRECIO</b> </td>
        <!--td class="center border"> <b>CORRESPONSAL</b> </td-->
        <td class="center border"> <b>UNIDAD</b> </td>
        <td class="center border"> <b>CANTIDAD</b> </td>
        <td class="center border"> <b>TOTAL</b> </td>
      </tr>
    </thead>
    <tbody>';
    $total_gen = 0;
    for ($i=0; $i < $length; $i++) {
          $data = $derechos[$i];
          $arancel= $data['arancel'];
          $costo = $data['precio'];
          $corr = $data['corresponsal'];
          $unidad = $data['unidad'];
          $cantidad = $data['cantidad'];
          $total = $costo * $cantidad;
          $total_gen += $total;
          $html.='
                <tr>
                  <td class="left tittleText border" width="30%"> '.$arancel.' </td>
                  <td class="center tittleText border">'.$costo.'</td>
                  <!--td class="center tittleText border">'.$corr.'</td-->
                  <td class="center tittleText border">'.$unidad.'</td>
                  <td class="center tittleText border">'.$cantidad.'</td>
                  <td class="center tittleText border">$'.number_format((float)$total, 2, '.', '').'</td>
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
    <div class="fr">Total a pagar<div class="br"><b>$'.$total_gen.'</b></div></div>
  </div>
  <div class="row titterN center bold"> DATOS DEL PREDIO</div>
  <div class="row">
    Propietario: <b>'.$propietario['nombre'].'</b><br>
    Ubicación: <b>'.$predio['calle'].', '.$predio['colonia'].'</b>
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

      <barcode code="'.$propietario['nombre'].' '.$predio['calle'].' '.$predio['colonia'].' " size="1.5" type="QR" error="M" class="barcode" />
    </div>

  </div>
</div>
';

$mpdf=new mPDF('c','Letter');
$stylesheet = file_get_contents('https://guadalupe.licenciaszac.net/pdfs/css_pdfs/style.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html,2);
$mpdf->OutPut("pago_arancel_".$folio.".pdf","I");
?>
