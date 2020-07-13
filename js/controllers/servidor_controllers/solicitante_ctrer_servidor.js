angular.module('servidor')
.controller('solicitante_ctrer_servidor', function($scope, $window, $http, $q){

  const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

  get_users()

  // get users
  $scope.req_error = false;
  function get_users(){
    var token =  $window.sessionStorage.getItem('__token');
    var req = {
     'method': 'POST',
     'url': host+"Users/get_sol_ser",
     'headers': {
       'Content-Type':  "application/json",
       'auth': token
     }
    }
    var request = $http(req);
 		request.then(function(response){
      console.log(response.data);
      if (angular.equals(response.data.status, true)) {
        user_pagin(response.data.data);
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
      if (data[i].tipo_usuario === 'id_5ebe11ce542b89.25367119') { data[i].desc_tipo_usuario = 'Particular'; }
      if (data[i].tipo_usuario === 'id_5ebe10d0b4db29.48120814') { data[i].desc_tipo_usuario = 'DRO'; }
    }
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

  $scope.tipo_usr = 'id_5ebe10d0b4db29.48120814';
  $scope.change_tu = function(){
    if (angular.equals($scope.tipo_usr, 'id_5ebe10d0b4db29.48120814')) $scope.tipo_usr = 'id_5ebe11ce542b89.25367119';
    else $scope.tipo_usr = 'id_5ebe10d0b4db29.48120814';
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
