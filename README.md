# Unimatrix Backend

[![Build Status](https://api.travis-ci.org/unimatrix/backend.svg?branch=master)](https://travis-ci.org/unimatrix/backend)
[![Coverage Status](https://codecov.io/gh/unimatrix/backend/branch/master/graph/badge.svg)](https://codecov.io/gh/unimatrix/backend)
[![Latest Stable Version](https://poser.pugx.org/unimatrix/backend/v/stable.svg)](https://packagist.org/packages/unimatrix/backend)
[![License](https://poser.pugx.org/unimatrix/backend/license.svg)](https://github.com/unimatrix/backend/blob/master/LICENSE)
[![Total Downloads](https://poser.pugx.org/unimatrix/backend/d/total.svg)](https://packagist.org/packages/unimatrix/backend)

Backend for CakePHP 3.6

## Requirements
* PHP >= 7.1
* CakePHP >= 3.6

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require unimatrix/backend:^3.0
```

Don't forget to load it under your bootstrap function in `Application.php`
```
$this->addPlugin('Unimatrix/Cake');
$this->addPlugin('Unimatrix/Backend');
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
            'salt' => '....64characters....', // optional to encrypt backend cookies automatically
            'enabled' => true,
            'ssl' => false
        ],
        'credentials' => [
            'username' => 'user',
            'password' => 'pass',
            'cookie' => 'backend_credentials_remember' // optional cookie autologin name
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
                'filesystemEncoding' => 'UTF-8'
            ]
        ],
        'whitelabel' => [
            'product' => 'Unimatrix Venture Digital Platform System',
            'website' => 'https://venture.unimatrix.ro'
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
});
```
