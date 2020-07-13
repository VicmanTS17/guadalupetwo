angular.module('servidor')

.controller('licencias_ctrer_servidor', function($scope, $window, $http, $q){

const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

// get licencias get_tramites
$scope.req_error = false
$scope.get_licenias = function(){
  var token =  $window.sessionStorage.getItem('__token');
  var req = {
   'method': 'POST',
   'url': host+"Licencias/get_licencias_ser",
   'headers': {
     'Content-Type':  "application/json",
     'auth': token
   }
  }
  var request = $http(req);
  request.then(function(response){
    console.log(response.data);
    if (angular.equals(response.data.status, true)) {
      pagination_lic(response.data.data)
      $scope.licencias_list = response.data.data;
      tramitesmapa();
    }else {
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
      $scope.kind = 'warning';
    }
  }).catch('error '+console.error);
}

  function tramitesmapa (){
      $scope.ubicaciones=[];
      $scope.tramites=[];
      for(var i = 0; i < $scope.licencias_list.length; i++){
          $scope.ubicaciones.push({
            lat: parseFloat($scope.licencias_list[i].lat),
            lng: parseFloat($scope.licencias_list[i].lng),
          });
          $scope.tramites.push({
                  nombre : $scope.licencias_list[i].solicitante,
                   calle : $scope.licencias_list[i].calle,
                 colonia : $scope.licencias_list[i].colonia,
                 tramite : $scope.licencias_list[i].tramite,
          });
      }
      initMap();
    }

$scope.set_session = function(data){
    $window.sessionStorage.setItem('__lic_data', JSON.stringify(data));
  }

function get_objt_session(){
    $scope.lic_data = JSON.parse($window.sessionStorage.getItem('__lic_data'));
}

  $scope.get_re_docs_lic = function(){
    get_objt_session()
    var token = $window.sessionStorage.getItem('__token');
    var data  = JSON.parse($window.sessionStorage.getItem('__lic_data'));
    //console.log(data);

    var settings = {
         "url": host+"Licencias/get_re_docs_lic",
         "method": "POST",
         "headers": {
           "Content-Type": "application/json",
           'auth': token
        },
        "data" : data
    }
    var request = $http(settings);
    request.then(function(response){
    console.log(response.data)
    if (angular.equals(response.data.status, true)) {
      $scope.docs_list = response.data.data;
      console.log($scope.docs_list);
    }
    if (angular.equals(response.data.status, false)) {
      $scope.req_error = true; $scope.message = 'Problemas para guardar informacion, contacte con soporte.';
      $scope.kind = 'warning';
    }
    })
    .catch('error '+console.error);
  }

  function valida_files(data){
    var flag = false;
      //file not null
      if (!angular.isUndefined(data.file) || !angular.equals(data.file, '')){
        //file size
        if(data.file.filesize <= 5242880){
          //file type
          if(angular.equals(data.file.filetype, 'image/jpeg') || angular.equals(data.file.filetype, 'image/JPEG')
            || angular.equals(data.file.filetype, 'image/jpg') || angular.equals(data.file.filetype, 'image/JPG')
            || angular.equals(data.file.filetype, 'image/png') || angular.equals(data.file.filetype, 'image/PNG')
            || angular.equals(data.file.filetype, 'application/pdf') || angular.equals(data.file.filetype, 'application/PDF')
            || angular.equals(data.file.filetype, 'application/zip') || angular.equals(data.file.filetype, 'application/ZIP')
            || angular.equals(data.file.filetype, 'application/x-rar-compressed')){
              return !flag;
              // dwg
          }else if(angular.equals(data.file.filetype, '')){
            if(angular.equals(data.file.filename.slice(-4), '.dwg') || angular.equals(data.file.filename.slice(-4), '.DWG')){
              data.file.filetype = 'application/dwg'
              return !flag;
            // formato invalido
            }else{
              $scope.req_error = true; $scope.message = "Formatos permitidos. 'JPEG', 'JPG', 'PNG', 'PDF', 'ZIP', 'RAR', 'DWG'";
              $scope.kind = 'danger';
              return flag;
            }
          }else{
            $scope.req_error = true; $scope.message = "Formatos permitidos. 'JPEG', 'JPG', 'PNG', 'PDF', 'ZIP', 'RAR', 'DWG'";
            $scope.kind = 'danger';
            return flag;
          }
        // mayor de 5 megas
        }else{
          $scope.req_error = true; $scope.message = 'Tamaño máximo permitido 5 MB.';
          $scope.kind = 'warning';
          setTimeout(function() { $scope.req_error = false }, 3000);
          return flag;
        }
      // archivo indefinido
      }else{
        $scope.req_error = true; $scope.message = 'Debe de cargar archivo valido para continuar.';
        $scope.kind = 'warning';
        setTimeout(function() { $scope.req_error = false }, 3000);
        return flag;
      }
  }

  $scope.val_file = function(index) {
    if (!angular.equals(index, false) ){  var to_val = $scope.docs_list[index]; var is_lic = false;}
    else{ var to_val = $scope.lic_data; var is_lic = true;}

    var predata = JSON.parse($window.sessionStorage.getItem('__lic_data'));
    if(valida_files(to_val)){
      if(!is_lic){
        $scope.docs_list[index].bandera_carga = true;
        var data = { 'data' : $scope.docs_list[index], 'traid': predata.public_id , 'solid': predata.id_solicitante }
        send_file(data,false)//add requisitos
      }else{
        var data = { 'data' : $scope.lic_data, 'traid': predata.public_id , 'solid': predata.id_solicitante }
        send_file(data,true)//add licencia firmada
      }
      
    }else{
      $scope.req_error = true; $scope.message = 'Problemas para realizar carga contacte con soporte.';
      $scope.kind = 'warning';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }
    
  }

  function send_file(data,flag){
    if (!flag) var route = "Licencias/add_doc";
    else var route = "Licencias/add_lic_au";
    var token = $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host + route,
      "method": "POST",
      "headers": {
        "Content-Type": "application/json; charset=UTF-8",
        'auth': token
      },
      "data": data
    }
    var request = $http(settings);
    request.then(function(response) {
    // console.log(response);
    console.log(response.data);
      if(angular.equals(response.data.status, true) && angular.equals(response.data.data, 0)){
        $scope.req_error = true; $scope.message = 'Documento cargado';
        $scope.kind = 'success';
        if (!flag)  $scope.get_re_docs_lic();
        else $window.location = "#!licencias";
        setTimeout(function() { $scope.req_error = false }, 3000);
      }if(angular.equals(response.data.status, true) && angular.equals(response.data.data, 100)){
        $scope.req_error = true; $scope.message = 'Problemas para cargar archivo, contacte con soporte.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
      if(angular.equals(response.data.status, true) && angular.equals(response.data.data, 10)){
        $scope.req_error = true; $scope.message = 'Tamaño máximo permitido 5 MB.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
      if(angular.equals(response.data.status, true) && angular.equals(response.data.data, 1)){
        $scope.req_error = true; $scope.message = "Formatos permitidos. 'JPEG', 'JPG', 'PNG', 'PDF', 'ZIP', 'RAR', 'DWG'";
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
    })
    .catch('error ' + console.error);
  }

  $scope.save_est = function(flag){
    if (angular.equals(flag, 1)) { flag = 'Docs. con Observaciones'; }
    if (angular.equals(flag, 2)) { flag = 'Docs. Validados'; }
    if (angular.equals(flag, 3)) { flag = 'Pago Validado'; }
    if (angular.equals(flag, 4)) { flag = 'Licencia para Firma'; }
    if (angular.equals(flag, 5)) { flag = 'Firma Municipio'; }
    if (angular.equals(flag, 6)) { flag = 'Licencia Autorizada'; }

    var predata = JSON.parse($window.sessionStorage.getItem('__lic_data'));
    var data = {'flag' : flag, 'traid': predata.public_id}
    var token = $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host + "Licencias/save_est",
      "method": "POST",
      "headers": {
        "Content-Type": "application/json; charset=UTF-8",
        'auth': token
      },
      "data": data
    }
    var request = $http(settings);
    request.then(function(response) {
      console.log(response.data);
      if(angular.equals(response.data.status, true)){
        $scope.req_error = true; $scope.message = 'Estatus Actualizado';
        $scope.kind = 'success';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }if(angular.equals(response.data.status, false)){
        $scope.req_error = true; $scope.message = 'Problemas para actualiza información, contacte con soporte.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
    })
    .catch('error ' + console.error);
  }

  $scope.add_coment = function(index){
    var data  = $scope.docs_list[index]
    var token = $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host + "Licencias/add_comen_doc",
      "method": "POST",
      "headers": {
        "Content-Type": "application/json; charset=UTF-8",
        'auth': token
      },
      "data": data
    }
    var request = $http(settings);
    request.then(function(response) {
    // console.log(response);
    console.log(response.data);
      if(angular.equals(response.data.status, true)){
        $scope.req_error = true; $scope.message = 'Documento cargado';
        $scope.kind = 'success';
        $scope.get_re_docs_lic()
        setTimeout(function() { $scope.req_error = false }, 3000);
      }if(!angular.equals(response.data.status, true)){
        $scope.req_error = true; $scope.message = 'Problemas para guardar información, contacte con soporte.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
    })
    .catch('error ' + console.error);
  }

  $scope.orden = async function(id){
    try {
        let options = {
          headers:{
            "Content-type": "application/json",
            "auth": $window.sessionStorage.getItem('__token')
          }
        };
        let response = await $http.get(`${host}/Pdf/orden_pago/${id}`, options);
        console.log(response.data);
        if(response.status === 200){
          window.open('pdfs/public_pdfs/pagos/orden_pago.php', '_blank');
        }
      } catch (error) {
        console.log(error);
      }
  }

  $scope.arancel = async function(id){
    try {
        let options = {
          headers:{
            "Content-type": "application/json",
            "auth": $window.sessionStorage.getItem('__token')
          }
        };
        let response = await $http.get(`${host}/Pdf/arancel_pago/${id}`, options);
        console.log(response.status);
        if(response.status === 200){
          window.open('pdfs/public_pdfs/pagos/arancel_pago.php', '_blank');
        }
      } catch (error) {
        console.log(error);
      }
  }

  $scope.get_detalles_lic = function(){
    get_objt_session()
    var token = $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host + "Licencias/get_detalles_lic",
      "method": "POST",
      "headers": {
        "Content-Type": "application/json; charset=UTF-8",
        'auth': token
      },
      "data":  $scope.lic_data
    }
    var request = $http(settings);
    request.then(function(response) {
    // console.log(response);
    console.log(response.data);
      if(angular.equals(response.data.status, true)){
        //$scope.req_error = true; $scope.message = 'Documento cargado';
        //$scope.kind = 'success';
        $scope.detalles_lic = response.data.data;
        $scope.datoslic = response.data.lic;
        $window.sessionStorage.setItem('__lat',response.data.lic[0].lat);
        $window.sessionStorage.setItem('__lng',response.data.lic[0].lng);
        init();
      }if(!angular.equals(response.data.status, true)){
        $scope.req_error = true; $scope.message = 'Problemas para guardar información, contacte con soporte.';
        $scope.kind = 'danger';
      }
    })
    .catch('error ' + console.error);
  }

  $scope.get_group_form = function(group){
    $scope.datosform = {};
    var predata = JSON.parse($window.sessionStorage.getItem('__lic_data'));
    var data = {
      'lic_id' : predata.public_id,
      'grup_id' : group.public_id
    }
    var token =  $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host+"Licencias/form_data",
      "method": "POST",
      "headers": {
      "Content-Type": "application/json",
      'auth': token
      },
      "data" : data
    }
    var request = $http(settings);
      request.then(function(response) { 
        //console.log(response);
        $scope.form = response.data.form
        
        if (!angular.isUndefined(response.data.data[0])) { $scope.datosform = response.data.data[0]; }
        for (let index = 0; index < response.data.form.length; index++) {
          const element = response.data.form[index];
          //console.log(element);

          if (angular.equals(element.tipo_campo, '2') ) { response.data.data[0][element.campo_db] =  Number(response.data.data[0][element.campo_db])}
          if (angular.equals(element.tipo_campo, '3') ) { response.data.data[0][element.campo_db] =  new Date(response.data.data[0][element.campo_db])}
          if (angular.equals(element.tipo_campo, '6') ) { response.data.data[0][element.campo_db] =  (response.data.data[0][element.campo_db] == '1') ? true:false }
          console.log(response.data.data[0][element.campo_db]); 
          console.log(typeof(response.data.data[0][element.campo_db]));
           
        }

        
        /*else{
          $scope.form.forEach(element => {
            element.data = "";
          });
          $scope.datosform = $scope.form
        }*/
        //console.log($scope.datosform);
        $scope.btn_save = false
      })
      .catch('error ' + console.error);
    
  }
  
$scope.update_group = function(item){
  var predata = JSON.parse($window.sessionStorage.getItem('__lic_data'));
  var token =  $window.sessionStorage.getItem('__token');
  var data   = {}
  data.datos = $scope.datosform;
  data.id_licencia = predata.public_id;
  data.groupid = item[0].id_grupo;
  console.log(item)
  console.log($scope.datosform)
  console.log(data)

  var req = {
   'method': 'POST',
   'url': host+"Licencias/update_gral_ser",
   'headers': {
     'Content-Type':  "application/json",
     'auth': token
   },
   'data' : data
  }
  var request = $http(req);
  request.then(function(response){
    console.log(response);
    if (angular.equals(response.data.status, true)) {
      $scope.req_error = true; $scope.message = 'Datos actualizados correctamente.';
      $scope.kind = 'success';
      $scope.get_detalles_lic()
    }else {
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
      $scope.kind = 'warning';
    }
  }).catch('error '+console.error);
}

//P A G I N A D O   D E    A R A N C E L E S 
  //filtrado para paginado de tabla
  $scope.pageData_lic = [];
  $scope.currentPage_lic = 0;
  $scope.pages_lic = 0;
  $scope.pageSize_lic = 8;

  function pagination_lic(array){
    $scope.pages_lic=Math.ceil(array.length/$scope.pageSize_lic);
    for(var i=0; i<$scope.pages_lic; i++) {
        $scope.pageData_lic.push({
            numero:i,
        });
    }
  }

  //requisitos ara
  $scope.cambio_lic = function(i){
    if(i>$scope.pages_lic-1){
    }else if(i<0){
    }else{
    	$scope.currentPage_lic=i;
    }
  }


//////////////// P A G O S /////////////////////
$scope.get_pagos = function(){
  get_objt_session()
  var token =  $window.sessionStorage.getItem('__token');
  var req = {
  'method': 'POST',
  'url': host+"Pagos/get_comprobante_pago",
  'headers': {
      'Content-Type':  "application/json", 'auth': token
  },
  'data' : $scope.lic_data
  }
  var request = $http(req);
  request.then(function(response){
      console.log(response);
      if (angular.equals(response.data.status, true)) {
      $scope.pagos_list = response.data.data;
      }else {
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte licencias.';
      $scope.kind = 'warning';
      }
  }).catch('error '+console.error);
}

$scope.add_coment_pay = function(item){
  var data  = item
  var token = $window.sessionStorage.getItem('__token');
  var settings = {
    "url": host + "Pagos/add_comen_doc",
    "method": "POST",
    "headers": {
      "Content-Type": "application/json; charset=UTF-8",
      'auth': token
    },
    "data": data
  }
  var request = $http(settings);
  request.then(function(response) {
  // console.log(response);
  console.log(response.data);
    if(angular.equals(response.data.status, true)){
      $scope.notif_pay = true; $scope.message = 'Comentario cargado';
      $scope.kind = 'success';
      $scope.get_re_docs_lic()
      setTimeout(function() { $scope.req_error = false }, 3000);
    }if(!angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Problemas para guardar información, contacte con soporte.';
      $scope.kind = 'danger';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }
  })
  .catch('error ' + console.error);
}
///////////////////////////////////////////////////////////

//suma mtrs sup
function suma_mtr(array){ 
  let suma = 0;
  for (let index = 0; index < array.length; index++) {
    const element = array[index];
    if (angular.equals(element.public_id, 'id_5ea3505b722961.38505127') && !angular.isUndefined(element.data)) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b722f39.93139399') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b727392.84979258') && !angular.isUndefined(element.data) ) { suma += Number(element.data); } 
    if (angular.equals(element.public_id, 'id_5ea3505b7233f4.42787311') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b7239c5.46320891') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b723fc2.69098624') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b724846.83541752') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b724fe0.07950270') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b725555.43831823') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b725ab0.82392995') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b7262f8.22431048') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b726911.93484489') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }
    if (angular.equals(element.public_id, 'id_5ea3505b726e46.82514606') && !angular.isUndefined(element.data) ) { suma += Number(element.data); }

    //asignar sumatoria
    if (angular.equals(element.public_id, 'id_5ea3505b722463.97745423')) { var indice = index}
  }
  array[indice].data = suma;
  console.log(suma)
  return array
}

  $scope.actubic = function(){
    var draw = kendo.drawing;
    var geom = kendo.geometry;
    var contentSize = new geom.Rect([0, 0], [1200, 600]);
    var imageSize = new geom.Rect([0, 0], [1200, 600]);
    draw.drawDOM($("#map")).then(function(group) {
      // Fill the background and set the dimensions for the exported image
      var background = draw.Path.fromRect(imageSize, {
        fill: {
          color: "#ffffff",
        },
        stroke: null
      });
      // Fit content to size, if needed
      draw.fit(group, contentSize);
      // Note that the following methods accept arrays
      draw.align([group], imageSize, "center");
      draw.vAlign([group], imageSize, "center");
      // Bundle it all together
      var wrap = new draw.Group();
      wrap.append(group);
      // export the image and crop it for our desired size
      return draw.exportImage(wrap, {
        cors: "anonymous"
      });
    })
    .done(function(data) {
      resultado = data.replace('data:image/png;base64,', '');
      savemapa(resultado);
    })
  }

  function savemapa(base64){
    var predata = JSON.parse($window.sessionStorage.getItem('__lic_data'));
    var data = {
      'lic_id' : predata.public_id,
      'base64' : base64,
      'sol_id' : predata.id_solicitante
    }
    var token = $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host + "Licencias/updateMapa",
      "method": "PUT",
      "headers": {
        "Content-Type": "application/json; charset=UTF-8",
        'auth': token
      },
      "data":  data
    }
    var request = $http(settings);
    request
    .then(function(response) {
        if (angular.equals(response.data.status, true)) {
          $window.location.reload();
        }
        if (angular.equals(response.data.status, false)) {
          $scope.req_error = true; $scope.message = 'Problemas para guardar informacion, contacte con soporte.';
          $scope.kind = 'warning';
        }
    })
    .catch('error ' + console.error);
  }

  function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 14
    });
    
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(localizacion,error);
    }else{
      console.log("No se puede buscar posicion actual");
    }
    function localizacion(position){
      var pos = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      map.setCenter(pos);
    }
    function error(){
      console.log("error al obtener localizacion");
    }

    for(var i = 0 ;i < $scope.ubicaciones.length;i++){

      var contentString = 
      '<div class="container">'+
        '<h5>'+$scope.tramites[i].tramite+'</h5>'+
        '<div>'+
        '<p><b>'+$scope.tramites[i].nombre+'</b> calle: '+$scope.tramites[i].calle+
        ' colonia <b>'+$scope.tramites[i].colonia+
        '</b></p>'+
        '</div>'+
      '</div>';

      const infowindow = new google.maps.InfoWindow({
        content: contentString
      });

      const marker = new google.maps.Marker({
        position: $scope.ubicaciones[i],
        map: map,
        title: $scope.tramites[i].nombre
      });

      marker.addListener('click', function(){
        infowindow.open(map, marker);
      });
    }
  }

  function init() {
    var pre_center = { lat: parseFloat(sessionStorage.getItem('__lat')), lng: parseFloat(sessionStorage.getItem('__lng')) };
    var map = new google.maps.Map(document.getElementById('map'), {
        center: pre_center,
        zoom: 17
      });
    var infoWindow = new google.maps.InfoWindow;


    var marker = new google.maps.Marker({
      position: pre_center,
      map: map,
      draggable: true,
      animation: google.maps.Animation.DROP,
      title: 'Ubicación del predio'
    });

    google.maps.event.addListener(marker, 'dragend', function (evt) {
      sessionStorage.setItem('__lat', evt.latLng.lat());
      sessionStorage.setItem('__lng', evt.latLng.lng());
      // document.getElementById('current').innerHTML = '<p>Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) + '</p>';
    });

    google.maps.event.addListener(marker, 'dragstart', function (evt) {
      // document.getElementById('current').innerHTML = '<p>Currently dragging marker...</p>';
    });

    marker.addListener('click', toggleBounce);
    marker.setMap(map);

  }

  $scope.solicitud = async function(id){
    try {
        let options = {
          headers:{
            "Content-type": "application/json",
            "auth": $window.sessionStorage.getItem('__token')
          }
        };
        let response = await $http.get(`${host}/Pdf/solicitud/${id}`, options);
        console.log(response);
        if(response.status === 200){
          window.open(`pdfs/public_pdfs/${response.data}`, '_blank');
        }
      } catch (error) {
        console.log(error);
      }
  }

  $scope.licencia = async function(id){
    try {
        let options = {
          headers:{
            "Content-type": "application/json",
            "auth": $window.sessionStorage.getItem('__token')
          }
        };
        let response = await $http.get(`${host}/Pdf/licencia/${id}`, options);
        console.log(response);
        if(response.status === 200){
          window.open(`pdfs/public_pdfs/licencias/${response.data}`, '_blank');
        }
      } catch (error) {
        console.log(error);
      }
  }


  function toggleBounce() {
    if (marker.getAnimation() !== null) {
      marker.setAnimation(null);
    } else {
      marker.setAnimation(google.maps.Animation.BOUNCE);
    }
  }

  $scope.$watch('search', function(term) {
    $scope.filter = function() {
        $scope.noOfPages = Math.ceil($scope.filtered.length/$scope.entryLimit);
    }
});

});

angular.module('servidor')
.filter('startFrom', function() {
return function(input, start) {
    if(input) {
        start = +start; //parse to int
        return input.slice(start);
    }
    return [];
}
});
