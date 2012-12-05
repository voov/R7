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

class Request {

	private $data = array();
	public function __construct() {
		// TODO: clean the input
		$this->data = $_REQUEST;
	}

	/**
	 * Magic getter
	 * @param $key
	 * @return mixed
	 */
	public function __get($key) {
		return $this->data[$key];
	}


	/**
	 * Magic setter
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->data[$key] = $value;
	}


	/**
	 * Verifies if data wasn't tampered with
	 * @param $pubKey
	 * @return int
	 * @throws Exception
	 */
	public function verifySignature($pubKey) {
		if(!isset($this->data["signature"])) {
			throw new Exception("Signature is not found!");
		}
		$key = openssl_pkey_get_public($pubKey);
		$bufferToVerify = $this->data;
		unset($bufferToVerify["signature"]); // remove the signature element
		return openssl_verify(http_build_query($bufferToVerify), $this->data["signature"], $key);
	}

	/**
	 * Creates a signed request string
	 * @param $data
	 * @param $privKey
	 * @param $password
	 * @return string
	 */
	public static function makeRequest($data, $privKey, $password) {
		$queryToSign = http_build_query($data);
		$key = openssl_pkey_get_private($privKey, $password);
		$signature = "";
		openssl_sign($queryToSign, $signature, $key);
		$data["signature"] = $signature;
		return http_build_query($data); // recompose query string and return it
	}
}
