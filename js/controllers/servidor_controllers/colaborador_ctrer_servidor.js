angular.module('servidor')

.controller('colaborador_ctrer_servidor', function($scope, $window, $http, $q){

  const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

  get_users()
  get_tramites()

  // get users
  $scope.req_error = false;
  $scope.editing   = false;
  function get_users(){
    var token =  $window.sessionStorage.getItem('__token');
    var req = {
     'method': 'POST',
     'url': host+"Users/get_admsusrs",
     'headers': {
       'Content-Type':  "application/json",
       'auth': token
     }
    }
    var request = $http(req);
 		request.then(function(response){
      console.log(response.data);
      if (angular.equals(response.data.status, true)) {
        add_reqcheck(response.data.data,0)
        $scope.users_list = response.data.data;
        desc_type($scope.users_list)
      }else {
        $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
        $scope.kind = 'warning';
      }
    }).catch('error '+console.error);
  }

  function desc_type(data){
    for (var i = 0; i < data.length; i++) {
      if (data[i].tipo_usuario === 'id_5ebc623f267603.74886889') { data[i].desc_tipo_usuario = 'Colaborador'; }
    }
  }

  $scope.val_datos = function(flag_query){
    var flag_error = false; var flag_nombre = false; var flag_correo = false; var flag_inicial = false;

    if(angular.equals($scope.name, '') ||angular.isUndefined($scope.name)){ flag_nombre = true }
    if(angular.equals($scope.email, '') ||angular.isUndefined($scope.email)){ flag_correo = true }
    if(angular.equals($scope.inicial, '') ||angular.isUndefined($scope.inicial)){ flag_inicial = true }

    if(flag_correo || flag_nombre || flag_inicial){ flag_error = true}
    if(!flag_error){

      var data = { 'name' : $scope.name, 'email' : $scope.email, 'inicial' : $scope.inicial, 'utype' : 'id_5ebc623f267603.74886889' };
      if (angular.equals(flag_query, 1)) {
        add_user(data)
      }
      if (angular.equals(flag_query, 0)) {
        data.user_id =  $scope.user_id;
        up_user(data)
      }

    }else {
      $scope.req_error = true; $scope.message = 'Todos los datos son requeridos.';
      $scope.kind = 'warning';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }
  }

  function add_user(data){
    var token = $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host + "Users/add_inside_user",
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
      if(angular.equals(response.data.status, true) && !angular.equals(response.data.data, '10')){
        $scope.req_error = true; $scope.message = 'Usuario actualizadó';
        $scope.kind = 'success';
        $scope.name = '';  $scope.email = ''; $scope.inicial = '';
        get_users()
        setTimeout(function() { $scope.req_error = false }, 3000);
      }if(angular.equals(response.data.status, true) && angular.equals(response.data.data, '10')){
        $scope.req_error = true; $scope.message = 'El correo ya ha sido registrado con otra cuenta, selecione otro.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }if(!angular.equals(response.data.status, true)){
        $scope.req_error = true; $scope.message = 'Problemas para cargar información, contacte con soporte.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
    })
    .catch('error ' + console.error);
  }

  $scope.edit_usr = function(data){
    $scope.editing = true;
    $scope.name = data.nombre; $scope.email = data.correo;  $scope.inicial = data.iniciales; 
    $scope.user_id =  data.public_id;
  }

  $scope.cancel = function(){
    $scope.editing = false;
    $scope.name = ''; $scope.email = '';
  }

  function up_user(data){
    var token = $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host + "Users/up_user_mfd",
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
      if(angular.equals(response.data.status, true) && !angular.equals(response.data.data, '10')){
        $scope.req_error = true; $scope.message = 'Usuario actializadó';
        $scope.kind = 'success';
        $scope.name = '';  $scope.email = ''; $scope.inicial = '';
        get_users()
        setTimeout(function() { $scope.req_error = false }, 3000);
      }if(angular.equals(response.data.status, true) && angular.equals(response.data.data, '10')){
        $scope.req_error = true; $scope.message = 'El correo ya ha sido registrado con otra cuenta, selecione otro.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }if(!angular.equals(response.data.status, true)){
        $scope.req_error = true; $scope.message = 'Problemas para cargar información, contacte con soporte.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
    })
    .catch('error ' + console.error);
  }

  $scope.est_usr = function(data){
    var token = $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host + "Users/estado_usr",
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
        $scope.req_error = true; $scope.message = 'Usuario actializadó';
        $scope.kind = 'success';
        $scope.name = '';  $scope.email = '';
        get_users()
        setTimeout(function() { $scope.req_error = false }, 3000);
      }if(!angular.equals(response.data.status, true)){
        $scope.req_error = true; $scope.message = 'Problemas para cargar información, contacte con soporte.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.req_error = false }, 3000);
      }
    })
    .catch('error ' + console.error);
  }


