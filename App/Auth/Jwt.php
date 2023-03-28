<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Authentication
 * @package   App\Auth
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Auth;

class Jwt
{
    public function create($data)
    {
        $array = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $header = json_encode($array);
        $payload = json_encode($data);

        $hBase = $this->base64url_encode($header);
        $pBase = $this->base64url_encode($payload);

        $signature = hash_hmac('sha256', $hBase . '.' . $pBase, JWT_SECRET_KEY, true);
        $bSig = $this->base64url_encode($signature);

        $jwt = $hBase . '.' . $pBase . '.' . $bSig;

        return $jwt;
    }
    public function validate($jwt)
    {
        $array = [];
        $jwtSplits = explode('.', $jwt);
        if (count($jwtSplits) == 3) {
            $signature = hash_hmac('sha256', $jwtSplits[0] . '.' . $jwtSplits[1], JWT_SECRET_KEY, true);
            $bSig = $this->base64url_encode($signature);
            if ($bSig == $jwtSplits[2]) {
                $decodedPayload = json_decode($this->base64url_decode($jwtSplits[1]));
                if (isset($decodedPayload->exp) && time() < $decodedPayload->exp) {
                    $array = $decodedPayload;
                }
            }
        }
        return $array;
    }
    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    private function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), 4 - ((strlen($data) % 4) ?: 4), '=', STR_PAD_RIGHT));
    }
}
