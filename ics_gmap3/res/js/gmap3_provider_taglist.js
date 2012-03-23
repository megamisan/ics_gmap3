if (typeof ics != 'object')
	ics = {};
// surcharge createMarkersStatic_
(function() {
	var oldfuncCreateMarkersStatic_ = ics.Map.prototype.createMarkersStatic_;
	ics.Map.prototype.createMarkersStatic_ = function(data) {
		var tags = [];
		var icons = [];
		jQuery.each(data, function() {
			if (this.tag && jQuery.inArray(this.tag, tags) < 0) {
				tags.push(this.tag);
				icons[this.tag] = this.icon;
			}
		});
		
		this.tl = {};
		this.tl.markersTags = tags;
		this.tl.iconsTags = icons;
		oldfuncCreateMarkersStatic_.apply(this, arguments);
	};
})();

ics.TagList = function() {};
ics.TagList.nextId = 0;
// generate tags list 
ics.TagList.prototype.init = function(map, exclusivesTags, hiddenTags, defaultTags, viewDefaultTags) {
	var container = document.getElementById(map.gmap3);
	if (!container)
		return false;
	this.listId = ics.TagList.nextId++;
	
	var content = '';
	var list = [];
	var tags = [];
	var finalTags = [];
	if (map.tl.markersTags)
		tags = map.tl.markersTags;
	
	// save tags list
	this.exclusivesTags = exclusivesTags; 	// Array Exclusives tags
	this.hiddenTags = hiddenTags;			// Array Hidden tags
	this.defaultTags = defaultTags;			// Array Default tags
	this.viewDefaultTags = viewDefaultTags;	// Boolean View default tags if selected tags are empty
	
	// add exclusivesTags to tags list
	for (var i = 0; i < exclusivesTags.length; i++)
	{
		tag = exclusivesTags[i];
		if (tag && jQuery.inArray(tag, tags) < 0)
			tags.push(tag);
	}
	
	// sort tags list 
	tags.sort();
	
	// create item list for each tag
	for (var i = 0; i < tags.length; i++)
	{
		tag = tags[i];
		if (tag && jQuery.inArray(tag, this.hiddenTags) < 0) {
			finalTags.push(tag);
			list.push(this.makeTagNode_(tag, map.tl.iconsTags[tag], (jQuery.inArray(tag, defaultTags) >= 0) ? true : false, i));
		}
	}
	
	map.listTags = finalTags;		// Array Visible tags
	
	// add tags list after map
	content = ics.createElement({
		'tag': 'ul', 
		'properties': { 'className': 'tagList tagListNum' + this.listId },
		'children': list 
	});
	this.addToContainer(container, content);
	
	// remove all markers except default tags (include hidden tags)
	if (this.defaultTags.length) 
		this.viewDefaultsTags(map, true);
	else
		this.viewDefaultsTags(map, false);
	
	// add click event 
	var tagList = this;
	jQuery('ul.tagListNum' + this.listId + ' li input').click(function() {
		tagList.click_(this, map);
	});
	return true;
};

ics.TagList.prototype.viewDefaultsTags = function(map, forceDefautTags) {
	var markers = map.getMarkers();
	map.displayMarkers(markers, false);	
			
	if (forceDefautTags || this.viewDefaultTags) {
		var markers = map.getMarkers(this.defaultTags);
		map.displayMarkers(markers, true);
		map.centerMap();
	} else {
		map.centerMapDefault();
	}
	
	var defaultTags = this.defaultTags;
	// on coche tous les tags par défaut
	jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
		if (jQuery.inArray(jQuery(this).attr('value'), defaultTags) >= 0)
			jQuery(this).attr('checked', true);
	});
};

ics.TagList.prototype.hideExclusivesTags = function(map) {
	var markers = map.getMarkers(this.exclusivesTags);
	map.displayMarkers(markers, false);	
	// on décoche tous les tags exclusifs
	var exclusivesTags = this.exclusivesTags;
	jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
		if (jQuery.inArray(jQuery(this).attr('value'), exclusivesTags) >= 0)
			jQuery(this).attr('checked', false);
	});
};
	
ics.TagList.prototype.addToContainer = function(container, content) {
	container.parentNode.appendChild(content);
};

ics.TagList.prototype.makeTagNode_ = function(tag, icon, checked, index) {
	var node = {
		'tag': 'li', 
		'children': [
			{
				'tag': 'img',
				'attributes': { 
					'src': icon
				}
			},
			{
				'tag': 'input',
				'properties': { 
					'type': 'checkbox', 
					'id': 'tx_icsgmap3_taglist_checkbox' + index, 
					'value':  tag,
					'checked': checked
				}
			},
			{ 	
				'tag': 'label', 
				'attributes': { 
					'for': 'tx_icsgmap3_taglist_checkbox' + index
				},
				'children': [{ 'tag': '', 'value': tag }]					 
			}						
		]
	};
	if (icon == null) {
		node.children.shift();
	}
	return node;
};

ics.TagList.prototype.click_ = function(element, map) {
	var resize = true;
	/* 
		S'il s'agit d'un tag exclusif : 
			- Il doit être affiché seul
			- On cache tous les autres markers
	*/	
	if (element.checked && jQuery.inArray(element.value, this.exclusivesTags) >= 0) {
		var allMarkers = map.getMarkers(map.listTags);
		map.displayMarkers(allMarkers, false);
		// on décoche toutes les cases à cocher
		var exclusiveTag = element.value;
		jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
			if (jQuery(this).attr('value') != exclusiveTag) 
				jQuery(this).attr('checked', false);
		});
	}
	
	// ADD OR REMOVE MARKERS
	// get markers checked tag
	var markers = map.getMarkers([element.value]);
	map.displayMarkers(markers, element.checked ? true : false);
	
	/*
		Si on décoche une case:
			- On vérifie qu'il reste encore des cases cochées
			- Si non :
				- si l'option: this.viewDefaultTags est à true: on affiche les tags par defaut
				- si l'option: this.viewDefaultTags est à false: on centre la carte sur le point défini en BE
	*/
	if (!element.checked && !jQuery('ul.tagListNum' + this.listId + ' li input:checked').size()) {
		// remove all markers except default tags (include hidden tags)
		this.viewDefaultsTags(map, false);
		if (!this.viewDefaultTags) {
			resize = false;
		}
	}
	
	/* 
		Au clic d'un tag autre qu'un tag exclusif: 
			- On efface les tags exclusifs
	*/
	if (jQuery.inArray(element.value, this.exclusivesTags) < 0) {
		this.hideExclusivesTags(map);
	}
	
	// CENTER MAP
	if (resize) 
		map.centerMap();
};