<?php
function extractCustomHeaders() {
    $ignoreHeaders = ['Host', 'Accept', 'Accept-Encoding', 'user-agent', 'User-Agent', 'Content-Length'];

    $headers = getallheaders();

    $extractedHeaders = [];
    foreach ($headers as $key => $value) {
        if (!in_array($key, $ignoreHeaders)) {
            $extractedHeaders[$key] = $value;
        }
    }

    return $extractedHeaders;
}
