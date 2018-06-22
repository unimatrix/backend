<?php

namespace Unimatrix\Backend\Routing\Middleware;

use Cake\Core\Plugin;
use Cake\Utility\Inflector;
use CKSource\CKFinder\CKFinder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Wysiwyg (What You See Is What You Get)
 * This middleware will serve the ckfinder php correctly
 *
 * @author Flavius
 * @version 1.2
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
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next) {
        // got wysiwyg? get that and stop execution
        $wysiwyg = $this->isCKFinder($request->getUri()->getPath());
        if($wysiwyg) {
            $config = str_replace('core' . DS . 'connector' . DS . 'php' . DS . 'connector.php', 'config.php', $wysiwyg);
            $ckfinder = new CKFinder($config, $request);
            $symfonyRequest = Request::createFromGlobals();
            $symfonyResponse = $ckfinder->handle($symfonyRequest);

            ob_start(); // symfony cleared this :(
            return $response->withType('json')->withStringBody($symfonyResponse->getContent());

        // not wysiwyg? next!
        } else return $next($request, $response);
    }

    /**
     * Find the asset file location
     * @param string $url
     * @return string or bool
     */
    private function isCKFinder($url) {
        // match wysiwyg path
        if(strpos($url, 'ckfinder/core/connector/php/connector.php') !== false) {
            $pluginFile = $this->_getPluginFile($url);
            if($pluginFile !== null && file_exists($pluginFile))
                return $pluginFile;
        }

        // nope
        return false;
    }

    /**
     * Builds plugin file path based off url
     * @param string $url Plugin URL
     * @return string Absolute path for plugin file
     */
    protected function _getPluginFile($url) {
        $parts = explode('/', ltrim($url, '/'));
        $pluginPart = [];
        for($i = 0; $i < 2; $i++) {
            // @codeCoverageIgnoreStart
            if(!isset($parts[$i]))
                break;
            // @codeCoverageIgnoreEnd
            $pluginPart[] = Inflector::camelize($parts[$i]);
            $plugin = implode('/', $pluginPart);
            if($plugin && Plugin::loaded($plugin)) {
                $parts = array_slice($parts, $i + 1);
                $fileFragment = implode(DIRECTORY_SEPARATOR, $parts);
                $pluginWebroot = Plugin::path($plugin) . 'webroot' . DIRECTORY_SEPARATOR;
                return $pluginWebroot . $fileFragment;
            }
        }
    }
}
