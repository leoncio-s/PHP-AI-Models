<?php

namespace App;

use InvalidArgumentException;
use App\Utils\AIUtils;

class SimpleLinearRegression implements AIModelInterface{
    private float $b;
    private float $a;
    private array $e;

    /// yᵢ - a - (b.xᵢ)
    private function difference(float $a, float $b, array $y, array $x) : array
    {
        $e = [];

        for($i=0; $i<count($x); $i++)
        {
            if(is_array($y[$i]) || is_array($x[$i])) throw new InvalidArgumentException('array x or y is invalid');

            $e[] = $y[$i] - $a - ($b * $x[$i]);
        }

        return $e;
    }

    /// √(Σ(xᵢ-x̅)²/n)
    private function standartDeviation(array $matrix)
    {
        $num = 0;
        $n = count($matrix);
        $mean = AIUtils::avg($matrix);
        foreach($matrix as $a)
        {
            if(is_array($a))
            {
                throw new InvalidArgumentException("Invalid matrix");
            }
            $num += ($a - $mean)**2;

        }
        return sqrt((1/$n) * $num);
    }

    /// r . Sy/Sx;
    private function inclination(array $x, array $y) : float
    {
        $r = AIUtils::correlation($x, $y);
        $Sy = $this->standartDeviation($y);
        $Sx = $this->standartDeviation($x);

        return $r * ($Sy / $Sx);
    }

    // ȳ - (b.x̅)
    private function intersection(array $x, $y) : float
    {
        $mx = AIUtils::avg($x);
        $my = AIUtils::avg($y);
        $b = $this->inclination($x, $y);

        return $my - ($b * $mx);
    }


    public function fit(array $x, array $y)
    {
        $this->b = $this->inclination($x, $y);
        $this->a = $this->intersection($x, $y);
        $this->e = $this->difference($this->a, $this->b, $y, $x);
    }

    /// b.x + a
    public function predict(array $x)
    {
        $y = [];
        for($i = 0; $i < count($x); $i++)
        {
            $y[] = $this->a + ($this->b * $x[$i]) + $this->e[$i];
        }

        return $y;
    }

}