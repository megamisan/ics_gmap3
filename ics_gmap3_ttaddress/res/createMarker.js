(function() {
	var oldfuncCreateMarkersStatic_ = ics.Map.prototype.createMarkersStatic_;
	ics.Map.prototype.createMarkersStatic_ = function(data) {
		for (var index in data) {
			if (data[index].options == null)
				data[index].options = {};
			if (data[index].icon)
				data[index].options.icon = new google.maps.MarkerImage(data[index].icon);
		}
		oldfuncCreateMarkersStatic_.apply(this, arguments);
	};
	
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