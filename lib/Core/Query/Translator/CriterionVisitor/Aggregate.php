<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\CriterionVisitor;

use Cabbage\Core\Query\Translator\CriterionVisitor;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use RuntimeException;

final class Aggregate extends CriterionVisitor
{
    /**
     * @var \Cabbage\Core\Query\Translator\CriterionVisitor[]
     */
    private $visitors = [];

    /**
     * @param \Cabbage\Core\Query\Translator\CriterionVisitor[] $visitors
     */
    public function __construct(array $visitors)
    {
        foreach ($visitors as $visitor)
        {
            $this->addVisitor($visitor);
        }
    }

    private function addVisitor(CriterionVisitor $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    public function accept(Criterion $criterion): bool
    {
        return true;
    }

    public function visit(Criterion $criterion): array
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->accept($criterion)) {
                return $visitor->visit($criterion);
            }
        }

        $class = \get_class($criterion);

        throw new RuntimeException(
            "No visitor accepts instance of '{$class}'"
        );
    }
}
