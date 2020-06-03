<?php

namespace PhpGame;

class SoundManager
{
    /** @var array|\Mix_Chunk[] */
    private array $chunks = [];
    /** @var array|\Mix_Music[] */
    private array $musics = [];
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

    public function playSound(string $path): void
    {
        if (!isset($this->chunks[$path])) {
            $this->chunks[$path] = \Mix_LoadWAV($this->basePath.'/sounds/'.$path);
        }

        \Mix_PlayChannel(-1, $this->chunks[$path], 0);
    }

    /**
     * @param float $volume from 0 to 1
     */
    public function setMusicVolume(float $volume): void
    {
        $this->volume = $volume;
        \Mix_VolumeMusic(MIX_MAX_VOLUME * $volume);
    }

    public function getMusicVolume(): float
    {
        return $this->volume;
    }

    public function playMusic(string $path): void
    {
        if (!isset($this->chunks[$path])) {
            $this->musics[$path] = \Mix_LoadMUS($this->basePath.'/music/'.$path);
        }

        \Mix_PlayMusic($this->musics[$path], 0);
    }
}
