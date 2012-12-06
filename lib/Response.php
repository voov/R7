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
class Response {

    private $data = array();
    private $status = 200;

    function __construct($status=200, $data=array()) {
        $this->status = $status;
        $this->data = $data;
    }

    /**
     * Get internal value
     * @param $name
     * @return string the value
     */
    public function __get($name) {
        return $this->data[$name];
    }

    /**
     * Sets internal value
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    /**
     * return the dataset in JSON
     * @return string
     */
    public function toJSON() {
        header("Status: HTTP/1.1 " . $this->status);
        header("Content-type: application/json");

        return json_encode($this->data);
    }

    /**
     * Function to convert data to XML
     * @param $data the data to convert
     * @param $root the root of the XML node
     * @return string the xml string
     */
    private function convertToXml($data, &$root) {

        foreach($data as $key => $value) {
            if(is_array($value)) {
                // recurse
                $newnode = $root->addChild($key);
                $this->convertToXml($value, $newnode);
            } else {
                $root->addChild($key, $value);
            }
        }
        return $root->asXML();
    }

    /**
     * Return the dataset in XML
     * @return string
     */
    public function toXML() {
        header("Status: HTTP/1.1 " . $this->status);
        header("Content-type: application/xml");

        // TODO: to be implemented
        $root = new \SimpleXMLElement("<root></root>");
        return $this->convertToXml($this->data, $root);
    }

    /**
     * Return the dataset in CSV
     */
    public function toCSV() {
        // TODO: to be implemented
    }
}
