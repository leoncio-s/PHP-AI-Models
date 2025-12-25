<?php

namespace App\Utils;

use InvalidArgumentException;

class InversionGaussJordan
{

    public function inverse(array $matrix)
    {
        $inverser = $this->inversion_gauss_jordan($matrix);
        return $this->final_inversion_result($inverser);
    }

    //// define a identidade da matriz para inversão
    private function identidad(int $nLines, int $nColumns) : array
    {
        if($nLines !== $nColumns)
        {
            throw new InvalidArgumentException("A Matrix deve ser quadrada");
        }
        $I = [];
        $l = 0;
        for($i=0; $i < $nLines; $i++)
        {
            for($j=0;$j<$nColumns;$j++)
            {
                $I[$i][$j]=0;
                if($j >= $l && !in_array(1, $I[$i]))
                {
                    $I[$i][$j]=1;
                    $l+=1;
                }
            }
        }
        return $I;
    }

    ///// aumenta a matriz acrescentando 1 na diagonal
    private function augmented_matrix(array $matrix, array $identidad)
    {
        $augmented = [];
        for($i=0;$i < count($matrix); $i++)
        {
            $n = array_merge($matrix[$i], $identidad[$i]);
            $augmented[$i]=$n;
        }

        return $augmented;
    }


    /// caso o pivô 0,0 for 0, faz a substituição com outra
    private function invert_matrix(array $matrix, int $pivot=0) : array
    {

        if(empty($matrix) || !is_array($matrix[0]) ||count($matrix) < 1 || !isset($matrix))
        {
            throw new InvalidArgumentException("Matriz inválida");
        }

        $firstLine = $matrix[$pivot];
        $is_zero = abs($firstLine[$pivot])>0.0001;

        if($is_zero) return $matrix;

        for($i=$pivot+1; $i < count($matrix); $i++)
        {
            $curLine = $matrix[$i];

            $isZero = abs($curLine[$pivot])>0.0001;

            if($isZero)
            {
                $matrix[0] = $curLine;
                $matrix[$i]=$firstLine;
                return $matrix;
            }
        }

        return $matrix;
    }


    // caso o pivo não seja 1, faz a normalização
    private function normalize(array $matrix, int $pivot)
    {
        if(count($matrix[$pivot]) / 2 != count($matrix)) 
        {
            die("matriz inválida");
        }
        $tmp_matrix = $matrix;
        $p=$tmp_matrix[$pivot][$pivot];

        if($p==0)
        {
            $tmp_matrix = $this::invert_matrix($matrix, $pivot);
        }
        $r=$tmp_matrix[$pivot];
        for($i=0; $i < count($tmp_matrix[$pivot]); $i++)
        {
            if($p==0) continue;
            $r[$i]=(1/$p * $tmp_matrix[$pivot][$i]);
        }
        $tmp_matrix[$pivot]=$r;
        return $tmp_matrix;
    }

    /// se o elemento acima ou abaixo do pivo for diferente de zero, faz a inversão
    private function column_inversion(array $matrix, int $pivot)
    {
        if(count($matrix[$pivot]) / 2 != count($matrix)) 
        {
            die("matriz inválida");
        }
        $tmp_matrix = $matrix;
        if($matrix[$pivot][$pivot]==0)
        {
            $tmp_matrix = $this::invert_matrix($matrix);
        }

        $lp = $tmp_matrix[$pivot];
        $tmp = $tmp_matrix;

        for($i=0;$i < count($tmp_matrix); $i++)
        {
            $r1 = $tmp[$i];
            if($i != $pivot && $lp[$pivot] != 0)
            {
                $k = $tmp[$i][$pivot];
                $r2 = $tmp[$i];
                for($j=0;$j<count($r2);$j++)
                {
                    if($lp[$j] == 0)
                    {
                        continue;
                    }else if($lp[$pivot] < 0)
                    {
                        $r1[$j]= $r2[$j] + ($k / $lp[$j]);
                    }else{
                        $r1[$j] = $r2[$j] - ($k * $lp[$j]);
                    }
                }
                $tmp[$i]=$r1;
            }
        }

        return $tmp;
        
    }

    /// retorna o valor da inversão
    private function final_inversion_result(array $AI)
    {
        $A=[];
        for($i=0;$i < count($AI); $i++)
        {
            $tmp = [];

            for($j=(count($AI[$i])/2);$j<count($AI[$i]);$j++)
            {
                array_push($tmp, $AI[$i][$j]);
            }
            array_push($A, $tmp);
        }

        return $A;
    }

    private function inversion_gauss_jordan(array $matrix)
    {
        if(count($matrix[0]) != count($matrix)) 
        {
            die("matriz inválida");
        }
        
        $I = $this::identidad(count($matrix), count($matrix[0]));
        $A = $this::augmented_matrix($matrix, $I);

        for( $pivot=0; $pivot < count($A); $pivot++)
        {
            if((count($A[$pivot]) / 2) != count($A))
            {
                die("inv: matriz inválida");
            }
            $nomalized = $this::normalize($A, $pivot);

            $column = $this::column_inversion($nomalized, $pivot);

            $A = $column;
        }

        return $A;
    }
}