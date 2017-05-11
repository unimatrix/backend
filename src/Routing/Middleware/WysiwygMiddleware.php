<?php

namespace Unimatrix\Backend\Routing\Middleware;

use Cake\Core\Plugin;
use Cake\Utility\Inflector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Wysiwyg (What You See Is What You Get)
 * This middleware will serve the ckfinder php correctly
 *
 * @author Flavius
 * @version 0.1
 */
class WysiwygMiddleware
{
    /**
     * Serve assets if the path matches one.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return \Psr\Http\Message\ResponseInterface A response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // got wysiwyg? get that and stop execution
        $wysiwyg = $this->_checkWysiwyg($request->getUri()->getPath());
        if($wysiwyg) {
            require $wysiwyg;
            exit;
        }

        // not wysiwyg? next!
        return $next($request, $response);
    }

    /**
     * Find the asset file location
     * @param string $url
     * @return string or bool
     */
    protected function _checkWysiwyg($url) {
        // match wysiwyg path
        if(strpos($url, 'ckfinder/core/connector/php/connector.php') !== false) {
            $assetFile = $this->_getAssetFile($url);
            if($assetFile)
                return $assetFile;
        }

        // nope
        return false;
    }

    /**
     * Find the asset file location
     * @param string $url
     * @return string or bool
     */
    protected function _getAssetFile($url) {
        // do plugin webroot path
        $parts = [];
        $segments = explode('/', ltrim($url, '/'));
        for($i = 0; $i < 2; $i++) {
            if(!isset($segments[$i]))
                break;

            $parts[] = Inflector::camelize($segments[$i]);
            $plugin = implode('/', $parts);

            if($plugin && Plugin::loaded($plugin)) {
                $segments = array_slice($segments, $i + 1);
                $pluginWebrootPath = str_replace('/', DS, Plugin::path($plugin)) . 'webroot' . DS . implode(DS, $segments);
                if(file_exists($pluginWebrootPath))
                    return $pluginWebrootPath;
            }
        }

        // not found?
        return false;
    }
}
