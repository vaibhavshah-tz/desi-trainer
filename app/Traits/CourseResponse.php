<?php

namespace App\Traits;

trait CourseResponse
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSuccessResponse($result, $message = 'OK')
    {

        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return $response;
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendErrorResponse($error, $code = 404, $errorMessages = [])
    {
        $response = [
            'success' => false,
            'message' => $error,
            'status' => $code
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return $response;
    }
}
