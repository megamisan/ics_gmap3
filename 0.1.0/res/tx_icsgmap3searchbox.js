if (typeof ics != 'object')
	ics = {};

(function() {
	var oldCreateMarkerCallback_ = ics.Map.prototype.createMarkerCallback_;
	ics.Map.prototype.createMarkerCallback_ = function(marker, data) {
		oldCreateMarkerCallback_.apply(this, arguments);
		marker.data = data.data;
	}
})();

ics.SearchBox = function() {};
ics.SearchBox.nextId = 0;

ics.SearchBox.prototype.init = function(map, fields) {
	var container = document.getElementById(map.gmap3);
	if (!container || fields.length == 0)
		return false;
	this.map = map;
	this.textBuilder = this.buildTextBuilder_(fields);
	this.boxId = ics.SearchBox.nextId++;
	var content = this.makeSearchBox();
	content = ics.createElement(this.wrapSearchBox(content));
	this.addToContainer(content, container);
	return true;
};

ics.SearchBox.prototype.addToContainer = function(content, container) {
	container.parentNode.appendChild(content);
};

ics.SearchBox.prototype.makeSearchBox = function() {
	return {
			'tag': 'form',
			'properties': {
				'className': 'searchForm searchForm' + this.boxId,
				'onsubmit': this.makeFormOnSubmit_()
			},
			'children': [
				{
					'tag': 'fieldset',
					'children': [
						{
							'tag': 'legend',
							'children': [
								{ 'tag': '', 'value': arguments.callee.fieldsetLegend }
							]
						},
						{
							'tag': 'p',
							'children': [
								{
									'tag': 'label',
									'properties': {
										'htmlFor': 'searchBox' + this.boxId + '_text'
									},
									'children': [
										{ 'tag': '', 'value': arguments.callee.label }
									]
								},
								{
									'tag': 'input',
									'properties': {
										'type': 'input',
										'id': 'searchBox' + this.boxId + '_text',
										'name': 'search_text',
										'size': arguments.callee.inputSize
									}
								}
							]
						}
					]
				}
			]
		};
};
ics.SearchBox.prototype.makeSearchBox.label = 'Search';
ics.SearchBox.prototype.makeSearchBox.fieldsetLegend = 'Search';
ics.SearchBox.prototype.makeSearchBox.inputSize = 30;

ics.SearchBox.prototype.wrapSearchBox = function(content) {
	return content;
};

ics.SearchBox.prototype.makeFormOnSubmit_ = function() {
	var searchBox = this;
	var callee = arguments.callee;
	return function() {
		var text = jQuery.trim(jQuery('input[name=search_text]', this).val());
		if (text.length == 0) {
			alert(callee.alertEmptyText);
		}
		else {
			searchBox.lookUp(text);
		}
		return false;
	};
};
ics.SearchBox.prototype.makeFormOnSubmit_.alertEmptyText = 'Veuillez saisir un ou plusieurs mot(s) à chercher.';

ics.SearchBox.prototype.buildTextBuilder_ = function(fields) {
	var leaf = arguments.callee.leaf;
	var makeNode = arguments.callee.makeNode;
	var calls = [];
	jQuery.each(fields, function() {
		var segments = this.split('.');
		var call = leaf;
		jQuery.each(segments.reverse(), function() {
			call = makeNode(this, call);
		});
		calls.push(call);
	});
	return arguments.callee.makeConcat(calls);
};

ics.SearchBox.prototype.buildTextBuilder_.leaf = function(data) {
	return data;
};

ics.SearchBox.prototype.buildTextBuilder_.makeNode = function(name, nextNode) {
	return function(data) {
		return nextNode(data[name] || '');
	};
};

ics.SearchBox.prototype.buildTextBuilder_.makeConcat = function(calls) {
	return function(data) {
		var values = [''];
		jQuery.each(calls, function() {
			var value = this(data);
			if ((typeof value == 'string') && (jQuery.trim(value).length > 0)) values.push(value);
		});
		values.push('');
		return values.join(' ');
	};
};

ics.SearchBox.prototype.lookUp = function(searchText) {
	var allMarkers = this.map.getMarkers();
	this.map.displayMarkers(allMarkers, false);
	jQuery('div.tx-icsgmap3-pi1 ul.tagList input').attr('checked', false);
	
	var markers = [];
	var textSegments = searchText.split(' ');
	var searchElements = [];
	var cleanText = this.cleanText;
	jQuery.each(textSegments, function() {
		var v = jQuery.trim(this);
		if (v.length > 0) {
			searchElements.push(RegExp.escape(cleanText(v)));
		}
	});
	var testRegEx = new RegExp('\\W((' + searchElements.join(')|(') + '))\\W', 'gi');
	var textBuilder = this.textBuilder;
	jQuery.each(allMarkers, function() {
		if (testRegEx.test(cleanText(textBuilder(this.data)))) {
			markers.push(this);
		}
	});
	this.map.displayMarkers(markers, true);
	switch (markers.length) {
		case 0:
			this.map.centerMapDefault();
			break;
		case 1:
			var map = jQuery('#' + this.map.gmap3).gmap3('get');
			map.setCenter(markers[0].getPosition());
			map.setZoom(17);
			break;
		default:
			this.map.centerMap();
	}
};

ics.SearchBox.prototype.cleanText = function(text) {
	return text;
};

if (!RegExp.escape) {
	// Copied from http://simonwillison.net/2006/Jan/20/escape/
	RegExp.escape = function(text) {
		return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
	}
}