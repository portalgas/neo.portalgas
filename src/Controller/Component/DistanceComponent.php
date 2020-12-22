<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class DistanceComponent extends Component {

	private $_where_delivery = ['Deliveries.stato_elaborazione' => 'OPEN',
            					'Deliveries.sys' => 'N'];
    private $_where_order = ['Orders.state_code' => 'PROCESSED-ON-DELIVERY'];

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    public function get($user, $suppliers_organization, $debug=false) {

        if(empty($suppliers_organization))
            return;

        $user_positions = $this->_getUserPositions($user);
        // debug($user_positions);
      
        if($user_positions===false)
            return;

        $results = null;
        $user_lat = $user_positions['lat'];
        $user_lng = $user_positions['lng'];

        // debug($suppliers_organization);

        $supplier_lat = $suppliers_organization->supplier->lat;
        $supplier_lng = $suppliers_organization->supplier->lng;

        if($this->_isPositionValid($supplier_lat, $supplier_lng)) {

            $results = [];
            $results['supplierName'] = $suppliers_organization->name;

            $address = "";
            $address = $suppliers_organization->supplier->localita;
            //if(!empty($suppliers_organization->supplier->cap))
            //  $address .= ' ('.$suppliers_organization->supplier->cap.')';
            $results['supplierLocalita'] = $address;

            $distance = $this->_distance($user_lat, $user_lng, $supplier_lat, $supplier_lng);

            $results['distance'] = $distance;
            $percentuale = 100;
            if ($distance < Configure::read('LatLngDistanceAbsolute')) {
                $percentuale = $distance * 100 / Configure::read('LatLngDistanceAbsolute');
                $percentuale = round($percentuale);
            }
            $results['percentuale'] = $percentuale;
        }

        return $results;
    }

    /*
     * prima cerco tra la position dello user, dopo organization
     */
    private function _getUserPositions($user) {

        $lat = '';
        $lng = '';

        if(isset($user->user_profiles->lat))
            $lat = $user->user_profiles->lat;
        if(isset($user->user_profiles->lng))
            $lng = $user->user_profiles->lng;

        if(!$this->_isPositionValid($lat, $lng)) {
            $lat = $user->organization->lat;
            $lng = $user->organization->lng;
        }

        if(!$this->_isPositionValid($lat, $lng))
            return false;
        else
            return ['lat' => $lat, 'lng' => $lng];
    }

    private function _isPositionValid($lat='', $lng='') {
        $results = false;
        if(!empty($lat) && $lat != Configure::read('LatLngNotFound') && 
           !empty($lng) && $lng != Configure::read('LatLngNotFound'))
            $results = true;

        return $results;
    }

    /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    /* ::                                                                         : */
    /* ::  This routine calculates the distance between two points (given the     : */
    /* ::  latitude/longitude of those points). It is being used to calculate     : */
    /* ::  the distance between two locations using GeoDataSource(TM) Products    : */
    /* ::                                                                         : */
    /* ::  Definitions:                                                           : */
    /* ::    South latitudes are negative, east longitudes are positive           : */
    /* ::                                                                         : */
    /* ::  Passed to function:                                                    : */
    /* ::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  : */
    /* ::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  : */
    /* ::    unit = the unit you desire for results                               : */
    /* ::           where: 'M' is statute miles (default)                         : */
    /* ::                  'K' is kilometers                                      : */
    /* ::                  'N' is nautical miles                                  : */
    /* ::  Worldwide cities and other features databases with latitude longitude  : */
    /* ::  are available at http://www.geodatasource.com                          : */
    /* ::                                                                         : */
    /* ::  For enquiries, please contact sales@geodatasource.com                  : */
    /* ::                                                                         : */
    /* ::  Official Web site: http://www.geodatasource.com                        : */
    /* ::                                                                         : */
    /* ::         GeoDataSource.com (C) All Rights Reserved 2015                     : */
    /* ::                                                                         : */
    /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    private function _distance($lat1, $lon1, $lat2, $lon2, $unit='K') {

        $results = 0;
        
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            $results = ($miles * 1.609344);
        } else if ($unit == "N") {
            $results = ($miles * 0.8684);
        } else {
            $results = $miles;
        }
        
        $results = round($results, 2);
        
        return $results;
    }
}