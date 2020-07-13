angular.module('solicitante')

.controller('pagos_ctrer_solicitante', function($scope, $window, $http){

    const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

    $scope.req_error = false;
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

    function get_objt_session(){
        $scope.lic_data = JSON.parse($window.sessionStorage.getItem('__lic_data'));
    }

    $scope.val_file = function() {
        var flag = false;
        
        //file not null
        if (angular.isUndefined($scope.file) || angular.equals($scope.file, '')){
            // archivo indefinido
            $scope.req_error = true; $scope.message = 'Debe de cargar archivo valido para continuar.';
            $scope.kind = 'warning';
            setTimeout(function() { $scope.req_error = false }, 3000);
        }else{
          //file
          if($scope.file.filesize <= 5242880){
            //file type
            if(angular.equals($scope.file.filetype, 'image/jpeg') || angular.equals($scope.file.filetype, 'image/JPEG')
              || angular.equals($scope.file.filetype, 'image/jpg') || angular.equals($scope.file.filetype, 'image/JPG')
              || angular.equals($scope.file.filetype, 'image/png') || angular.equals($scope.file.filetype, 'image/PNG')
              || angular.equals($scope.file.filetype, 'application/pdf') || angular.equals($scope.file.filetype, 'application/PDF')){
                flag = true;
              }else{
                $scope.req_error = true; $scope.message = "Formatos permitidos. 'JPEG', 'JPG', 'PNG', 'PDF', 'ZIP', 'RAR', 'DWG'";
                $scope.kind = 'danger';
              }
          // mayor de 5 megas
          }else{
            $scope.req_error = true; $scope.message = 'Tamaño máximo permitido 5 MB.';
            $scope.kind = 'warning';
            setTimeout(function() { $scope.req_error = false }, 3000);
          }
        } 
      if(flag){
        var predata = JSON.parse($window.sessionStorage.getItem('__lic_data'));
        var data = {};  data.bandera_carga = true; data.pago_para = ( $scope.pago_para == true ) ? true:false;
        var data = { 'data' : data, 'file' : $scope.file, 'traid': predata.public_id, }
        send_file(data)
      }
    }
    
      function send_file(data){
        var token = $window.sessionStorage.getItem('__token');
        var settings = {
          "url": host + "Pagos/add_pago",
          "method": "POST",
          "headers": {
            "Content-Type": "application/json; charset=UTF-8",
            'auth': token
          },
          "data": data
        }
        var request = $http(settings);
        request.then(function(response) {
        console.log(response);
          if(angular.equals(response.data.status, true) && angular.equals(response.data.data, 0)){
            $scope.req_error = true; $scope.message = 'Documento cargado';
            $scope.kind = 'success';
            $scope.get_pagos()
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
});