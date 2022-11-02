<?php

namespace DefStudio\Telegraph\DTO;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Attachment implements Arrayable
{
    private string $name;

    public function __construct(
        private string $path,
        private string|null $filename = null,
        private bool $preload = false,
    ) {
        $this->name = $this->generateRandomName();
    }

    public function contents(): string
    {
        if ($this->local()) {
            return File::get($this->path);
        }

        return (string) Utils::streamFor($this->path);
    }

    public function filename(): string
    {
        return $this->filename ?? File::basename($this->path);
    }

    public function path(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function media(): string
    {
        return $this->asMultipart() ? $this->attachString() : $this->path;
    }

    public function attachString(): string
    {
        return 'attach://' . $this->getName();
    }

    public function asMultipart(): bool
    {
        return $this->local() || ($this->remote() && $this->preload);
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

    protected function local(): bool
    {
        return Str::of($this->path)->startsWith('/');
    }

    protected function remote(): bool
    {
        return (bool) filter_var($this->path, FILTER_VALIDATE_URL);
    }

    private function generateRandomName(): string
    {
        return substr(md5(uniqid((string) $this->filename, true)), 0, 10);
    }
}
