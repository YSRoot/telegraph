<?php

namespace DefStudio\Telegraph\DTO;

abstract class InputMedia
{
    protected string $type;
    protected Attachment $attachment;

    public function __construct(
        protected string $path,
        ?string $filename = null,
        protected ?string $caption = null,
        protected ?string $parseMode = null,
        bool $preload = false,
    ) {
        $this->type = 'photo';

        $this->validate();

        $this->attachment = new Attachment($this->path, $filename, $preload);
    }

    public function attachment(): Attachment
    {
        return $this->attachment;
    }

    public function asMultipart(): bool
    {
        return $this->attachment()->asMultipart();
    }

    /**
     * @return array<string, string>
     */
    abstract public function toMediaArray(): array;

    abstract protected function validate(): void;

}
