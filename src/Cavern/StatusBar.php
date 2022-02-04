<?php

namespace Cavern;

use PhpGame\SDL\Renderer;

class StatusBar
{
    private const CHAR_WIDTH = [27, 26, 25, 26, 25, 25, 26, 25, 12, 26, 26, 25, 33, 25, 26, 25, 27, 26, 26, 25, 26, 26, 38, 25, 25, 25];
    private const IMAGE_WIDTH = ["life" => 44, "plus" => 40, "health" => 40];
    private const WIDTH = 800;

    private function charWidth($char): int
    {
        $index = max(0, ord($char) - 65);

        return self::CHAR_WIDTH[$index] ?? 27;
    }

    private function drawText(Renderer $renderer, string $text, int $y, ?int $x = null): void
    {
        $chars = str_split($text);
        if ($x === null) {
            $textWidth = array_reduce($chars, fn(int $sum, $c): int => $sum + $this->charWidth($c), 0);
            $x = (int) ((self::WIDTH - $textWidth) / 2);
        }

        foreach ($chars as $char) {
            $renderer->drawImage(__DIR__.'/images/font0'.(ord($char)).'.png', $x, $y, $this->charWidth($char), 30);
            $x += $this->charWidth($char);
        }

    }

    public function draw(Renderer $renderer, Player $player, Level $level): void
    {
        $numberWidth = self::CHAR_WIDTH[0];
        $s = (string)$player->getScore();
        $this->drawText($renderer, $s, 451, self::WIDTH - 2 - ($numberWidth * strlen($s)));
        $this->drawText($renderer, "LEVEL ".($level->level + 1), 451);

        $livesHealth = array_fill(0, min(2, $player->getLives()), "life");
        if ($player->getLives() > 2) {
            $livesHealth[] = "plus";
        }
        if ($player->getLives() >= 0) {
            $livesHealth = array_merge($livesHealth, array_fill(0, $player->getHealth(), "health"));
        }

        $x = 0;
        foreach ($livesHealth as $image) {
            $imageWidth = self::IMAGE_WIDTH[$image];
            $renderer->drawImage(__DIR__.'/images/'.$image.'.png', $x, 450, $imageWidth, 28);
            $x += $imageWidth;
        }
    }
}