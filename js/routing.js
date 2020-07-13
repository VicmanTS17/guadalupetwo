//controlador master
angular.module('master', ['ngRoute', 'naif.base64'])
  .config(['$routeProvider', '$locationProvider',
    function ($routeProvider, $locationProvider) {
      $routeProvider
        .when('/users', {
          templateUrl: 'templates/Master/users_list.html',
          controller: 'users_crter_master'
        })
        .when('/users_lic', {
          templateUrl: 'templates/Master/users_lic.html',
          controller: 'users_crter_master'
        })
        .when('/ticket', {
          templateUrl: 'templates/Master/ticket.html',
          controller: 'ticket_crter_master'
        })
        .when('/perfil', {
          templateUrl: 'templates/Master/perfil.html',
          controller: 'perfil_crter_master'
        })
        .otherwise("/users");
    }]);

//controlador solicitante
angular.module('solicitante', ['ngRoute', 'oc.lazyLoad', 'naif.base64', 'kendo.directives'])
  .config(['$routeProvider', '$locationProvider',
    function ($routeProvider, $locationProvider) {
      $routeProvider
        .when('/licencias', {
          templateUrl: 'templates/Solicitante/licencias_list.html',
          controller: 'licencias_ctrer_solicitante',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/solicitante_controllers/licencias_ctrer_solicitante.js');
            }]
          }
        })
        .when('/corres', {
          templateUrl: 'templates/Solicitante/corres_list.html',
          controller: 'licencias_ctrer_solicitante',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/solicitante_controllers/licencias_ctrer_solicitante.js');
            }]
          }
        })
        .when('/add_lic', {
          templateUrl: 'templates/Solicitante/add_licencia.html',
          controller: 'licencias_ctrer_solicitante',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/solicitante_controllers/licencias_ctrer_solicitante.js');
            }]
          }
        })
        .when('/add_docs', {
          templateUrl: 'templates/Solicitante/add_docs.html',
          controller: 'licencias_ctrer_solicitante',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/solicitante_controllers/licencias_ctrer_solicitante.js');
            }]
          }
        })
        .when('/add_pagos', {
          templateUrl: 'templates/Solicitante/add_pagos.html',
          controller: 'pagos_ctrer_solicitante',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/solicitante_controllers/pagos_ctrer_solicitante.js');
            }]
          }
        })
        .when('/ticket', {
          templateUrl: 'templates/Solicitante/ticket.html',
          controller: 'ticket_ctrer_solicitante',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/solicitante_controllers/ticket_ctrer_solicitante.js');
            }]
          }
        })
        .when('/perfil', {
          templateUrl: 'templates/Solicitante/perfil.html',
          controller: 'perfil_ctrer_solicitante',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/solicitante_controllers/perfil_ctrer_solicitante.js');
            }]
          }
        })
        .when('/docs', {
          templateUrl: 'templates/Solicitante/docs.html',
          controller: 'docs_ctrer_solicitante',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/solicitante_controllers/docs_ctrer_solicitante.js');
            }]
          }
        })
        .otherwise("/licencias");
    }]);

