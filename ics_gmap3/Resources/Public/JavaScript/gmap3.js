if (typeof ics !== 'object') {
    ics = {};
}
ics.Map = function () {
};
ics.Map.prototype.setConf = function (gmap3, mapLng, mapLat, mapZoom, mapTypeId, mapTypeControl, navigationControl, scrollwheel, streetViewControl) {
    this.gmap3 = gmap3;
    this.mapLng = mapLng;
    this.mapLat = mapLat;
    this.mapZoom = mapZoom;
    this.mapTypeId = mapTypeId;
    this.mapTypeControl = mapTypeControl;
    this.navigationControl = navigationControl;
    this.scrollwheel = scrollwheel;
    this.streetViewControl = streetViewControl;
    this.behaviours = [];
    this.data = [];
}; // ou le faire avec des propriétés.

ics.Map.prototype.createMap = function () {
    this.initGMap_();
    this.initMarkerEvents();
    this.createMarkersStatic_(this.data);
    if (this.behaviours.length > 0) {
        this.addBehaviours_();
    }
};// Créer la carte, appel les méthodes ci-dessous, pas forcément dans l'ordre de déclaration

ics.Map.prototype.initGMap_ = function () {
    jQuery('#' + this.gmap3).gmap3({
        action: 'init',
        options: {
            center: [this.mapLat, this.mapLng],
            zoom: this.mapZoom,
            mapTypeId: this.mapTypeId,
            mapTypeControl: this.mapTypeControl,
            zoomControl: this.navigationControl,
            scrollwheel: this.scrollwheel,
            streetViewControl: this.streetViewControl
        }
    });
}; // Initialise jQuery.gmap3

ics.Map.prototype.setOptions = function (options) {
    var map = jQuery('#' + this.gmap3).gmap3('get');
    map.setOptions(options);
};
ics.Map.prototype.addStaticData = function (data) {
    if (data && typeof(data) === 'object') {
        for (var i = 0; i < data.length; i++) {
            this.data.push(data[i]);
        }
    }
}; // Ajoute une liste de marqueur statique au stockage local

ics.Map.prototype.createMarkersStatic_ = function (data) {
    var icsmap = this;
    var gmap3 = jQuery('#' + this.gmap3);
    jQuery.each(data, function () {
        var point = this;
        gmap3.gmap3({
            action: 'addMarkers',
            markers: [point],
            marker: {
                events: icsmap.markerEvents
            },
            callback: function (markers) {
                icsmap.createMarkerCallback_(markers[0], point);
            }
        });
    });
}; // Exécution l'action d'ajout des marqueurs, à surcharger si on veux effectuer des actions supplémentaires sur les données des marqueurs.

ics.Map.prototype.createMarkerCallback_ = function (marker, data) {
    if (data.data.recId === null) {
        data.data.recId = 'rec:' + (++arguments.callee.counter);
    }
    marker.recId = data.data.recId;
}; // Rappel de l'action d'ajout des marqueurs, à surcharger si on veux effectuer des actions supplémentaires sur les marqueurs créés.
ics.Map.prototype.createMarkerCallback_.counter = 0;

ics.Map.prototype.initMarkerEvents = function () {
    markerEvents = {};
    markerEvents['click'] = this.markerEventClick;
    markerEvents['mouseover'] = this.markerEventMouseover;
    markerEvents['closeclick'] = this.markerEventCloseclick;
    markerEvents['mouseout'] = this.markerEventMouseout;

    this.markerEvents = markerEvents;
};

ics.Map.prototype.markerEventClick = function (marker, event, data) {
    var map = jQuery(this).gmap3('get'),
        infowindow = jQuery(this).gmap3({action: 'get', name: 'infowindow'});
    if (infowindow) {
        infowindow.open(map, marker);
        infowindow.setContent(ics.Map.prototype.createWindowsInfo(data));
    } else {
        jQuery(this).gmap3({
            action: 'addinfowindow',
            anchor: marker,
            options: {content: ics.Map.prototype.createWindowsInfo(data)}
        });
    }
};

ics.Map.prototype.markerEventMouseover = function (marker, event, data) {
    var map = jQuery(this).gmap3('get'),
        infowindow = jQuery(this).gmap3({action: 'get', name: 'infowindow'});
    if (infowindow) {
        infowindow.open(map, marker);
        infowindow.setContent(ics.Map.prototype.createWindowsInfo(data));
    } else {
        jQuery(this).gmap3({
            action: 'addinfowindow',
            anchor: marker,
            options: {content: ics.Map.prototype.createWindowsInfo(data)}
        });
    }
};

