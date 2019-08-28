<?php
/**
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category  ThdSolution
 * @package   ThdSolution_RadiusDistance
 *
 * @author    Tiago Daniel <tiago.daniel@gmail.com.br>
 */
class ThdSolution_RadiusDistance_Model_Radius extends Mage_Core_Model_Abstract
{

    CONST GOOGLE_API = 'https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s';

    /**
     * Request data from Google API
     *
     * @param $request
     * @return string
     */
    public function getApiInformation($request)
    {
        $url             = sprintf(self::GOOGLE_API, $request['address'], $request['key']) ;

        $service         = Mage::getModel('thdsolution_radiusdistance/webservice')
                            ->setRequestType('GET')
                            ->send($url, 'json', array('timeout' => 60));

        $jsonResponse    = json_decode($service->getBody());
        $location        = $jsonResponse->results[0]->geometry->location;
        $placeLat        = $location->lat;
        $placeLon        = $location->lng;
        $resource        = Mage::getSingleton('core/resource');
        $readConnection  = $resource->getConnection('core_read');
        $query           = Mage::helper('thdsolution_radiusdistance')->getHaversineQuery($placeLat, $placeLon);
        $results         = $readConnection->fetchAll($query);

        foreach($results as  $key => $place){
            $places[$request['address']][$key] = array(
                'name'      => $place['name'],
                'distancia' => $place['distance']
            );
        }

        Mage::helper('thdsolution_radiusdistance')->setAddresstoCookie($places);

        return json_encode($places);
    }

}