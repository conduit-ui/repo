<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class Content
{
    public function __construct(
        public string $name,
        public string $path,
        public string $sha,
        public int $size,
        public string $type,
        public string $url,
        public ?string $content = null,
        public ?string $encoding = null,
        public ?string $htmlUrl = null,
        public ?string $gitUrl = null,
        public ?string $downloadUrl = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            path: $data['path'],
            sha: $data['sha'],
            size: $data['size'],
            type: $data['type'],
            url: $data['url'],
            content: $data['content'] ?? null,
            encoding: $data['encoding'] ?? null,
            htmlUrl: $data['html_url'] ?? null,
            gitUrl: $data['git_url'] ?? null,
            downloadUrl: $data['download_url'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'sha' => $this->sha,
            'size' => $this->size,
            'type' => $this->type,
            'url' => $this->url,
            'content' => $this->content,
            'encoding' => $this->encoding,
            'html_url' => $this->htmlUrl,
            'git_url' => $this->gitUrl,
            'download_url' => $this->downloadUrl,
        ];
    }

    public function decoded(): ?string
    {
        if ($this->content === null) {
            return null;
        }

        if ($this->encoding === 'base64') {
            return base64_decode(str_replace("\n", '', $this->content), true) ?: null;
        }

        return $this->content;
    }

    public function json(): ?array
    {
        $decoded = $this->decoded();

        if ($decoded === null) {
            return null;
        }

        $result = json_decode($decoded, true);

        return is_array($result) ? $result : null;
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function isDirectory(): bool
    {
        return $this->type === 'dir';
    }

    public function isSymlink(): bool
    {
        return $this->type === 'symlink';
    }
}
