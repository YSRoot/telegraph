<?php

namespace DefStudio\Telegraph\DTO;

use DefStudio\Telegraph\Exceptions\FileException;
use DefStudio\Telegraph\Exceptions\InputMediaException;
use DefStudio\Telegraph\Telegraph;
use DefStudio\Telegraph\Validator;

class InputMediaPhoto extends InputMedia
{
    /**
     * @throws FileException
     * @throws InputMediaException
     */
    public function __construct(
        string $path,
        ?string $filename = null,
        private ?string $caption = null,
        private ?string $parseMode = null,
    ) {
        $this->type = 'photo';
        $this->path = $path;
        $this->filename = $filename;
        $this->attachName = $this->generateRandomName();

        $this->validate();
    }

    public function html(string $message = null): static
    {
        $this->parseMode = Telegraph::PARSE_HTML;
        $this->caption = $message;

        return $this;
    }

    public function markdown(string $message = null): static
    {
        $this->parseMode = Telegraph::PARSE_MARKDOWN;
        $this->caption = $message;

        return $this;
    }

    public function toAttachment(): Attachment
    {
        return new Attachment($this->path, $this->filename);
    }

    /**
     * @return array<string, string>
     */
    public function toMediaArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'media' => $this->local() ? $this->attachString() : $this->path,
            'caption' => $this->caption,
            'parse_mode' => $this->parseMode,
        ]);
    }

    /**
     * @throws FileException
     * @throws InputMediaException
     */
    protected function validate(): void
    {
        Validator::validatePhoto($this->path);
    }
}
