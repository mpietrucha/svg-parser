<?php

namespace Mpietrucha\Svg;

use Mpietrucha\Svg\Contracts\DelimitersInterface;

class Delimiters implements DelimitersInterface
{
    public function start(): string
    {
        return 'param(';
    }

    public function end(): string
    {
        return '"';
    }

    public function startBeginning(): string
    {
        return '"';
    }

    public function startClosing(): string
    {
        return ')';
    }
}
