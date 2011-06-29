ics = {}; 
ics.Map = function() {};
ics.Map.prototype.setConf = function(gmap3,mapLng,mapLat,mapZoom,mapTypeId,mapTypeControl,navigationControl,scrollwheel,streetViewControl) {
	this.gmap3 = gmap3;
	this.mapLng = mapLng;
	this.mapLat = mapLat;
	this.mapZoom = mapZoom;
	this.mapTypeId = mapTypeId;
	this.mapTypeControl = mapTypeControl;
	this.navigationControl = navigationControl;
	this.scrollwheel = scrollwheel;
	this.streetViewControl = streetViewControl;
}; // ou le faire avec des propriétés.

ics.Map.prototype.createMap = function() {
	this.initGMap_();
	this.initMarkerEvents();
	this.createMarkersStatic_(this.data);
	this.addBehaviours_(this.func);
	//var eventFuncList = this.eventFuncList();
	//alert(eventFuncList);
};// Créer la carte, appel les méthodes ci-dessous, pas forcément dans l'ordre de déclaration

ics.Map.prototype.initGMap_ = function() {
	jQuery('#' + this.gmap3).gmap3({
		action: 'init',
		options:{
			center: [this.mapLng,this.mapLat],
			zoom: this.mapZoom,
			mapTypeId: this.mapTypeId,
			mapTypeControl: this.mapTypeControl,
			zoomControl: this.navigationControl,
			scrollwheel: this.scrollwheel,
			streetViewControl: this.streetViewControl
		}
	});
}; // Initialise jQuery.gmap3
	
ics.Map.prototype.addStaticData = function(data) {
	this.data = data;
}; // Ajoute une liste de marqueur statique au stockage local

ics.Map.prototype.createMarkersStatic_ = function(data) {
	var icsmap = this;
	var markersData = new Array();
	for (index in data) {
		markersData.push(data[index]);
	}
	
//	alert(this.markerEvents);
	jQuery('#' + this.gmap3).gmap3({
		action: 'addMarkers',
		markers: markersData,
		marker: {
			events:{
				click:function(marker, event, data){
					var map = jQuery(this).gmap3('get'),
						infowindow = jQuery(this).gmap3({action:'get', name:'infowindow'});
					if (infowindow){
						infowindow.open(map, marker);
						infowindow.setContent(icsmap.createWindowsInfo(data));
					} else {
						jQuery(this).gmap3({action:'addinfowindow', anchor:marker, options:{content: icsmap.createWindowsInfo(data)}});
					}
				},
				closeclick: function(){
					var infowindow = $(this).gmap3({action:'get', name:'infowindow'});
					if (infowindow){
						infowindow.close();
					}
				},
				mouseover: function(marker, event, data){
					var map = jQuery(this).gmap3('get'),
						infowindow = jQuery(this).gmap3({action:'get', name:'infowindow'});
					if (infowindow){
						infowindow.open(map, marker);
						infowindow.setContent(icsmap.createWindowsInfo(data));
					} else {
						jQuery(this).gmap3({action:'addinfowindow', anchor:marker, options:{content: icsmap.createWindowsInfo(data)}});
					}
				},
				mouseout: function(){
					var infowindow = $(this).gmap3({action:'get', name:'infowindow'});
					if (infowindow){
						infowindow.close();
					}
				}
			}
		}
	});
}; // Exécution l'action d'ajout des marqueurs, à surcharger si on veux effectuer des actions supplémentaires sur les données des marqueurs.

ics.Map.prototype.initMarkerEvents = function() {
	markerEvents = new Array();
	// markerEvents.push(this.markerEventClick);
	// markerEvents.push(this.markerEventMouseover(marker, event, data));
	// markerEvents.push(this.markerEventCloseclick(marker, event, data));
	// markerEvents.push(this.markerEventMouseout(marker, event, data));
	this.markerEvents = markerEvents;
};

ics.Map.prototype.markerEventClick = function(marker, event, data) {
	var map = jQuery('#' + this.gmap3).gmap3('get'),
		infowindow = jQuery('#' + this.gmap3).gmap3({action:'get', name:'infowindow'});
	if (infowindow){
		infowindow.open(map, marker);
		infowindow.setContent(this.createWindowsInfo(data));
	} else {
		jQuery('#' + this.gmap3).gmap3({action:'addinfowindow', anchor:marker, options:{content: this.createWindowsInfo(data)}});
	}
};

ics.Map.prototype.markerEventMouseover = function(marker, event, data) {
	var map = jQuery('#' + this.gmap3).gmap3('get'),
		infowindow = jQuery('#' + this.gmap3).gmap3({action:'get', name:'infowindow'});
	if (infowindow){
		infowindow.open(map, marker);
		infowindow.setContent(this.createWindowsInfo(data));
	} else {
		jQuery('#' + this.gmap3).gmap3({action:'addinfowindow', anchor:marker, options:{content: this.createWindowsInfo(data)}});
	}
};

