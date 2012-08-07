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
ics.DataList.prototype.init = function(map,markerperpage) {
	var conteneur = document.getElementById(map.gmap3);
	if (!conteneur)
		return false;
	this.map = map;
	this.listId = ics.DataList.nextId++;
	this.map.dataList = this;
	this.mpPage = markerperpage;
	// Create datas table
	this.createTable(conteneur, markerperpage, this.map.data);
	
	return true;
};

ics.DataList.prototype.addToContainer = function(content, container) {
	container.parentNode.appendChild(content);
};

ics.DataList.prototype.createTable = function(conteneur, markerperpage, elements) {
	var content = [];
	var list = [];
	var contentB = [];
	var contentB2 = [];
	var cptB = 0;
	var dataList = this;
	this.nbElems = elements.length;
	list.push(this.makeHeadRow_());
	content.push(this.makeHead_(list));
	list = [];
	jQuery.each(elements, function () {
		if(markerperpage && markerperpage <= list.length) {
			content.push(dataList.makeBody_(list, cptB));
			list = [];
			cptB++;
		}
		list.push(dataList.makeDataRow_(this));
	});
	content.push(this.makeBody_(list, cptB++));
	
	content = ics.createElement(this.makeAllWrap_(content));
	
	// Create browsers
	contentB = this.makeBrowser_(this.nbElems);
	contentB2 = ics.createElement(contentB);
	contentB = ics.createElement(contentB);
	// Remove old table
	jQuery('table.datalist' + this.listId).remove();
	// Remove old browser
	jQuery('div.browser' + this.listId).remove();
	// elements to container
	this.addToContainer(contentB, conteneur);
	this.addToContainer(content, conteneur);
	this.addToContainer(contentB2, conteneur);
}

ics.DataList.prototype.makeBrowserText = function() {
	return {
		'tag': 'p',
		'children': [
			{
				'tag': 'span', 
				'properties': { 'className': 'resBrowser'},
				'children': [{'tag': '', 'value': 'Résultat '}]
			},
			{
				'tag': 'span', 
				'properties': { 'className': 'beginBrowser'},
				'children': [{'tag': '', 'value': '1'}]
			},
			{
				'tag': 'span', 
				'properties': { 'className': 'toBrowser'},
				'children': [{'tag': '', 'value': ' à '}]
			},
			{
				'tag': 'span', 
				'properties': { 'className': 'endBrowser'},
				'children': [{'tag': '', 'value': this.mpPage > this.nbElems ? this.nbElems : this.mpPage}]
			},
			{
				'tag': 'span', 
				'properties': { 'className': 'ofBrowser'},
				'children': [{'tag': '', 'value': ' sur '}]
			},
			{
				'tag': 'span', 
				'properties': { 'className': 'totalBrowser'},
				'children': [{'tag': '', 'value': this.nbElems}]
			}
		]
	};
};
ics.DataList.prototype.goToPage = function(i) {
	var cpt = 0;
	var showTbody = i;
	jQuery('table.datalist' + this.listId + ' tbody').each(function(index) {
		cpt == showTbody ? this.show() : this.hide();
		cpt++;
	});
	return false;
};
ics.DataList.prototype.goToPageClickEvent_ = function(i) {
	var dataList = this;
	return function() {
		dataList.goToPage(i);
		jQuery(".beginBrowser").html((dataList.mpPage*i)+1);
		endNumber = dataList.mpPage*(i+1);
		jQuery(".endBrowser").html(endNumber > dataList.nbElems ? dataList.nbElems : endNumber);
		jQuery(".numbersBrowser a").removeClass('current');
		jQuery(".numbersBrowser a.page"+i).addClass('current');
		return false;
	};
};
ics.DataList.prototype.makeBrowserLink_ = function(i) {
	return {
		'tag': 'a',
		'properties': { 
			'href': '#',
			'className': 'page' + i + (i == 0 ? ' current':''),
			'onclick': this.goToPageClickEvent_(i)
		},
		'children': [
			{
				'tag': 'span', 
				'properties': { 'className': 'textBrowser'},
				'children': [{'tag': '', 'value': 'Page '}]
			},
			{
				'tag': 'span', 
				'properties': { 'className': 'numberBrowser'},
				'children': [{'tag': '', 'value': i+1}]
			}
		]
	};
}
ics.DataList.prototype.makeBrowser_ = function(nbElems) {
	var pages = [];
	var browserText = [];
	browserText.push(this.makeBrowserText());
	var browser = Math.floor(nbElems / this.mpPage);
	for(var i = 0; i <= browser; i++) {
		pages.push(this.makeBrowserLink_(i));
	}
	return {
		'tag': 'div',
		'properties': {'className': 'browser browser' + this.listId},
		'children': [
			{
				'tag': 'div',
				'properties': {'className': 'sentenceBrowser'},
				'children': browserText
			},
			{
				'tag': 'div',
				'properties': {'className': 'numbersBrowser'},
				'children': pages
			}
		]
	};
}
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
ics.DataList.prototype.makeBody_ = function(bodyRows, id) {
	return {
		'tag': 'tbody',
		'properties': {
			'className': 'block block' + id,
			// displaying only the first (id = 0)
			'style' : { 'display' : id ? 'none':''}
		},
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