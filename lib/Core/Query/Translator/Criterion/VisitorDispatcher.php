<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use RuntimeException;

final class VisitorDispatcher
{
    /**
     * @var \Cabbage\Core\Query\Translator\Criterion\Visitor[]
     */
    private $visitors = [];

    /**
     * @param \Cabbage\Core\Query\Translator\Criterion\Visitor[] $visitors
     */
    public function __construct(array $visitors)
    {
        foreach ($visitors as $visitor) {
            $this->addVisitor($visitor);
        }
    }

    private function addVisitor(Visitor $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    public function dispatch(Criterion $criterion): array
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->accept($criterion)) {
                return $visitor->visit($criterion, $this);
            }
        }

        $class = \get_class($criterion);

        throw new RuntimeException(
            "No visitor accepts instance of '{$class}'"
        );
    }
}
