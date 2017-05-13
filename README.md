# Unimatrix Backend

[![Version](https://img.shields.io/packagist/v/unimatrix/backend.svg?style=flat-square)](https://packagist.org/packages/unimatrix/backend)
[![Total Downloads](https://img.shields.io/packagist/dt/unimatrix/backend.svg?style=flat-square)](https://packagist.org/packages/unimatrix/backend/stats)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/unimatrix/backend/master/LICENSE)

Backend for CakePHP 3.4

## Requirements
* PHP >= 7
* CakePHP >= 3.4

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require unimatrix/backend:~1.0
```

Don't forget to add it to bootstrap
```
Plugin::load('Unimatrix/Backend', ['routes' => true, 'bootstrap' => true]);
```

## Configuration

Of course you have to add some things in your `config/app.php`
```
    /**
     * Backend settings
     *
     * - security - Enables security modules, if ssl is set to true backend wont load without https
     * - credentials - The backend auth credentials that will allow you to login
     * - ckfinder - License information for ckfinder and backend settings (only local or ftp supported)
     *       - http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_backends
     */
    'Backend' => [
        'security' => [
            'enabled' => true,
            'ssl' => false
        ],
        'credentials' => [
            'username' => 'user',
            'password' => 'pass'
        ],
        'ckfinder' => [
            'license' => 'your-license',
            'key' => 'your-license-key',
            'tmp' => TMP,
            'backend' => [
                'name' => 'default',
                'adapter' => 'local',
                'baseUrl' => '/up/',
                'root' => WWW_ROOT . 'up',
                'chmodFiles' => 0777,
                'chmodFolders' => 0755,
                'filesystemEncoding' => 'UTF-8',
            ]
        ]
    ],
 ```

## Usage

To use the backend plugin you have to add a prefix in your `config/routes.php`

Login is handled by the plugin but the rest of the controllers / modules can be in your application under src/controller/backend

```
// backend
Router::prefix('backend', function(RouteBuilder $routes) {
    $routes->connect('/', ['controller' => 'Dashboard', 'action' => 'index']);
    $routes->fallbacks(DashedRoute::class);
});
```

