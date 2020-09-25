<?php

namespace PhpGame;

class Anchor
{
    public const LEFT = 'left';
    public const RIGHT = 'right';
    public const TOP = 'top';
    public const BOTTOM = 'bottom';
    public const CENTER = 'center';
    public const MIDDLE = 'middle';

    private const VALID_ANCHOR_NAMES_HORIZONTAL = [
        self::LEFT,
        self::RIGHT,
        self::CENTER,
        self::MIDDLE,
    ];

    private const VALID_ANCHOR_NAMES_VERTICAL = [
        self::TOP,
        self::BOTTOM,
        self::CENTER,
        self::MIDDLE,
    ];

    private const ANCHORS_RELATIVE_POSITION = [
        'x' => [
            self::LEFT => 0.0,
            self::CENTER => 0.5,
            self::MIDDLE => 0.5,
            self::RIGHT => 1.0,
        ],
        'y' => [
            self::TOP => 0.0,
            self::CENTER => 0.5,
            self::MIDDLE => 0.5,
            self::BOTTOM => 1.0,
        ],
    ];

    private float $x;
    private float $y;

    public function __construct(string $anchorHorizontal, string $anchorVertical)
    {
        if (!in_array($anchorHorizontal, self::VALID_ANCHOR_NAMES_HORIZONTAL, true)) {
            throw new \InvalidArgumentException("Invalid anchor name: ".$anchorHorizontal);
        }
        if (!in_array($anchorVertical, self::VALID_ANCHOR_NAMES_VERTICAL, true)) {
            throw new \InvalidArgumentException("Invalid anchor name: ".$anchorVertical);
        }

        $this->x = self::ANCHORS_RELATIVE_POSITION['x'][$anchorHorizontal];
        $this->y = self::ANCHORS_RELATIVE_POSITION['y'][$anchorVertical];
    }

    public static function CenterCenter(): Anchor
    {
        return new Anchor(self::CENTER, self::CENTER);
    }

    public static function CenterBottom(): Anchor
    {
        return new Anchor(self::CENTER, self::BOTTOM);
    }

    public static function LeftTop(): Anchor
    {
        return new Anchor(self::LEFT, self::TOP);
    }

    public static function LeftBottom(): Anchor
    {
        return new Anchor(self::LEFT, self::BOTTOM);
    }

    public function getBoundedRect(float $posX, float $posY, int $width, int $height): \SDL_Rect
    {
        return new \SDL_Rect($posX - $width * $this->x, $posY - $height * $this->y, $width, $height);
    }
}