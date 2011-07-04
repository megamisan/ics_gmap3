(function() {
	var oldfuncInitMarkerEvents = ics.Map.prototype.initMarkerEvents;
	ics.Map.prototype.initMarkerEvents = function() {
		markerEvents = new Array();
		markerEvents['click'] = this.markerEventClick;
		markerEvents['closeclick'] = this.markerEventCloseclick;
		
		this.markerEvents = markerEvents;
		//oldfuncInitMarkerEvents.apply(this, arguments);
	};
	
	var oldfuncCreateMarkersStatic_ = ics.Map.prototype.createMarkersStatic_;
	ics.Map.prototype.createMarkersStatic_ = function(data) {
		var icsmap = this;
		var tagData = new Array();
		var markerData = new Array();
		for (var index in data) {
			tagData[index] = data[index].tag;
			markerData[index] = data[index];
		}
		
		for (var index in tagData) {
			jQuery('#' + this.gmap3).gmap3({
				action: 'addMarkers',
				markers: [markerData[index]],
				marker: {
		             options:{
						icon: (markerData[index].icon ? new google.maps.MarkerImage(markerData[index].icon) : '')
					},
		            events: this.markerEvents
				}
			});
		}
		//oldfuncCreateMarkersStatic_.apply(this, arguments);
	}
	
})();