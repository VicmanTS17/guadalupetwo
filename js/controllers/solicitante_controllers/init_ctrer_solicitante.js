angular.module('solicitante')

.controller('init_ctrer_solicitante', function($scope, $window, $http, $q){

const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';
  
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
  
  init()
  function init(){
    const __token = $window.sessionStorage.getItem('__token');
    if (angular.isUndefined(__token)) { $scope.logout(); }
    $scope.__solflag = ( $window.sessionStorage.getItem('__solflag')  == 'true' );
    get_pro();
    $scope.req_error = false;
  }

  $scope.logout = function(){
    $window.sessionStorage.removeItem('__token');
    $window.sessionStorage.removeItem('__solflag');
    $window.location = 'index.html';
  }

  function get_pro(){
    var token =  $window.sessionStorage.getItem('__token');
    var req = { 'method': 'POST',
     'url': host+"Users/get_pro",
     'headers': { 'Content-Type':  "application/json", 'auth': token }
    }
    var request = $http(req);
 		request.then(function(response){
      console.log(response.data);
      if (angular.equals(response.data.status, true)) {
        $scope.user_data = response.data.data[0];
      }else {
        $scope.req_error = true; $scope.message = 'Problemas para consultar perfil, contacte con soporte.';
        $scope.kind = 'warning';
      }
    }).catch('error '+console.error);
  }

});
