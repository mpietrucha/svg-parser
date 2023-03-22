<?php

namespace Mpietrucha\Svg\Contracts;

interface DelimitersInterface
{
    public function start(): string;

    public function end(): string;

    public function startBeginning(): string;

    public function startClosing(): string;
}
