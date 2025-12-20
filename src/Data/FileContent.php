<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class FileContent
{
    public function __construct(
        public string $name,
        public string $path,
        public string $sha,
        public int $size,
        public string $url,
        public string $htmlUrl,
        public string $gitUrl,
        public string $downloadUrl,
        public string $type,
        public ?string $content,
        public string $encoding,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            path: $data['path'],
            sha: $data['sha'],
            size: $data['size'],
            url: $data['url'],
            htmlUrl: $data['html_url'],
            gitUrl: $data['git_url'],
            downloadUrl: $data['download_url'],
            type: $data['type'],
            content: $data['content'] ?? null,
            encoding: $data['encoding'] ?? 'none',
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'sha' => $this->sha,
            'size' => $this->size,
            'url' => $this->url,
            'html_url' => $this->htmlUrl,
            'git_url' => $this->gitUrl,
            'download_url' => $this->downloadUrl,
            'type' => $this->type,
            'content' => $this->content,
            'encoding' => $this->encoding,
        ];
    }

    public function decodedContent(): ?string
    {
        if ($this->content === null) {
            return null;
        }

        if ($this->encoding === 'base64') {
            return base64_decode($this->content, true) ?: null;
        }

        return $this->content;
    }
}
