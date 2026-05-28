<?php

namespace App\Observers;

use App\Models\Pago;

class PagoObserver
{
     public function created(Pago $pago)
    {
        // 1. Obtenemos el préstamo a través de la relación
        $prestamo = $pago->calendarioPago->prestamo;

        // 2. Sumamos TODO el dinero directamente en la base de datos (Ultra rápido)
        $totalPagado = $prestamo->pagos()->sum('monto_pagado');

        // 3. Si el total pagado cubre o supera la deuda, cerramos el crédito
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
