angular.module('servidor')
.controller('ticket_ctrer_servidor', function($scope, $window, $http, $q){
  const host = 'https://guadalupe.licenciaszac.net/back_end/index.php/';

  $scope.flag = 0;
  $scope.touch = false;

  $('#myModal').on('shown.bs.modal', function () {
    $('#myInput').trigger('focus')
  });

  $scope.validarImg = function () {
    var date = new Date();
		var dateStr = date.getFullYear() + "-" +
			("00" + (date.getMonth() + 1)).slice(-2) + "-" +
			("00" + date.getDate()).slice(-2) + " " +
			("00" + date.getHours()).slice(-2) + ":" +
			("00" + date.getMinutes()).slice(-2) + ":" +
			("00" + date.getSeconds()).slice(-2)
    $q(function (resolve, reject) {
      if (angular.isUndefined($scope.file) === true || angular.isUndefined($scope.ticket.descripcion) === true || angular.isUndefined($scope.ticket.asunto) === true ) {
        $scope.flag = 1;
        resolve(
          $scope.ticket.fecha = dateStr
        );
      }else if(angular.isUndefined($scope.file) === false && $scope.file.filetype !== "image/jpeg" && $scope.file.filetype !== "image/png" && $scope.file.filetype !== "image/jpg"){
        $scope.flag = 0;
        reject(
          console.log("el archivo")
        );
      } else {
        $scope.flag = 1;
        resolve(
          $scope.ticket.data_img = $scope.file,
          $scope.ticket.fecha = dateStr
        );
      }
    });
  }

  $scope.createTicket = async function(){
    if ($scope.touch == false) {
      $scope.touch = true;
      $scope.validarImg();
      if ($scope.flag === 1) {
        try {
          let settings = {
            "url": `${host}Ticket/createTicket`,
            "method": "POST",
            "headers": {
              "Content-Type": "application/json; charset=UTF-8",
              'auth': $window.sessionStorage.getItem('__token')
            },
            "data": $scope.ticket
          }
          let response = await $http(settings);
              console.log(response);
              $scope.gettickets()
        } catch (error) {
          console.log(error);
        }
      } else {
        console.log("nada");
      }
    }
  }

  $scope.gettickets = function(){
    var settings = {
        "url": `${host}Ticket/getUsrTicket`,
        "method": "GET",
        "headers": {
          "Content-Type": "application/json; charset=UTF-8",
          'auth': $window.sessionStorage.getItem('__token')
        }
      }
      var request = $http(settings)
      request.then(function(response){
                $scope.problemas = response.data;
                console.log(response);
      })
      .catch('error '+console.error);
  }

  $scope.getmsn = function(d){
    $scope.imagen = d.path_imagen;
    $scope.asunto = d.asunto;
    $scope.fecha = d.fecha;
    $scope.id_asunto = d.public_id;
    var settings = {
        "url": `${host}Ticket/get_dataticket/${d.public_id}`,
        "method": "GET",
        "headers": {
          "Content-Type": "application/json; charset=UTF-8",
          'auth': $window.sessionStorage.getItem('__token')
        }
      }
      var request = $http(settings)
      request.then(function(response){
                $scope.mensajes = response.data;
                console.log(response);
      })
      .catch('error '+console.error);
  }

  function validarmsn() {
    $scope.mensaje={};
    var date = new Date();
    var today = date.getFullYear() + "-" +
      ("00" + (date.getMonth() + 1)).slice(-2) + "-" +
      ("00" + date.getDate()).slice(-2) + " " +
      ("00" + date.getHours()).slice(-2) + ":" +
      ("00" + date.getMinutes()).slice(-2) + ":" +
      ("00" + date.getSeconds()).slice(-2)
    $q(function (resolve, reject) {
      if (angular.isUndefined($scope.msj) === true || angular.isUndefined($scope.id_asunto) === true) {
        $scope.flag = 0;
        $scope.touch = false;
        reject(
          console.log("no selccionaste un problema o llenaste el mensaje")
        );
      } else {
        $scope.flag = 1;
        resolve(
          $scope.mensaje.ticket_head = $scope.id_asunto,
          $scope.mensaje.msj = $scope.msj,
          $scope.mensaje.fecha = today
        );
      }
    });
  }

  $scope.sendmsn = async function(){
    if ($scope.touch == false) {
      $scope.touch = true;
      validarmsn();
      if ($scope.flag === 1) {
        try {
          let settings = {
            "url": `${host}Ticket/sendmsn`,
            "method": "POST",
            "headers": {
              "Content-Type": "application/json; charset=UTF-8",
              'auth': $window.sessionStorage.getItem('__token')
            },
            "data": $scope.mensaje
          }
          let response = await $http(settings);
              if(angular.equals(response.data,"se registro mensaje")){
                reloadmsn($scope.mensaje.ticket_head);
                $scope.msj = "";
              }else{
                $scope.touch = false;
              }
              console.log(response);
        } catch (error) {
          console.log(error);
        }
      } else {
        console.log("nada");
      }
    }
  }

  function reloadmsn(id){
    var settings = {
        "url": `${host}Ticket/get_dataticket/${id}`,
        "method": "GET",
        "headers": {
          "Content-Type": "application/json; charset=UTF-8",
          'auth': $window.sessionStorage.getItem('__token')
        }
      }
      var request = $http(settings)
      request.then(function(response){
                $scope.mensajes = response.data;
                console.log(response);
      })
      .catch('error '+console.error);
  }

  $scope.closeTicket = function(id){
    var settings = {
        "url": `${host}Ticket/closeticket/${id}`,
        "method": "DELETE",
        "headers": {
          "Content-Type": "application/json; charset=UTF-8",
          'auth': $window.sessionStorage.getItem('__token')
        }
      }
      var request = $http(settings)
      request.then(function(response){
                if(angular.equals(response.data,"Ticket cerrado")){
                  $scope.gettickets();
                }
      })
      .catch('error '+console.error);
  }

});
