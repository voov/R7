<?php
/*
    R7 is the new RESTful API framework created by VOOV
    Copyright (c) 2012, VOOV Ltd.
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:
     * Redistributions of source code must retain the above copyright
    notice, this list of conditions and the following disclaimer.
     * Redistributions in binary form must reproduce the above copyright
    notice, this list of conditions and the following disclaimer in the
    documentation and/or other materials provided with the distribution.
     * Neither the name of the VOOV Ltd. nor the
    names of its contributors may be used to endorse or promote products
    derived from this software without specific prior written permission.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
    ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL VOOV LTD. BE LIABLE FOR ANY
    DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
    LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
    ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
    SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */


/**
 * Some ideas taken from:
 * http://blog.sosedoff.com/2009/09/20/rails-like-php-url-router/
 */
class Route {
    private $routeUri;
    private $uriRegex;
    private $params;
    private $funcHooks = array();

    function __construct($routeUri) {
        $this->routeUri = $routeUri;
        $parameter_names = array();
        preg_match_all('/:([\w]+)/', $routeUri, $parameter_names, PREG_PATTERN_ORDER);
        foreach($parameter_names[0] as $pnames) {
            $this->params[substr($pnames, 1)] = null;
        }

        $script = preg_quote($_SERVER["SCRIPT_NAME"], '/');

        // TODO: add possibility to override default regex
        $this->uriRegex = '/^';
        $this->uriRegex .= '(?:' . $script . ')?';
        $routeUri = str_replace('/', '\/', $routeUri);
        $this->uriRegex .=  preg_replace('/:[\w]+/', '([a-zA-Z0-9_\+\-%]+)', $routeUri);
        $this->uriRegex .= '\/?$/';
    }

    /**
     * Call the given method based on the request method
     * add it to the function hooks
     * @param $name
     * @param $arguments
     * @return \R7\Route
     */
    public function __call($name, $arguments) {
        // check if there is a function hook already for that method
        $func = is_callable($arguments[0]) ? $arguments[0] : array($arguments[0], "main");
        if(isset($this->funcHooks[$name])) {
            $this->funcHooks[$name][] = $func;
        }  else {
            // make it an array, always!
            $this->funcHooks[$name] = array($func);
        }

        return $this; //to be able to chain
    }

    /**
     * Execute the route on the given URI with the given method
     * @param $uri
     * @param string $method
     * @return bool|mixed|null
     */
    public function executeOn($uri, $method="get") {
        $matches = array();
        $response = null;
        if(preg_match($this->uriRegex, $uri, $matches)) {

            $index = 1;
            foreach($this->params as $param_key => $param_value) {
                $this->params[$param_key] = $matches[$index++];
            }

            // There can be more than one method for a route, so make it
            // iterate through the array

            foreach($this->funcHooks[$method] as $func) {

                //$retvalue = $func($this->params);
                $retvalue = call_user_func($func, new Request(), $this->params);
                if($retvalue != null) $response = $retvalue;
            }
            return $response;
        }
        return false;
    }

    /**
     * Creates a new Route object with the given route URI
     * @static
     * @param $routeUri
     * @return Route
     */
    static function create($routeUri) {
        return new self($routeUri);
    }
}
