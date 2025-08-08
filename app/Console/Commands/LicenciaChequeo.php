<?php

namespace App\Console\Commands;

use App\Licencia;
use DateTime;
use DateTimeZone;

use Illuminate\Console\Command;

class LicenciaChequeo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licencia:chequeo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para chequear las licencias que se encuentran vencidas y bloquear el acceso';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $datetime = new DateTime("now", new DateTimeZone(config('app.timezone')));
        Licencia::chequearLicencias($datetime);
    }
}