ics.Map.prototype.markerEventCloseclick = function (marker, event, data) {
    var infowindow = jQuery(this).gmap3({action: 'get', name: 'infowindow'});
    if (infowindow) {
        infowindow.close();
    }
};

ics.Map.prototype.markerEventMouseout = function (marker, event, data) {
    var infowindow = jQuery(this).gmap3({action: 'get', name: 'infowindow'});
    if (infowindow) {
        infowindow.close();
    }
};

ics.Map.prototype.createWindowsInfo = function (row) {
    var content = [];

    for (var name in row) {
        //noinspection JSUnfilteredForInLoop
        if (name !== '' && name !== 'undefined' && row[name] !== 'undefined' && row[name] !== '') {
            //noinspection JSUnfilteredForInLoop
            content.push({
                'tag': 'p',
                'children': [{'tag': '', 'value': row[name]}]
            });
        }
    }

    return ics.createElement({
        'tag': 'div',
        'properties': {
            'className': 'infoWindows'
        },
        'children': content
    });
};

ics.Map.prototype.addBehaviourInit = function (func) {
    if (typeof(func) === 'function') {
        this.behaviours.push(func);
    }
};// Ajoute une fonction à appeler au stockage local

ics.Map.prototype.addBehaviours_ = function () {
    for (var i = 0; i < this.behaviours.length; i++) {
        try {
            this.behaviours[i](this);
        }
        catch (e) {
        }
    }
    this.behaviours = [];
};// Exécution les méthodes d'initialisation des comportements.

/**
 * Get markers to correspond to tags array. If no specified tag, return all markers
 *
 * @param tags tags array
 * @return markers array
 */
ics.Map.prototype.getMarkers = function (tags) {
    var markers = [];

    var markersTags;
    if (typeof tags === 'object') {
        for (var i = 0; i < tags.length; i++) {
            markersTags = jQuery('#' + this.gmap3).gmap3({
                action: 'get',
                name: 'marker',
                tag: tags[i],
                all: true
            });
            jQuery.each(markersTags, function () {
                markers.push(this);
            });
        }
    } else {
        markersTags = jQuery('#' + this.gmap3).gmap3({
            action: 'get',
            name: 'marker',
            all: true
        });
        jQuery.each(markersTags, function () {
            markers.push(this);
        });
    }
    return markers;
};

/**
 * Display Marker
 *
 * @param    marker    Marker to render visible or not
 * @param    visible    True/false => visible / hide
 */
ics.Map.prototype.displayMarker = function (marker, visible) {
    var map = jQuery('#' + this.gmap3).gmap3('get');
    if (visible) {
        marker.setMap(map);
    } else {
        marker.setMap(null);
    }
};

/**
 * Display Markers
 *
 * @param    markers    Markers array
 * @param    visible    True/false => visible / hide
 */
ics.Map.prototype.displayMarkers = function (markers, visible) {
    for (var i = 0; i < markers.length; i++) {
        this.displayMarker(markers[i], visible);
    }
};

/**
 * Center map depending on visibles markers
 */
ics.Map.prototype.centerMap = function () {
    var map = jQuery('#' + this.gmap3).gmap3('get');
    var allMarkers = this.getMarkers();
    bounds = new google.maps.LatLngBounds();
    jQuery.each(allMarkers, function () {
        if (this.getMap()) {
            bounds.extend(this.getPosition());
        }
    });
    map.fitBounds(bounds);
};

/**
 * Center map depending on default latitude/longitude
 */
ics.Map.prototype.centerMapDefault = function () {
    var map = jQuery('#' + this.gmap3).gmap3('get');
    var center = new google.maps.LatLng(this.mapLat, this.mapLng);
    map.setCenter(center);
    map.setZoom(this.mapZoom);
};

//ics.Map.prototype.addDynamicData = function(url) { ... }; // Ajoute une url de données dynamique au stockage local
//ics.Map.prototype.createMarkersDynamic_ = function(url) { ... }; // Exécute la requête de données pour l'url indiquée.

ics.Map.prototype.getMapId = function () {
    return this.gmap3;
};
