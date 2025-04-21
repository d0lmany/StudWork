<?php
class baseView
{
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    public static function render($data, $code):void
    {
        #headers
        header('Content-Type: application/json');
        header("Allow: GET, POST, DELETE, PUT, PATCH");
        header('Access-Control-Allow-Origin: *');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT, PATCH');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        header('X-Frame-Options: DENY');
        #handle errors
        if (isset($data['error'])) {
            error_log("Error occurred: " . print_r($data['error'], true));
            $code = self::handleError($data['error']);
            if (is_array($data['error'])) {
                $data = ['error' => $data['error'][2]];
            }
        }
        #hide hashes
        if (is_array($data)) {
            if (array_key_exists(HASH_FIELD, $data)) {
                unset($data[HASH_FIELD]);
            }
            foreach ($data as &$item) {
                if (is_array($item) && array_key_exists(HASH_FIELD, $item)) {
                    unset($item[HASH_FIELD]);
                }
            }
        }
        #echo
        http_response_code($code);
        echo json_encode($data);
    }

    private static function handleError($error)
    {
        if (is_array($error)) {
            return self::HTTP_UNPROCESSABLE_ENTITY;
        }
    
        $errorMap = [
            'not found' => self::HTTP_NOT_FOUND,
            'no data' => self::HTTP_BAD_REQUEST,
            'forbidden' => self::HTTP_FORBIDDEN,
            'unauthorized' => self::HTTP_UNAUTHORIZED,
        ];
    
        foreach ($errorMap as $key => $code) {
            if (strpos($error, $key) !== false) {
                return $code;
            }
        }
    
        return self::HTTP_INTERNAL_SERVER_ERROR;
    }
}
