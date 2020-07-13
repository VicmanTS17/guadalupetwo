angular.module('servidor')
    .controller('perfil_ctrer_servidor', function ($scope, $window, $http, $q) {

        const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

        get_pro();
        
        $scope.req_error = false;
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
                    $scope.user_data.uma = parseFloat($scope.user_data.uma)
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
                if(angular.equals(response.data.status, true)){
                    $scope.kind = 'success'; $scope.message = 'Registro realizadó correctamente';
                    $scope.req_error = true;
                    setTimeout(function() { $scope.req_error = false; }, 2000);
                }else{
                    $scope.kind = 'warning'; $scope.message = 'Problemas para realizar registro, contacte con soporte.';
                    $scope.req_error = true;
                    setTimeout(function() { $scope.req_error = false; }, 2000);
                }
            }).catch('error ' + console.error);
        }

    });