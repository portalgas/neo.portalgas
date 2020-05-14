<?php
namespace App\Middleware;

use Cake\Http\Cookie\Cookie;
use Cake\I18n\Time;
use App\Traits;

class ResponseMiddleware
{
    use Traits\UtilTrait;
    use Traits\SqlTrait;

    public function __invoke($request, $response, $next)
    {
    	// debug($request->getData());

    	$request_data = $request->getData();

        foreach ($request_data as $key => $value) {
            if ($this->stringStartsWith($key, 'data_') && !empty($value)) {
                $request_data[$key] = $this->convertDate($value);
                // debug($key.' '.$request_data[$key]);
            }   

            if ($this->stringStartsWith($key, 'price') && !empty($value)) {
                $request_data[$key] = $this->convertImport($value);
                // debug($key.' '.$request_data[$key]);
            }
        }

        $request = $request->withParsedBody($request_data);
		// debug($request->getData());

        return $next($request, $response);
    }      
} 