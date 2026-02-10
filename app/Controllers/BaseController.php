<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
        
        // Update session with role_name if missing (for existing sessions)
        if (session()->get('logged_in') && !session()->has('role_name')) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->select('users.*, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.id', session()->get('user_id'))
                ->first();
            
            if ($user) {
                session()->set('role_name', $user['role_name'] ?? 'No Role');
            }
        }
    }

    protected function getRegion1Provinces(): array
    {
        return [
            'Ilocos Norte',
            'Ilocos Sur',
            'La Union',
            'Pangasinan',
        ];
    }

    protected function getRegion1Municipalities(): array
    {
        return [
            'Ilocos Norte' => [
                'Laoag City',
                'Batac City',
                'Pagudpud',
                'Bangui',
                'Pasuquin',
                'Burgos',
                'Bacarra',
                'Vintar',
                'Dumalneg',
                'Solsona',
                'Dingras',
                'Nueva Era',
                'Marcos',
                'Banna',
                'Sarrat',
                'Carasi',
                'Piddig',
                'Pinili',
                'San Nicolas',
                'Badoc',
                'Currimao',
                'Paoay',
            ],
            'Ilocos Sur' => [
                'Vigan City',
                'Candon City',
                'Santa Cruz',
                'Santa Maria',
                'Narvacan',
                'Santiago',
                'Bantay',
                'Caoayan',
                'Santa Catalina',
                'Magsingal',
                'San Vicente',
                'San Ildefonso',
                'San Juan',
                'Cabugao',
                'Sinait',
                'San Esteban',
                'Burgos',
                'Santa Lucia',
                'Lidlidda',
                'Tagudin',
                'Suyo',
                'Alilem',
                'Sugpon',
                'Sudipen',
                'Banayoyo',
                'Galimuyod',
                'Gregorio del Pilar',
                'Sigay',
                'Salcedo',
                'Santa',
                'Quirino',
                'Cervantes',
            ],
            'La Union' => [
                'San Fernando City',
                'Bauang',
                'Naguilian',
                'San Juan',
                'Bacnotan',
                'Balaoan',
                'Luna',
                'Bangar',
                'Santol',
                'San Gabriel',
                'Sudipen',
                'Caba',
                'Aringay',
                'Tubao',
                'Pugo',
                'Rosario',
                'Santo Tomas',
                'Agoo',
                'Burgos',
            ],
            'Pangasinan' => [
                'Dagupan City',
                'San Carlos City',
                'Urdaneta City',
                'Alaminos City',
                'Lingayen',
                'Mangaldan',
                'Manaoag',
                'Pozorrubio',
                'Sison',
                'Binalonan',
                'Laoac',
                'San Fabian',
                'San Jacinto',
                'Rosales',
                'Umingan',
                'Balungao',
                'Santa Maria',
                'Alcala',
                'Bautista',
                'Bayambang',
                'Bugallon',
                'Infanta',
                'Labrador',
                'Mabini',
                'Malasiqui',
                'Mapandan',
                'Natividad',
                'San Manuel',
                'San Nicolas',
                'San Quintin',
                'Santa Barbara',
                'Tayug',
                'Uyong',
                'Villasis',
                'Asingan',
                'Binmaley',
                'Bolinao',
                'Burgos',
                'Dasol',
                'Sual',
            ],
        ];
    }

    protected function isValidRegion1Location(string $province, string $municipality): bool
    {
        $province = trim($province);
        $municipality = trim($municipality);

        if ($province === '' || $municipality === '') {
            return false;
        }

        $provinces = $this->getRegion1Provinces();
        if (!in_array($province, $provinces, true)) {
            return false;
        }

        $municipalities = $this->getRegion1Municipalities();
        $list = $municipalities[$province] ?? [];

        return in_array($municipality, $list, true);
    }
}
