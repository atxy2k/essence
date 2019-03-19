<?php namespace Atxy2k\Essence\Commands\MexicanTowns;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 14:37
 */

use Artisan;
use Atxy2k\Essence\Repositories\CountriesRepository;
use Atxy2k\Essence\Repositories\MunicipalitiesRepository;
use Atxy2k\Essence\Repositories\StatesRepository;
use Atxy2k\Essence\Services\CountriesService;
use Atxy2k\Essence\Services\SuburbsService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;
use Exception;

class MexicanTownsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essence:localization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generating mexican localizations';

    protected $statesRepository;
    protected $municipalitiesRepository;
    protected $suburbsRepository;
    protected $countriesRepository;
    protected $countriesService;

    /**
     * MexicanTownsCommand constructor.
     * @param StatesRepository $statesRepository
     * @param MunicipalitiesRepository $municipalitiesRepository
     * @param CountriesService $countriesService
     * @param SuburbsService $suburbsSer
     * @param CountriesRepository $countriesRepository
     */
    public function __construct( StatesRepository $statesRepository,
                                 MunicipalitiesRepository $municipalitiesRepository,
                                 CountriesService $countriesService,
                                 SuburbsService $suburbsSer, CountriesRepository $countriesRepository )
    {
        parent::__construct();
        $this->statesRepository         = $statesRepository;
        $this->municipalitiesRepository = $municipalitiesRepository;
        $this->suburbsRepository        = $suburbsSer;
        $this->countriesRepository      = $countriesRepository;
        $this->countriesService         = $countriesService;
    }

    /**
     * @throws Throwable
     */
    public function handle()
    {
        $this->call('essence:countries');
        $this->call('essence:states');
        $this->call('essence:municipalities');

        $file_path = __DIR__.'/suburbs.txt';
        if( file_exists($file_path) )
        {
            $data = [];
            foreach ( file($file_path) as $n => $line )
            {
                if( is_string($line) && strlen($line) > 10 )
                {
                    $newLine = substr( trim($line), 1, -3);
                    $items = explode(',', $newLine);
                    if( count($items) === 9 )
                    {
                        $obj = [
                            'state_slug'        => Str::slug(trim(str_replace("'", '', $items[1]))),
                            'municipality_slug' => Str::slug( str_replace("'", '', trim($items[3]) )),
                            'zone'              => str_replace("'",'', trim($items[5])),
                            'postal_code'       => str_replace("'",'', trim($items[6])),
                            'settlement'        => str_replace("'",'', trim($items[7])),
                            'type'              => str_replace("'",'', trim($items[8]))
                        ];
                        $data[] = $obj;
                        $this->info(sprintf('Saved for process %d', $n));
                    }
                    else
                    {
                        $this->error('The item does not have all required elements');
                    }
                }
                else
                {
                    $this->error('Insufficient length in line');
                }
            }
            $this->info(sprintf('Registering %d elements', count($data)));
            $mexico_country = $this->countriesRepository->findBySlug('mexico');
            if($mexico_country === null)
            {
                $mexico_country = $this->countriesService->create(['name' => 'México']);
            }
            throw_if($mexico_country === null, new Exception('Mexico country does not exits.'));
            foreach ( $data as &$item )
            {
                $state = $this->statesRepository->findBySlug( $mexico_country->id, $item['state_slug'] );
                if( $state!==null )
                {
                    $municipality = $this->municipalitiesRepository->findBySlug( $state->id, $item['municipality_slug'] );
                    if( $municipality !== null )
                    {
                        $item['municipality_id'] = $municipality->id;
                    }
                    unset($item['state_slug'], $item['municipality_slug']);
                    $item['name'] = $item['settlement'];
                    $response = $this->suburbsRepository->create( $item );
                    if( $response )
                    {
                        $this->info(sprintf('"%s" registered.', $item['name']));
                    }
                    else
                    {
                        $this->error(sprintf('"%s" not registered', $item['name']));
                    }
                }
                else
                {
                    $this->error(sprintf('"%s" state not registered ', $item['state_slug']));
                }
            }
        }
        else
        {
            $this->error(sprintf('No se localizo el archivo necesario'));
        }
    }
}
