<?php

namespace App\Repositories;

use App\Models\Cliente;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class ClienteRepository
{
    public function __construct()
    {
    }

    /**
     * Returns whole clients without casa assigned
     *
     * @return Collection
     */
    public function getClientesSinCasa()
    {
        /**
         * @var Builder $query
         */
        $query = "
          SELECT c.*
            FROM clientes c
            WHERE c.id NOT IN (SELECT cc.id_cliente FROM clientes_casas cc WHERE cc.deleted_at IS NULL) AND c.deleted_at IS NULL";

        return Cliente::fromQuery($query);
    }
}
