<?php
require_once "R7.php";
ini_set("display_errors", 1);

class NewUser {
    function main($data) {
        $ret_data = array(
            "username" => $data["name"],
            "moredata" => array("value" => $data["value"], "x" => "nemix")
        );
        return new \R7\Response(200, $ret_data);
    }
}

$app = new \R7\R7();
$app->addRoute(\R7\Route::create("/index/teszt/:name/:value")->get(new NewUser()));
$app->addRoute(\R7\Route::create("/auth/:name")->get(function($data) {
        return new \R7\Response(200, array("id" => $data["name"]));
    })
);

$app->execute();
