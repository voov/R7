# R7
Incredibly small multi-purpose RESTful PHP framework. The main idea behind R7 was to create the smallest, and fastest PHP framework
designed to handle RESTful calls.

## Design
The R7 is based around Routes and Responses. Every route is essentially a URL path that can have Rails like
URL parameters. Routes can be created by using either creating a new object or using the `create` method

    // the same as $route = new \R7\Route("/index/test/:name/:value");
    $route = \R7\Route::create("/index/test/:name/:value");

Routes can have methods bound. Every HTTP method type is a class method which takes either a callable closure or a class
as a parameter. When you specify a class, that class must have a `main` function with one parameter. The closure is also
called with one parameter. The parameter contains the URL parameters.

    // called on GET
    $route->get(function($data) {
        // $data["name"] and $data["value"] is set
    });

    class PostHandler {
        function main($data) {
            // $data["name"] and $data["value"] is set
        }
    }

    // called on POST
    $route->post(new PostHandler);

