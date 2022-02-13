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

    public function __construct(float $horizontalPercentage, float $verticalPercentage)
    {
        if ($horizontalPercentage < 0. || $horizontalPercentage > 1.0) {
            throw new \InvalidArgumentException("Invalid horizontal anchor percentage: ".$horizontalPercentage);
        }
        if ($verticalPercentage < 0. || $verticalPercentage > 1.0) {
            throw new \InvalidArgumentException("Invalid vertical anchor percentage: ".$verticalPercentage);
        }

        $this->x = $horizontalPercentage;
        $this->y = $verticalPercentage;
    }

    public static function fromFixedPoints(string $anchorHorizontal, string $anchorVertical)
    {
        if (!in_array($anchorHorizontal, self::VALID_ANCHOR_NAMES_HORIZONTAL, true)) {
            throw new \InvalidArgumentException("Invalid anchor name: ".$anchorHorizontal);
        }
        if (!in_array($anchorVertical, self::VALID_ANCHOR_NAMES_VERTICAL, true)) {
            throw new \InvalidArgumentException("Invalid anchor name: ".$anchorVertical);
        }

        $x = self::ANCHORS_RELATIVE_POSITION['x'][$anchorHorizontal];
        $y = self::ANCHORS_RELATIVE_POSITION['y'][$anchorVertical];

        return new Anchor($x, $y);
    }

    public static function CenterCenter(): Anchor
    {
        return self::fromFixedPoints(self::CENTER, self::CENTER);
    }

    public static function CenterBottom(): Anchor
    {
        return self::fromFixedPoints(self::CENTER, self::BOTTOM);
    }

    public static function LeftTop(): Anchor
    {
        return self::fromFixedPoints(self::LEFT, self::TOP);
    }

    public static function LeftBottom(): Anchor
    {
        return self::fromFixedPoints(self::LEFT, self::BOTTOM);
    }

    public static function LeftCenter()
    {
        return self::fromFixedPoints(self::LEFT, self::CENTER);
    }

    public static function RightBottom()
    {
        return self::fromFixedPoints(self::RIGHT, self::BOTTOM);
    }

    public function getBoundedRect(float $posX, float $posY, int $width, int $height): \SDL_Rect
    {
        return new \SDL_Rect((int)($posX - $width * $this->x), (int)($posY - $height * $this->y), $width, $height);
    }
}