//controlador servidor
angular.module('servidor', ['ngRoute', 'oc.lazyLoad', 'naif.base64', 'kendo.directives'])
  .config(['$routeProvider', '$locationProvider',
    function ($routeProvider, $locationProvider) {
      $routeProvider
        .when('/licencias', {
          templateUrl: 'templates/Servidor/licencias_list.html',
          controller: 'licencias_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/licencias_ctrer_servidor.js');
            }]
          }
        })
        .when('/add_docs', {
          templateUrl: 'templates/Servidor/add_docs.html',
          controller: 'licencias_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/licencias_ctrer_servidor.js');
            }]
          }
        })
        .when('/add_der', {
          templateUrl: 'templates/Servidor/add_derechos.html',
          controller: 'derechos_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/derechos_ctrer_servidor.js');
            }]
          }
        })
        .when('/detalles_lic', {
          templateUrl: 'templates/Servidor/detalles_lic.html',
          controller: 'licencias_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/licencias_ctrer_servidor.js');
            }]
          }
        })
        .when('/colaboradores', {
          templateUrl: 'templates/Servidor/cola_list.html',
          controller: 'colaborador_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/colaborador_ctrer_servidor.js');
            }]
          }
        })
        .when('/ver_pagos', {
          templateUrl: 'templates/Servidor/ver_pagos.html',
          controller: 'pagos_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/pagos_ctrer_servidor.js');
            }]
          }
        })
        .when('/users_lic', {
          templateUrl: 'templates/Servidor/users_lic.html',
          controller: 'colaborador_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/colaborador_ctrer_servidor.js');
            }]
          }
        })
        .when('/ticket', {
          templateUrl: 'templates/Servidor/ticket.html',
          controller: 'ticket_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/ticket_ctrer_servidor.js');
            }]
          }
        })
        .when('/derechos', {
          templateUrl: 'templates/Servidor/derechos.html',
          controller: 'derechos_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/derechos_ctrer_servidor.js');
            }]
          }
        })
        .when('/solicitantes', {
          templateUrl: 'templates/Servidor/solicitante_list.html',
          controller: 'solicitante_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/solicitante_ctrer_servidor.js');
            }]
          }
        })
        .when('/perfil', {
          templateUrl: 'templates/Servidor/perfil.html',
          controller: 'perfil_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/perfil_ctrer_servidor.js');
            }]
          }
        })
        .when('/docs', {
          templateUrl: 'templates/Servidor/docs.html',
          controller: 'docs_ctrer_servidor',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/servidor_controllers/docs_ctrer_servidor.js');
            }]
          }
        })
        .otherwise("/licencias");
    }]);

//controlador colegio
angular.module('colegio', ['ngRoute', 'oc.lazyLoad', 'naif.base64'])
  .config(['$routeProvider', '$locationProvider',
    function ($routeProvider, $locationProvider) {
      $routeProvider
        .when('/ticket', {
          templateUrl: 'templates/Colegio/ticket.html',
          controller: 'ticket_ctrer_colegio',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/colegio_controllers/ticket_ctrer_colegio.js');
            }]
          }
        })
        .when('/licencias', {
          templateUrl: 'templates/Colegio/licencias_list.html',
          controller: 'licencias_ctrer_colegio',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/colegio_controllers/licencias_ctrer_colegio.js');
            }]
          }
        })
        .when('/ver_pagos', {
          templateUrl: 'templates/Colegio/ver_pagos.html',
          controller: 'pagos_ctrer_colegio',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/colegio_controllers/pagos_ctrer_colegio.js');
            }]
          }
        })
        .when('/aranceles', {
          templateUrl: 'templates/Colegio/aranceles.html',
          controller: 'aranceles_ctrer_colegio',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/colegio_controllers/aranceles_ctrer_colegio.js');
            }]
          }
        })
        .when('/peritos', {
          templateUrl: 'templates/Colegio/perito_list.html',
          controller: 'peritos_ctrer_colegio',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/colegio_controllers/peritos_ctrer_colegio.js');
            }]
          }
        })
        .when('/perfil', {
          templateUrl: 'templates/Colegio/perfil.html',
          controller: 'perfil_ctrer_colegio',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/colegio_controllers/perfil_ctrer_colegio.js');
            }]
          }
        })
        .when('/docs', {
          templateUrl: 'templates/Colegio/docs.html',
          controller: 'docs_ctrer_colegio',
          resolve: {
            js: ['$ocLazyLoad', function ($ocLazyLoad) {
                  return $ocLazyLoad.load('js/controllers/colegio_controllers/docs_ctrer_colegio.js');
            }]
          }
        })
        .otherwise("/licencias");
    }]);
