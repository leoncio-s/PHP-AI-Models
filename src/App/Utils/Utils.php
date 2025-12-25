<?php

namespace App\Utils;

use InvalidArgumentException;

class Utils
{
    public static function load_csv_data($filename = 'data.csv', $column_predict=0, $csv_separator=',', bool $simpleLR=false) : array
    {
        $csv = fopen($filename, 'r');

        $data =[];
        while(($dt=fgetcsv(stream:$csv, separator:$csv_separator)) !== FALSE)
        {
            
            $data[]=$dt;
        }
        unset($dt);

        $X=[];
        $Y=[];
        for($i = 1; $i < count($data); $i++)
        {
            $tmp_y=[];
            $tmp_x=[];
            for($j=0;$j<count($data[$i]); $j++)
            {
                if($j==$column_predict)
                {
                    $tmp_y[] = $data[$i][$j];
                }else{
                    $tmp_x[] = $data[$i][$j];
                }
            }
            if($simpleLR)
            {
                // var_dump(array_values($tmp_x));
                $Y[] = $tmp_y[0];
                array_push($X, $tmp_x[0]);
            }
            else
            {
                $Y[] = [$tmp_y[0]];
                $X[]=$tmp_x;
            }
        }
        return [$X, $Y];
    }

    public static function test_train_split(array $X, array $Y, $bias = 0.2)
    {
        if($X == null || $Y == null) throw new InvalidArgumentException('Invalid array value of X or Y parameter');
        $qtX = (int)(count($X)*$bias +1);

        $X_test = array();
        $Y_test = [];

        while($qtX > 0)
        {
            $length = count($X);

            $rand_int = random_int(1, $length);
        
            $xt = array_splice($X, $rand_int-1, 1);
            $X_test[] = $xt[0];

            $yt=array_splice($Y, $rand_int-1, 1);
            $Y_test[]= $yt[0];
            unset($X[$rand_int-1]);
            unset($Y[$rand_int-1]);
            $X=array_values($X);
            $Y=array_values($Y);
            $qtX--;
        }

        return [$X, $X_test, $Y, $Y_test];
    }

    public static function print_matrix(array $matrix, string $title="")
    {
        printf("================== PRINTING MATRIX %15s" . PHP_EOL, $title);
        foreach($matrix as $arr)
        {
            if(is_array($arr))
            {
                foreach($arr as $vlr)
                {
                    printf('%-10.2f', $vlr);
                }
                echo PHP_EOL;
            }else{
                printf('%-10.2f', $arr);
                echo PHP_EOL;
            }
        }
        printf("==================  END  PRINTING  %15s" . PHP_EOL, $title);
    }
}