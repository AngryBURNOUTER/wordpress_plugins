<?php
/*
 * Plugin Name:       KABG Weather
 * Version:           0.0.1
 * Author:            Konstantin Grigurko
*/

class KabgWeather {
	
	private $baseUrl;
	private $period;
	private $location;
	private $userKey;
	private $mode;
	private $units;
	private $dayCount;

	public function __construct() {
		add_shortcode('pogoda', array($this, 'kabg_weather_listener'));
		add_filter('widget_text', 'do_shortcode');
		
		$this->setBaseUrl('https://api.openweathermap.org/data/2.5/');
		$this->setPeriod('forecast/daily');
		$this->setUserKey('c0c4a4b4047b97ebc5948ac9c48c0559');
		$this->setMode('json');
		$this->setUnits('metric');
		$this->setDayCount('10');
	}

	function kabg_weather_listener($atts) {
		extract(shortcode_atts( array(
	   		'city' => 'Kiev'
	  	), $atts ));

	  	$this->setLocation($city);

	  	$json = wp_remote_get($this->baseUrl . $this->period . $this->location . $this->userKey . $this->mode . $this->units . $this->dayCount . '&lang=ru');
	  	$weatherArray = json_decode($json['body'], true);
	  	
	  	for ($i=0; $i < count($weatherArray['list']); $i++) { 
	  		$averageTemperatureDay += (float)$weatherArray['list'][$i]['temp']['day'];
	  		$averageTemperatureNight += (float)$weatherArray['list'][$i]['temp']['night'];
	  	}
	  	$averageTemperatureDay = $averageTemperatureDay/count($weatherArray['list']);
	  	$averageTemperatureNight = $averageTemperatureNight/count($weatherArray['list']);

	  	$result = $weatherArray['city']['name'] 
	  			. " <img src=" . plugin_dir_url( __FILE__ ) . "img/" . $weatherArray['list'][0]['weather'][0]['icon'] . ".png> " 
	  			. $weatherArray['list'][0]['weather'][0]['description'] . " <br> " 
	  			. $weatherArray['list'][0]['temp']['day'] . " &deg;C, <br> " 
	  			. $weatherArray['list'][0]['temp']['night'] . " &deg;C <br>" 
	  			. $averageTemperatureDay . " &deg;C <br> " 
	  			. $averageTemperatureNight . " &deg;C";
		
		return $result;
	}

	public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function setPeriod($period) {
        $this->period = $period . '?';
        return $this;
    }

    public function setLocation($location) {
        $this->location = 'q=' . $location;
        return $this;
    }

    public function setUserKey($userKey) {
        $this->userKey = '&appid=' . $userKey;
        return $this;
    }

    public function setMode($mode) {
        $this->mode = '&mode=' . $mode;
        return $this;
    }

    public function setUnits($units) {
        $this->units = '&units=' . $units;
        return $this;
    }

    public function setDayCount($dayCount) {
        $this->dayCount = '&cnt=' . $dayCount;
        return $this;
    }

}

$kabgWeather = new KabgWeather();