<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Agenda;

use VsPoint\MoneyS3\DTO\Common\DefinitionXMLTransfer;
use VsPoint\MoneyS3\DTO\Wage\WageInput;
use VsPoint\MoneyS3\Result\MutationResult;

/**
 * Wages (mzda). Supports create, update and delete.
 */
final class WageService extends AbstractAgendaService
{
    public function create(WageInput $wage, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('createWage', [
            'wage' => $wage,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function update(WageInput $wage, ?DefinitionXMLTransfer $definition = null): MutationResult
    {
        return $this->mutate('updateWage', [
            'wage' => $wage,
            'definitionXMLTransfer' => $definition,
        ]);
    }

    public function delete(int $id): MutationResult
    {
        return $this->mutate('deleteWage', [
            'wage' => [
                'id' => $id,
            ],
        ]);
    }
}
