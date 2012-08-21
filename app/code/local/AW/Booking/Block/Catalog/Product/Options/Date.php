<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Booking
 * @version    1.2.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Booking_Block_Catalog_Product_Options_Date extends Varien_Object
{

	const ID_PREFIX = 'id-';
   
	/** Type identity hours */
	const TYPE_HOURS = 'hours';
	/** Type identity minutes */
	const TYPE_MINUTES = 'minutes';
	/** Type identity daypart */
	const TYPE_DAYPART = 'daypart';

	/** Type identity AM hours */
	const TYPE_HOURS_AM = 'hours-am';
	/** Type identity PM hours */
	const TYPE_HOURS_PM = 'hours-pm';


	public $hourStart;
	public $hourEnd;
	
	public $_hours;
	public $_minutes;
	public $_daypart;
	

	/**
	 * Return whole element HTML
	 * @return string
	 */
	public function getElementHtml(){
		$this->setClass('select');

		$value_hrs = $this->_hours;
		$value_min = 0;
		$value_daypart = 'am';


        if(($this->hourStart != $this->hourEnd) && ($this->hourStart > $this->hourEnd)){
            list($this->hourStart, $this->hourEnd) = array($this->hourEnd, $this->hourStart);
        }

		if($this->hourStart == $this->hourEnd && (!$this->hourStart)){
			$this->hourEnd = 23;
		}

		if ($this->is24hTimeFormat()) {
			$minHour = 0;
			$maxHour = 23;
			$dayPart = null;
			// 24h
			
			// Check ranges
			if($this->hourStart > $maxHour)
				$this->hourStart = $maxHour;
			if($this->hourStart < $minHour)
				$this->hourStart = $minHour;			
			if($this->hourEnd > $maxHour)
				$this->hourEnd = $maxHour;
			if($this->hourEnd < $minHour)
				$this->hourEnd = $maxHour;		
			
		}else{
			$minHour = 1;
			$maxHour = 12;
			$dayPart = 1;
			//12h
			if($this->_hours > 12){
				$value_daypart = 'pm';
				$value_hours = $this->_hours - 12;
			}
			
			if($this->hourEnd > 12){
				//$this->hourEnd = $this->hourEnd - 12;
			}
			
			if(!$this->_hours){
				$value_hours = 12;
			}
		}
		
		
		
		
		$html = '';

		if(!$this->is24hTimeFormat()){
			if($this->_isAccessibleInAM() && $this->_isAccessibleInPM()){
				// AM && PM
				$blockAm = clone $this;
				$blockPm = clone $this;
				
				$html .= $blockAm->getHoursHtml($this->hourStart, 11, $this->getHours(), self::TYPE_HOURS_AM, false);
				$html .= $blockPm->getHoursHtml(0, $this->hourEnd-12, $this->getHours()-12, self::TYPE_HOURS_PM, false);
				
				if($this->getDayPart() != 'pm'){
					$html .= $blockAm->getHoursHtml($this->hourStart, 11, $this->getHours());
				}else{
					$html .= $blockPm->getHoursHtml(0, $this->hourEnd-12, $this->getHours()-12);
				}
			}else{
				// AM or PM only
				$html .= $this->getHoursHtml($this->hourStart-(12 * (int)$this->_isAccessibleInPM()), $this->hourEnd-(12 * (int)$this->_isAccessibleInPM()), $this->getHours()-(12 * (int)$this->_isAccessibleInPM()));
			}
			
		}else{
			$html = $this->getHoursHtml($this->hourStart, $this->hourEnd, $this->getHours());
		}
		
		
		$html.= '&nbsp;<b>:</b>&nbsp;<select id="' . $this->getId('minutes') . '" name="'. $this->getName(self::TYPE_MINUTES) . '" '.$this->serialize($this->getHtmlAttributes()).' style="width:40px">'."\n";
		for( $i=0;$i<60;$i++ ) {
			$hour = str_pad($i, 2, '0', STR_PAD_LEFT);
			$html.= '<option value="'.$hour.'" '. ( ($value_min == $i) ? 'selected="selected"' : '' ) .'>' . $hour . '</option>';
		}
		$html.= '</select>'."\n";

		$html .= $this->getDayPartHtml($this->getDayPart());

		$html = "<div class=\"time-selector\">$html</div>";

		$html.= $this->getAfterElementHtml();
		$html .='<script type="text/javascript">'.$this->getHelperJs()."</script>";
		return $html;
	}
	
	/**
	 * Return HTML part with hours selector
	 * @param int $start
	 * @param int $end
	 * @param int $value_hrs
	 * @param string $id
	 * @param bool $visible
	 * @return string
	 */
	public function getHoursHtml($start, $end, $value_hrs = 0, $id='', $visible=true){
		$html = '<select id="' . $this->getId($id ? $id : self::TYPE_HOURS) . '" name="'. $this->getName($id ? $id : self::TYPE_HOURS) . '" '.$this->serialize($this->getHtmlAttributes()).' style="width:40px;'.($visible ? "":"display:none;").'">'."\n";
		for( $i=(int)$start;$i<=$end;$i++ ) {
			$z = $i;
			if(!$i && !$this->is24hTimeFormat()){
				$z = 12;
			}
			$hour = str_pad($z, 2, '0', STR_PAD_LEFT);
			$html.= '<option value="'.$hour.'" '. ( ($value_hrs == $i) ? 'selected="selected"' : '' ) .'>' . $hour . '</option>';
		}
		$html.= '</select>'."\n";
		return $html;
	}

	/**
	 * Return HTML part with date part selector (AM/PM)
	 * @param string $value_daypart
	 * @return string
	 */
	public function getDayPartHtml($value_daypart = 'am'){
		if (is_null($value_daypart)) $value_daypart = 'am';
        $html = '';
		if(!$this->is24hTimeFormat()){
			if(!$this->_isOnlyAMOrPmAccessible()){
				$html.= '&nbsp; &nbsp;<select id="' . $this->getId('daypart') . '" name="'. $this->getName(self::TYPE_DAYPART) . '" '.$this->serialize($this->getHtmlAttributes()).' style="width:40px" onchange="TimeInput'.$this->getId().'.dateSwitchHours(this)">'."\n";
				$html.= '<option value="am" '. ( ($value_daypart == 'am') ? 'selected="selected"' : '' ) .'>' . Mage::helper('catalog')->__('AM') . '</option>';
				$html.= '<option value="pm" '. ( ($value_daypart == 'pm') ? 'selected="selected"' : '' ) .'>' . Mage::helper('catalog')->__('PM') . '</option>';
				$html.= '</select>'."\n";
			}else{
				$html .= '<span class="daypart">'.($value_daypart == 'am' ? Mage::helper('catalog')->__('AM') : Mage::helper('catalog')->__('PM')).'</span>';
                $html .= '<input type="hidden" id="' . $this->getId('daypart') . '" name="'. $this->getName(self::TYPE_DAYPART) . '" value='.($value_daypart == 'am' ? 'am' : 'pm').' />';
			}
			return $html;
		}
		return '';
	}
	
	/**
	 * Set time recieved via array [h,m] or string "h,m"
	 * @param mixed $time
	 * @return object
	 */
	public function setTime($time){
		if(is_string($time)){
			list($h,$m) = explode(",", $time);
		}elseif(is_array($time)){
			$h = $time[0];
			$m = $time[1];
		}
		if(isset($h)){
			$this->setHours($h)->setMinutes($m);
			
			if(!$this->is24hTimeFormat()){
				if((int)$h > 11){
					$this->setDayPart('pm');
				}else{
					$this->setDayPart('am');
				}
			}
		}
		return $this;
	}
	
	public function setHours($h){
		// Sets hours value
		$this->setData('hours', $h >= 24 ? 23 : ( $h < 0 ? 0 : $h));
		return $this;
	}
	public function setMinutes($h){
		// Sets minutes value
		$this->setData('minutes', $h >= 60 ? 59 : ( $h < 0 ? 0 : $h));
		return $this;
	}	
	
	public function setHourStart($t){
		$this->hourStart = (int)$t;
		return $this;
	}
	public function setHourEnd($t){
		$this->hourEnd = (int)$t;
		return $this;
	}	
	
	/**
	 * Return element id
	 * @param object $postfix [optional]
	 * @return 
	 */
	public function getId($postfix=''){
		$id = $this->getData('id');
		if($postfix){
			$id .= '-'.$postfix;
		}
		return $id;
	}
	
	
	/**
	 * If AM part is accessible
	 * @return bool
	 */
	protected function _isAccessibleInAM(){
		return ($this->hourStart < 12);
	}
	/**
	 * If PM part is accessible
	 * @return bool
	 */
	protected function _isAccessibleInPM(){
		return ($this->hourEnd > 12);
	}	
	
	/**
	 * Indicate if only AM or only PM is accessible 
	 * @return bool 
	 */
	protected function _isOnlyAMOrPmAccessible(){
		return !($this->_isAccessibleInAM() && $this->_isAccessibleInPM());
	}
	
	
	/**
	 * Detect if 24 hour format used for output
	 * @return bool
	 */
	public function is24hTimeFormat(){
		if(is_null($this->getIs24hTimeFormat())){
			return Mage::getSingleton('catalog/product_option_type_date')->is24hTimeFormat();
		}
		return (bool)$this->getIs24hTimeFormat();
	}

	/**
	 * Return helping JS for switching between AM/PM and also get values methods
	 * @return string
	 */
	public function getHelperJs(){
		return "
			{$this->getJsObjectName()} = {
				dateSwitchHours : function(el){
					var isPm = el.selectedIndex;
					var toShow = isPm ? '{$this->getId(self::TYPE_HOURS_PM)}' : '{$this->getId(self::TYPE_HOURS_AM)}';
					$('{$this->getId(self::TYPE_HOURS)}').options.length = 0;
					\$A($(toShow).options).each(function(op){
						var newOp = new Option(op.text, op.value);
						$('{$this->getId(self::TYPE_HOURS)}')[$('{$this->getId(self::TYPE_HOURS)}').options.length] = (newOp);
					})
				},
				getMinutes : function(){
					var hours = {$this->getJsObjectName()}.getAbsoluteHours();
					return parseInt(hours*60 + \$F('{$this->getId(self::TYPE_MINUTES)}'));
				},
				getAbsoluteHours : function(){
				   var hours = 1*\$F($('{$this->getId(self::TYPE_HOURS)}'))
				    if($('{$this->getId(self::TYPE_DAYPART)}')){
					    if(hours == 12){
						    hours = 0;
					    }
					    if(\$F($('{$this->getId(self::TYPE_DAYPART)}')) == 'pm'){
						    hours += 12;
					    }
				    }
				    return hours;
				},
				getValueString : function(){
				    return '' + (100+this.getAbsoluteHours()+'').substr(1) + ':' + \$F('{$this->getId(self::TYPE_MINUTES)}') + ':00';
				}
			}
		";
	}

	/**
	 * Return name for JS Helper object
	 * @return string
	 */
	public function getJsObjectName(){
		return "TimeInput{$this->getId()}";
	}

	/**
	 * Return complete name 
	 * @param <type> $part
	 * @return <type>
	 */
	public function getName($part = null){
	    if(!$part){
		return $this->getData('name');
	    }
	    $name =  $this->getData('name')."[".$part."]";
	    switch($part){
		case self::TYPE_HOURS:
		case self::TYPE_MINUTES:
		case self::TYPE_DAYPART:
		case self::TYPE_HOURS_AM:
		case self::TYPE_HOURS_PM:
		    return $name;
		default:
		    return $this->getName();
	    }
	}
}