/// C O R R E S  P O S A N B L E    -   L I C E N C  I A S
 //boton guardar req
  $scope.btn_save = false;
  //boton restaurar req
  $scope.btn_rest_req = false;
  //boton invert req
  $scope.btn_inv_req = false;
  //boton seleccionar todos req
  $scope.btn_all_req = false;

  $scope.detalles = []

  function get_tramites(){
    var token =  $window.sessionStorage.getItem('__token');
    var req = {
     'method': 'POST',
     'url': host+"Licencias/get_tramites_ser",
     'headers': {
       'Content-Type':  "application/json",
       'auth': token
     }
    }
    var request = $http(req);
    request.then(function(response){
      console.log(response.data);
      if (angular.equals(response.data.status, true)) {
        add_ngv(response.data.data)
        $scope.tramites_list = response.data.data;
        tramite_pagin($scope.tramites_list)
      }else {
        $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte tramites.';
        $scope.kind = 'warning';
      }
    }).catch('error '+console.error);
  }

  //recorre arreglo de tramites para habilitar boton
  function add_ngv(array){
    for (var i = 0; i < array.length; i++) {
      array[i].btn_dis = true
    }
  }

  //restaura los tramites
  $scope.res_tra = function(){
    $scope.btn_save = false; $scope.btn_rest_req = false; $scope.btn_inv_req = false; $scope.btn_all_req = false;
    for (var i = 0; i < $scope.tramites_list.length; i++) {
      $scope.tramites_list[i].btn_dis = 'true';
    }
    add_reqcheck($scope.users_list, 0)
    $scope.detalles = []
  }

