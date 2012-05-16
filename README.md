# R7
Incredibly small multi-purpose RESTful PHP framework. The main idea behind R7 was to create the smallest, and fastest PHP framework
designed to handle RESTful calls.

## Design
The R7 is based around Routes and Responses. Every route is essentially a URL path that can have Rails like
URL parameters. Routes can be created by using either creating a new object or using the `create` method

    $route = \R7\Route::create("/index/test/:name/:value"); // the same as $route = new \R7\Route("/index/test/:name/:value");

