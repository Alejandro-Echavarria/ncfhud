<?php

namespace App\Traits;

trait ConcatenatesClientDetails
{
    /**
     * Combina los detalles del cliente en un string formateado.
     *
     * @return object
     */
    public function getCombinedDetails(): object
    {
        return (object)[
            'id' => $this->id,
            'business_name' => "{$this->rnc} - {$this->business_name} - {$this->commercial_activity}"
        ];
    }
}
