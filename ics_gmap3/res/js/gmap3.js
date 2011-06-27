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
	this.createMarkersStatic_(this.data);
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
	var markersData = new Array();
	for (index in data) {
		markersData.push(data[index]);
	}
	
	jQuery('#' + this.gmap3).gmap3({
		action: 'addMarkers',
		markers: markersData,
		marker: {
			events:{
				mouseover: function(marker, event, data){
					jQuery(this).gmap3(
					{
						action:'clear', list:'overlay'
					},
					{
						action:'addOverlay',
						latLng: marker.getPosition(),
						content:this.createWindowsInfo(data),
						offset: {
							x:-46,
							y:-73
						}
					});
				},
				mouseout: function(){
					jQuery(this).gmap3({action:'clear', list:'overlay'});
				},
				click: function(marker, event, data){
					jQuery(this).gmap3({
						action:'panTo', 
						args:[marker.position]
					});
				}
			}
		}
	});
}; // Exécution l'action d'ajout des marqueurs, à surcharger si on veux effectuer des actions supplémentaires sur les données des marqueurs.

ics.Map.prototype.createWindowsInfo = function(row) {
	content = '<div class="infobulle'+(data.access4disabled ? ' access4disabled' : '')+'">' +
			'	<div class="bg"></div>' +
			'	<div class="text"> ' + data.name  + ' ' + data.address  + '</div>' +
			'</div>' +
			'<div class="arrow"></div>';
	alert(content);
	return content;
};

//ics.Map.prototype.addDynamicData = function(url) { ... }; // Ajoute une url de données dynamique au stockage local
//ics.Map.prototype.addBehaviourInit = function(func) { ... }; // Ajoute une fonction à appeler au stockage local
//ics.Map.prototype.createMarkersStatic_ = function(data) { ... }; // Exécution l'action d'ajout des marqueurs, à surcharger si on veux effectuer des actions supplémentaires sur les données des marqueurs.
//ics.Map.prototype.createMarkersDynamic_ = function(url) { ... }; // Exécute la requête de données pour l'url indiquée.
//ics.Map.prototype.addBehaviours_ = function() { ... }; // Exécution les méthodes d'initialisation des comportements.
