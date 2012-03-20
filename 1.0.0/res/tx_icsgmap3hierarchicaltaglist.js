if (typeof ics != 'object')
	ics = {};


// surcharge createMarkersStatic_
(function() {
	var oldfuncCreateMarkersStatic_ = ics.Map.prototype.createMarkersStatic_;
	
	ics.Map.prototype.createMarkersStatic_ = function(data) {
		var tags = new Array();
		var icons = new Array();
		for (var index in data) {
			if (data[index].tag && jQuery.inArray(data[index].tag, tags) < 0) {
				tags.push(data[index].tag);
				icons[data[index].tag] = data[index].icon;
			}
		}
		this.htl = {};
		this.htl.markersTags = tags;
		this.htl.iconsTags = icons;
		oldfuncCreateMarkersStatic_.apply(this, arguments);
	}
})();

ics.HierarchicalTagList = function() {};
ics.HierarchicalTagList.nextId = 0;
// generate tags list 
ics.HierarchicalTagList.prototype.init = function(map, exclusivesTags, hiddenTags, defaultTags, viewDefaultTags, separator, checkOnParent, viewLinkSelectAll, lang) {
	var conteneur = document.getElementById(map.gmap3);
	if (!conteneur)
		return false;
	this.map = map;
	this.listId = ics.HierarchicalTagList.nextId++;
	this.separator = separator;
	this.checkOnParent = checkOnParent;
	this.viewLinkSelectAll = viewLinkSelectAll;
	this.lang = lang;
	this.hiera = {};
	this.iconHiera = {};
	
	var content = '';
	var list = new Array();
	var tags = new Array();
	var finalTags = new Array();
	if (map.htl.markersTags)
		tags = map.htl.markersTags;
	
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
	for (var i = 0; i < tags.length; i++) {
		tag = tags[i];
		if (tag && jQuery.inArray(tag, hiddenTags) < 0)
			finalTags.push(tag);
		
		// this.iconHiera[cat] = map.htl.iconsTags[tags[i]];
		 
		this.parseCat(this.hiera, tags[i], separator);
		
		var cat = '';
		var ls = tag.split(separator);
		while (ls.length) {
			var cat = ls.shift();
		}
		this.iconHiera[cat] = map.htl.iconsTags[tag];
	}
		
	for (var cat in this.hiera) {
		if(jQuery.inArray(cat, hiddenTags) < 0) {
			list.push(this.makeTagNode_(cat, (jQuery.inArray(cat, defaultTags) >= 0) ? true : false, '', this.hiera[cat]));
		}
	}
		
	map.listTags = finalTags;		// Array Visible tags
	
	// add tags list after map
	content = ics.createElement({
		'tag': 'ul', 
		'properties': { 'className': 'tagList tagListNum' + this.listId },
		'children': list 
	});
	conteneur.parentNode.insertBefore(content, conteneur);
	
	content = ics.createElement({
		'tag' : 'div',
		'properties': { 'className': 'clear' }
	});
	conteneur.parentNode.insertBefore(content, conteneur);
	this.checkH();
	// remove all markers except default tags (include hidden tags)
	var markers = map.getMarkers();
	map.displayMarkers(markers, false);	
	var markers = map.getMarkers(defaultTags);
	map.displayMarkers(markers, true);
	
	// add click event 
	var tagList = this;
	jQuery('ul.tagListNum' + this.listId + ' li input').click(function() {
		tagList.click_(this, map);
	});
	// tab click event
	jQuery('ul.tagListNum' + this.listId + ' li').click(function() {
		tagList.clickTab_(this, map);
	});
	// Select / unselect
	jQuery('ul.tagListNum' + this.listId + ' li.all a.select').click(function() {
		jQuery(this).parent('li').parent('ul').children('li').children('input').each(function() {
			jQuery(this).attr('checked', true);
			tagList.click_(this, map);
		});
		return false;
	});
	jQuery('ul.tagListNum' + this.listId + ' li.all a.unselect').click(function() {
		jQuery(this).parent('li').parent('ul').children('li').children('input').each(function() {
			jQuery(this).attr('checked', false);
			tagList.click_(this, map);
		});
		return false;
	});
	return true;
}

ics.HierarchicalTagList.prototype.checkH = function() {
		var myListH = jQuery('ul.tagListNum' + this.listId+' li.selected').height();
		var myUlTagListH = jQuery('ul.tagListNum' + this.listId+' li.selected ul').height();
		jQuery('ul.tagListNum' + this.listId).css('height', (myUlTagListH+myListH)+'px');
}

ics.HierarchicalTagList.prototype.parseCat = function(obj, tag, sep) {
		var ls = tag.split(sep);
		while (ls.length) {
			var val = ls.shift();
			if (typeof(obj[val]) != 'object') {
				obj[val] = {};
			}
			obj = obj[val];
		}
		return obj;
}

