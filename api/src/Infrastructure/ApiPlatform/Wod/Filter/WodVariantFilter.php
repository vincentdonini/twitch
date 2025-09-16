<?php

namespace App\Infrastructure\ApiPlatform\Wod\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

class WodVariantFilter extends AbstractFilter
{
    private const SUPPORTED_OPERATORS = ['eq', 'neq', 'lt', 'lte', 'gt', 'gte', 'in', 'notIn'];

    protected function filterProperty(
        string $property,
               $value,
        QueryBuilder $qb,
        QueryNameGeneratorInterface $qng,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        if ($property !== 'variant') {
            return;
        }

        $alias           = $qb->getRootAliases()[0];
        $wodVersionAlias = $qng->generateJoinAlias('wodVersions');
        $variantAlias    = $qng->generateJoinAlias('wodVersionVariants');

        $qb->innerJoin("$alias.wodVersions", $wodVersionAlias)
            ->innerJoin("$wodVersionAlias.wodVersionVariants", $variantAlias);

        foreach ($value as $field => $criteria) {
            // Si c’est juste une valeur simple -> équivalent à = (eq)
            if (!is_array($criteria)) {
                $param = $this->generateParamName($field);
                $qb->andWhere("$variantAlias.$field = :$param")
                    ->setParameter($param, $criteria);
                continue;
            }

            // Sinon, on boucle sur chaque opérateur
            foreach ($criteria as $operator => $operand) {
                if (!in_array($operator, self::SUPPORTED_OPERATORS, true)) {
                    continue;
                }

                $param = $this->generateParamName($field . '_' . $operator);

                switch ($operator) {
                    case 'eq':
                        $qb->andWhere("$variantAlias.$field = :$param");
                        break;
                    case 'neq':
                        $qb->andWhere("$variantAlias.$field != :$param");
                        break;
                    case 'lt':
                        $qb->andWhere("$variantAlias.$field < :$param");
                        break;
                    case 'lte':
                        $qb->andWhere("$variantAlias.$field <= :$param");
                        break;
                    case 'gt':
                        $qb->andWhere("$variantAlias.$field > :$param");
                        break;
                    case 'gte':
                        $qb->andWhere("$variantAlias.$field >= :$param");
                        break;
                    case 'in':
                        $qb->andWhere($qb->expr()->in("$variantAlias.$field", ":$param"));
                        break;
                    case 'notIn':
                        $qb->andWhere($qb->expr()->notIn("$variantAlias.$field", ":$param"));
                        break;
                }

                $qb->setParameter($param, $operand);
            }
        }
    }

    private function generateParamName(string $field): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '_', $field) . uniqid();
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'variant[gender]' => [
                'property' => 'variant',
                'type'     => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'swagger'  => ['description' => 'Ex: female or male'],
            ],
            'variant[timeCap][gt]' => [
                'property' => 'variant',
                'type'     => Type::BUILTIN_TYPE_INT,
                'required' => false,
                'swagger'  => ['description' => 'timeCap greater than'],
            ],
            'variant[rounds][lte]' => [
                'property' => 'variant',
                'type'     => Type::BUILTIN_TYPE_INT,
                'required' => false,
                'swagger'  => ['description' => 'rounds maximum'],
            ],
        ];
    }
}

