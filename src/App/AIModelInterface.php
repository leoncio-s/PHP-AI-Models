<?php

namespace App;

interface AIModelInterface
{
    public function fit(array $x, array $y);
    public function predict(array $x);
}