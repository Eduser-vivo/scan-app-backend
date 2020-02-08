<?php

namespace App\Filter;

use App\Filter\SearchAnnotation;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

use Doctrine\Common\Annotations\AnnotationReader;

final class SearchFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property === 'search') {
            $this->logger->info('Search for: ' . $value);
        } else {
            return;
        }

        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation(new \ReflectionClass(new $resourceClass), SearchAnnotation::class);

        if (!$annotation) {
            throw new \HttpInvalidParamException('No Search implemented.');
        }

        $parameterName = $queryNameGenerator->generateParameterName($property);
        $search = [];
        $mappedJoins = [];
        $searchItems = explode(' ', str_replace('-', ' ', $value));

        if (is_array($searchItems)) {
            $andx = $queryBuilder->expr()->andx();

            foreach ($searchItems as $index => $searchItem) {
                $orx = $queryBuilder->expr()->orx();

                foreach ($annotation->fields as $field) {
                    $orx->add($queryBuilder->expr()->like('o.' . $field, ':' . $parameterName . '_' . $index));
                }

                if ($orx->count()) {
                    $queryBuilder->setParameter($parameterName . '_' . $index, '%' . $searchItem . '%');
                    $andx->add($orx);
                }
            }

            if ($andx->count()) {
                $queryBuilder->andWhere($andx);
            }
        }
    

        foreach ($annotation->fields as $field)
        {
            $joins = explode(".", $field);
            for ($lastAlias = 'o', $i = 0, $num = count($joins); $i < $num; $i++) {
                $currentAlias = $joins[$i];
                if ($i === $num - 1) {
                    $search[] = "LOWER({$lastAlias}.{$currentAlias}) LIKE LOWER(:{$parameterName})";
                } else {
                    $join = "{$lastAlias}.{$currentAlias}";
                    if (false === array_search($join, $mappedJoins)) {
                        $queryBuilder->leftJoin($join, $currentAlias);
                        $mappedJoins[] = $join;
                    }
                }

                $lastAlias = $currentAlias;
            }
        }

        $queryBuilder->andWhere(implode(' OR ', $search));
        $queryBuilder->setParameter($parameterName, '%' . $value . '%');
    }

    /**
     * @param string $resourceClass
     * @return array
     */
    public function getDescription(string $resourceClass): array
    {
        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation(new \ReflectionClass(new $resourceClass), SearchAnnotation::class);

        $description['search'] = [
            'property' => 'search',
            'type' => 'string',
            'required' => false,
            'swagger' => ['description' => 'FullTextFilter on ' . implode(', ', $annotation->fields)],
        ];

        return $description;
    }
}
