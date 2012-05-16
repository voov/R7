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

For every HTTP method there can be *more than one* method bound. But it is important that if you return a response from
the method, only the last response will be used, the others will be ignored

### Responses

Responses are simple key-value datasets with http status information.

    $route->get(function($data) {
        // return everything from the URL parameters
        return new \R7\Response(200, $data); // HTTP status 200
    });

Responses can be returned to client as JSON, XML or CSV data. The `R7` class uses the `?type=<type>` URL query string
to choose from the data format. When type is not defined, JSON is automatically used.
Calling the above example with the URL `http://example.com/index/test/voov/10?type=json` returns

    {'name':'voov','value':'10'}

while calling it with `http://example.com/index/test/voov/10?type=xml` returns

     <root>
        <name>voov</name>
        <value>10</value>
     </root>