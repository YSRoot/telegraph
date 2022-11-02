<?php

namespace DefStudio\Telegraph\DTO;

use Illuminate\Support\Str;

abstract class InputMedia
{
    protected string $type;

    abstract public function asMultipart(): bool;

    abstract public function getAttachment(): Attachment;

    /**
     * @return array<string, string>
     */
    abstract public function toMediaArray(): array;

    abstract protected function validate(): void;

    protected function generateRandomName(): string
    {
        return substr(md5(uniqid((string) $this->filename, true)), 0, 10);
    }
}
