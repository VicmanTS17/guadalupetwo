angular.module('solicitante')

.controller('licencias_ctrer_solicitante', function($scope, $window, $http, $q){

const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

get_licenias(); $scope.show_map = false;
// get licencias get_tramites
$scope.req_error = false; $scope.especialidad = {};
function get_licenias(){
  var token =  $window.sessionStorage.getItem('__token');
  var req = {
   'method': 'POST',
   'url': host+"Licencias/get_licencias",
   'headers': {
     'Content-Type':  "application/json",
     'auth': token
   }
  }
  var request = $http(req);
  request.then(function(response){
    console.log(response.data.data);
    if (angular.equals(response.data.status, true)) {
      pagination_lic(response.data.data);
      diving_lic(response.data.data);
      $scope.licencias_list = response.data.data;
    }else {
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte licencias.';
      $scope.kind = 'warning';
    }
  }).catch('error '+console.error);
}

function diving_lic(array) {
  array.forEach(element => {
    console.log(element.estatus);
  });
}

$scope.get_licenias = function(){
  var token =  $window.sessionStorage.getItem('__token');
  var req = {
   'method': 'POST',
   'url': host+"Licencias/get_tramites",
   'headers': {
     'Content-Type':  "application/json",
     'auth': token
   }
  }
  var request = $http(req);
  request.then(function(response){
    console.log(response.data);
    if (angular.equals(response.data.status, true)) {
      $scope.tramites_list = response.data.data;
    }else {
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte tramites.';
      $scope.kind = 'warning';
    }
  }).catch('error '+console.error);
}

$scope.show_aranceles = false;
$scope.get_frm = function(tram){
  $scope.grupo_list = []
  var token =  $window.sessionStorage.getItem('__token');
  if(tram.arancel == '1'){
    get_ara({}); get_esp();
    $scope.show_aranceles = true;
  }else{
    $scope.show_aranceles = false;
  }

  $scope.traid = tram.public_id;
  if (angular.equals(tram.public_id,'id_5ee7cfb91bd489.36231307') || angular.equals(tram.public_id,'id_5eb6d2b728d801.27882313')) $scope.show_map = true;
  else $scope.show_map = false;
  var settings = {
       "url": host+"Licencias/get_tramite_group",
       "method": "POST",
       "headers": {
         "Content-Type": "application/json",
         'auth': token
      },
      "data" : tram
  }
  var request = $http(settings);
  request.then(function(response){
    console.log(response)
    if (angular.equals(response.data.status, true)) {
      console.log(response.data.data);
      $scope.grupo_list = response.data.data;
    }else if(angular.equals(response.data.status, false)) {
      $scope.req_error = true; $scope.message = 'Para este tramite solo seleccionar guardar y terminar.';
      $scope.kind = 'info';
    } else {
      console.log(response.data.data);
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte agrupaciones.';
      $scope.kind = 'warning';
    }
  })
  .catch('error '+console.error);
}

function get_ara(data){
  console.log(data);
  var token =  $window.sessionStorage.getItem('__token');
  var settings = {
   "url": host+"Aranceles/get_ara",
   "method": "POST",
   "headers": {
     "Content-Type": "application/json",
     'auth': token
   },"data" : data
  }
  var request = $http(settings);
  request.then(function(response){
    console.log(response.data)
    if (angular.equals(response.data.status, true)) {
      pagination(response.data.data)
      console.log($scope.especialidad);
      if ($scope.especialidad.public_id) $scope.aran_esp_list = response.data.data
      else $scope.aranceles_list = response.data.data
    }else {
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte aranceles.';
      $scope.kind = 'warning';
    }
  })
  .catch('error '+console.error);
}

  $scope.get_group_form = function(group){
    var token =  $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host+"Licencias/get_form_group_lic",
      "method": "POST",
      "headers": {
      "Content-Type": "application/json",
      'auth': token
      },
      "data" : group
    }
    var request = $http(settings);
    request.then(function(response){
      console.log(response.data)
      $scope.form = response.data.data
      $scope.btn_save = false
      // console.log($scope.form);

      for (var i = 0; i < $scope.predata.length; i++) {
       var predata = $scope.predata[i]
       // console.log(predata[predata.length-1].grupo_id);
       if (predata[predata.length-1].grupo_id == group.public_id) {
         $scope.form = $scope.predata[i]
       }
      }

      //filtrado para paginado de tabla
      $scope.pageData_form = [];
      $scope.currentPage_form = 0;
      $scope.pages_form = 0;
      $scope.pageSize_form = 6;

      $scope.pages_form = Math.ceil($scope.form.length/$scope.pageSize_form);
      for(var i=0; i<$scope.pages_form; i++) {
         $scope.pageData_form.push({
             numero:i,
         });
      }
    }).catch('error '+console.error);
  }

  $scope.predata = [];
  $scope.add_form = function(data,item){
    console.log(item)
    var is_new = true
    for (var i = 0; i < $scope.predata.length; i++) {
      var predata = $scope.predata[i]
      // console.log(predata[predata.length-1].grupo_id);
      console.log(predata);
      if (predata[predata.length-1].grupo_id == item.public_id) {
        is_new = false
      }
    }
    //llamar funcion para sumatoria
    if (angular.equals(item.public_id, 'id_5ea3457c2e3ba5.81867615')) {
      data = suma_mtr(data)
    }
    if (is_new == true){
      var index = data.length
      data[index] = {}
      data[index].nombre = item.nombre
      data[index].grupo_id = item.public_id
      $scope.predata.push(data)
      $scope.req_error = true; $scope.message = 'Datos guardados con exito.';
      $scope.kind = 'info';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }else {
      $scope.predata.splice(i-1, 1)
      $scope.predata.push(data)
      $scope.req_error = true; $scope.message = 'Datos guardados con exito.';
      $scope.kind = 'info';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }
    // console.log($scope.predata)
  }

  ///// Aranceles
  $scope.result = []
  $scope.result.total_gral = 0
  $scope.add = function(item) {
    if (angular.isUndefined(item.cantidad) || angular.equals(item.cantidad, "")) {
      M.toast({html: 'Cantidad indefinida.', classes: 'rounded', displayLength:6000})
    } else {
      // console.log(item);
      var precio = Number(item.precio);
      var cantidad = Number(item.cantidad);
      item.total = precio * cantidad
      //$scope.insert=(item);
      $scope.result.push(item);
      $scope.result.total_gral += item.total
      console.log($scope.result);
    }
  }

  $scope.remove_gral = function(index, item) {
    $scope.result.splice(index, 1)
    if ($scope.result.total_gral == 0) {
      $scope.result.total_gral = 0
    }
    if ($scope.result.total_gral != 0) {
      $scope.result.total_gral -= item.total
    }
    //console.log($scope.result);
  }

  function savelic(base64) {
    var token =  $window.sessionStorage.getItem('__token');
    var data      = {};
    data.traid    = $scope.traid;
    data.data     = $scope.predata;
    data.flag_ara = $scope.show_aranceles;
    if (data.flag_ara) { data.ara = $scope.result; data.ara[data.ara.length] = $scope.result.total_gral;}
    data.flag_ara_cor = $scope.show_aranceles;
    if (data.flag_ara_cor) { data.ara_cor = $scope.corres_aras; }
    
    var location = { 'lat' : $window.sessionStorage.getItem('__lat'), 'lng' : $window.sessionStorage.getItem('__lng')};
    data.location = location;
    data.base64 = base64;
    console.log(data);

    var settings = {
         "url": host+"Licencias/save_lic",
         "method": "POST",
         "headers": {
           "Content-Type": "application/json",
           'auth': token
        },
        "data" : data
    }
    var request = $http(settings);
    request.then(function(response){
    console.log(response)
    if (angular.equals(response.data, true)) {
      $scope.req_error = true; $scope.message = 'Datos guardados con exito.';
      $scope.kind = 'info';
      $window.sessionStorage.removeItem('__lat');
      $window.sessionStorage.removeItem('__lng');
      $window.location = "#!licencias";
    }
    if (angular.equals(response.data, false)) {
      $scope.req_error = true; $scope.message = 'Problemas para guardar informacion, contacte con soporte.';
      $scope.kind = 'warning';
    }
    })
    .catch('error '+console.error);
  }

  //save gral
  $scope.save_gral = function(){
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
      savelic(resultado);
    })
  }

  //validar el guardar la licencia
  $scope.val_save_lic = function(){
    console.log($scope.traid)
    if (angular.isUndefined($scope.traid)) {
      $scope.req_error_save = true; $scope.kind_save = 'danger'; $scope.message_save = 'No ha seleccionado el tipo de trámite.'
    }else{
      /*if ($scope.corres_div){
        if(angular.isUndefined($scope.corres_aras[0])){
         $scope.req_error_save = true; $scope.kind_save = 'warning'; $scope.message_save_cor = 'No ha seleccionado los aranceles correspondientes a los corresponsable.'
         var flag_ara_cor_ok = false; 
        } else var flag_ara_cor_ok = true; 
      } */
      if ($scope.show_aranceles){
        console.log($scope.result[0])
        if(angular.isUndefined($scope.result[0])){ 
          $scope.req_error_save = true; $scope.kind_save = 'warning'; $scope.message_save = 'No ha seleccionado los aranceles correspondientes.';
          var flag_ara_ok = false; console.log('is null'); console.log($scope.req_error_save) 
        }else{
          var flag_ara_ok = true; $scope.req_error_save = false;
        }
      }else var flag_ara_ok = true; 
    }
    //if (flag_ara_ok && flag_ara_cor_ok) console.log('se guarda')
    if (flag_ara_ok){ $scope.btn_stat = true; console.log('se guarda');}
    else $scope.btn_stat = false;
  }

  $scope.set_session = function(data){
    $window.sessionStorage.setItem('__lic_data', JSON.stringify(data));
  }

  function get_objt_session(){
    $scope.lic_data = JSON.parse($window.sessionStorage.getItem('__lic_data'));
  }

  $scope.pre_cancel = function(){ get_objt_session(); }

  $scope.get_re_docs_lic = function(){
    get_objt_session()
    var token = $window.sessionStorage.getItem('__token');
    var data  = JSON.parse($window.sessionStorage.getItem('__lic_data'));
    console.log(data);

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
    if (angular.equals(flag, 1)) { flag = 'Docs. Cargados'; }
    if (angular.equals(flag, 2)) { flag = 'Docs. Corregidos'; }
    if (angular.equals(flag, 5)) { flag = 'Firma DRO'; }

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

  // G E S T I O N   C O R E S P O N S A B L E S
  $scope.corres_div = false; $scope.tbl_ara_cor_div = false;
  $scope.corres_aras = [];
  
  $scope.btn_rest_esp = false; $scope.btn_inv_ = false;
  // aranceles Corresponsables
  //get especialidades
  function get_esp(){
    $http.post(host+"Common/get_publicesp")
    .then(function(response){
      console.log(response.data);
      add_ngv(response.data)
      $scope.esp_list = response.data;
    }).catch('error '+console.error);
  }

  //*recorre arreglo de tramites para habilitar boton
  function add_ngv(array){
    for (var i = 0; i < array.length; i++) {
      array[i].btn_dis = true
    }
  }

  //* elegir especialidad
  $scope.set_data = function(data,index){
    for (var i = 0; i < $scope.esp_list.length; i++) {
      if(i != index){ $scope.esp_list[i].view = false; $scope.esp_list[i].btn_dis = false; }
    }
    $scope.especialidad = data;
    get_corres(data)
  }

  //*
  //restaura los tramites
  $scope.res_esp = function(flag){
    //primer click
    if (angular.equals(flag, 0)) {
      //array arancel corr vacio
      if (angular.equals($scope.cor_pre_result.length,0)) {
        for (var i = 0; i < $scope.esp_list.length; i++) {
          $scope.esp_list[i].btn_dis = 'true';
        }
        //get_esp(); 
        $scope.especialidad = {}; $scope.corres_list = {}; $scope.tbl_ara_cor_div = false;
        $scope.res_usrs();
      } else if(!angular.equals($scope.cor_pre_result.length,0) && angular.equals(flag, 0)) { //array arancel corr no vacio
        $('#staticBackdrop_esp').modal('show');
      }
    } 
     else if(angular.equals(flag, 1)) { //confirmacion de reset
      for (var i = 0; i < $scope.esp_list.length; i++) {
        $scope.esp_list[i].btn_dis = 'true';
      }
      //get_esp(); 
      $scope.especialidad = {}; $scope.corres_list = {}; $scope.tbl_ara_cor_div = false;
      $scope.res_usrs();
    }
  }

  //*restaura los usuarios
  $scope.res_usrs = function(flag){
    //primer click
    if (angular.equals(flag, 0)) {
      //array arancel corr vacio
      if (angular.equals($scope.cor_pre_result.length,0)) {
        $scope.tbl_ara_cor_div = false; $scope.corresponsable = {};
        for (var i = 0; i < $scope.corres_list.length; i++) {
          $scope.corres_list[i].btn_dis = 'true'; $scope.corres_list[i].checked = false;
        }
      } else if(!angular.equals($scope.cor_pre_result.length,0) && angular.equals(flag, 0)) { //array arancel corr no vacio
        $('#staticBackdrop').modal('show');
      }
    } 
     else if(angular.equals(flag, 1)) { //confirmacion de reset
      $scope.tbl_ara_cor_div = false; $scope.corresponsable = {};
      for (var i = 0; i < $scope.corres_list.length; i++) {
        $scope.corres_list[i].btn_dis = 'true'; $scope.corres_list[i].checked = false;
      }
    }
    //get_corres($scope.especialidad)
    //console.log($scope.corres_aras)
  }


  function get_corres(data){
    data.aid = data.public_id;
    get_ara(data);
    var token =  $window.sessionStorage.getItem('__token');
    var settings = {
         "url": host+"Users/get_corres", "method": "POST",
         "headers": {
           "Content-Type": "application/json",  'auth': token
        }, "data" : data
    }
    var request = $http(settings);
    request.then(function(response){
      console.log(response.data)
      if (angular.equals(response.data.status, true)) {
        add_ngv(response.data.data);
        $scope.corres_list = response.data.data;
      }else {
        $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte corresponsables.';
        $scope.kind = 'warning';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
    })
    .catch('error '+console.error);
  }

  $scope.set_corres = function(data, index){
    for (var i = 0; i < $scope.corres_list.length; i++) {
      if(i != index){ $scope.corres_list[i].view = false; $scope.corres_list[i].btn_dis = false; }
    }
    $scope.corresponsable  = data; $scope.tbl_ara_cor_div = true;
    $scope.corres_aras.push({'user_id' : data.public_id, 'id_especialidad' : $scope.especialidad.public_id, 
    'nombre' : data.nombre, 'especialidad' : $scope.especialidad.descripcion});
  }

  $scope.cor_pre_result = [];  $scope.cor_pre_result.total_gral = 0;
  $scope.add_corres = function(item) {
    if (angular.isUndefined(item.cantidad) || angular.equals(item.cantidad, "")) {
      $scope.cor_error = true; $scope.message_cor = item.nomas+' Cantidad indefinida.';
      $scope.kind_cor = 'danger';
      setTimeout(function() { $scope.req_error = false }, 3000);
    } else {
      // console.log(item);
      var precio = Number(item.precio);
      var cantidad = Number(item.cantidad);
      item.total = precio * cantidad
      //$scope.insert=(item);
      $scope.cor_pre_result.push(item);
      $scope.cor_pre_result.total_gral += item.total
      //console.log($scope.cor_pre_result);
    }
  }

  $scope.remove_gral_corres = function(index, item) {
    $scope.cor_pre_result.splice(index, 1)
    if ($scope.cor_pre_result.total_gral == 0) {
      $scope.cor_pre_result.total_gral = 0
    }
    if ($scope.cor_pre_result.total_gral != 0) {
      $scope.cor_pre_result.total_gral -= item.total
    }
    //console.log($scope.result);
  }

  $scope.add_cor_ara = function(){
    if ($scope.cor_pre_result.length !== 0) { //longitud rancele corresponsable
      //validacion que sea la misma epecialidad y mismo corres
      if (angular.equals($scope.corres_aras[$scope.corres_aras.length-1].user_id, $scope.corresponsable.public_id) && angular.equals($scope.corres_aras[$scope.corres_aras.length-1].id_especialidad, $scope.especialidad.public_id)){ 
        $scope.cor_pre_result[$scope.cor_pre_result.length] = $scope.cor_pre_result.total_gral;
        $scope.corres_aras[$scope.corres_aras.length-1]['aranceles'] = $scope.cor_pre_result; 
        $scope.cor_error = true; $scope.message_cor = 'Guardados aranceles para '+$scope.corresponsable.nombre+' corresponsable en '+$scope.especialidad.descripcion;
        $scope.kind_cor = 'success';
        setTimeout(function() { $scope.req_error = false }, 3000);
        $scope.especialidad = ''; $scope.corresponsable = ''; //datos auxiliares
        $scope.corres_div = false; $scope.tbl_ara_cor_div = false; //banderas de divs ng-if
        $scope.cor_pre_result = [];  $scope.cor_pre_result.total_gral = 0;  $scope.res_esp(0);//reinicio de datos
      }
    }else{
      $scope.cor_error = true; $scope.message_cor = 'Debe de registrar por lo menos un arancel para '+$scope.corresponsable.nombre+' corresponsable en '+$scope.especialidad.descripcion;
      $scope.kind_cor = 'danger';
      setTimeout(function() { $scope.req_error = true }, 3000);
      //$scope.especialidad = ''; $scope.corresponsable = '';
      //$scope.corres_div = false; $scope.tbl_ara_cor_div = false;
      //$scope.cor_pre_result = [];  $scope.cor_pre_result.total_gral = 0;
    }
    console.log($scope.corres_aras)
  }


// S O L I C I T U D   P D F S
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

  $scope.orden = async function(id){
    try {
        let options = {
          headers:{
            "Content-type": "application/json",
            "auth": $window.sessionStorage.getItem('__token')
          }
        };
        let response = await $http.get(`${host}/Pdf/orden_pago/${id}`, options);
        console.log(response.status);
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
          window.open('pdfs/public_pdfs/pagos/licencia_arancel.php', '_blank');
        }
      } catch (error) {
        console.log(error);
      }
  }

//P A G I N A D O   D E    A R A N C E L E S 
  //filtrado para paginado de tabla
  $scope.pageData_ara = [];
  $scope.currentPage_ara = 0;
  $scope.pages_ara = 0;
  $scope.pageSize_ara = 13;

  $scope.pageData_lic = [];
  $scope.currentPage_lic = 0;
  $scope.pages_lic = 0;
  $scope.pageSize_lic = 8;

  function pagination(array){
    $scope.pages_ara=Math.ceil(array.length/$scope.pageSize_ara);
    for(var i=0; i<$scope.pages_ara; i++) {
        $scope.pageData_ara.push({
            numero:i,
        });
    }
  }

  function pagination_lic(array){
    $scope.pages_lic=Math.ceil(array.length/$scope.pageSize_lic);
    for(var i=0; i<$scope.pages_lic; i++) {
        $scope.pageData_lic.push({
            numero:i,
        });
    }
  }

  //requisitos ara
  $scope.cambio_ara = function(i){
    if(i>$scope.pages_ara-1){
    }else if(i<0){
    }else{
    	$scope.currentPage_ara=i;
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
///////////////////////////////////////////////////////////
  ///////// A N E X O S 
  //clear lic para
  $scope.clear_model = function(array,data){
    array.forEach(element => {
      if (!angular.equals(data.public_id, element.public_id)) { element.data = false }
    });
  }

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

  ///// C O R R E S P O N S A B I L I D A D 
  $scope.get_lic_cor = function(){
    var token =  $window.sessionStorage.getItem('__token');
    var settings = {
         "url": host+"Licencias/get_lic_corres", "method": "POST",
         "headers": {
           "Content-Type": "application/json",  'auth': token
        }
    }
    var request = $http(settings);
    request.then(function(response){
      console.log(response.data)
      if (angular.equals(response.data.status, true)) {
        console.log(response.data.data)
        $scope.licencias_cor_list = response.data.data;
      }else {
        $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte corresponsables.';
        $scope.kind = 'warning';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
    })
    .catch('error '+console.error);
  }

  //eliminar elemento de arancel corres
  $scope.del_ara_cor = function(index, item) {
    //console.log(index); console.log(item);
    $scope.corres_aras.splice(index, 1);
    //console.log($scope.corres_aras);
  }

  //CANCELACION DEL FOLIO
  $scope.notif_can = false;
  $scope.val_pass = function(){
    console.log($scope.contra);
    if (!angular.equals($scope.contra, '') && angular.isUndefined($scope.contra)) {
      $scope.notif_can = true; $scope.kind = 'warning'; $scope.msj = 'La contraseña es requerida para la cancelación de un folio'
    }else{ sol_can() }
  }

  function sol_can(){
    $scope.lic_data.contra = $scope.contra;
    console.log($scope.lic_data);
    var token =  $window.sessionStorage.getItem('__token');
    var settings = {
         "url": host+"Licencias/sol_can", "method": "POST",
         "headers": {
           "Content-Type": "application/json",  'auth': token
        }, "data" : $scope.lic_data
    }
    var request = $http(settings);
    request.then(function(response){
      console.log(response.data);
      if (angular.equals(response.data.status, true) && angular.equals(response.data.data, null)) {
        $('#config_cancel').modal('hide'); get_licenias();
      }else if (angular.equals(response.data.status, true) && angular.equals(response.data.data, 0)) {
        $scope.notif_can = true; $scope.msj = 'Contraseña incorrecta.';
        $scope.kind = 'warning';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }else {
        $scope.notif_can = true; $scope.msj = 'Problemas para realizar consulta, contacte con soporte corresponsables.';
        $scope.kind = 'warning';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
    })
    .catch('error '+console.error);
  }
  ///////////////////////

  

  $scope.$watch('search', function(term) {
  			$scope.filter = function() {
  					$scope.noOfPages = Math.ceil($scope.filtered.length/$scope.entryLimit);
  			}
  	});

  });

  angular.module('solicitante')
  .filter('startFrom', function() {
      return function(input, start) {
          if(input) {
              start = +start; //parse to int
              return input.slice(start);
          }
          return [];
      }
  });
