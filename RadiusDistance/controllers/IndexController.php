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
Class ThdSolution_RadiusDistance_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $request        = Mage::app()->getRequest()->getParams();
        $url            = sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s', $request['address'], $request['key']) ;
        $service        = Mage::getModel('thdsolution_radiusdistance/webservice')->send($url);

        $jsonResponse   = json_decode($service->getBody());
        $location       = $jsonResponse->results[0]->geometry->location;

        $placeLat       = $location->lat;
        $placeLon       = $location->lng;

        $resource       = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query          = Mage::helper('thdsolution_radiusdistance')->getHaversineQuery($placeLat, $placeLon);
        $results        = $readConnection->fetchAll($query);

        foreach($results as  $key => $place){
            $places[$key] = array(
                'name'      => $place['name'],
                'distancia' => $place['distance']
            );
        }

        //echo json_encode($places);

        return $places;
    }

}
