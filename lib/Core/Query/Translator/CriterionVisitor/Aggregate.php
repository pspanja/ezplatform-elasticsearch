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
    private $visitors;

    public function __construct(array $visitors)
    {
        $this->visitors = $visitors;
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
