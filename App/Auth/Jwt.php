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

/**
 * The Jwt class provides two main functionalities for JSON Web Tokens create and validate.
 */
class Jwt
{
    /**
     * The function generates a JSON Web Token (JWT) from the data passed as
     * a parameter. It encodes the header and payload sections of the JWT
     * using base64 URL encoding, computes the signature using HMAC-SHA256 with a
     * secret key, encodes the resulting signature using base64 URL encoding, and
     * concatenates the encoded sections into a single string before returning it.
     * 
     * @param mixed $data
     * 
     * @return string
     */
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

        $signature = hash_hmac(
            'sha256',
            $hBase . '.' . $pBase,
            JWT_SECRET_KEY,
            true
        );
        $bSig = $this->base64url_encode($signature);

        $jwt = $hBase . '.' . $pBase . '.' . $bSig;

        return $jwt;
    }
    /**
     * The function validates a JSON Web Token (JWT), checking if the token
     * is valid and if the expiration time has not yet passed.
     * 
     * @param mixed $jwt
     * 
     * @return mixed
     */
    public function validate($jwt)
    {
        $array = [];
        $jwtSplits = explode('.', $jwt);

        if (count($jwtSplits) !== 3) {
            return $array;
        }

        $signature = hash_hmac(
            'sha256',
            $jwtSplits[0] . '.' . $jwtSplits[1],
            JWT_SECRET_KEY,
            true
        );
        $bSig = $this->base64url_encode($signature);

        if ($bSig !== $jwtSplits[2]) {
            return $array;
        }

        $decodedPayload = json_decode(
            $this->base64url_decode($jwtSplits[1])
        );

        if (!isset($decodedPayload->exp) || time() >= $decodedPayload->exp) {
            return $array;
        }

        return $decodedPayload;
    }
    /**
     * This is a private method named base64url_encode. It takes one parameter of
     * any type and returns a string. It encodes the given data using Base64URL
     * encoding by replacing the + and / characters with - and _, respectively,
     * and removing any trailing = characters.
     * 
     * @param mixed $data
     * 
     * @return string
     */
    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    /**
     * This is a private function called "base64url_decode" that decodes a string
     * that was encoded using base64url_encode. It takes a string as input,
     * replaces the "-" and "_" characters with "+" and "/", pads the input
     * with "=" characters as needed, and finally decodes the resulting string
     * using base64_decode. It returns the decoded string.
     * 
     * @param mixed $data
     * 
     * @return bool|string
     */
    private function base64url_decode($data)
    {
        return base64_decode(
            str_pad(
                strtr($data, '-_', '+/'),
                4 - ((strlen($data) % 4) ?: 4),
                '=',
                STR_PAD_RIGHT
            )
        );
    }
}
