<?php

namespace App\Utils;

use InvalidArgumentException;

class AIUtils
{
    /// Σ(xᵢ)
    private static function sum(array $a)
    {
        $sum = 0;
        foreach($a as $i)
        {
            if(is_array($i))
            {
                foreach($i as $j)
                {
                    $sum += $j;
                }
            }else $sum += $i;
        }

        return $sum;
    }

    /// 1 - (SSres/SStot)
    /// SSres = Σ(yᵢ - ŷᵢ)²
    /// SStot = Σ(yᵢ - ȳ)²
    public static function rsquared(array $y, array $yp)
    {
        $ssres = 0;
        $sstot = 0;
        $ssexp = 0;
        $my = AIUtils::sum($y) / count($y);
        $myp = AIUtils::sum($yp) / count($yp);


        for($i=0;$i < count($y); $i++)
        {
            if(is_array($y[$i]))
            {
                for($j =0; $j < count($y[$i]); $j++)
                {
                    $ssres += ($y[$i][$j] - $yp[$i]) ** 2;
                    $sstot += ($y[$i][$j] - $my) ** 2;
                    $ssexp += ($yp[$i][$j] - $myp) ** 2;
                }
            }else
            {
                $ssres += ($y[$i] - $yp[$i]) ** 2;
                $sstot += ($y[$i] - $my) ** 2;
                $ssexp += ($yp[$i] - $myp) ** 2;
            }
        }

        return 1 - ($ssres/$sstot);
    }


    /// 1 - (1-R²) (n - 1) / ($n - $k - 1)
    /**
     * sumary: \(k\) is the number of independent variables (predictors) in the model(X variable), excluding the constant (intercept).
     */
    public static function r2adjust(array $y, array $yp, int $k)
    {
        $r2 = AIUtils::rsquared($y, $yp);
        $n = count($y);
        
        $result = 1 - (((1 - $r2)*(($n - 1)) / ($n - $k - 1)));
        
        return $result;
    }

    /// √(Σ(yᵢ - ŷᵢ)² / (n-k-1)
    /**
     * sumary: \(k\) is the number of independent variables (predictors) in the model(X variable), excluding the constant (intercept).
     */
    public static function standardError(array $y, array $yp, int $k)
    {
        $sum = 0;
        $n = count($yp);

        for($i=0;$i<$n;$i++)
        {
            if(is_array($y[$i]) && is_array($yp[$i]))
            {
                for($j=0;$j<count($y[$i]); $j++)
                {
                    $sum += ($y[$i][$j] - $yp[$i][$j])**2;
                }
            }else if(is_array($y[$i]))
            {
                for($j=0;$j<count($y[$i]); $j++)
                {
                    $sum += ($y[$i][$j] - $yp[$i])**2;
                }
            }else $sum += ($y[$i] - $yp[$i])**2;
            
        }

        $result = ($n-$k-1)==0 ? 0 : sqrt(($sum/($n-$k-1)));

        return $result;
    }

    
    /// (Σ(xᵢ))/n
    public static function avg(array $matrix) : float
    {
        $count = 0;
        $e = 0;
        foreach($matrix as $l)
        {
            if(is_array($l))
            {
                foreach($l as $c)
                {
                    $count++;
                    $e += $c;
                }
            }else
            {
                $count++;
                $e += $l;
            }
        }

        return $e / $count;
    }

    
    /// Σ(xᵢ-x̅)²
    private static function S2(array $arr)
        {
            $sum = 0;
            $med = self::avg($arr);
            foreach($arr as $a)
            {
                if(is_array($a))
                {
                    foreach($a as $b)
                    {
                        $sum += ($b - $med) ** 2;
                    }
                } else $sum += ($a - $med) ** 2;
            }

            return $sum;
        }

    /// Σ(xᵢ-x̅).(yᵢ - ȳ)
    private static function Sxy(array $x, array $y)
    {
        $mx = self::avg($x);
        $my = self::avg($y);
        $n = count($x);
        $s = 0;

        for($i = 0; $i < $n; $i++)
        {
            if(is_array($x[$i]) && is_array($y[$i]))
            {
                for($j=0;$j<count($x[$i]); $j++)
                {
                    $s += ($x[$i][$j] - $mx) * ($y[$i][$j] - $my);
                }
            }else if(is_array($x[$i]))
            {
                for($j=0;$j<count($x[$i]); $j++)
                {
                    $s += ($x[$i][$j] - $mx) * ($y[$i] - $my);
                }
            }else $s += ($x[$i] - $mx) * ($y[$i] - $my);
        }

        return $s;
    }

    /// Sxy/√(Sxx*Syy)
    public static function correlation(array $x, array $y)
    {
        $sxy = self::Sxy($x, $y);
        $s2x = self::S2($x);
        $s2y = self::S2($y);
        $sqrt = sqrt(($s2x * $s2y));

        return  $sxy / $sqrt;
    }
}