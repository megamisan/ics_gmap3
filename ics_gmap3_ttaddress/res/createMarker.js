if (typeof ics != 'object')
	ics = {};

(function() {
	var oldfuncCreateMarkersStatic_ = ics.Map.prototype.createMarkersStatic_;
	ics.Map.prototype.createMarkersStatic_ = function(data) {
		for (var index in data) {
			if (data[index].options == null)
				data[index].options = {};
			if (data[index].icon) {
				data[index].options.icon = new google.maps.MarkerImage(data[index].icon);
				data[index].options.icon_nothover = data[index].icon;
			}
			if (data[index].icon_hover)
				data[index].options.icon_hover = data[index].icon_hover;
		}
		oldfuncCreateMarkersStatic_.apply(this, arguments);
	};

	var oldfuncCreateWindowsInfo = ics.Map.prototype.createWindowsInfo;
	ics.Map.prototype.createWindowsInfo = function(row) {
		var content = new Array();
	
		for(var name in row) {
			if(name != '' && name != 'undefined' && row[name] != 'undefined' && row[name] != '' && name != 'recId') {
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
						'value':row[name]
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
	
	
	var oldfuncmarkerEventClick = ics.Map.prototype.markerEventClick;
	ics.Map.prototype.markerEventClick	= function(marker, event, data) {
		(new ics.DataAddress()).setIconHover(marker, event, data)
		oldfuncmarkerEventClick.apply(this, arguments);
	};
	var oldfuncmarkerEventCloseclick = ics.Map.prototype.markerEventCloseclick;
	ics.Map.prototype.markerEventCloseclick	= function(marker, event, data) {
		(new ics.DataAddress()).setIcon(marker, event, data)
		oldfuncmarkerEventCloseclick.apply(this, arguments);
	};
	var oldfuncmarkerEventMouseover = ics.Map.prototype.markerEventMouseover;
	ics.Map.prototype.markerEventMouseover	= function(marker, event, data) {
		(new ics.DataAddress()).setIconHover(marker, event, data)
		oldfuncmarkerEventMouseover.apply(this, arguments);
	};
	var oldfuncmarkerEventMouseout = ics.Map.prototype.markerEventMouseout;
	ics.Map.prototype.markerEventMouseout	= function(marker, event, data) {
		(new ics.DataAddress()).setIcon(marker, event, data)
		oldfuncmarkerEventMouseout.apply(this, arguments);
	};

	var oldfunctionMapCreateKmlLayer = ics.Map.prototype.createKmlLayer;
	ics.Map.prototype.createKmlLayer = function(kml, visible, data) {
	jQuery('#' + this.gmap3).gmap3({
		action: 'addKmlLayer',
		url: kml,
		options:{
			suppressInfoWindows: false,
			preserveViewport: true
		},
		tag: 'kml' + (data != undefined && data.tag ? data.tag : (++arguments.callee.counter))
	});
	if(data == undefined){
		this.centerMapDefault();
	}
	
	if(!visible) {
		kml = jQuery('#' + this.gmap3).gmap3({
			action: 'get',
			name: 'kmllayer',
			tag: 'kml' + (data != undefined && data.tag ? data.tag : (arguments.callee.counter))
		});
		kml.setMap(null);
	}
	};
	ics.Map.prototype.createKmlLayer.counter = 0;
	
})();

ics.DataAddress = function() {};
ics.DataAddress.prototype.setIcon = function(marker, event, data) {
	var icon = marker.icon_nothover;
	if (icon != undefined)
		marker.setIcon(icon);
};
ics.DataAddress.prototype.setIconHover = function(marker, event, data) {
	var icon_hover = marker.icon_hover;
	if (icon_hover != undefined)
		marker.setIcon(icon_hover);
};
