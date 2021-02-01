<?php

namespace Frankie\Auth\PasswordGetter;

use Frankie\Storage\Storage;

final class FolderGetter implements Getter
{
    private Storage $storage;
    private ?string $extension;

    public function __construct(Storage $storage, ?string $extension)
    {
        $this->storage = $storage;
        $this->extension = $extension;
    }

    public function __clone()
    {
        $this->storage = clone $this->storage;
    }

    public function has(string $name): bool
    {
        if ($this->extension === null) {
            return \in_array($name, $this->storage->getFilesList(), true);
        }
        return \in_array($name . '.' . $this->extension, $this->storage->getFilesList(), true);
    }

    public function getSecret(string $name): string
    {
        if ($this->extension === null) {
            $fileName = $name;
        } else {
            $fileName = $name . '.' . $this->extension;
        }
        return $this->storage->getFiles()[$fileName]->getContent();
    }
}