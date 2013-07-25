<?php

/* ------------------------------------------------------------------------ */
/* EasyPeasyICS
/* ------------------------------------------------------------------------ */
/* Manuel Reinhard, manu@sprain.ch
/* Twitter: @sprain
/* Web: www.sprain.ch
/* Latest revision by Alexander Hultner, ahultner@gmail.com
/*
/* Built with inspiration by
/" http://stackoverflow.com/questions/1463480/how-can-i-use-php-to-dynamically-publish-an-ical-file-to-be-read-by-google-calend/1464355#1464355
/* ------------------------------------------------------------------------ */
/* History:
/* 2010/12/17 - Manuel Reinhard - when it all started
/* 2013/04/15 - Alexander Hultner - Added possibility to return as string
/* 2013/04/16 - Alexander Hultner - Heavily modified for specific usage, should not be used
/* ------------------------------------------------------------------------ */  

class EasyPeasyICS {

	protected $calendarName;
	protected $events = array();
	

	/**
	 * Constructor
	 * @param string $calendarName
	 */	
	public function __construct($calendarName=""){
		$this->calendarName = $calendarName;
	}//function


	/**
	 * Add event to calendar
	 * @param string $calendarName
	 */	
	public function addEvent($start, $end, $summary="", $description="", $url=""){
		$this->events[] = array(
			"start" => $start,
			"end"   => $end,
			"summary" => $summary,
			"description" => $description,
			"url" => $url
		);
	}//function
	
	
	public function render(){
		$this->renderString($this->toString());

	}//function
	
	public function toString(){
				$ics = "";
			
				//Add header
				$ics .= "BEGIN:VCALENDAR
METHOD:PUBLISH
VERSION:2.0
X-WR-CALNAME:".$this->calendarName."
PRODID:-//hacksw/handcal//NONSGML v1.0//EN";
				
				//Add events
				foreach($this->events as $event){
					$ics .= "
BEGIN:VEVENT
UID:". md5(uniqid(mt_rand(), true)) ."@hultner.se
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:".$event["start"]."
DTEND:".$event["end"]."
SUMMARY:".str_replace("\n", "\\n", $event['summary'])."
DESCRIPTION:".str_replace("\n", "\\n", $event['description'])."
LOCATION:
SEQUENCE:0
STATUS:CONFIRUMED
TRANSP:TRANSPARENT
END:VEVENT";
				}//foreach
				
				
				//Footer
				$ics .= "
END:VCALENDAR";
		//var_dump($this->events);
		return $ics;
	}
	
	public function renderString($String){
		//Output
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: inline; filename='.$this->calendarName.'.ics');
		echo $String;
	}

}//class