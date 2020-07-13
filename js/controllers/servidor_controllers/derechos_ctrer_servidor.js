angular.module('servidor')

.controller('derechos_ctrer_servidor', function($scope, $window, $http, $q){

const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

get_derechos()
// get licencias get_tramites
$scope.req_error = false
function get_derechos(){
  get_objt_session()
  var token =  $window.sessionStorage.getItem('__token');
  var req = {
   'method': 'POST',
   'url': host+"Derechos/get_der_ser",
   'headers': {
     'Content-Type':  "application/json",
     'auth': token
   }
  }
  var request = $http(req);
  request.then(function(response){
    console.log(response.data);
    if (angular.equals(response.data.status, true)) {
      $scope.derechos_list = response.data.data;

      $scope.derechos_list.forEach(element => {
        element.precio = parseFloat(element.costo) * parseFloat($scope.user_data.uma);
      });

      //filtrado para paginado de tabla
      $scope.pageData_ara = [];
      $scope.currentPage_ara = 0;
      $scope.pages_ara = 0;
      $scope.pageSize_ara = 10;

      $scope.pages_ara=Math.ceil($scope.derechos_list.length/$scope.pageSize_ara);
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

///// Aranceles
$scope.result = []
$scope.result.total_gral = 0
  $scope.add = function(item) {
    if (angular.isUndefined(item.cantidad) || angular.equals(item.cantidad, "")) {
      M.toast({html: 'Cantidad indefinida.', classes: 'rounded', displayLength:6000})
    } else {
      // console.log(item);
      var precio = Number(item.costo);
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

//save gral
$scope.save_gral = function(){
    var token 	=  $window.sessionStorage.getItem('__token');
    var predata = JSON.parse($window.sessionStorage.getItem('__lic_data'));
    var data 	= {};

    data.licid 				  = predata.public_id
    data.traid 				  = predata.id_tramite
    data.ara 				  = $scope.result
    data.ara[data.ara.length] = $scope.result.total_gral
    console.log(data);

    var settings = {
         "url": host+"Derechos/save_lic_der",
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
      setTimeout(function() { $window.location = "#!licencias" }, 5000);
    }
    if (angular.equals(response.data, false)) {
      $scope.req_error = true; $scope.message = 'Problemas para guardar informacion, contacte con soporte.';
      $scope.kind = 'warning';
    }
    })
    .catch('error '+console.error);
}

$scope.set_session = function(data){
    $window.sessionStorage.setItem('__lic_data', JSON.stringify(data));
  }

function get_objt_session(){
    $scope.lic_data = JSON.parse($window.sessionStorage.getItem('__lic_data'));
}

// G E S T I O N   D E R E C H O
$scope.val_datos = function(flag_query){
  var flag_error = false;
  var flag_clv = false; var flag_con = false; var flag_und = false; var flag_cos = false;

  if(angular.equals($scope.clave, '') ||angular.isUndefined($scope.clave)){ flag_clv = true }
  if(angular.equals($scope.concepto, '') ||angular.isUndefined($scope.concepto)){ flag_con = true }
  if(angular.equals($scope.unidad, '') ||angular.isUndefined($scope.unidad)){ flag_und = true }
  if(angular.equals($scope.costo, '') ||angular.isUndefined($scope.costo)){ flag_cos = true }

  if( flag_clv || flag_con ||flag_und || flag_cos ){ flag_error = true}
  if(!flag_error){

    var data = { 'clave' : $scope.clave, 'concepto' : $scope.concepto, 'unidad' : $scope.unidad, 'costo' : $scope.costo };
    if (angular.equals(flag_query, 1)) { add_der(data) }

    if (angular.equals(flag_query, 0)) {
      data.id_derecho = $scope.id_derecho; up_der(data);
    }

  }else {
    $scope.req_error = true; $scope.message = 'Todos los datos son requeridos.';
    $scope.kind = 'warning';
    setTimeout(function() { $scope.req_error = false }, 3000);
  }
}

function add_der(data){
  var token = $window.sessionStorage.getItem('__token');
  var settings = {
    "url": host + "Derechos/add_der", "method": "POST",
    "headers": {
      "Content-Type": "application/json; charset=UTF-8", 'auth': token
    },
    "data": data
  }
  var request = $http(settings);
  request.then(function(response) {
  console.log(response.data);
    if(angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Derecho actualizadó';
      $scope.kind = 'success';
      $scope.clave = ''; $scope.concepto = ''; $scope.unidad = ''; $scope.costo = '';
      get_derechos()
      setTimeout(function() { $scope.req_error = false }, 3000);
    }if(!angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
      $scope.kind = 'danger';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }
  })
  .catch('error ' + console.error);
}

$scope.edit_der = function(data){
  $scope.editing = true;
  $scope.clave = data.clave; $scope.concepto = data.concepto; $scope.unidad = data.unidad; $scope.costo = data.costo; $scope.id_derecho = data.id_derecho;
}

$scope.cancel = function(){
  $scope.editing = false;
  $scope.clave = ''; $scope.concepto = ''; $scope.unidad = ''; $scope.costo = '';
}

function up_der(data){
  var token = $window.sessionStorage.getItem('__token');
  var settings = {
    "url": host + "Derechos/up_der", "method": "POST",
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
      $scope.req_error = true; $scope.message = 'Derecho actializadó';
      $scope.kind = 'success';
      $scope.name = '';  $scope.email = '';
      get_derechos()
      setTimeout(function() { $scope.req_error = false }, 3000);
    }if(!angular.equals(response.data.status, true)){
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
      $scope.kind = 'danger';
      setTimeout(function() { $scope.req_error = false }, 3000);
    }
  })
  .catch('error ' + console.error);
}

$scope.del_der = function(data){
  var token = $window.sessionStorage.getItem('__token');
  var settings = {
    "url": host + "Derechos/del_der", "method": "POST",
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
      $scope.req_error = true; $scope.message = 'Derecho eliminado';
      $scope.kind = 'success';
      $scope.name = '';  $scope.email = '';
      get_derechos()
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
