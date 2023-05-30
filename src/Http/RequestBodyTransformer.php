<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class RequestBodyTransformer
{
    public function transform(Request $request)
    {
        switch ($request->headers->get('Content-Type')) {
            case 'application/json':
                $data = \json_decode($request->getContent(), true);
                $request->request = new ParameterBag($data);
                break;
            default:
                break;
        }
    }
}