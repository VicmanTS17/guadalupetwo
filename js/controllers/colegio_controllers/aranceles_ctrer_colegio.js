angular.module('colegio')

.controller('aranceles_ctrer_colegio', function($scope, $window, $http, $q){

const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

$scope.req_error = false; $scope.especialidad_flag = false;
get_aranceles()

// get licencias get_tramites
function get_aranceles(){
  var data = {}
  $scope.kind_ara = get_objt_session()
  if ($scope.kind_ara.descripcion) $scope.especialidad_flag = true; data.aid = $scope.kind_ara.public_id;

  var token =  $window.sessionStorage.getItem('__token');
  var req = { 'method': 'POST', 'url': host+"Aranceles/get_ara",
   'headers': {
     'Content-Type':  "application/json", 'auth': token
   }, "data" : data
  }
  var request = $http(req);
  request.then(function(response){
    console.log(response.data);
    if (angular.equals(response.data.status, true)) {
      $scope.aranceles_list = response.data.data;

      //filtrado para paginado de tabla
      $scope.pageData_ara = [];
      $scope.currentPage_ara = 0;
      $scope.pages_ara = 0;
      $scope.pageSize_ara = 10;

      $scope.pages_ara=Math.ceil($scope.aranceles_list.length/$scope.pageSize_ara);
      for(var i=0; i<$scope.pages_ara; i++) {
          $scope.pageData_ara.push({
              numero:i,
          });
      }
    }else {
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
      $scope.kind = 'warning';
    }
  }).catch('error '+console.error);
}

function get_objt_session(){
  return JSON.parse($window.sessionStorage.getItem('__ara_data'));
}

// G E S T I O N   D E R E C H O
$scope.val_datos = function(flag_query){
  var flag_error = false;
  var flag_clv = false; var flag_con = false; var flag_und = false; var flag_cos = false; //var flag_cor = false;

  if(angular.equals($scope.clave, '') ||angular.isUndefined($scope.clave)){ flag_clv = true }
  if(angular.equals($scope.concepto, '') ||angular.isUndefined($scope.concepto)){ flag_con = true }
  if(angular.equals($scope.unidad, '') ||angular.isUndefined($scope.unidad)){ flag_und = true }
  if(angular.equals($scope.costo, '') ||angular.isUndefined($scope.costo)){ flag_cos = true }
  // if(angular.equals($scope.corres, '') ||angular.isUndefined($scope.corres)){ flag_cor = true }
  //|| flag_cor
  if( flag_clv || flag_con || flag_und || flag_cos ){ flag_error = true}
  if(!flag_error){
    // , 'corres' : $scope.corres
    var data = { 'clave' : $scope.clave, 'concepto' : $scope.concepto, 'unidad' : $scope.unidad, 'costo' : $scope.costo, 'flag' : 0 };
    if ($scope.kind_ara.descripcion) data.flag = 1; data.esid = $scope.kind_ara.public_id;
    if (angular.equals(flag_query, 1)) { add_ar(data) }

    if (angular.equals(flag_query, 0)) {
      data.id_arancel = $scope.id_arancel; up_ar(data);
    }

  }else {
    $scope.req_error = true; $scope.message = 'Todos los datos son requeridos.';
    $scope.kind = 'warning';
    setTimeout(function() { $scope.req_error = false }, 3000);
  }
}

function add_ar(data){
  var token = $window.sessionStorage.getItem('__token');
  var settings = {
    "url": host + "aranceles/add_ar", "method": "POST",
    "headers": {
      "Content-Type": "application/json; charset=UTF-8", 'auth': token
    },
    "data": data
  }
  var request = $http(settings);
  request.then(function(response) {
  console.log(response.data);
    if(angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Arancel actualizadó';
      $scope.kind = 'success';
      $scope.clave = ''; $scope.concepto = ''; $scope.unidad = ''; $scope.costo = ''; $scope.corres = '';
      get_aranceles()
      setTimeout(function() { $scope.req_error = false }, 3000);
    }if(!angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
      $scope.kind = 'danger';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }
  })
  .catch('error ' + console.error);
}

$scope.edit_ar = function(data){
  $scope.editing = true;
  $scope.clave = data.tipo; $scope.concepto = data.nomas; $scope.unidad = data.unidad; $scope.costo = data.precio;  $scope.id_arancel = data.idar2; //$scope.corres = data.corresponsal;
}

$scope.cancel = function(){
  $scope.editing = false;
  $scope.clave = ''; $scope.concepto = ''; $scope.unidad = ''; $scope.corres = ''; $scope.costo = '';
}

function up_ar(data){
  var token = $window.sessionStorage.getItem('__token');
  var settings = {
    "url": host + "aranceles/up_ar", "method": "POST",
    "headers": {
      "Content-Type": "application/json; charset=UTF-8", 'auth': token
    },
    "data": data
  }
  var request = $http(settings);
  request.then(function(response) {
  // console.log(response);
  console.log(response.data);
    if(angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Arancel actializadó';
      $scope.kind = 'success';
      $scope.cancel()
      get_aranceles()
      setTimeout(function() { $scope.req_error = false }, 3000);
    }if(!angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
      $scope.kind = 'danger';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }
  })
  .catch('error ' + console.error);
}

$scope.del_ar = function(data){
  if ($scope.kind_ara.descripcion) data.esid = $scope.kind_ara.public_id;
  var token = $window.sessionStorage.getItem('__token');
  var settings = {
    "url": host + "aranceles/del_ar", "method": "POST",
    "headers": {
      "Content-Type": "application/json; charset=UTF-8", 'auth': token
    },
    "data": data
  }
  var request = $http(settings);
  request.then(function(response) {
  // console.log(response);
  console.log(response.data);
    if(angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Arancel eliminado';
      $scope.kind = 'success';
      $scope.name = '';  $scope.email = '';
      get_aranceles()
      setTimeout(function() { $scope.req_error = false }, 3000);
    }if(!angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
      $scope.kind = 'danger';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }
  })
  .catch('error ' + console.error);
}

//requisitos ara
$scope.cambio_ara = function(i){
  if(i>$scope.pages_ara-1){
  }else if(i<0){
  }else{
   	$scope.currentPage_ara=i;
  }
 }

$scope.$watch('search', function(term) {
	$scope.filter = function() {
		$scope.noOfPages = Math.ceil($scope.filtered.length/$scope.entryLimit);
  	}
});

});

angular.module('colegio')
  .filter('startFrom', function() {
      return function(input, start) {
          if(input) {
              start = +start; //parse to int
              return input.slice(start);
          }
          return [];
      }
  });
