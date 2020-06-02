<?php

namespace PhpGame;

class SoundManager
{
    /** @var array|\Mix_Chunk[] */
    private array $chunks = [];
    private string $basePath = '';
    private int $volume = \MIX_MAX_VOLUME;

    public function __construct(int $frequency, int $format, int $nchannels, int $chunksize)
    {
        if (\Mix_OpenAudio($frequency, $format, $nchannels, $chunksize) < 0) {
            throw new \RuntimeException("Sound system fails to open");
        }
    }

    public function setBaseAssetsPath(string $path): void
    {
        $this->basePath = rtrim($path, '/').'/';
    }

    public function play(string $path): void
    {
        if (!isset($this->chunks[$path])) {
            $this->chunks[$path] = \Mix_LoadWAV($this->basePath.$path);
        }

        \Mix_PlayChannel(-1, $this->chunks[$path], 0);
    }

    public function setVolume(int $volume): void
    {
        $this->volume = $volume;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }
}