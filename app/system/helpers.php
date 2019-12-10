<?php

function httpResonseSuccess()
{
    http_response_code(200);
}

function httpResonseUnauthorized()
{
    http_response_code(401);
}

function httpResonseNotFound()
{
    http_response_code(404);
}

function httpResonseUnprocessable()
{
    http_response_code(422);
}
