<?php

namespace Mpietrucha\Svg;

use Mpietrucha\Support\Concerns\HasFactory;
use Illuminate\Support\Stringable;
use Illuminate\Support\Collection;
use Mpietrucha\Svg\Contracts\DelimitersInterface;

class Bag
{
    use HasFactory;

    protected Stringable $full;

    protected ?Stringable $default = null;

    protected array $sets = [];

    public function __construct(protected DelimitersInterface $delimiters, protected Collection $parameters = new Collection)
    {
    }

    public function build(Stringable $parameter): self
    {
        $this->full = $parameter->prepend($this->delimiters->start());

        $options = $parameter->toWordsCollection()->toStringable();

        $this->parameters->push($options->shift()->remove($this->delimiters->startClosing()));

        [$parameters, $defaults] = $options->partition(fn (Stringable $parameter) => $parameter->startsWith($this->delimiters->start()));

        $this->default = $defaults->last();

        $parameters->map(fn (Stringable $parameter) => $parameter->removeFirst($this->delimiters->start()))
            ->map(fn (Stringable $parameter) => $parameter->removeLast($this->delimiters->startClosing()))
            ->each(fn (Stringable $parameter) => $this->parameters->push($parameter));

        return $this;
    }

    public function parameterIndex(string $name): int|bool
    {
        return $this->parameters->search(fn (Stringable $parameter) => $parameter->is($name));
    }

    public function set(int $index, string $value): void
    {
        $this->sets[$index] = $value;
    }

    public function replace(): array
    {
        if (count($this->sets) === 0) {
            return [$this->full, $this->default ?? ''];
        }

        return [$this->full->toString(), collect($this->sets)->sortKeys()->values()->first()];
    }
}
