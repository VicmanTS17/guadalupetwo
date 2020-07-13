angular.module('colegio')

.controller('init_ctrer_colegio', function($scope, $window, $http, $q){

const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

  init()
  function init(){
    const __token = $window.sessionStorage.getItem('__token');
    if (angular.isUndefined(__token)) { $scope.logout(); }
    $scope.__caflag = ( $window.sessionStorage.getItem('__caflag')  == 'true' );
    get_pro(); get_esp();
    $scope.req_error = false;
  }

  $scope.logout = function(){
    $window.sessionStorage.removeItem('__token');
    $window.sessionStorage.removeItem('__caflag');
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

  function get_esp(){
    $http.post(host+"Common/get_publicesp")
    .then(function(response){
      console.log(response.data);
      $scope.esp_list = response.data;
    }).catch('error '+console.error);
  }

  $scope.set_session = function(data){
    $window.sessionStorage.setItem('__ara_data', JSON.stringify(data));
    $window.location.assign("#!aranceles");
  }
});
