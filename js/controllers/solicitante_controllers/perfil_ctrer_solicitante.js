angular.module('solicitante')
    .controller('perfil_ctrer_solicitante', function ($scope, $window, $http, $q) {

        const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

        get_pro(); $scope.especialidad = {};
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
                    $scope.user_data = response.data.data[0]; get_esp_cor(); get_esp();
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
            }).catch('error ' + console.error);
        }
        
        function get_esp_cor() {
            var token = $window.sessionStorage.getItem('__token');
            var req = {
                'method': 'POST', 'url': host + "Users/get_esp_cor",
                'headers': { 'Content-Type': "application/json", 'auth': token }
            }
            var request = $http(req);
            request.then(function (response) {
                console.log(response.data);
                if (angular.equals(response.data.status, true)) {
                    $scope.user_esp = response.data.data;
                } else {
                    $scope.req_error = true; $scope.message = 'Problemas para consultar perfil, contacte con soporte.';
                    $scope.kind = 'warning';
                }
            }).catch('error ' + console.error);
        }
        //get especialidades lista
        function get_esp(){
            $http.post(host+"Common/get_publicesp")
            .then(function(response){
              console.log(response.data);
              $scope.esp_list = response.data;
            }).catch('error '+console.error);
        }

        $scope.add_esp = function(){
            console.log($scope.especialidad);
            var token = $window.sessionStorage.getItem('__token');
            var req = {
                'method': 'POST', 'url': host + "Users/add_esp",
                'headers': { 'Content-Type': "application/json", 'auth': token },
                'data' : $scope.especialidad
            }
            var request = $http(req);
            request.then(function (response) {
                console.log(response.data);
                if (angular.equals(response.data.status, true)) {
                    if (angular.equals(response.data.data, false)) {
                        $scope.req_error = true; $scope.message = 'Esta especialidad ya fue registrada.';
                        $scope.kind = 'warning';
                    }
                    if (angular.equals(response.data.data, true)) {
                        $scope.req_error = true; $scope.message = 'Especialidad registrada con éxito.';
                        $scope.kind = 'succes'; get_esp_cor();
                    }
                } else {
                    $scope.req_error = true; $scope.message = 'Problemas para consultar perfil, contacte con soporte.';
                    $scope.kind = 'warning';
                }
            }).catch('error ' + console.error);
        }

        $scope.del_esp = function(data){
            var token = $window.sessionStorage.getItem('__token');
            var req = {
                'method': 'POST', 'url': host + "Users/del_esp",
                'headers': { 'Content-Type': "application/json", 'auth': token },
                'data' : data.public_id
            }
            var request = $http(req);
            request.then(function (response) {
                console.log(response.data);
                if (angular.equals(response.data.status, true)) {
                    $scope.req_error = true; $scope.message = 'Especialidad registrada con éxito.';
                    $scope.kind = 'succes'; get_esp_cor();
                } else {
                    $scope.req_error = true; $scope.message = 'Problemas para consultar perfil, contacte con soporte.';
                    $scope.kind = 'warning';
                }
            }).catch('error ' + console.error);
        }
    });