<?php

namespace App\Support;

// Simple TVET-style percentage -> letter grade / grade-point scale.
// Used to compute a per-course grade and an overall GPA/CGPA for the report card.
class GradeScale
{
    protected static array $bands = [
        ['min' => 80, 'letter' => 'A', 'point' => 4.0],
        ['min' => 70, 'letter' => 'B', 'point' => 3.0],
        ['min' => 60, 'letter' => 'C', 'point' => 2.0],
        ['min' => 50, 'letter' => 'D', 'point' => 1.0],
        ['min' => 0,  'letter' => 'F', 'point' => 0.0],
    ];

    public static function letter(float $percent): string
    {
        return self::band($percent)['letter'];
    }

    public static function point(float $percent): float
    {
        return self::band($percent)['point'];
    }

    public static function band(float $percent): array
    {
        foreach (self::$bands as $band) {
            if ($percent >= $band['min']) {
                return $band;
            }
        }

        return end(self::$bands);
    }

    public static function passed(float $percent): bool
    {
        return $percent >= 50;
    }
}
