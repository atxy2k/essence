<?php namespace Atxy2k\Essence\Commands\MexicanTowns;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 14:37
 */
use Illuminate\Console\Command;

class MexicanTownsCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codelab:localidades';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generando localidades';

    protected $estadosRepository;
    protected $municipiosRepository;
    protected $localidadesService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( EstadosRepository $estadosRepository,
                                 MunicipiosRepository $municipiosRepository, LocalidadesService $localidadesService )
    {
        parent::__construct();
        $this->estadosRepository    = $estadosRepository;
        $this->municipiosRepository = $municipiosRepository;
        $this->localidadesService   = $localidadesService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file_path = app_path(sprintf('Kernel%sResources%s',  DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR)).'localidades.txt';
        if( file_exists($file_path) )
        {
            $data = [];
            foreach ( file($file_path) as $n => $line )
            {
                if( is_string($line) && strlen($line) > 10 )
                {
                    $newLine = substr( trim($line), 1, -3);
                    $items = explode(',', $newLine);
                    // 0 => id estado
                    // 1 => nombre estado
                    // 2 => idMunicipio
                    // 3 => nombre municipio
                    // 4 => ciudad
                    // 5 => zona
                    // 6 => cp
                    // 7=> asentamiento
                    // 8 => tipo
                    if( count($items) == 9 )
                    {
                        $obj = [
                            "estado_slug" => Str::slug(trim(str_replace("'", '', $items[1]))),
                            "municipio_slug" => Str::slug( str_replace("'", '', trim($items[3]) )),
                            "ciudad" => str_replace("'",'', trim($items[4])),
                            "zona" => str_replace("'",'', trim($items[5])),
                            "codigo_postal" => str_replace("'",'', trim($items[6])),
                            "asentamiento" => str_replace("'",'', trim($items[7])),
                            "tipo" => str_replace("'",'', trim($items[8]))
                        ];
                        $data[] = $obj;
                        print sprintf('Almacenando para procesar %d'.PHP_EOL, $n);
                    }
                    else
                    {
                        print sprintf('El elemento no cuenta con los elementos acomodados correctamente'.PHP_EOL);
                    }
                }
                else
                {
                    print sprintf('La linea no cuenta con la longitud adecuada'.PHP_EOL);
                }
            }
            sprintf('Registrando %d elementos', count($data));
            foreach ( $data as &$item )
            {
                $estado = $this->estadosRepository->findBySlug( $item['estado_slug'] );
                if( !is_null($estado) )
                {
                    $municipio = $this->municipiosRepository->findBySlug( $estado->id, $item['municipio_slug'] );
                    if( !is_null($municipio) )
                    {
                        $item['municipio_id'] = $municipio->id;
                    }
                    unset( $item['estado_slug'] );
                    unset( $item['municipio_slug'] );
                    $item['nombre'] = $item['asentamiento'];
                    $response = $this->localidadesService->create( $item );
                    if( $response )
                    {
                        print sprintf('"%s" registrado '.PHP_EOL, $item['nombre']);
                    }
                    else
                    {
                        print sprintf('"%s" no pudo registrarse'.PHP_EOL, $item['nombre']);
                    }
                }
                else
                {
                    print sprintf('"%s" no pudo registrarse'.PHP_EOL, $item['nombre']);
                }
            }
        }
        else
        {
            print sprintf('No se localizo el archivo necesario');
        }
    }
}
