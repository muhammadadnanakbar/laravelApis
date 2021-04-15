<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RequestLogging
{
    public function __construct()
    {

    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $file_name = 'request-logging.log';
        if(Storage::disk('local')->exists($file_name)){
            $file_string = file_get_contents(storage_path('app/' . $file_name));
            $lines = explode("\n",file_get_contents(storage_path('app/' . $file_name)));
            $route = ltrim(strstr(request()->route()->uri, '/'), '/');
            File::delete(storage_path('app/' . $file_name));
            foreach ($lines as $line){
                $array = explode(",",$line);
                $uri = $array[0];
                $count = $array[1];
                if($route==$array[0]){
                    $count = $array[1];
                    $count = trim($count)+1;
                    $message = $uri.','.$count;
                    unset($count);
                    Storage::disk('local')->append($file_name, $message);
                }else{
                    $message = $uri.','.trim($count);
                    Storage::disk('local')->append($file_name, $message);
                }
            }
            if(!str_contains($file_string, $route)) {
                $message = $route.',1';
                Storage::disk('local')->append($file_name, $message);
            }
        }else{
            $route = ltrim(strstr(request()->route()->uri, '/'), '/');
            $message = $route.',1';
            Storage::disk('local')->append($file_name, $message);
        }

        return $response;
    }

}
