if (typeof ics != 'object')
	ics = {};

(function() {
	var oldfuncCreateMarkersStatic_ = ics.Map.prototype.createMarkersStatic_;
	ics.Map.prototype.createMarkersStatic_ = function(data) {
		jQuery.each(data, function() {
			if (this.options == null)
				this.options = {};
			this.options.title = this.data.name;
		});
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
	var dataList = this;
	var list = [];
	var content = [];
	
	list.push(this.makeHeadRow_());
	content.push(this.makeHead_(list));
	list = [];
	jQuery.each(map.data, function () {
		list.push(dataList.makeDataRow_(this));
	});
	content.push(this.makeBody_(list));
	content = ics.createElement(this.makeAllWrap_(content));
	conteneur.parentNode.appendChild(content);
	return true;
};

ics.DataList.prototype.makeAllWrap_ = function(content) {
	return {
		'tag': 'table',
		'properties': {'className': 'datalist datalist' + this.listId},
		'children': content
	};
};

ics.DataList.prototype.makeHead_ = function(headRows) {
	return {
		'tag': 'thead',
		'children': headRows
	};
};

ics.DataList.prototype.makeHeadRow_ = function() {
	return {
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
	};
};

ics.DataList.prototype.makeBody_ = function(bodyRows) {
	return {
		'tag': 'tbody',
		'children': bodyRows
	};
};

ics.DataList.prototype.createLocateClickEvent_ = function(data) {
	var dataList = this;
	return function() {
		dataList.locate(data.tag, data.data.recId);
		return false;
	};
};

ics.DataList.prototype.makeDataRow_ = function(data) {
	return {
		'tag': 'tr',
		'children': [
			{
				'tag': 'td',
				'children': [{ 'tag': '', 'value': data.data.name }]
			},
			{
				'tag': 'td',
				'children': [{ 'tag': '', 'value': data.data.address }]
			},
			{
				'tag': 'td',
				'children': [{ 'tag': '', 'value': data.data.zip }]
			},
			{
				'tag': 'td',
				'children': [{ 'tag': '', 'value': data.data.city }]
			},
			{
				'tag': 'td',
				'children': [
					{ 
						'tag': 'a',
						'properties': { 
							'href': '#',
							'className': 'locate',
							'onclick': this.createLocateClickEvent_(data)
						},
						'children': [
							{
								'tag': '', 
								'value': 'Situer'
							}
						]
					}
				]
			}
		]
	};
};

ics.DataList.prototype.locate = function(tag, recId) {
	var allMarkers = this.map.getMarkers();
	this.map.displayMarkers(allMarkers, false);
	jQuery('div.tx-icsgmap3-pi1 ul.tagList input').attr('checked', false);
	
	var markers = this.map.getMarkers([tag]);
	var marker = [];

	for (var index in markers) {
		if (markers[index].recId == recId) {
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