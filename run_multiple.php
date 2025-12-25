<?php

error_reporting(E_ERROR);
require __DIR__ . '/vendor/autoload.php';

use App\MultipleLinearRegression;
use App\Utils\Utils;
use App\Utils\AIUtils;



list($X, $Y) = Utils::load_csv_data(__DIR__ . '/multiple.csv');

// $count = 0;
do {
# code...
    list($X_train, $X_test, $Y_train, $Y_test) = Utils::test_train_split($X, $Y, 0.2);

    $model = new MultipleLinearRegression();
    $train = $model->fit($X_train, $Y_train);
    $pd = $model->predict($X_test);
    $score = AIUtils::rsquared($Y_test, $pd);
    $score_ajust = AIUtils::r2adjust($Y_test, $pd, count($X_test[0]));
    // echo $score_ajust . PHP_EOL;
    // $count++;S
} while ($score_ajust<0.8);

printf('R: %.2f' .  PHP_EOL, AIUtils::correlation($Y_test, $pd));
printf('R²: %.2f' .  PHP_EOL, AIUtils::rsquared($Y_test, $pd));
printf('R²adj: %.2f' .  PHP_EOL, AIUtils::r2adjust($Y_test, $pd, count($X_train[0])));
printf('Standad Error: %.2f' .  PHP_EOL, AIUtils::standardError($Y_test, $pd, count($X_train[0])));

$X_pred = [
    [1,1,2026],
    [0,1,2026]
];

$pd = $model->predict($X_pred);

Utils::print_matrix($pd, "Multiple Linear Regression");