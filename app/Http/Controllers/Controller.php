<?php

namespace App\Http\Controllers;

use stdClass;

abstract class Controller
{
    /**
     * Send a successful response.
     *
     * @param string $message
     * @param int $code
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($message = '', $code = 200, $data = [])
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data ?: (new stdClass),
            'code' => $code
        ]);
    }

    /**
     * Send an error response.
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message = '', $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code
        ]);
    }
}