ics.HierarchicalTagList.prototype.makeTreeNode_ = function(tag, icon, checked, path, children, index) {
	var list = [];
	var hasChild = false;
	for (var child in children) {
		if(jQuery.inArray(child, this..hiddenTags) < 0) {
			hasChild = true;
			list.push(this.makeTagNode_(child, (jQuery.inArray(tag, this.defaultTags) >= 0) ? true : false, path, children[child]));
		}
	}
	
	if (this.viewLinkSelectAll && hasChild) {
		var oldList = list;
		list = [];
		list.push({
			'tag': 'li',
			'properties': { 'className': 'all'},
			'children': [
				{
					'tag': 'a',
					'properties': {'href': '#', 'className': 'select'},
					'children': [{'tag': '', 'value': this.lang['select']}]
				}, 
				{
					'tag': '',
					'value': '/'
				},
				{
					'tag': 'a',
					'properties': {'href': '#', 'className': 'unselect'},
					'children': [{'tag': '', 'value': this.lang['unselect']}]
				}
			]
		});
		list = list.concat(oldList);
	}
	
	return {
			'tag': 'ul', 
			'children': list
		};
}
ics.HierarchicalTagList.prototype.makeTreeNode_.nextId = 0;

ics.HierarchicalTagList.prototype.makeTagNode_ = function(tag, checked, path, children) {
	var index = arguments.callee.nextId++;
	var icon = this.iconHiera[tag];
	var curPath = path.length ? path + this.separator + tag : tag
	var node = {
			'tag': 'li', 
			'properties': { 'className': 'tagList tagListNum-' + tag.replace(/[^a-z0-9]/gi, '-') + (index ? ' unselected':' selected') },
			'children': [
				{
					'tag': 'img',
					'properties': { 
						'src': icon
					}
				},
				{
					'tag': 'input',
					'properties': { 
						'type': 'checkbox', 
						'id': 'tx_icsgmap3_taglist_checkbox' + index, 
						'value':  curPath,
						'checked': checked
					}
				},
				{ 	
					'tag': 'label', 
					'attributes': { 
						'for': 'tx_icsgmap3_taglist_checkbox' + index
					},
					'children': [
						{ 
							'tag': '', 
							'value': tag 
						},
						{ 	
							'tag': 'span', 
							'properties': { 'className': 'tagList-l' },
						},
						{ 	
							'tag': 'span', 
							'properties': { 'className': 'tagList-r' },	 
						}
					]		 
				}
			]
		};
	
	for (var child in children) {
		if(!this.checkOnParent)
			node.children.splice(1,1);
		node.children.push(this.makeTreeNode_(tag, icon, checked, curPath, children, index));
		break;
	}
	return node;
};
ics.HierarchicalTagList.prototype.makeTagNode_.nextId = 0;

ics.HierarchicalTagList.prototype.clickTab_ = function (element, map) {
	if(element.className.indexOf('unselected') > 1) {
		var myElemClass = element.className.split(' ').join('.');
		jQuery('.' + myElemClass).parent().find("li.selected").toggleClass("selected").addClass("unselected");
		jQuery('.' + myElemClass).removeClass("unselected").addClass("selected");
		this.checkH();
	}
	return false;
}
ics.HierarchicalTagList.prototype.click_ = function (element, map) {
	var allMarkers = map.getMarkers(map.listTags);
	var rezise = true;
	/* 
		S'il s'agit d'un tag exclusif : 
			- Il doit être affiché seul
			- On cache tous les autres markers
	*/	
	if (element.checked && jQuery.inArray(element.value, this.exclusivesTags) >= 0) {
		map.displayMarkers(allMarkers, false);
		// on décoche toutes les cases à cocher
		jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
			if (jQuery(this).attr('id') != element.id)
				jQuery(this).attr('checked', false);
		});
	}
	
	// ADD OR REMOVE MARKERS
	// get markers checked tag
	var markers = map.getMarkers([element.value]);
	map.displayMarkers(markers, element.checked ? true : false);
	
	
	/* 
		Au clic d'un tag autre qu'un tag exclusif: 
			- On efface les tags exclusifs
	*/
	if (jQuery.inArray(element.value, this.exclusivesTags) < 0) {
		var markers = map.getMarkers(this.exclusivesTags);
		map.displayMarkers(markers, false);	
		// on décoche tous les tags exclusifs
		// jQuery('#' + map.gmap3 + ' + ul.tagList li input').each(function() {
		jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
			if (jQuery.inArray(jQuery(this).attr('value'), this.exclusivesTags) >= 0)
				jQuery(this).attr('checked', false);
		});
	}
	/*
		Si on décoche une case:
			- On vérifie qu'il reste encore des cases cochées
			- Si non :
				- si l'option: this.viewDefaultTags est à true: on affiche les tags par defaut
				- si l'option: this.viewDefaultTags est à false: on centre la carte sur le point défini en BE
	*/
	if (!element.checked && !jQuery('ul.tagListNum' + this.listId + ' li input:checked').size()) {
		// remove all markers except default tags (include hidden tags)
		var markers = map.getMarkers();
		map.displayMarkers(markers, false);	
		
		var defaultChecked = false;
			
		if (this.viewDefaultTags) {
			var markers = map.getMarkers(this.defaultTags);
			map.displayMarkers(markers, true);
			// on coche tous les tags par défaut
			jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
				if (jQuery.inArray(jQuery(this).attr('value'), this.defaultTags) >= 0) {
					jQuery(this).attr('checked', true);
					defaultChecked = true;
				}
			});
			
		} 
		if (!defaultChecked) {
			map.centerMapDefault();
			rezise = false;
		}
	}
	
	// CENTER MAP
	if (rezise) 
		map.centerMap();
};