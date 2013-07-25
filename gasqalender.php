<?php
require_once("phpQuery-onefile.php");
require_once("EasyPeasyICS.php");
require_once("class.fileaccess.php");
function preDump($String){
	echo '<pre>';
	var_dump(htmlentities($String));
	echo '</pre>';
}
function extractString($start, $end, $subject){
	
	$startPos = strpos($subject, $start);
	$endPos =  strpos($subject, $end);
	if($startPos !== false && $endPos !== false){
		$startPos += strlen($start);
		$length = $endPos - $startPos;
		
		return str_replace("&lt;br&gt;", " ",substr($subject, $startPos, $length) );
	
	}
	return false;
}

class GasqueParse{
	
	private $months = array();
	private $month;
	private $year;
	private $gasqueCal;
	private $fileName;
	private $iCal;
	
	//table>tr>td[1]>div
	function __construct(){
		$this->month = date("n");
		$this->year = date("Y");
		$this->fileName='gasqueCal_'.$this->year."-".$this->month."-".date("d").".psz";
		
		$this->iCal = new EasyPeasyICS("Hultners Gasquekalender");
		
		$fa = new FileAccess('./','icalcache');
		
		if($fa->FileExist($this->fileName)) {
			$cache = $fa->VarOfPlain($this->fileName);
			$this->iCal->renderString($cache);
			exit();
		}
		
		$this->gasqueCal=file_get_contents("http://gasquen.chs.chalmers.se/newsite/?page_id=4");
		$this->fetchMonths();

		foreach( $this->months as $monthDiv)
			$this->parseMonth($monthDiv);
			
		$this->iCal->render();
		$fa->FileOfPlain($this->fileName, $this->iCal->toString());
		
	}
	
	
	function fetchMonths(){
		phpQuery::newDocumentHTML($this->gasqueCal);
		for($i= 0; $i <= 6; $i++){
			$this->months[] = pq("#calendar".$i);
		}
	}
	
	
	function parseMonth($monthDiv){
		$this->month = ($this->month==7) ? 8 : $this->month;
		foreach( pq('table>tr', $monthDiv) as $weekTr)
			$this->parseWeek($weekTr);
		$this->month = ($this->month+1==13) ? 1 : $this->month+1;
	}
	
	function parseWeek($weekTr){
		foreach( pq('td.cal_td', $weekTr) as $dayTd )
			$this->parseDay($dayTd);
	}
	
	
	function parseDay($dayTd){
		$xml = utf8_decode($dayTd->ownerDocument->saveXML($dayTd));
		if($xml != NULL){
			$eventName = extractString("tooltip.show('", "');\" onmouseout=\"", $xml);
			if($eventName !== FALSE){
				$eventDay = extractString('<center>', "</center>", $xml);
				$eventDay = ($eventDay <= 9) ? "0".$eventDay : $eventDay;
				$eventNext = (($eventDay+1) <= 9) ? "0".($eventDay+1) : $eventDay+1;
				$eventMonth = ($this->month <= 9) ? "0".$this->month : $this->month;
				$this->iCal->addEvent( $this->year.$eventMonth.$eventDay , $this->year.$eventMonth.$eventNext, html_entity_decode($eventName));
			}
		}
		
	}	
}

new GasqueParse();
?>