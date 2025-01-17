<?php

namespace App\Services;

use Carbon\Carbon;

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
        $fieldsByMonthYear = $this->getFieldsToCompareByMonthYear();

        foreach ($fieldsToCompare as $field606 => $field607) {
            if (is_int($field606)) {
                $field606 = $field607;
            }

            $value606 = $invoice606[$field606] ?? null;
            $value607 = $invoice607[$field607] ?? null;

            if (in_array($field606, $fieldsByMonthYear, true)) {
                // Comparar solo mes/año
                $monthYear606 = $this->extractMonthYear($value606);
                $monthYear607 = $this->extractMonthYear($value607);

                if ($monthYear606 !== $monthYear607) {
                    $rowDifferences[$attributeTranslations[$field606] ?? $field606] = [
                        'invoices606' => $monthYear606,
                        'invoices607' => $monthYear607,
                    ];
                }
            } elseif (in_array($field606, ['amount', 'itbis', 'third_party_itbis_withheld'])) {
                // Comparar como números con redondeo
                if (round((float)$value606, 2) !== round((float)$value607, 2)) {
                    $rowDifferences[$attributeTranslations[$field606] ?? $field606] = [
                        'invoices606' => $value606,
                        'invoices607' => $value607,
                        'amount_difference' => $this->amountDifference($value606, $value607),
                    ];
                }
            } elseif ((string)$value606 !== (string)$value607) {
                // Comparar como cadenas
                $rowDifferences[$attributeTranslations[$field606] ?? $field606] = [
                    'invoices606' => $value606,
                    'invoices607' => $value607,
                ];
            }
        }

        return $rowDifferences;
    }

    private function extractMonthYear(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Convertir fecha al formato deseado usando Carbon
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y/m');
        } catch (\Exception $e) {
            return null; // Manejo de fechas inválidas
        }
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

    private function getFieldsToCompareByMonthYear(): array
    {
        return [
            'proof_date'
        ];
    }

    private function normalizeAmount(string|float|int $amount): float
    {
        // Convierte a float después de eliminar separadores de miles si es un string
        if (is_string($amount)) {
            $amount = (float)str_replace(',', '', $amount);
        }

        // Asegura que los valores negativos se normalicen como positivos
        return $amount < 0 ? $amount * -1 : $amount;
    }

    private function formatAmount(float $amount): string
    {
        // Devuelve el valor formateado con separadores de miles y dos decimales
        return number_format($amount, 2, '.', ',');
    }

    private function amountDifference(string|float|int $value606, string|float|int $value607): string
    {
        // Normaliza los montos
        $amount606 = $this->normalizeAmount($value606);
        $amount607 = $this->normalizeAmount($value607);

        // Si ambos valores son iguales después de la normalización
        if ($amount606 === $amount607) {
            return "Montos iguales (negativo)";
        }

        // Calcula la diferencia entre los montos y devuelve el valor formateado
        $difference = $amount606 - $amount607;
        return $this->formatAmount($difference);
    }

}
