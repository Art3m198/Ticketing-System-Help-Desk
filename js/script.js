let key = "4759dff0356b47099069a55dc71463bd"

var cityName = "";
var flag = 0;
var link = "";
var link_trend = "";
var link_time = "";
var flag_link = "";
var time = new Date().getHours();
var lat = "";
var lon = "";
ext_str="";
function convert_unix(time){				
var date = new Date(time*1000);
var day = "0"+date.getDate();
var mon = date.getMonth();
var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
var yr = date.getFullYear();
var hours = date.getHours();
var min = "0"+date.getMinutes();
return day.substr(-2)+"-"+months[mon]+"-"+yr+", "+hours+":"+min.substr(-2);
}
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } 
}

function showPosition(position) {
 window.lat = position.coords.latitude;
 window.lon = position.coords.longitude;
 parseJson();
}

function getLocalTime(lat,lon){
	link_time = "https://api.timezonedb.com/v2.1/get-time-zone?key=HDYY2MHLPCQV&format=json&by=position&lng="+lon+"&lat="+lat;
	var request3 = new XMLHttpRequest();
	request3.open('GET',link_time,true);
	request3.onload = function(){
		var obj = JSON.parse(this.response); 
		if (request3.status >= 200 && request3.status < 400) {
			var ext = obj.abbreviation;
			ext_str = JSON.stringify(ext);
			document.getElementById('last_update').innerHTML += " "+ext;
		}
		else{
			Window.alert("Some thing went wrong with api call");
		}	
	}
	request3.send();
	return window.ext_str;
}


function parseJson(){
cityName = document.getElementById('city').value;
	ext_str="";
	if(cityName == ""){
		link = "https://api.openweathermap.org/data/2.5/weather?lat="+window.lat+"&lon="+window.lon+"139&units=metric&apikey="+key;
	}
	else if(cityName != ""){
		link = "https://api.openweathermap.org/data/2.5/weather?q="+cityName+"&units=metric&apikey="+key;
	}
	link_trend = "https://api.openweathermap.org/data/2.5/forecast?q="+cityName+"&units=metric&APPID="+key;
	var request = new XMLHttpRequest();
	request.open('GET',link,true);

	request.onload = function(){
		var obj = JSON.parse(this.response); 
		if (request.status >= 200 && request.status < 400) {
			if(cityName == ""){
				cityName = obj.name;
				console.log(cityName);
				parseJson2(window.cityName);
			}
			document.getElementById('listweather').style.visibility = 'visible';
			var lastupdate_unix = obj.dt;
			var offset = obj.timezone;
			var lastupdate_human = convert_unix(lastupdate_unix);
			var time_zone = getLocalTime(window.lat,window.lon);
			document.getElementById('last_update').innerHTML = "Last update on: "+lastupdate_human;
			var temp = Math.floor(obj.main.temp);
			document.getElementById('Temperature').innerHTML = obj.main.temp+"°C";
			document.getElementById('Climate').innerHTML = obj.weather[0].description;
			document.getElementById('City').innerHTML = obj.name;
			var country = obj.sys.country;
			flag_link = "https://www.countryflags.io/"+country+"/shiny/64.png";
            document.getElementById('flag').src = flag_link;
            document.getElementById('image').src = getWeather(obj.weather[0].main);
			document.getElementById('htemp').innerHTML = obj.main.temp_min+"°C";
			document.getElementById('ltemp').innerHTML = obj.main.temp_max+"°C";
			document.getElementById('wind').innerHTML = obj.wind.speed+" m/s";
		}
		else{
			document.getElementById('listweather').style.visibility = 'invisible';
		}
	}
	request.send();
	return;
}

function getWeather(weather){
	if(weather == "Haze"){
				if(time<18 && time>6)
					return "images/cloudy-day-1.svg"
				else
					return "images/cloudy-night-1.svg"
			}
			else if(weather == "Clouds"){
				return "images/cloudy.svg"	
			}
			else if(weather == "Snow"){
				return "images/snowy-5.svg"	
			}
			else if(weather == "Rain"){
				return "images/rainy-6.svg"	
			}
			else if(weather == "Mist"){
				return "images/rainy-5.svg"	
			}
			else if(weather == "Clear"){
				if(time<18 && time>6)
					return "images/day.svg"
				else
					return "images/night.svg"
			}
			else if(weather == "Smoke"){
				return "images/snowy-6.svg"	
			}
			else if(weather == "Drizzle"){
				return "images/rainy-7.svg"	
			}
			else if(weather == "Thunderstorm"){
				return "images/thunder.svg"		
			}
			return;
}

function parseJson2(cityName){
	link_trend = "https://api.openweathermap.org/data/2.5/forecast?q="+cityName+"&units=metric&APPID="+key;
	var request1 = new XMLHttpRequest();
	request1.open('GET',link_trend,true);
	request1.send();
}
$(document).keypress(function (e) {
    if (e.which == 13) {
            document.getElementById("getWeather").click();
            document.getElementById("city").value = "";
    }
});