angular.module('colegio')

.controller('licencias_ctrer_colegio', function($scope, $window, $http, $q){

const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

get_licenias()
// get licencias get_tramites
$scope.req_error = false
function get_licenias(){
  var token =  $window.sessionStorage.getItem('__token');
  var req = {
   'method': 'POST',
   'url': host+"Licencias/get_licencias_col",
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
    }else {
      $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte.';
      $scope.kind = 'warning';
    }
  }).catch('error '+console.error);
}

$scope.set_session = function(data){
  $window.sessionStorage.setItem('__lic_data', JSON.stringify(data));
}

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