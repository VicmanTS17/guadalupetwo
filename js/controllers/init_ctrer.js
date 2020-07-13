var app = angular.module('init', [])

app.controller('init_controller', function($scope, $window, $http, $q) {

  const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

  setTimeout(function() { get_col(); get_esp(); get_plcdros(); }, 2000);

  function get_col(){
    $http.post(host+"Common/get_publicol")
    .then(function(response){
      // console.log(response.data);
      $scope.coll_list = response.data;
    }).catch('error '+console.error);
  }

  function get_esp(){
    $http.post(host+"Common/get_publicesp")
    .then(function(response){
      // console.log(response.data);
      $scope.esp_list = response.data;
    }).catch('error '+console.error);
  }

  function get_plcdros(){
    $http.post(host+"Common/get_plcdros")
    .then(function(response){
      //console.log(response.data);
      $scope.dro_list = response.data;
    }).catch('error '+console.error);
  }

  //especial
  $scope.especial = []
  $scope.especial[0] = '';
  $scope.alert = false;

  $scope.add_esp = function(){
    if (angular.equals($scope.especial[$scope.especial.length-1], '')) {
      $scope.alert = true;
    }else {
      $scope.especial.push('');
      $scope.alert = false;
    }
    $scope.ver()
  }

  $scope.del_esp = function(index){
    $scope.especial.splice(index, 1);
  }

  $scope.ver = function(){
    console.log($scope.especial);
  }

  $scope.val_reg_gral = function(flag){
    $scope.flag_name = false; $scope.flag_email = false; $scope.flag_street = false;
    $scope.flag_suburb = false; $scope.flag_zipc = false; $scope.flag_cel = false;

    if (!angular.equals($scope.name,"") && !angular.isUndefined($scope.name)) { $scope.flag_name =  true }
    if (!angular.equals($scope.email,"") && !angular.isUndefined($scope.email)) { $scope.flag_email =  true }
    if (!angular.equals($scope.street,"") && !angular.isUndefined($scope.street)) { $scope.flag_street =  true }
    if (!angular.equals($scope.suburb,"") && !angular.isUndefined($scope.suburb)) { $scope.flag_suburb =  true }
    if (!angular.equals($scope.zipc,"") && !angular.isUndefined($scope.zipc)) { $scope.flag_zipc =  true }
    if (!angular.equals($scope.cel,"") && !angular.isUndefined($scope.cel)) { $scope.flag_cel =  true }

    //perito
    if (angular.equals(flag, 1010)) {
      $scope.flag_idcard = false; $scope.flag_noreg = false; $scope.flag_college = false;

      if (angular.isUndefined($scope.is_corres)) { $scope.is_corres = false }
      if (!angular.equals($scope.idcard,"") && !angular.isUndefined($scope.idcard)) { $scope.flag_idcard =  true }
      if (!angular.equals($scope.noreg,"") && !angular.isUndefined($scope.noreg)) { $scope.flag_noreg =  true }
      if (!angular.equals($scope.college,"") && !angular.isUndefined($scope.college)) { $scope.flag_college =  true }

      // especial
      if ($scope.is_corres) {
        if (angular.equals($scope.especial[$scope.especial.length-1], '')) {
          $scope.alert = true;
        }
      }

      // ok
      if ($scope.flag_name && $scope.flag_email && $scope.flag_street && $scope.flag_suburb && $scope.flag_zipc && $scope.flag_cel &&
          $scope.flag_idcard && $scope.flag_noreg && $scope.flag_college) {

        var data = { "name" : $scope.name, "email" : $scope.email, "utype" : "id_5ebe10d0b4db29.48120814", "street" : $scope.street,
                     "suburb" : $scope.suburb, "pc" : $scope.zipc,  "cel" : $scope.cel, "idcard" : $scope.idcard,
	                   "numreg" : $scope.noreg, "college" : $scope.college, "is_cor" : $scope.is_corres,
                     "especial" :  $scope.especial }
        console.log(data);
        $scope.error_per = false
        send(data)
      // error
      }else{$scope.error_per = true}
    }
    //particular
    if (angular.equals(flag, 1011)) {
      $scope.flag_curp = false;

      if (!angular.equals($scope.curp,"") && !angular.isUndefined($scope.curp)) { $scope.flag_curp =  true }
      // ok
      if ($scope.flag_name && $scope.flag_email && $scope.flag_street && $scope.flag_suburb && $scope.flag_zipc && $scope.flag_cel && $scope.flag_curp) {
        var data = { "name" : $scope.name, "email" : $scope.email, "utype" : "id_5ebe11ce542b89.25367119", "curp" : $scope.curp,
	                   "street" : $scope.street, "suburb" : $scope.suburb, "pc" : $scope.zipc,  "cel" : $scope.cel }
        console.log(data);
        $scope.error_par = false
        send(data)
      // error
      }else{$scope.error_par = true}
    }
  }

  function send(data){
    $scope.request_done = true

    $http.post(host+"Users/add_outside_user", data)
    .then(function(response){
      console.log(response.data);
      if (angular.equals(response.data, true)) { $scope.response = true; setTimeout(function() { $window.location = "#page-top" }, 4000);}
      if (angular.equals(response.data, 10)) {
        $scope.message = 'Registro duplicado, sé tienes problemas contacte soporte.';
      }
      if (!angular.equals(response.data, true) && !angular.equals(response.data, 10)) {
        $scope.message = 'Problemas para realizar registro, contacte con soporte.'; }
    }).catch('error '+console.error);
  }

  // validacion del emai login
  $scope.val_queremail = function(){
    $scope.flag_log_email = false;

    if (!angular.equals($scope.log_email, '') && !angular.isUndefined($scope.log_email)) { $scope.flag_log_email = true; }
    else {
      $scope.login_error = true; $scope.message_queryma = 'El correo es requerido.'; $scope.kind = 'light';
    }
    var data  = { "email" : $scope.log_email}
    if ($scope.flag_log_email) { query_email(data)   }
  }

  //login email fiel visble
  $scope.is_maillog = true;
  $scope.is_passlog = false;
  // consulta del email login
  function query_email(data){
    $scope.request_done = true

    $http.post(host+"Common/query_email", data)
    .then(function(response){
      console.log(response.data);
      if (angular.equals(response.data, true)) { $scope.is_passlog = true; $scope.login_error = false; $scope.is_maillog = false;}
      if (angular.equals(response.data, false)) {
        $scope.login_error = true; $scope.message_queryma = 'Correo inexistente, registrese para iniciar.';
        $scope.kind = 'light';
      }
      if (!angular.equals(response.data, true) && !angular.equals(response.data, false)) {
        $scope.login_error = true; $scope.message_queryma = 'Problemas para realizar consulta, contacte con soporte.';
        $scope.kind = 'danger';
      }
    }).catch('error '+console.error);
  }

  // validacion del emai login
  $scope.val_querpass = function(){
    $scope.flag_log_pass = false;

    if (!angular.equals($scope.log_pass, '') && !angular.isUndefined($scope.log_pass)) { $scope.flag_log_pass = true; }
    else {
      $scope.login_error = true; $scope.message_queryma = 'La contraseña es requerida.'; $scope.kind = 'light';
    }
    var data  = { "email" : $scope.log_email, "pass" : $scope.log_pass}
    if ($scope.flag_log_pass) { login(data)   }
  }

  // consulta del email login
  function login(data){
    $scope.request_done = true

    $http.post(host+"Common/login", data)
    .then(function(response){
      console.log(response.data);
      if (angular.equals(response.data.status, true)) {
        $window.sessionStorage.setItem('__token', response.data.token);
        // sol flags
        if (!angular.isUndefined(response.data.estado) && angular.equals(response.data.estado, '1')){
          $window.sessionStorage.setItem('__solflag', true);
        }
        if (!angular.isUndefined(response.data.estado) && angular.equals(response.data.estado, '0')){
          $window.sessionStorage.setItem('__solflag', false);
        }
        // jb flags
        if (!angular.isUndefined(response.data.jdflag) && angular.equals(response.data.jdflag, '1')){
          $window.sessionStorage.setItem('__jdflag', true);
        }
        if (!angular.isUndefined(response.data.jdflag) && angular.equals(response.data.jdflag, '0')){
          $window.sessionStorage.setItem('__jdflag', false);
        }
        // cad flags
        if (!angular.isUndefined(response.data.caflag) && angular.equals(response.data.caflag, '1')){
          $window.sessionStorage.setItem('__caflag', true);
        }
        if (!angular.isUndefined(response.data.jdflag) && angular.equals(response.data.caflag, '0')){
          $window.sessionStorage.setItem('__caflag', false);
        }
        $window.location = response.data.path;
      }
      if (angular.equals(response.data.status, false) && angular.equals(response.data.data, 0)) {
          $scope.login_error = true; $scope.message_queryma = 'Contraseña incorrecta.';
          $scope.kind = 'warning';
      }
      if (angular.equals(response.data.status, false) && angular.equals(response.data.data, 10)) {
          $scope.login_error = true; $scope.message_queryma = 'Cuenta suspendida.';
          $scope.kind = 'warning';
      }
      if (angular.equals(response.data.status, false) && !angular.equals(response.data.data, 10) && !angular.equals(response.data.data, 0)) {
          $scope.login_error = true; $scope.message_queryma = 'Problemas para realizar consulta, contacte con soporte.';
          $scope.kind = 'danger';
      }
    }).catch('error '+console.error);
  }

});
