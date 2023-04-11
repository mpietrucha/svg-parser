<?php

namespace Mpietrucha\Svg;

use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Macro;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Mpietrucha\Svg\Contracts\DelimitersInterface;
use Mpietrucha\Svg\Delimiters;
use Mpietrucha\Support\Condition;
use Mpietrucha\Support\Concerns\HasInputFile;

class Parser
{
    use HasFactory;
    use HasInputFile;

    protected Collection $bags;

    protected Stringable $contents;

    public function __construct(string $contents, protected DelimitersInterface $delimiters = new Delimiters)
    {
        Macro::bootstrap();

        $this->contents = str($contents);

        $this->buildParameters();
    }

    public function set(string $parameter, string $value): self
    {
        $bagWithIndex = fn (Bag $bag) => Condition::create([$bag, $index = $bag->parameterIndex($parameter)])
            ->addNull($index === false)
            ->resolve();

        $this->bags->map($bagWithIndex)->filter()->eachSpread(fn (Bag $bag, int $index) => $bag->set($index, $value));

        return $this;
    }

    public function toString(): string
    {
        $this->bags->each(fn (Bag $bag) => $this->contents = $this->contents->replace(...$bag->replace()));

        return $this->contents->toString();
    }

    protected function buildParameters(): void
    {
        $parameters = $this->contents->toBetweenCollection(
            $this->delimiters->startBeginning().$this->delimiters->start(), $this->delimiters->end()
        )->toStringable();

        $this->bags = $parameters->map(fn (Stringable $parameter) => Bag::create($this->delimiters)->build($parameter));
    }
}
