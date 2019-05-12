<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\QueryTranslator\Criterion\Visitor;

use Cabbage\API\Query\Criterion\TranslationResolver as TranslationResolverCriterion;
use Cabbage\Core\Searcher\QueryTranslator\Criterion\Visitor;
use Cabbage\Core\Searcher\QueryTranslator\Criterion\Converter;
use Cabbage\SPI\TranslationFilter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchAll;

/**
 * Visits TranslationResolver criterion.
 *
 * @see \Cabbage\API\Query\Criterion\TranslationResolver
 */
final class TranslationResolver extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof TranslationResolverCriterion;
    }

    public function visit(Criterion $criterion, Converter $converter): array
    {
        /** @var \Cabbage\API\Query\Criterion\TranslationResolver $criterion */
        $criteria = $this->buildCriteria($criterion->translationFilter);

        return $converter->convert($criteria);
    }

    private function buildCriteria(TranslationFilter $translationFilter): Criterion
    {
        return new MatchAll();
    }
}
