<?php

if (!function_exists('respondWithError')) {
    function respondWithError($message, $statusCode = 400, $errors = [])
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}

if (!function_exists('respondWithSuccess')) {
    function respondWithSuccess($message, $data = [], $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