ics.Map.prototype.markerEventCloseclick = function(marker, event, data) {
	var infowindow = $('#' + this.gmap3).gmap3({action:'get', name:'infowindow'});
	if (infowindow){
		infowindow.close();
	}
};

ics.Map.prototype.markerEventMouseout = function(marker, event, data) {
	var infowindow = $('#' + this.gmap3).gmap3({action:'get', name:'infowindow'});
	if (infowindow){
		infowindow.close();
	}
};

ics.Map.prototype.createWindowsInfo = function(row) {
	var content = '';
	
	/*content = ics.createElement({
		'tag': 'p', 
		'children': 'test'
	});*/
	
	for(var name in row) {
		if(name != '' && name != 'undefined' && row[name] != 'undefined' && row[name] != '') {
			content += '<p>' + row[name] + '</p>'; // à modifier DOM
			//content += row[name];
		}
	}
	return content;
};


ics.Map.prototype.addBehaviourInit = function (func) {
	this.func = func;
}// Ajoute une fonction à appeler au stockage local


ics.Map.prototype.addBehaviours_ = function () {
	this.func(this);
}// Exécution les méthodes d'initialisation des comportements.

ics.Map.prototype.eventFuncList = function() {
	//this.eventFunc = ['click', 'closeclick', 'mouseover', 'mouseout'];
	this.eventFunc = ['click', 'closeclick'];
}


/*ics.Map.prototype.eventFunc = function(event) {
	return event:ics.Map.prototype.event;
}
						for(var event2 in this.eventFunc) {
							alert(this.eventFunc[event2]);
						}*/

/*function click(marker, event, data) {
	var map = jQuery(this).gmap3('get'),
	infowindow = jQuery(this).gmap3({action:'get', name:'infowindow'});
	alert(infowindow);
	
	if (infowindow){
		infowindow.open(map, marker);
		infowindow.setContent(icsmap.createWindowsInfo(data));
		//alert('a');
	} else {
		jQuery(this).gmap3({action:'addinfowindow', anchor:marker, options:{content: icsmap.createWindowsInfo(data)}});
		//alert(infowindow);
		//alert('b');
	}
}*/
	
ics.Map.prototype.closeclick = function(marker, event, data) {
	alert('closeclick');
}
	
/**
 * Get markers to correspond to tags array. If no specified tag, return all markers
 *
 * @param tags tags array
 * @return markers array
 */
ics.Map.prototype.getMarkers = function(tags) {
	var markers = new Array();
	
	if (tags != null) {
		for (var i = 0; i < tags.length; i++) {
			markersTags = jQuery('#' + this.gmap3).gmap3({
				action:	'get', 
				name:	'marker', 
				tag:	tags[i], 
				all:	true
			});
			jQuery.each(markersTags, function(key, value) {
				markers.push(value);
			});
		}
	} else {
		markersTags = jQuery('#' + this.gmap3).gmap3({
			action:	'get', 
			name:	'marker',  
			all:	true
		});
		jQuery.each(markersTags, function(key, value) {
			markers.push(value);
		});
	}
	return markers;
}

/**
 * Display Marker
  *
 * @param 	marker 	Marker to render visible or not
 * @param 	visible 	True/false => visible / hide
 */
ics.Map.prototype.displayMarker = function(marker, visible) {
	var map = jQuery('#' + this.gmap3).gmap3('get');
	if (visible) 
		marker.setMap(map);
	else
		marker.setMap(null);
}

/**
 * Display Markers
  *
 * @param 	markers 	Markers array
 * @param 	visible 	True/false => visible / hide
 */
ics.Map.prototype.displayMarkers = function(markers, visible) {
	for (var i = 0; i < markers.length; i++)
	{
		this.displayMarker(markers[i], visible);
	}
}

/**
 * Center map depending on visibles markers
 */
ics.Map.prototype.centerMap = function(tags) {
	var map = jQuery('#' + this.gmap3).gmap3('get');
	var allMarkers = this.getMarkers();
	bounds = new google.maps.LatLngBounds();
	jQuery.each(allMarkers, function(key, value) {
		if(value.getMap()) 
			bounds.extend(value.getPosition());
	});
	map.fitBounds(bounds);
}
/**
 * Center map depending on default latitude/longitude
 */
ics.Map.prototype.centerMapDefault = function() {
	var map = jQuery('#' + this.gmap3).gmap3('get');
	
	var center = new google.maps.LatLng(this.mapLng, this.mapLat);
	var bounds = new google.maps.LatLngBounds(center);
	map.fitBounds(bounds);
	map.setZoom(this.mapZoom);
}
	
//ics.Map.prototype.addDynamicData = function(url) { ... }; // Ajoute une url de données dynamique au stockage local
//ics.Map.prototype.createMarkersDynamic_ = function(url) { ... }; // Exécute la requête de données pour l'url indiquée.
