<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * This is the catalog geocoordsfield extension file.
 *
 * PHP version 5
 * @copyright  Nikolas Runde 2011
 * @author     Nikolas Runde  <nikolas.runde@nrmedia.de> 
 * @package    CatalogGeoCoordsField
 * @license    GPL 
 * @filesource
 */

// class to manipulate the field info to be as we want it to be, to render it and to make editing possible.
class CatalogGeoCoordsField extends Backend {

	public function getCoords($varValue, DataContainer $dc)
	{	
		$objFields = $this->Database->prepare("SELECT * FROM tl_catalog_fields WHERE  type='geocoordsfield' and pid=?") 
				->limit(1)
				->execute($dc->activeRecord->pid);

		$streetfield = $objFields->geocoords_streetfield;
		$postalfield = $objFields->geocoords_postalfield;
		$cityfield = $objFields->geocoords_cityfield;
		$countryfield = $objFields->geocoords_countryfield;
		
		$arrAddress = array();
		$arrAddress[] =$dc->activeRecord->$streetfield;		
		$arrAddress[] =$dc->activeRecord->$postalfield;
		$arrAddress[] =$dc->activeRecord->$cityfield;
		$arrAddress[] =$dc->activeRecord->$countryfield;
		$arrAddress = array_filter($arrAddress); // remove empty elements
		
		$strAdress =  urlencode(implode(",",$arrAddress));;

		

		$strGeoURL = 'http://maps.googleapis.com/maps/api/geocode/json?address='.$strAdress .'&sensor=false';
		
		$objRequest = new Request();
		$objRequest->send($strGeoURL);
		if (!$objRequest->hasError())
		{
			$arrInfo = json_decode($objRequest->response);
			
			if($arrInfo->status=='OK')
			{
				$objAddress = $arrInfo->results[0];
				
				$arrFieldValues = array();
				
				foreach ($objAddress->address_components as $addressComponents)
				{
					$arrFieldValues[$addressComponents->types[0]] = $addressComponents->long_name;
				}
				
				$varValue = $objAddress->geometry->location->lat . ',' . $objAddress->geometry->location->lng;
				
				$arrValueSets=array();
				if ($objFields->geocoords_streetfield_rewrite)
				{
					$arrValueSets[$streetfield] = $arrFieldValues['route'].' '.$arrFieldValues['street_number'];
				}
				
				if ($objFields->geocoords_postalfield_rewrite)
				{
					$arrValueSets[$postalfield] = $arrFieldValues['postal_code'];
				}
				if ($objFields->geocoords_countryfield_rewrite)
				{
					$arrValueSets[$countryfield] = $arrFieldValues['country'];
				}
				if ($objFields->geocoords_cityfield_rewrite)
				{
					$arrValueSets[$cityfield] = $arrFieldValues['locality'];
				}
				if (count($arrValueSets)>0)
				{
				
					$objDestinationTable = $this->Database->prepare("SELECT tableName FROM tl_catalog_types WHERE id=?") 
									->limit(1)
									->execute($dc->activeRecord->pid);
				
				
					$objValueUpdate = $this->Database->prepare("UPDATE ".$objDestinationTable->tableName." %s WHERE id=?")->set($arrValueSets)
											->executeUncached($dc->activeRecord->id);
					
				}
			}
			else
			{
				$varValue = $arrInfo->status;

				$varValue = $objAddress->geometry->location->lat . ',' . $objAddress->geometry->location->lng;
				
			}
			
		}
		else
		{
			$varValue = 'REQUEST ERROR';
		}
		
		return $varValue;
	}
}
?>