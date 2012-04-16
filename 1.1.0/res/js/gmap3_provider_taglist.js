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
ics.TagList.prototype.init = function(map, exclusivesTags, hiddenTags, defaultTags, viewDefaultTags, tagsSelector) {
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
	this.tagsSelector = tagsSelector;	// Checkbox or Select
	if (!this.tagsSelector)
		this.tagsSelector = 'checkbox';
			
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
	this.createForm(container);
	content = this.makeTagParentNode_(list);
	this.addToForm(content);
	
	// remove all markers except default tags (include hidden tags)
	if (this.defaultTags.length) 
		this.viewDefaultsTags(map, true);
	else
		this.viewDefaultsTags(map, false);
	
	// add click event 
	this.initEvent_(map);
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
	
	if (forceDefautTags || this.viewDefaultTags) {
		var defaultTags = this.defaultTags;
		// on coche tous les tags par défaut
		if (this.tagsSelector == 'select') {
			jQuery('select.tagListNum' + this.listId + ' option').each(function() {
				if (jQuery.inArray(jQuery(this).attr('value'), defaultTags) >= 0)
					jQuery(this).attr('selected', true);
			});
		} else {
			jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
				if (jQuery.inArray(jQuery(this).attr('value'), defaultTags) >= 0)
					jQuery(this).attr('checked', true);
			});
		}
	}
};

ics.TagList.prototype.hideExclusivesTags = function(map) {
	if (this.tagsSelector == 'checkbox') {
		var markers = map.getMarkers(this.exclusivesTags);
		map.displayMarkers(markers, false);	
		// on décoche tous les tags exclusifs
		var exclusivesTags = this.exclusivesTags;
		jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
			if (jQuery.inArray(jQuery(this).attr('value'), exclusivesTags) >= 0)
				jQuery(this).attr('checked', false);
		});
	}
};
	
ics.TagList.prototype.addToContainer = function(container, content) {
	container.parentNode.appendChild(content);
};

ics.TagList.prototype.createForm = function(container) {
	var form = ics.createElement({
		'tag': 'form',
		'properties': { 
			'action':  '', 
			'id':  'tagListForm' + this.listId
		},
		'children': [{
			'tag': 'fieldset'
		}]
	});
	this.addToContainer(container, form);
};

ics.TagList.prototype.addToForm = function(content) {
	container = document.getElementById('tagListForm' + this.listId);
	container = container.getElementsByTagName('fieldset')[0];
	container.appendChild(content);
};

ics.TagList.prototype.makeTagParentNode_ = function(list) {
	if (this.tagsSelector == 'select') {
		var listWithOptionEmpty = [{
			'tag': 'option', 
			'properties': { 
				'value':  '', 
				'id': 'tx_icsgmap3_taglist_checkbox_empty'
			}
		}];
		listWithOptionEmpty = listWithOptionEmpty.concat(list);
		var content = ics.createElement({
			'tag': 'select', 
			'properties': { 
				'className': 'tagList tagListNum' + this.listId,
				'name': 'tagListNum' + this.listId
			},
			'children': listWithOptionEmpty 
		});
	} else {
		var content = ics.createElement({
			'tag': 'ul', 
			'properties': { 'className': 'tagList tagListNum' + this.listId },
			'children': list 
		});
	}
	return content;
};

ics.TagList.prototype.makeTagNode_ = function(tag, icon, checked, index) {
	if (this.tagsSelector == 'select') {
		var node = {
			'tag': 'option', 
			'properties': { 
				'value':  tag, 
				'id': 'tx_icsgmap3_taglist_checkbox' + index
			},
			'children': [{ 'tag': '', 'value': tag }]
		};
	} else {
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
	}
	return node;
};

ics.TagList.prototype.initEvent_ = function(map) {
	if (this.tagsSelector == 'select') {
		var tagList = this;
		jQuery('select.tagListNum' + this.listId).change(function() {
			tagList.click_(this, map);
		});
	} else {
		var tagList = this;
		jQuery('ul.tagListNum' + this.listId + ' li input').click(function() {
			tagList.click_(this, map);
		});
	}
};

ics.TagList.prototype.click_ = function(element, map) {
	var resize = true;
	var checked = '';
	if (this.tagsSelector == 'select') {
		checked = true;
		if (!element.value)
			checked = false;
	} else {
		checked = element.checked;
	}
	
	/* 
		S'il s'agit d'un tag exclusif : 
			- Seulement dans le cas de cases à cocher (liste déroulante = toujours seul)
			- Il doit être affiché seul
			- On cache tous les autres markers
	*/	
	if (this.tagsSelector == 'checkbox' && checked && jQuery.inArray(element.value, this.exclusivesTags) >= 0) {
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
	if (this.tagsSelector == 'select') {
		// on enlève tous les marqueurs 
		var allMarkers = map.getMarkers(map.listTags);
		map.displayMarkers(allMarkers, false);
	}
	
	var markers = map.getMarkers([element.value]);
	map.displayMarkers(markers, checked ? true : false);
	
	/*
		Si on décoche une case:
			- Dans le cas de cases à cocher: On vérifie qu'il reste encore des cases cochées
			- Dans le cas d'une liste déroulante: On vérifie que la valeur n'est pas nulle
			- Si non :
				- si l'option: this.viewDefaultTags est à true: on affiche les tags par defaut
				- si l'option: this.viewDefaultTags est à false: on centre la carte sur le point défini en BE
	*/
	if ((this.tagsSelector == 'checkbox'  && !checked && !jQuery('ul.tagListNum' + this.listId + ' li input:checked').size())
		|| (this.tagsSelector == 'select' && !element.value)) {
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