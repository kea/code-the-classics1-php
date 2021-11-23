<?php

namespace PhpGame;

use function Mix_LoadMUS;
use function Mix_LoadWAV;
use function Mix_PlayMusic;
use function Mix_VolumeMusic;

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

    public function setAssetsPath(string $path): void
    {
        $this->basePath = rtrim($path, '/').'/';
    }

    /**
     * This can load WAVE, AIFF, RIFF, OGG, and VOC files
     */
    public function playSound(string $path): bool
    {
        if (!isset($this->chunks[$path])) {
            $fullPath = $this->basePath.'/sounds/'.$path;
            if (!file_exists($fullPath)) {
                return false;
            }
            $this->chunks[$path] = Mix_LoadWAV($fullPath);
        }

        \Mix_PlayChannel(-1, $this->chunks[$path], 0);

        return true;
    }

    /**
     * @param float $volume from 0 to 1
     * @param int   $channel channel number or -1 for all channels
     */
    public function setChannelVolume(float $volume, int $channel = -1): void
    {
        $volume = max(0, min(1.0, $volume));
        // Mix_Volume add binding do sdl_mixer extension
        // \Mix_Volume($channel, MIX_MAX_VOLUME * $volume);
    }

    /**
     * @param float $volume from 0 to 1
     */
    public function setMusicVolume(float $volume): void
    {
        $this->volume = max(0, min(1.0, $volume));
        Mix_VolumeMusic(MIX_MAX_VOLUME * $this->volume);
    }

    public function getMusicVolume(): float
    {
        return $this->volume;
    }

    public function playMusic(string $path): void
    {
        if (!isset($this->chunks[$path])) {
            $this->musics[$path] = Mix_LoadMUS($this->basePath.'/music/'.$path);
        }

        Mix_PlayMusic($this->musics[$path], -1);
    }
}
