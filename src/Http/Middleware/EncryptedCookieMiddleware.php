<?php

namespace Unimatrix\Backend\Http\Middleware;

use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\Cookie\CookieCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Http\Middleware\EncryptedCookieMiddleware as CakeEncryptedCookieMiddleware;

/**
 * Encrypted Cookie
 * Wrapper of the original cake encrypted cookie middleware with the addition
 * of encrypting / decrypting automatically every cookie that starts with 'backend_'
 *
 * @author Flavius
 * @version 1.0
 */
class EncryptedCookieMiddleware extends CakeEncryptedCookieMiddleware
{
    /**
     * Constructor
     *
     * @param string $cipherType The cipher type to use. Defaults to 'aes', but can also be 'rijndael' for
     *   backwards compatibility.
     */
    public function __construct($cipherType = 'aes') {
        parent::__construct([], Configure::read('Backend.security.salt'), $cipherType);
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Http\Middleware\EncryptedCookieMiddleware::decodeCookies()
     */
    protected function decodeCookies(ServerRequestInterface $request) {
        $this->cookieNames = $this->processCookies($request->getCookieParams());
        return parent::decodeCookies($request);
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Http\Middleware\EncryptedCookieMiddleware::encodeCookies()
     */
    protected function encodeCookies(Response $response) {
        $this->cookieNames = $this->processCookies($response->getCookieCollection());
        return parent::encodeCookies($response);
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Http\Middleware\EncryptedCookieMiddleware::encodeSetCookieHeader()
     */
    protected function encodeSetCookieHeader(ResponseInterface $response) {
        $this->cookieNames = $this->processCookies(CookieCollection::createFromHeader($response->getHeader('Set-Cookie')));
        return parent::encodeSetCookieHeader($response);
    }

    /**
     * Filter out cookies and return only those that start with '_backend'
     *
     * @param array $cookies
     * @return array
     */
    private function processCookies($cookies = []) {
        // collection? overwrite as simple array
        if($cookies instanceof CookieCollection) {
            $temp = [];
            foreach($cookies as $cookie)
               $temp[$cookie->getName()] = $cookie->getValue();
            $cookies = $temp;
        }

        // search for backend cookies
        $out = [];
        foreach($cookies as $cookie => $value)
            if(substr($cookie, 0, 8) === 'backend_')
                $out[] = $cookie;

        // return backend cookies
        return $out;
    }
}
