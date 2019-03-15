<?php namespace Atxy2k\Essence\Commands;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 14/03/2019
 * Time: 22:38
 */

use Atxy2k\Essence\Exceptions\Countries\CountryNotFoundException;
use Atxy2k\Essence\Services\StatesService;
use Illuminate\Console\Command;
use Throwable;
use Atxy2k\Essence\Repositories\CountriesRepository;

class StatesCommandSeeder extends Command
{
    /** @var StatesService  */
    protected $statesService;
    /** @var CountriesRepository */
    protected $countriesRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essence:states';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generating states of Mexico';

    public function __construct( StatesService $states_service, CountriesRepository $countriesRepository )
    {
        parent::__construct();
        $this->statesService = $states_service;
        $this->countriesRepository = $countriesRepository;
    }

    /**
     * @throws Throwable
     */
    public function handle()
    {
        $mexico = $this->countriesRepository->findBySlug('mexico');
        throw_if(is_null($mexico), CountryNotFoundException::class);

        $states = [
            [ 'name' => 'Aguascalientes', 'key' => '01' ],
            [ 'name' => 'Baja California', 'key' => '02' ],
            [ 'name' => 'Baja California Sur', 'key' => '03' ],
            [ 'name' => 'Campeche', 'key' => '04' ],
            [ 'name' => 'Chiapas', 'key' => '05' ],
            [ 'name' => 'Chihuahua', 'key' => '06' ],
            [ 'name' => 'Ciudad de México', 'key' => '07' ],
            [ 'name' => 'Coahuila de Zaragoza', 'key' => '08' ],
            [ 'name' => 'Colima', 'key' => '09' ],
            [ 'name' => 'Durango', 'key' => '10' ],
            [ 'name' => 'Guanajuato', 'key' => '11' ],
            [ 'name' => 'Guerrero', 'key' => '12' ],
            [ 'name' => 'Hidalgo', 'key' => '13' ],
            [ 'name' => 'Jalisco', 'key' => '14' ],
            [ 'name' => 'México', 'key' => '15' ],
            [ 'name' => 'Michoacán de Ocampo', 'key' => '16' ],
            [ 'name' => 'Morelos', 'key' => '17' ],
            [ 'name' => 'Nayarit', 'key' => '18' ],
            [ 'name' => 'Nuevo León', 'key' => '19' ],
            [ 'name' => 'Oaxaca', 'key' => '20' ],
            [ 'name' => 'Puebla', 'key' => '21' ],
            [ 'name' => 'Querétaro', 'key' => '22' ],
            [ 'name' => 'Quintana Roo', 'key' => '23' ],
            [ 'name' => 'San Luis Potosí', 'key' => '24' ],
            [ 'name' => 'Sinaloa', 'key' => '25' ],
            [ 'name' => 'Sonora', 'key' => '26' ],
            [ 'name' => 'Tabasco', 'key' => '27' ],
            [ 'name' => 'Tamaulipas', 'key' => '28' ],
            [ 'name' => 'Tlaxcala', 'key' => '29' ],
            [ 'name' => 'Veracruz de Ignacio de la Llave', 'key' => '30' ],
            [ 'name' => 'Yucatán', 'key' => '31' ],
            [ 'name' => 'Zacatecas', 'key' => '32' ],
        ];
        foreach ( $states as $item )
        {
            $item['country_id'] = $mexico->id;
            $response = $this->statesService->create( $item );
            if( $response )
            {
                $this->info(sprintf('"%s" registrado.', $item['name']));
            }
            else
            {
                $this->error(sprintf('"%s" no pudo registrarse.', $item['name']));
            }
        }
    }
}
