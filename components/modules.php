<?php
class Methods
{
    public static function fetch($url, $options = []):array
    {
        $defaultOptions = [
            'method' => 'GET',
            'headers' => [],
            'body' => null,
            'timeout' => 30
        ];

        $options = array_merge($defaultOptions, $options);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);

        // Установка метода запроса
        if ($options['method'] !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);
        }

        // Установка тела запроса
        if ($options['body'] !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
        }
        // Установка заголовков
        if (!empty($options['headers'])) {
            $headers = [];
            foreach ($options['headers'] as $key => $value) {
                $headers[] = "$key: $value";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Curl error: $error");
        }

        curl_close($ch);

        return [
            'status' => $httpCode,
            'headers' => [], // Можно извлечь с помощью CURLOPT_HEADERFUNCTION
            'body' => $response
        ];
    }

    public static function msg($header, $content, $buttons = null):void
    {
        require_once 'modules.html';
        if ($buttons) {
            $buttons = "\"".$buttons."\"";
        }
        echo <<< LOL
        <script>openAlert('$header', '$content', $buttons)</script>
        LOL;
    }

    public static function breakSession():void
    {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION = [];
        $_SESSION['isAuth'] = 'false';
        $_SESSION['is_verified'] = 'false';
        setcookie('isAuth', 'false', 0, '/', '', true);
        setcookie('is_verified', 'false', 0, '/', '', true);

        setcookie('city', false, time()-3600, '/', '', true);
        setcookie('csrf_token', false, time()-3600, '/', '', true);
        setcookie('end', false, time()-3600, '/', '', true);
        setcookie('id', false, time()-3600, '/', '', true);
        setcookie('role', false, time()-3600, '/', '', true);
        setcookie('token', false, time()-3600, '/', '', true);
    }
}