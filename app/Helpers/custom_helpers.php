<?php

function sendResponse($data = null, $message = null, $http_status_code = 200)
{
    $response = [
        'status'    => TRUE,
        'message'   => $message,
        'data'      => $data,
    ];
    return response()->json($response, $http_status_code);
}
function sendError($error = null, $message = null, $http_status_code = 200)
{
    $response = [
        'status'    =>  FALSE,
        'message'   =>  isset($message) ? $message : 'An error occurred!',
        'error'     =>  $error,
    ];
    return response()->json($response, $http_status_code);
}
