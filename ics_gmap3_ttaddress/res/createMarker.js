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
	};
	
	// surcharge createWindowsInfo
	var oldfuncCreateWindowsInfo = ics.Map.prototype.createWindowsInfo;
	ics.Map.prototype.createWindowsInfo = function(row) {
		var content = new Array();
	
		for(var name in row) {
			if(name != '' && name != 'undefined' && row[name] != 'undefined' && row[name] != '') {
				var children = new Array();
				switch (name) {
					case 'tx_damttaddress_dam_image':
						children.push({
							'tag': 'img',
							'properties': { 
								'src': row[name]
							}
						});
					break;
					case 'description':
						var parts = row[name].split('\r\n');
						var partsHtml = [];
						for (var i=0; i < parts.length; i++) {
							children.push(
								{
									'tag': '',
									'value': parts[i]
								},
								{
									'tag': 'br'
								}									
							);
						}
					break;
				}
				
				if (name != 'description' && name != 'tx_damttaddress_dam_image') {
					children.push({
						'tag': '',
						'value': row[name]
					});
				}
				
				content.push({
					'tag': 'p', 
					'children': children
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
	
})();