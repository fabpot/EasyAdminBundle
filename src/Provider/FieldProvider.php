<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Provider;

use Doctrine\DBAL\Types\Types;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FieldProvider
{
    private $adminContextProvider;

    public function __construct(AdminContextProvider $adminContextProvider)
    {
        $this->adminContextProvider = $adminContextProvider;
    }

    public function getDefaultFields(string $pageName): array
    {
        $defaultPropertyNames = [];
        $maxNumProperties = Crud::PAGE_INDEX === $pageName ? 7 : \PHP_INT_MAX;
        $entityDto = $this->adminContextProvider->getContext()->getEntity();

        $excludedPropertyTypes = [
            Crud::PAGE_EDIT => [Types::BINARY, Types::BLOB, Types::JSON, Types::OBJECT],
            Crud::PAGE_INDEX => [Types::BINARY, Types::BLOB, Types::GUID, Types::JSON, Types::OBJECT, Types::TEXT],
            Crud::PAGE_NEW => [Types::BINARY, Types::BLOB, Types::JSON, Types::OBJECT],
            Crud::PAGE_DETAIL => [Types::BINARY, Types::JSON, Types::OBJECT],
        ];

        $excludedPropertyNames = [
            Crud::PAGE_EDIT => [$entityDto->getPrimaryKeyName()],
            Crud::PAGE_INDEX => ['password', 'salt', 'slug', 'updatedAt', 'uuid'],
            Crud::PAGE_NEW => [$entityDto->getPrimaryKeyName()],
            Crud::PAGE_DETAIL => [],
        ];

        $fields = [];
        foreach ($entityDto->getAllPropertyNames() as $propertyName) {
            $metadata = $entityDto->getPropertyMetadata($propertyName);
            $type = $metadata->get('type');
            if (\in_array($propertyName, $excludedPropertyNames[$pageName], true) || \in_array($type, $excludedPropertyTypes[$pageName], true)) {
                continue;
            }

            switch ($type) {
                case Types::DATE_IMMUTABLE:
                case Types::DATE_MUTABLE:
                case Types::DATETIME_IMMUTABLE:
                case Types::DATETIME_MUTABLE:
                case Types::DATETIMETZ_IMMUTABLE:
                case Types::DATETIMETZ_MUTABLE:
                    $fields[] = DateField::new($propertyName);
                    break;
                case Types::BOOLEAN:
                    $fields[] = BooleanField::new($propertyName);
                    break;
                default:
                    $fields[] = Field::new($propertyName);
            }

            if (\count($fields) === $maxNumProperties) {
                break;
            }
        }

        return $fields;
    }
}
