<?php

namespace App;

use App\Utils\InversionGaussJordan;
use InvalidArgumentException;

class MultipleLinearRegression implements AIModelInterface{
    private $gaussInverter;
    private $coeficcients;
    private array $e;

    public function __construct() {
        $this->gaussInverter = new InversionGaussJordan();
    }

    private function fitConstante( array $data) : array 
    {
        $to_ret = [];
        foreach($data as $x)
        {
            if(is_array($x)) array_unshift($x, 1);
            else $x = [1, $x];
            $to_ret[] = $x;
        }

        return $to_ret;
        
    }
    
    ///// Funções de Álgebra Linear e Convolução 1D
    private function transposition(array $matrix)
    {
        $transposta = [];
        foreach($matrix as $linha => $valores) {
            foreach($valores as $coluna => $valor) {
                $transposta[$coluna][$linha] = $valor;
            }
        }
        return $transposta;
    }

    /// soma do quadrado de regressão
    /// E = (ypi - med($y))^2
    private function SSreg(array $y, array $y_pred)
    {
        $med = 0;
        $n = count($y);
        for($i=0;$i<$n;$i++)
        {
            if(is_array($y[$i]))
            {
                for($j=0;$j<count($y[$i]);$j++)
                {
                    $m+=$y[$i][$j];
                }
            }else $m+=$y[$i];
        }
        $m = $m/$n;
        $e = 0;
        for($i=0;$i<count($y_pred); $i++)
        {
            if(is_array($y_pred[$i]))
            {
                for($j=0;$j<count($y_pred[$i]); $j++)
                {
                    $e += ($y_pred[$i][$j] - $m )**2;
                }
            }else $e += ($y_pred[$i] - $m )**2;
        }

        return $e;
    }

    /// soma do quadrado do residuos
    /// E += (yi - y_predi)^2
    private function SSres(array $y, array $y_pred) : float
    {
        $e=0;
        for($i=0;$i<count($y);$i++)
        {
            if(is_array($y[$i]))
            {
                for($j=0;$j<count($y[$i]); $j++)
                {
                    $e+=($y[$i][$j]-$y_pred[$i][$j])**2;
                }
            } else $e+=($y[$i]-$y_pred[$i])**2;
        }
        unset($i);
        unset($j);
        return $e;
    }

    /// soma do quadrado total
    /// E += (yi - med(y_pred))^2
    private function SStot(array $y, array $y_pred)
    {
        $n=count($y_pred);
        $m=0;
        foreach($y_pred as $si)
        {
            if(is_array($si))
            {
                foreach($si as $i)
                {
                    $m+=$i;
                }
            }else $m+=$i;
        }
        unset($si);
        unset($i);
        $m = $m / $n;

        $s = 0;
        for($i=0;$i<count($y);$i++)
        {
            if(is_array($y[$i]))
            {
                for($j=0;$j<count($y[$i]);$j++)
                {
                    $s+=($y[$i][$j] - $m)**2;               
                }
            }else $s+=($y[$i] - $m)**2;
        }
        unset($i);
        unset($j);
        unset($sqrt);
        return $s;
    }

    ///// multiplicação de matrizes
    private function matrixMultiply(array $A, array $B)
    {
        $rowsA = count($A);
        $colsA = count($A[0]);
        $rowsB = count($B);
        $colsB = count($B[0]);

        if($colsA != $rowsB) {
            throw new InvalidArgumentException("Número de colunas da primeira matriz({$colsA}) deve ser igual ao número de linhas da segunda matriz({$rowsB}).");
        }

        $result = array_fill(0, $rowsA, array_fill(0, $colsB, 0));

        for($i = 0; $i < $rowsA; $i++) {
            for($j = 0; $j < $colsB; $j++) {
                for($k = 0; $k < $colsA; $k++) {
                    $result[$i][$j] += (float)$A[$i][$k] * (float)$B[$k][$j];
                }
            }
        }
        return $result;
    }
    
    /// individual residuo
    // \(e_{i}=y_{i}-\^{y}_{i}\)
    private function individualResiduo(array $y, array $yp)
    {
        $e = [];
        for($i=0;$i<count($y);$i++)
        {
            if(is_array($y[$i]) && is_array($yp[$i]) )
            {
                for($j=0;$j<count($y[$i]); $j++)
                {
                    $e[] = $y[$i][$j] - $yp[$i][$j];
                }
            }else if(is_array($y[$i]))
            {
                for($j=0;$j<count($y[$i]); $j++)
                {
                    $e[] = $y[$i][$j] - $yp[$i];
                }
            }else $e[] = $y[$i] - $yp[$i];
        }
        return $e;
    }

    public function fit(array $X, array $y)
    {
        // add 1 on first column of matrix
        $X = $this->fitConstante($X);
        $Y = $this->fitConstante($y);

        // X^T
        $XT = $this->transposition($X);

        // (X^T.X)
        $XTX = $this->matrixMultiply($XT, $X);

        // (X^T.X)^(-1)
        $XTX_inv = $this->gaussInverter->inverse($XTX);

        // (X^T.y)
        $XTy = $this->matrixMultiply($XT, $Y);

        // (X^T.X)^(-1).(X^T.y)
        $this->coeficcients = $this->matrixMultiply($XTX_inv, $XTy);
        
        $yp = $this->matrixMultiply($X, $this->coeficcients);

        $this->e = $this->individualResiduo($Y, $yp);

        return $this->coeficcients;
    }

    public function predict(array $data)
    {
        $data = $this->fitConstante($data);
        $pred = $this->matrixMultiply($data, $this->coeficcients);
        
        $tmp1 = [];

        for($j=0;$j<count($pred);$j++)
        {
            for($i=1;$i<count($pred[$j]); $i++)
            {
                $tmp1[]=$pred[$j][$i] + $this->e[$j];
            }
        }

        return $tmp1;
    }
}