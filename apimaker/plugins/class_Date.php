<?php

class Date{
	public $date;
	public $timezone;
	function __construct( $date = "" ){
		if( $date ){
			$this->date = $date;
		}else{
			$this->date = date("Y-m-d");
		}
	}
}