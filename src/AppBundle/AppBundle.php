<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function boot()
    {
        // SQLite foreign key support
        $conn = $this->container->get('database_connection');
        if ($conn->getParams()['driver'] == 'pdo_sqlite') {
            $conn->exec('PRAGMA foreign_keys=1');
        }
    }
}