// recorrido de arreglo de usuarios y privilegios
  function add_reqcheck(array, indi){
      for (var i = 0; i < array.length; i++) {
        if (indi === 0) { array[i].disabled = true }
        if (indi === 1) {
          array[i].disabled = false
          array[i].checked = false
          array[i].plid_rta = 'non'
        }
        if (indi === 2) {
          // array[i].plid_rtr = array.plid_rta

          array[i].disabled = false
          if(array[i].estatus == 1 || array[i].estatus === '1'){ array[i].checked = true }
          else{ array[i].checked = false }

          var prepare_data = {}
          prepare_data.user = array[i].nombre
          prepare_data.uid = array[i].public_id
          if (angular.equals(array[i].editar,1) || angular.equals(array[i].editar,'1'))
            prepare_data.update = true
          else
            prepare_data.update = false
          if (angular.equals(array[i].eliminar,1) || angular.equals(array[i].eliminar,'1'))
            prepare_data.delete = true
          else
            prepare_data.delete = false

          $scope.detalles.push(prepare_data)
          // console.log(array);
        }
      }
  }

  $scope.set_data = function(data,index){
    console.log(data)
    for (var i = 0; i < $scope.tramites_list.length; i++) {
      if(i != index){
        $scope.tramites_list[i].view = false
        $scope.tramites_list[i].btn_dis = false
      }
    }

    $scope.btn_save = true;  $scope.btn_rest_req = true; $scope.btn_inv_req = true; $scope.btn_all_req = true;
    $scope.tramite_id =  data.public_id
    getAdmsFrom(data.public_id)
    $scope.detalles['tramite'] = data.tramite
    $scope.detalles['public_id'] = data.public_id
  }

   //restaura los usuarios
  $scope.res_usrs = function(){
    for (var i = 0; i < $scope.users_list.length; i++) {
      $scope.users_list[i].checked = false;

      for (var j = 0; j < $scope.detalles.length; j++) {
        if(angular.equals($scope.users_list[i].public_id,$scope.detalles[j].uid))
            $scope.detalles.splice(j, 1);
      }
    }
  }

  //obtiene requisitos de tramite especifico
  function getAdmsFrom(data_lic){
    var data = {}
    var token =  $window.sessionStorage.getItem('__token');
    data.public_id = data_lic
    var settings = {
         "url": host+"Licencias/get_admin_tra",
         "method": "POST",
         "headers": {
           "Content-Type": "application/json",
           'auth': token
        },
        "data" : data
    }
    var request = $http(settings);
    request.then(function(response){
      console.log(response.data);
      var req_backup = $scope.users_list;
      add_reqcheck(req_backup,1)

      if (angular.equals(response.data.status, false)) { add_reqcheck($scope.users_list,1); }
      if (angular.equals(response.data.status, true)) {
        add_reqcheck(response.data.data,2);

        //concatenacion de elementos de req faltantes en response
        if(req_backup.length > response.data.data.length){
          for (var i = response.data.data.length; i < req_backup.length; i++) {
            response.data.data = response.data.data.concat(req_backup[i]);
          }
        }
        $scope.users_list = response.data.data
      }
      console.log($scope.users_list);
    })
    .catch('error '+console.error);
  }

  $scope.mod_detalles = function(data){
    var prepare_data = {}
    if(data.checked){
      prepare_data.user = data.nombre
      prepare_data.uid = data.public_id
      prepare_data.update = true
      prepare_data.delete = true
      $scope.detalles.push(prepare_data)
    }
    if(!data.checked){
      for (var i = 0; i < $scope.detalles.length; i++) {
        if(angular.equals(data.public_id,$scope.detalles[i].uid))
            $scope.detalles.splice(i, 1);
      }
    }
    // console.log($scope.detalles);
  }

  //seleccionar todos req
  $scope.all_req = function(){
    for (var i = 0; i < $scope.users_list.length; i++) {
      if(!$scope.users_list[i].checked){
        var prepare_data = {}
        prepare_data.user = $scope.users_list[i].nombre
        prepare_data.uid = $scope.users_list[i].public_id
        prepare_data.update = true
        prepare_data.delete = true
        $scope.detalles.push(prepare_data)
      }
      $scope.users_list[i].checked = true;
    }
  }

  //invert seleccion req
  $scope.inv_req = function(){
    for (var i = 0; i < $scope.users_list.length; i++) {
      if ($scope.users_list[i].checked) {
        $scope.users_list[i].checked = false
      }else{ $scope.users_list[i].checked = true }

      var prepare_data = {}
      if($scope.users_list[i].checked){
        prepare_data.user = $scope.users_list[i].nombre
        prepare_data.uid = $scope.users_list[i].public_id
        prepare_data.update = true
        prepare_data.delete = true
        $scope.detalles.push(prepare_data)
      }
      if(!$scope.users_list[i].checked){
        for (var j = 0; j < $scope.detalles.length; j++) {
          if(angular.equals($scope.users_list[i].public_id,$scope.detalles[j].uid))
              $scope.detalles.splice(j, 1);
        }
      }
    }
  }

  //guardar relacion req-tram
  $scope.save_rel = function(){
    var token =  $window.sessionStorage.getItem('__token');
    var data = {}
    data.traid = $scope.tramite_id
    data.data = $scope.users_list
    data.detalles = $scope.detalles
    console.log(data);

    var settings = {
         "url": host+"Licencias/save_r_tad",
         "method": "POST",
         "headers": {
           "Content-Type": "application/json",
           'auth': token
        },
        "data" : data
    }
    var request = $http(settings);
    request.then(function(response){
      console.log(response.data);
      if (angular.equals(response.data.status, true)) {
        $scope.req_error = true; $scope.message = 'Registro realizadó con éxito.';
        $scope.kind = 'success';
        setTimeout(function() { $window.location.reload(); }, 2000);
      }else{
        $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte tramites.';
        $scope.kind = 'warning';
      }
    })
    .catch('error '+console.error);
  }

  //tramite filter
  function tramite_pagin(array){
    $scope.pageData_tra = [];
    $scope.currentPage_tra = 0;
    $scope.pages_tra = 0;
    $scope.pageSize_tra = 5;
    //filtrado para paginado de tabla
    $scope.pages_tra=Math.ceil(array.length/$scope.pageSize_tra);
    for(var i=0; i<$scope.pages_tra; i++) {
        $scope.pageData_tra.push({
            numero:i,
        });
    }
  }

$scope.cambio_tra = function(i){
    if(i>$scope.pages_tra-1){
    }else if(i<0){
    }else{
      $scope.currentPage_tra=i;
    }
  }

  //user filter
  function user_pagin(array){
    $scope.pageData_user = [];
    $scope.currentPage_user = 0;
    $scope.pages_user = 0;
    $scope.pageSize_user = 10;
    //filtrado para paginado de tabla
    $scope.pages_user=Math.ceil(array.length/$scope.pageSize_user);
    for(var i=0; i<$scope.pages_user; i++) {
        $scope.pageData_user.push({
            numero:i,
        });
    }
  }

  $scope.cambio_user = function(i){
    if(i>$scope.pages_user-1){
    }else if(i<0){
    }else{
      $scope.currentPage_user=i;
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