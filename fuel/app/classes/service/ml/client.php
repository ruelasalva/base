<?php

/**
 * SERVICIO API MERCADO LIBRE
 * Versión alineada 100% con tus modelos reales:
 *
 * - Model_Plataforma_Ml_Configuration
 * - Model_Plataforma_Ml_Log
 * - Model_Plataforma_Ml_Error
 *
 * Compatible total con FuelPHP 1.8.2
 */

class Service_Ml_Client
{
    protected $config;
    protected $api_url = 'https://api.mercadolibre.com';
    protected $auth_url = 'https://api.mercadolibre.com/oauth/token';

    public function __construct(Model_Plataforma_Ml_Configuration $config)
    {
        $this->config = $config;
    }

    /* ============================================================
     * 1) OAuth: Intercambio de CODE → Tokens
     * ============================================================ */
    public function exchange_code_for_tokens($code)
    {
        $payload = [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->config->client_id,
            'client_secret' => $this->config->client_secret,
            'code'          => $code,
            'redirect_uri'  => $this->config->redirect_uri
        ];

        return $this->oauth_post($payload);
    }


    /* ============================================================
     * 2) Validar y refrescar token automáticamente
     * ============================================================ */
    public function ensure_token()
{
    // margen 60s
    if (time() < ($this->config->token_expires_at - 60)) {
        return;
    }

    \Log::info("[ML][TOKEN] Token expirado. Renovando…");

    $payload = [
        'grant_type'    => 'refresh_token',
        'client_id'     => $this->config->client_id,
        'client_secret' => $this->config->client_secret,
        'refresh_token' => $this->config->refresh_token
    ];

    $response = $this->oauth_post($payload);

    if (!isset($response['access_token'])) {
        throw new Exception("No se pudo renovar el token: ".json_encode($response));
    }

    $this->config->access_token     = $response['access_token'];
    $this->config->refresh_token    = $response['refresh_token'];
    $this->config->token_expires_at = time() + $response['expires_in'];
    $this->config->save();

    \Log::info("[ML][TOKEN] Token renovado correctamente.");
}



    /* ============================================================
     * 3) GET / POST / PUT
     * ============================================================ */
    public function get($endpoint, $params = [], $resource = null, $resource_id = null)
    {
        $this->ensure_token();
        $url = $this->api_url . $endpoint . '?' . http_build_query($params);

        return $this->request('GET', $url, null, $resource, $resource_id);
    }

    public function post($endpoint, $payload = [], $resource = null, $resource_id = null)
    {
        $this->ensure_token();
        $url = $this->api_url . $endpoint;

        return $this->request('POST', $url, $payload, $resource, $resource_id);
    }

    public function put($endpoint, $payload = [], $resource = null, $resource_id = null)
    {
        $this->ensure_token();
        $url = $this->api_url . $endpoint;

        return $this->request('PUT', $url, $payload, $resource, $resource_id);
    }


    /* ============================================================
     * 4) Método central de peticiones
     * ============================================================ */
    private function request($method, $url, $payload = null, $resource = null, $resource_id = null)
    {
        $ch = curl_init();

        $headers = [
            "Authorization: Bearer ".$this->config->access_token,
            "Content-Type: application/json"
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        /* --- Error de conexión --- */
        if ($errno) {
            $this->registrar_error(null, null, 0, "CURL ERROR: ".$error, $url);
            throw new Exception("Error de conexión: ".$error);
        }

        $decoded = json_decode($response, true);

        /* --- Registrar log de negocio --- */
        $this->registrar_log(
            $resource,
            $resource_id,
            $method,
            $status,
            $response
        );

        /* --- Error HTTP de ML --- */
        if ($status >= 400) {
            $this->registrar_error(
                $resource_id,
                $decoded['id'] ?? null,
                $status,
                $response,
                $url
            );

            throw new Exception("Error ML {$status}: ".$response);
        }

        return $decoded;
    }


    /* ============================================================
     * 5) Registrar Logs (Tu modelo real)
     * ============================================================ */
    private function registrar_log($resource, $resource_id, $operation, $status, $message)
{
    // Evitar NULL en columnas NOT NULL
    $resource     = $resource     ?: 'unknown';
    $resource_id  = $resource_id  ?: 0;
    $operation    = $operation    ?: 'undefined';

    Model_Plataforma_Ml_Log::forge([
        'configuration_id' => $this->config->id,
        'resource'         => $resource,
        'resource_id'      => $resource_id,
        'operation'        => $operation,
        'status'           => $status,
        'message'          => $message,
    ])->save();
}



    /* ============================================================
     * 6) Registrar Errores (Tu modelo real)
     * ============================================================ */
    private function registrar_error($product_id, $ml_item_id, $error_code, $error_message, $origin)
{
    $product_id = $product_id ?: 0;

    Model_Plataforma_Ml_Error::forge([
        'configuration_id' => $this->config->id,
        'product_id'       => $product_id,
        'ml_item_id'       => $ml_item_id,
        'error_code'       => $error_code ?: 0,
        'error_message'    => $error_message ?: 'Unknown error',
        'origin'           => $origin ?: 'unknown'
    ])->save();
}



    /* ============================================================
     * 7) POST simple para OAuth
     * ============================================================ */
    private function oauth_post($payload)
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $this->auth_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($payload),
        CURLOPT_HTTPHEADER     => [
            "Accept: application/json",
            "Content-Type: application/x-www-form-urlencoded"
        ],
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $errno    = curl_errno($ch);
    $error    = curl_error($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($errno) {
        throw new Exception("Error OAuth: ".$error);
    }

    $json = json_decode($response, true);

    // 🚨 Validar errores devueltos por ML
    if ($status >= 400 || isset($json['error'])) {
        \Log::error("[ML][OAUTH ERROR] HTTP {$status} → ".json_encode($json));
        throw new Exception(
            "OAuth error {$status}: ".($json['message'] ?? json_encode($response))
        );
    }

    return $json;
}

}
