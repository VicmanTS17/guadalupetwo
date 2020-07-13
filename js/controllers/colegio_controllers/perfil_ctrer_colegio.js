angular.module('colegio')

  .controller('perfil_ctrer_colegio', function ($scope, $window, $http, $q) {

    const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

    get_pro();
    //document.getElementById('files').addEventListener('change', preview_file, false);
    //$scope.__caflag = ( $window.sessionStorage.getItem('__caflag')  == 'true' );

    function get_pro() {
      var token = $window.sessionStorage.getItem('__token');
      var req = {
        'method': 'POST',
        'url': host + "Users/get_pro",
        'headers': { 'Content-Type': "application/json", 'auth': token }
      }
      var request = $http(req);
      request.then(function (response) {
        console.log(response.data);
        if (angular.equals(response.data.status, true)) {
          $scope.user_data = response.data.data[0];
        } else {
          $scope.req_error = true; $scope.message = 'Problemas para consultar perfil, contacte con soporte.';
          $scope.kind = 'warning';
        }
      }).catch('error ' + console.error);
    }

    $scope.save_pro = function (data) {
      var token = $window.sessionStorage.getItem('__token');
      var req = {
        'method': 'POST',
        'url': host + "Users/save_pro",
        'headers': { 'Content-Type': "application/json", 'auth': token }, "data": data
      }

      var request = $http(req);
      request.then(function (response) {
        console.log(response);
        // if (angular.equals(response.data.status, true)) {
        //   $scope.user_data = response.data.data[0];
        // }else {
        //   $scope.req_error = true; $scope.message = 'Problemas para consultar perfil, contacte con soporte.';
        //   $scope.kind = 'warning';
        // }
      }).catch('error ' + console.error);
    }

    /*function preview_file(evt) {
      var files = evt.target.files; // FileList object

      //Obtenemos la imagen del campo "file". 
      for (var i = 0, f; f = files[i]; i++) {
        //Solo admitimos imágenes.
        if (!f.type.match('image.*')) {
          continue;
        }

        var reader = new FileReader();

        reader.onload = (function (theFile) {
          return function (e) {
            // Creamos la imagen.
            document.getElementById("list").innerHTML = ['<img class="thumb" src="', e.target.result, '" title="', escape(theFile.name), '"/>'].join('');
            document.getElementById('file-lavel').innerText = theFile.name;
          };
        })(f);

        reader.readAsDataURL(f);
      }
    }*/

  });