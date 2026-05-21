<?php

namespace App\Observers;

use App\Models\Pago;

class PagoObserver
{
    public function created(Pago $pago)
    {
        // Obtenemos el préstamo a través de la relación de la cuota [4, 5]
        $prestamo = $pago->calendarioPago->prestamo;

        // Calculamos el total pagado históricamente en este crédito
        $totalPagado = $prestamo->calendarioPagos()->withSum('pagos', 'monto_pagado')->get()->sum('pagos_sum_monto_pagado');

        // Si el total pagado es igual o mayor al monto total a pagar, liquidamos
        if ($totalPagado >= $prestamo->monto_total_pagar) {
            $prestamo->update(['estado' => 'liquidado']);
        }
    }


    /**
     * Handle the Pago "updated" event.
     */
    public function updated(Pago $pago): void
    {
        //
    }

    /**
     * Handle the Pago "deleted" event.
     */
    public function deleted(Pago $pago): void
    {
        //
    }

    /**
     * Handle the Pago "restored" event.
     */
    public function restored(Pago $pago): void
    {
        //
    }

    /**
     * Handle the Pago "force deleted" event.
     */
    public function forceDeleted(Pago $pago): void
    {
        //
    }
}
