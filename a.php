<?php

require_once 'vendor/autoload.php';

use Mpietrucha\Svg\Parser;

$file = file_get_contents('a.svg');

$parser = Parser::create($file);

$parser->set('o', 'xd');

dd($parser->toString());
