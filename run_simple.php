<?php

error_reporting(E_ERROR);
require __DIR__ . '/vendor/autoload.php';

use App\SimpleLinearRegression;
use App\Utils\Utils;
use App\Utils\AIUtils;

# ------------------------------

list($X, $Y) = Utils::load_csv_data(__DIR__ . '/simple.csv', simpleLR:true);

$model = new SimpleLinearRegression();
do {
# code...
    list($X_train, $X_test, $Y_train, $Y_test) = Utils::test_train_split($X, $Y, 0.2);
    $train = $model->fit($X_train, $Y_train);
    $pd = $model->predict($X_test);
    $score = AIUtils::rsquared($Y_test, $pd);
    $score_ajust = AIUtils::r2adjust($Y_test, $pd, 1);
} while ($score_ajust<0.8);

printf('R: %.2f' .  PHP_EOL, AIUtils::correlation($Y_test, $pd));
printf('R²: %.2f' .  PHP_EOL, AIUtils::rsquared($Y_test, $pd));
printf('R²adj: %.2f' .  PHP_EOL, AIUtils::r2adjust($Y_test, $pd, 1));
printf('Standad Error: %.2f' .  PHP_EOL, AIUtils::standardError($Y_test, $pd, 1));

$X_pred = [
    12026,
    12026
];
$pd = $model->predict($X_pred);

Utils::print_matrix($pd, 'Simple Linear Regression');