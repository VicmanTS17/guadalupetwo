angular.module('servidor')

.controller('pagos_ctrer_servidor', function($scope, $window, $http){

    const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

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
            $scope.get_pagos()
            setTimeout(function() { $scope.req_error = false }, 3000);
          }if(!angular.equals(response.data.status, true)){
            $scope.req_error = true; $scope.message = 'Problemas para guardar información, contacte con soporte.';
            $scope.kind = 'danger';
            setTimeout(function() { $scope.req_error = false }, 3000);
          }
        })
        .catch('error ' + console.error);
    }

  function get_objt_session(){
    $scope.lic_data = JSON.parse($window.sessionStorage.getItem('__lic_data'));
  }
});