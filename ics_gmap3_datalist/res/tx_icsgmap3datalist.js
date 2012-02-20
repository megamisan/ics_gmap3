if (typeof ics != 'object')
	ics = {};

(function() {
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
						icon: (markerData[index].icon ? new google.maps.MarkerImage(markerData[index].icon) : ''),
						title: markerData[index].data.name
					},
		            events: this.markerEvents
				}
			});
		}
		oldfuncCreateMarkersStatic_.apply(this, arguments);
	}
		
})();
	
ics.DataList = function() {};
ics.DataList.nextId = 0;

// generate tags list 
ics.DataList.prototype.init = function(map) {
	var conteneur = document.getElementById(map.gmap3);
	if (!conteneur)
		return false;
	this.map = map;
	this.listId = ics.DataList.nextId++;
	var content = '';
	var list = [];
	
	list.push({
		'tag': 'tr',
		'children': [
			{
				'tag': 'th',
				'children': [{ 'tag': '', 'value': 'Nom' }]
			},
			{
				'tag': 'th',
				'children': [{ 'tag': '', 'value': 'Adresse' }]
			},
			{
				'tag': 'th',
				'children': [{ 'tag': '', 'value': 'Code postal' }]
			},
			{
				'tag': 'th',
				'children': [{ 'tag': '', 'value': 'Ville' }]
			},
			{
				'tag': 'th',
				'children': [{ 'tag': '', 'value': 'Situer' }]
			}
		]
	});
	//On affiche les données dans sheetContainer en fonction de ce qui est coché
	for(var index in map.data) {
		data = map.data[index].data;

		list.push({
			'tag': 'tr',
			'children': [
				{
					'tag': 'td',
					'children': [{ 'tag': '', 'value': data.name }]
				},
				{
					'tag': 'td',
					'children': [{ 'tag': '', 'value': data.address }]
				},
				{
					'tag': 'td',
					'children': [{ 'tag': '', 'value': data.zip }]
				},
				{
					'tag': 'td',
					'children': [{ 'tag': '', 'value': data.city }]
				},
				{
					'tag': 'td',
					'children': [
						{ 
							'tag': 'a',
							'properties': { 
								'href': '#',
								'className': 'locate'
							},
							'children': [
								{
									'tag': '', 
									'value': 'Situer'
								},
								{
									'tag': 'span',
									'properties': { 
										'className': 'tag hide'
									},
									'children': [{ 'tag': '', 'value': map.data[index].tag }]
								},
								{
									'tag': 'span',
									'properties': { 
										'className': 'title hide'
									},
									'children': [{ 'tag': '', 'value': data.name }]
								}
							]
						}
					]
				}
			]
		});
	}
	// add tags list after map	
	content = ics.createElement({
		'tag': 'table',
		'properties': {'className': 'datalist datalist' + this.listId},
		'children': list
	});
	conteneur.parentNode.appendChild(content);
	
	var dataList = this;
	jQuery('table.datalist td a.locate').click(function() {
		dataList.locate(jQuery(this).children('span.tag').html(), jQuery(this).children('span.title').html());
		return false;
	});	
	
	return true;
};
ics.DataList.prototype.locate = function(tag, title) {
	var allMarkers = this.map.getMarkers();
	this.map.displayMarkers(allMarkers, false);
	jQuery('div.tx-icsgmap3-pi1 ul.tagList input').attr('checked', false);
	
	var markers = this.map.getMarkers([tag]);
	var marker = [];

	for(var index in markers) {
		if (markers[index].getTitle() == title) {
			marker.push(markers[index]);
			break;
		}
	};
	this.map.displayMarkers(marker, true);
	// open popup
	this.map.centerMap();
	
	var map = jQuery('#' + this.map.gmap3).gmap3('get');
	map.setZoom(17);
	return false;
};