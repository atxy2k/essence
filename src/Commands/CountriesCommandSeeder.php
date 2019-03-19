<?php namespace Atxy2k\Essence\Commands;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 14/03/2019
 * Time: 17:48
 */
use Atxy2k\Essence\Services\CountriesService;
use Illuminate\Console\Command;
use Throwable;

class CountriesCommandSeeder extends Command
{
    /** @var CountriesService  */
    protected $countriesService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essence:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generating countries of the world';

    public function __construct( CountriesService $countriesService )
    {
        parent::__construct();
        $this->countriesService = $countriesService;
    }

    /**
     * @throws Throwable
     */
    public function handle()
    {
        $countries = [
            [ 'name' => "Afganistan" ],
            [ 'name' => "Albania" ],
            [ 'name' => "Alemania" ],
            [ 'name' => "Andorra" ],
            [ 'name' => "Angola" ],
            [ 'name' => "Antartida" ],
            [ 'name' => "AntiguayBarbuda" ],
            [ 'name' => "ArabiaSaudi" ],
            [ 'name' => "Argelia" ],
            [ 'name' => "Argentina" ],
            [ 'name' => "Armenia" ],
            [ 'name' => "Australia" ],
            [ 'name' => "Austria" ],
            [ 'name' => "Azerbaiyan" ],
            [ 'name' => "Bahamas" ],
            [ 'name' => "Bahrain" ],
            [ 'name' => "Bangladesh" ],
            [ 'name' => "Barbados" ],
            [ 'name' => "Belgica" ],
            [ 'name' => "Belice" ],
            [ 'name' => "Benin" ],
            [ 'name' => "Bermudas" ],
            [ 'name' => "Bielorrusia" ],
            [ 'name' => "BirmaniaMyanmar" ],
            [ 'name' => "Bolivia" ],
            [ 'name' => "BosniayHerzegovina" ],
            [ 'name' => "Botswana" ],
            [ 'name' => "Brasil" ],
            [ 'name' => "Brunei" ],
            [ 'name' => "Bulgaria" ],
            [ 'name' => "BurkinaFaso" ],
            [ 'name' => "Burundi" ],
            [ 'name' => "Butan" ],
            [ 'name' => "CaboVerde" ],
            [ 'name' => "Camboya" ],
            [ 'name' => "Camerun" ],
            [ 'name' => "Canada" ],
            [ 'name' => "Chad" ],
            [ 'name' => "Chile" ],
            [ 'name' => "China" ],
            [ 'name' => "Chipre" ],
            [ 'name' => "Colombia" ],
            [ 'name' => "Comores" ],
            [ 'name' => "Congo" ],
            [ 'name' => "CoreadelNorte" ],
            [ 'name' => "CoreadelSur" ],
            [ 'name' => "CostadeMarfil" ],
            [ 'name' => "CostaRica" ],
            [ 'name' => "Croacia" ],
            [ 'name' => "Cuba" ],
            [ 'name' => "Dinamarca" ],
            [ 'name' => "Dominica" ],
            [ 'name' => "Ecuador" ],
            [ 'name' => "Egipto" ],
            [ 'name' => "ElSalvador" ],
            [ 'name' => "ElVaticano" ],
            [ 'name' => "EmiratosarabesUnidos" ],
            [ 'name' => "Eritrea" ],
            [ 'name' => "Eslovaquia" ],
            [ 'name' => "Eslovenia" ],
            [ 'name' => "España" ],
            [ 'name' => "Estados Unidos" ],
            [ 'name' => "Estonia" ],
            [ 'name' => "Etiopia" ],
            [ 'name' => "Filipinas" ],
            [ 'name' => "Finlandia" ],
            [ 'name' => "Fiji" ],
            [ 'name' => "Francia" ],
            [ 'name' => "Gabon" ],
            [ 'name' => "Gambia" ],
            [ 'name' => "Georgia" ],
            [ 'name' => "Ghana" ],
            [ 'name' => "Gibraltar" ],
            [ 'name' => "Granada" ],
            [ 'name' => "Grecia" ],
            [ 'name' => "Guam" ],
            [ 'name' => "Guatemala" ],
            [ 'name' => "Guinea" ],
            [ 'name' => "Guinea Ecuatorial" ],
            [ 'name' => "Guinea Bissau" ],
            [ 'name' => "Guyana" ],
            [ 'name' => "Haiti" ],
            [ 'name' => "Honduras" ],
            [ 'name' => "Hungria" ],
            [ 'name' => "India" ],
            [ 'name' => "IndianOcean" ],
            [ 'name' => "Indonesia" ],
            [ 'name' => "Iran" ],
            [ 'name' => "Iraq" ],
            [ 'name' => "Irlanda" ],
            [ 'name' => "Islandia" ],
            [ 'name' => "Israel" ],
            [ 'name' => "Italia" ],
            [ 'name' => "Jamaica" ],
            [ 'name' => "Japon" ],
            [ 'name' => "Jersey" ],
            [ 'name' => "Jordania" ],
            [ 'name' => "Kazajstan" ],
            [ 'name' => "Kenia" ],
            [ 'name' => "Kirguistan" ],
            [ 'name' => "Kiribati" ],
            [ 'name' => "Kuwait" ],
            [ 'name' => "Laos" ],
            [ 'name' => "Lesoto" ],
            [ 'name' => "Letonia" ],
            [ 'name' => "Libano" ],
            [ 'name' => "Liberia" ],
            [ 'name' => "Libia" ],
            [ 'name' => "Liechtenstein" ],
            [ 'name' => "Lituania" ],
            [ 'name' => "Luxemburgo" ],
            [ 'name' => "Macedonia" ],
            [ 'name' => "Madagascar" ],
            [ 'name' => "Malasia" ],
            [ 'name' => "Malawi" ],
            [ 'name' => "Maldivas" ],
            [ 'name' => "Mali" ],
            [ 'name' => "Malta" ],
            [ 'name' => "Marruecos" ],
            [ 'name' => "Mauricio" ],
            [ 'name' => "Mauritania" ],
            [ 'name' => "Mexico" ],
            [ 'name' => "Micronesia" ],
            [ 'name' => "Moldavia" ],
            [ 'name' => "Monaco" ],
            [ 'name' => "Mongolia" ],
            [ 'name' => "Montserrat" ],
            [ 'name' => "Mozambique" ],
            [ 'name' => "Namibia" ],
            [ 'name' => "Nauru" ],
            [ 'name' => "Nepal" ],
            [ 'name' => "Nicaragua" ],
            [ 'name' => "Niger" ],
            [ 'name' => "Nigeria" ],
            [ 'name' => "Noruega" ],
            [ 'name' => "NuevaZelanda" ],
            [ 'name' => "Oman" ],
            [ 'name' => "PaisesBajos" ],
            [ 'name' => "Pakistan" ],
            [ 'name' => "Palau" ],
            [ 'name' => "Panama" ],
            [ 'name' => "PapuaNuevaGuinea" ],
            [ 'name' => "Paraguay" ],
            [ 'name' => "Peru" ],
            [ 'name' => "Polonia" ],
            [ 'name' => "Portugal" ],
            [ 'name' => "PuertoRico" ],
            [ 'name' => "Qatar" ],
            [ 'name' => "Reino Unido" ],
            [ 'name' => "Republica Centro Africana" ],
            [ 'name' => "Republica Checa" ],
            [ 'name' => "RepublicaDemocraticadelCongo" ],
            [ 'name' => "RepublicaDominicana" ],
            [ 'name' => "Ruanda" ],
            [ 'name' => "Rumania" ],
            [ 'name' => "Rusia" ],
            [ 'name' => "SaharaOccidental" ],
            [ 'name' => "Samoa" ],
            [ 'name' => "SanCristobalyNevis" ],
            [ 'name' => "SanMarino" ],
            [ 'name' => "SanVicenteylasGranadinas" ],
            [ 'name' => "SantaLucia" ],
            [ 'name' => "SantoTomeyPrincipe" ],
            [ 'name' => "Senegal" ],
            [ 'name' => "Seychelles" ],
            [ 'name' => "SierraLeona" ],
            [ 'name' => "Singapur" ],
            [ 'name' => "Siria" ],
            [ 'name' => "Somalia" ],
            [ 'name' => "SouthernOcean" ],
            [ 'name' => "SriLanka" ],
            [ 'name' => "Swazilandia" ],
            [ 'name' => "Sudafrica" ],
            [ 'name' => "Sudan" ],
            [ 'name' => "Suecia" ],
            [ 'name' => "Suiza" ],
            [ 'name' => "Surinam" ],
            [ 'name' => "Tailandia" ],
            [ 'name' => "Taiwan" ],
            [ 'name' => "Tanzania" ],
            [ 'name' => "Tayikistan" ],
            [ 'name' => "Togo" ],
            [ 'name' => "Tokelau" ],
            [ 'name' => "Tonga" ],
            [ 'name' => "TrinidadyTobago" ],
            [ 'name' => "Tunez" ],
            [ 'name' => "Turkmekistan" ],
            [ 'name' => "Turquia" ],
            [ 'name' => "Tuvalu" ],
            [ 'name' => "Ucrania" ],
            [ 'name' => "Uganda" ],
            [ 'name' => "Uruguay" ],
            [ 'name' => "Uzbekistan" ],
            [ 'name' => "Vanuatu" ],
            [ 'name' => "Venezuela" ],
            [ 'name' => "Vietnam" ],
            [ 'name' => "Yemen" ],
            [ 'name' => "Djibouti" ],
            [ 'name' => "Zambia" ],
            [ 'name' => "Zimbabue" ],
        ];
        foreach ( $countries as $item )
        {
            $response = $this->countriesService->create( $item );
            if( $response )
            {
                $this->info(sprintf('"%s" registered '.PHP_EOL, $item['name']));
            }
            else
            {
                $this->error(sprintf('"%s" could not registered', $item['name']));
            }
        }
    }
}
