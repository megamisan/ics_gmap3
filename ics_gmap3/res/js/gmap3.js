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
	var icsmap = this;
	var markersData = new Array();
	for (index in data) {
		markersData.push(data[index]);
	}
	//alert('1');
	jQuery('#' + this.gmap3).gmap3({
		action: 'addMarkers',
		markers: markersData,
		marker: {
            options:{
                icon:icsmap.getIcon(data),
            },
            events:{
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

ics.Map.prototype.createWindowsInfo = function(row) {
	var content = '';
	for(var name in row) {
		if(name != '' && name != 'undefined' && row[name] != 'undefined' && row[name] != '') {
			content += '<p>' + row[name] + '</p>'; // à modifier DOM
		}
	}
	return content;
};

ics.Map.prototype.getIcon = function(row) {
	//alert(row);
	//new google.maps.MarkerImage("http://jquery-ui.googlecode.com/svn-history/r3145/branches/labs/assets/theme/images/ui-icons_222222_256x240.png", new google.maps.Size(16, 16), new google.maps.Point((14.5*1), (14.5*10)))
	return '';
}


//ics.Map.prototype.addDynamicData = function(url) { ... }; // Ajoute une url de données dynamique au stockage local
//ics.Map.prototype.addBehaviourInit = function(func) { ... }; // Ajoute une fonction à appeler au stockage local
//ics.Map.prototype.createMarkersStatic_ = function(data) { ... }; // Exécution l'action d'ajout des marqueurs, à surcharger si on veux effectuer des actions supplémentaires sur les données des marqueurs.
//ics.Map.prototype.createMarkersDynamic_ = function(url) { ... }; // Exécute la requête de données pour l'url indiquée.
//ics.Map.prototype.addBehaviours_ = function() { ... }; // Exécution les méthodes d'initialisation des comportements.
