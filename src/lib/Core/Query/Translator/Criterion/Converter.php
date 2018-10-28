<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use RuntimeException;

/**
 * VisitorDispatcher aggregates and dispatches criterion visitors.
 */
final class Converter
{
    /**
     * A collection of aggregated visitors.
     *
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

    /**
     * Add visitor to the internal collection.
     *
     * @param \Cabbage\Core\Query\Translator\Criterion\Visitor $visitor
     */
    private function addVisitor(Visitor $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    /**
     * Visit the given criterion by dispatching aggregated visitors.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return array
     */
    public function convert(Criterion $criterion): array
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
