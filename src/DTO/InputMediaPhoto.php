<?php

namespace DefStudio\Telegraph\DTO;

use DefStudio\Telegraph\Telegraph;

class InputMediaPhoto
{
    private string $type;

    final public function __construct(
        private Attachment $attachment,
        private ?string $caption = null,
        private ?string $parseMode = null,
    ) {
        $this->type = 'photo';
    }

    public static function make(
        string $path,
        bool $preload = false,
        ?string $filename = null,
        ?string $caption = null,
        ?string $parseMode = null,
    ): static {
        return new static(
            new Attachment($path, $filename, $preload),
            $caption,
            $parseMode,
        );
    }

    public function html(string $caption = null): static
    {
        $this->parseMode = Telegraph::PARSE_HTML;
        $this->caption = $caption;

        return $this;
    }

    public function markdown(string $caption = null): static
    {
        $this->parseMode = Telegraph::PARSE_MARKDOWN;
        $this->caption = $caption;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function toMediaArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'media' => $this->attachment->media(),
            'caption' => $this->caption,
            'parse_mode' => $this->parseMode,
        ]);
    }

    public function asMultipart(): bool
    {
        return $this->attachment->asMultipart();
    }

    public function getAttachment(): Attachment
    {
        return $this->attachment;
    }
}
