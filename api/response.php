<?php

function sendResponse($statusCode, $body)
{
    http_response_code($statusCode);
    echo json_encode($body);
}
