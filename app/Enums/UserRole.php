<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case EJECUTIVO = 'ejecutivo';
    case SUPERVISORA = 'supervisora';
    case PROMOTORA = 'promotora';
    case CLIENTE = 'cliente';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador',
            self::EJECUTIVO => 'Ejecutivo de Cuenta',
            self::SUPERVISORA => 'Supervisora de Plaza',
            self::PROMOTORA => 'Promotora Operativa',
            self::CLIENTE => 'Cliente Beneficiario',
        };
    }
    
}