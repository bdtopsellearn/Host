<?php

namespace App\Services\Provisioning;

class ProvisioningResult
{
    public bool $success;
    public string $message;
    public array $data;
    public mixed $rawResponse;

    public function __construct(bool $success, string $message = '', array $data = [], mixed $rawResponse = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->rawResponse = $rawResponse;
    }

    public static function success(array $data = [], string $message = 'Success', mixed $rawResponse = null): self
    {
        return new self(true, $message, $data, $rawResponse);
    }

    public static function failure(string $message, mixed $rawResponse = null, array $data = []): self
    {
        return new self(false, $message, $data, $rawResponse);
    }
}
