<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class SelectField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_ALLOW_MULTIPLE_SELECT = 'allowMultipleSelect';
    public const OPTION_AUTOCOMPLETE = 'autocomplete';
    public const OPTION_CHOICES = 'choices';
    public const OPTION_RENDER_EXPANDED = 'renderExpanded';

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/select')
            ->setFormType(ChoiceType::class)
            ->addCssClass('field-select')
            ->setCustomOption(self::OPTION_ALLOW_MULTIPLE_SELECT, false)
            ->setCustomOption(self::OPTION_CHOICES, null)
            ->setCustomOption(self::OPTION_RENDER_EXPANDED, false);
    }

    public function allowMultipleSelect(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_MULTIPLE_SELECT, $allow);

        return $this;
    }

    public function autocomplete(): self
    {
        $this->setCustomOption(self::OPTION_AUTOCOMPLETE, true);

        return $this;
    }

    /**
     * Given choices must follow the same format used in Symfony Forms:
     * ['Label visible to users' => 'submitted_value', ...]
     */
    public function setChoices(array $keyValueChoices): self
    {
        $this->setCustomOption(self::OPTION_CHOICES, $keyValueChoices);

        return $this;
    }

    public function renderExpanded(bool $expanded = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_EXPANDED, $expanded);

        return $this;
    }
}
