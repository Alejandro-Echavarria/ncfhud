<?php

namespace App\Services;

class InvoiceComparisonService
{
    public function compareInvoices(array $invoices606, array $invoices607): array
    {
        $differences = [];
        $notIn607 = $this->findNotIn607($invoices606, $invoices607, $differences);
        $notIn606 = $this->findNotIn606($invoices606, $invoices607);

        return [
            'invoices607' => $invoices607,
            'invoices606' => $invoices606,
            'notIn607' => array_values($notIn607),
            'notIn606' => array_values($notIn606),
            'differences' => $differences,
        ];
    }

    private function findNotIn607(array $invoices606, array $invoices607, array &$differences): array
    {
        $attributeTranslations = $this->getAttributeTranslations();
        $fieldsToCompare = $this->getFieldsToCompare();

        return array_filter($invoices606, function ($invoice606) use ($invoices607, &$differences, $attributeTranslations, $fieldsToCompare) {
            foreach ($invoices607 as $invoice607) {
                if ($invoice607['ncf'] === $invoice606['ncf']) {
                    $rowDifferences = $this->compareFields($invoice606, $invoice607, $fieldsToCompare, $attributeTranslations);
                    if (!empty($rowDifferences)) {
                        $differences[] = [
                            'invoices606' => $invoice606,
                            'invoices607' => $invoice607,
                            'differences' => $rowDifferences,
                        ];
                    }
                    return false;
                }
            }
            return true;
        });
    }

    private function findNotIn606(array $invoices606, array $invoices607): array
    {
        return array_filter($invoices607, function ($invoice607) use ($invoices606) {
            foreach ($invoices606 as $invoice606) {
                if ($invoice606['ncf'] === $invoice607['ncf']) {
                    return false;
                }
            }
            return true;
        });
    }

    private function compareFields(array $invoice606, array $invoice607, array $fieldsToCompare, array $attributeTranslations): array
    {
        $rowDifferences = [];

        foreach ($fieldsToCompare as $field606 => $field607) {
            if (is_int($field606)) {
                $field606 = $field607;
            }

            $value606 = $invoice606[$field606] ?? null;
            $value607 = $invoice607[$field607] ?? null;

            if (in_array($field606, ['amount', 'itbis', 'withheld_itbis'])) {
                if (round((float)$value606, 2) !== round((float)$value607, 2)) {
                    $rowDifferences[$attributeTranslations[$field606] ?? $field606] = [
                        'invoices606' => $value606,
                        'invoices607' => $value607,
                    ];
                }
            } elseif ((string)$value606 !== (string)$value607) {
                $rowDifferences[$attributeTranslations[$field606] ?? $field606] = [
                    'invoices606' => $value606,
                    'invoices607' => $value607,
                ];
            }
        }

        return $rowDifferences;
    }

    private function getAttributeTranslations(): array
    {
        return [
            'amount' => 'monto',
            'proof_date' => 'fecha del comprobante',
            'withheld_itbis' => 'itbis retenido',
            'rnc' => 'rnc',
            'ncf' => 'número de comprobante fiscal',
            'itbis' => 'itbis',
            'third_party_itbis_withheld' => 'itbis retenido por terceros',
        ];
    }

    private function getFieldsToCompare(): array
    {
        return [
            'rnc',
            'ncf',
            'proof_date',
            'amount',
            'itbis',
            'withheld_itbis' => 'third_party_itbis_withheld',
        ];
    }
}
