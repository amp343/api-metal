<?php

namespace ApiMetal\Auth\BasicAuth;

class BasicAuthCredential
{
    protected $user;
    protected $password;

    public function __construct(string $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function toFlatArray(): array
    {
        return [$this->getUser(), $this->getPassword()];
    }

    public function toArray(): array
    {
        return ['user' => $this->getUser(), 'password' => $this->getPassword()];
    }

    public function getEncodedCredential(): string
    {
        return base64_encode($this->getUser() . ':' . $this->getPassword());
    }

    public function getEncodedHeaderValue(): string
    {
        return 'Basic ' . $this->getEncodedCredential();
    }

    public function getEncodedHeader(): array
    {
        return ['Authorization' => $this->getEncodedHeaderValue()];
    }

    public static function isMatch(BasicAuthCredential $credentialA, BasicAuthCredential $credentialB): bool
    {
        return $credentialA->toArray() === $credentialB->toArray();
    }
}
