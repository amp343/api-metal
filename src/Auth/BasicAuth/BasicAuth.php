<?php

namespace ApiMetal\Auth\BasicAuth;

class BasicAuth
{
    public static function getCredentialFromBasicAuthString(string $basicAuthString)
    {
        $pieces = explode(':', base64_decode(str_replace('Basic ', '', $basicAuthString)));

        return (array_key_exists(0, $pieces) && array_key_exists(1, $pieces))
            ? new BasicAuthCredential($pieces[0], $pieces[1])
            : null;
    }

    public static function isBasicAuthString(string $basicAuthString): bool
    {
        return (strpos($basicAuthString, 'Basic') !== false);
    }
}
