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

namespace R7;
require_once "lib/Request.php";
require_once "lib/Response.php";
require_once "lib/Route.php";

class R7 {
    private $routes = array();

    /**
     * Add a route to the application
     * @param Route $r
     */
    public function addRoute(Route $r) {
        $this->routes[] = $r;
    }

    /**
     * Executes the application
     * if there is a response object coming from one of the routes, then it will be displayed
     * @return mixed
     */
    public function execute() {
        $method = strtolower($_SERVER["REQUEST_METHOD"]);
        $fullUri = parse_url($_SERVER["REQUEST_URI"]);
        $uri = $fullUri["path"];

        $response = null;
        foreach($this->routes as $route) {
			try {
            	$r = $route->executeOn($uri, $method);
				if($r) $response = $r;
			} catch (\Exception $e) {
				$response = new Response(500, array("status" => "Server Error" ,"message" => $e->getMessage()));
			}
        }
		if(!$response) $response = new Response(404, array("status" => "Not Found"));
        $type = isset($_GET["type"]) ? $_GET["type"] : "json";
        switch($type) {
            case "json":
                echo $response->toJSON();
                break;
            case "xml":
                echo $response->toXML();
                break;
            case "csv":
                echo $response->toCSV();
                break;
            default:
                break;
        }
    }
}
