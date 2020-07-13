angular.module('solicitante')

.controller('docs_ctrer_solicitante', function($scope, $window, $http){

    const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

    $scope.req_error = false;
    $scope.get_docs = function(){
      $scope.comentario = ''; $scope.file = '';
      get_objt_session()
      var token =  $window.sessionStorage.getItem('__token');
      var req = {
      'method': 'POST',
      'url': host+"Documentos/get_docs",
      'headers': {
          'Content-Type':  "application/json", 'auth': token
      },
      'data' : token
      }
      var request = $http(req);
      request.then(function(response){
          console.log(response);
          if (angular.equals(response.data.status, true)) {
            type_desc(response.data.data);
            $scope.pagos_list = response.data.data;
          }else {
          $scope.req_error = true; $scope.message = 'Problemas para realizar consulta, contacte con soporte licencias.';
          $scope.kind = 'warning';
          }
      }).catch('error '+console.error);
    }

    function type_desc(array) {
        array.forEach(element => {
            switch (element.tipo_usuario) {
                case 'id_5ebafe3b361083.91981512':
                    element.tipo_usuario_desc = 'Municipio';
                    break;
                case 'id_5ebc623f267603.74886889':
                    element.tipo_usuario_desc = 'Municipio';
                    break;
                case 'id_5ebc667ccb01b7.90108736':
                    element.tipo_usuario_desc = 'Colegio';
                    break;
                case 'id_5ebe11ce542b89.25366485':
                    element.tipo_usuario_desc = 'CADRO';
                    break;
                default:
                    break;
            }
        });
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
        var data = {}; data.doc_para = false;
        data.comentario = ( $scope.comentario == '' || $scope.comentario == undefined ) ? '':$scope.comentario;
        data.file = $scope.file;
        send_file(data)
      }
    }
    
    function send_file(data){
        var token = $window.sessionStorage.getItem('__token');
        var settings = {
          "url": host + "Documentos/add_doc",
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
            $scope.kind = 'success'; $scope.get_docs();
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

    $scope.add_coment_doc = function(item){
      var data  = item
      var token = $window.sessionStorage.getItem('__token');
      var settings = {
        "url": host + "Documentos/add_comen_doc",
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
          $scope.kind = 'success'; $scope.get_docs();
          setTimeout(function() { $scope.req_error = false }, 3000);
        }if(!angular.equals(response.data.status, true)){
          $scope.req_error = true; $scope.message = 'Problemas para guardar información, contacte con soporte.';
          $scope.kind = 'danger';
          setTimeout(function() { $scope.req_error = false }, 3000);
        }
      })
      .catch('error ' + console.error);
  }

  $scope.est_doc = function(data){
    var token = $window.sessionStorage.getItem('__token');
    var settings = {
      "url": host + "Documentos/estado_doc",
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
        $scope.notif_pay = true; $scope.message = 'Estado actializadó';
        $scope.kind = 'success'; $scope.get_docs();
        setTimeout(function() { $scope.req_error = false }, 3000);
      }if(!angular.equals(response.data.status, true)){
        $scope.notif_pay = true; $scope.message = 'Problemas para cargar información, contacte con soporte.';
        $scope.kind = 'danger';
        setTimeout(function() { $scope.notif_pay = false }, 3000);
      }
    })
    .catch('error ' + console.error);
  }
});