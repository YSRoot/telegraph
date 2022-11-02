<?php

namespace DefStudio\Telegraph\DTO;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Psr\Http\Message\StreamInterface;

class Attachment implements Arrayable
{
    private string $name;

    public function __construct(
        private string|StreamInterface $contents,
        private string|null $filename = null,
        private bool $preload = false,
    ) {
        $this->name = $this->generateRandomName();
    }

    public function contents(): string
    {
        if ($this->contents instanceof StreamInterface) {
            return (string) $this->contents;
        }

        if ($this->isLocal()) {
            return File::get($this->contents);
        }

        return (string) Utils::streamFor($this->contents);
    }

    public function filename(): string
    {
        if ($this->contents instanceof StreamInterface || getimagesizefromstring($this->contents)) {
            return 'temp';
        }

        return $this->filename ?? File::basename($this->contents);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function media(): string
    {
        if ($this->asMultipart()) {
            return $this->attachString();
        }

        return $this->contents;
    }

    public function attachString(): string
    {
        return 'attach://' . $this->getName();
    }

    public function asMultipart(): bool
    {
        return $this->isLocal()
            || $this->isStream()
            || getimagesizefromstring($this->contents)
            || ($this->isRemote() && $this->preload);
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'contents' => $this->contents(),
            'filename' => $this->filename(),
        ];
    }

    protected function isLocal(): bool
    {
        return Str::of($this->contents)->startsWith('/');
    }

    public function isStream(): bool
    {
        return $this->contents instanceof StreamInterface;
    }

    protected function isRemote(): bool
    {
        return (bool) filter_var($this->contents, FILTER_VALIDATE_URL);
    }

    private function generateRandomName(): string
    {
        return substr(md5(uniqid((string) $this->filename, true)), 0, 10);
    }
}
