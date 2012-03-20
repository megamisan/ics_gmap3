if (typeof ics != 'object')
	ics = {};
// surcharge init
(function() {
	var oldfuncCreateMarkerCallback_ = ics.Map.prototype.createMarkerCallback_;
	ics.Map.prototype.createMarkerCallback_ = function(marker, data) {
		oldfuncCreateMarkerCallback_.apply(this, arguments);
		marker.data = data.data;
		marker.tag = data.tag;
	}; 

	// generate tags list 
	var oldfuncTagListInit = ics.TagList.prototype.init;
	ics.TagList.prototype.init = function(map, exclusivesTags, hiddenTags, defaultTags, viewDefaultTags, secondListFieldName) {
		if (!oldfuncTagListInit.apply(this, arguments))
			return false;
		if (!secondListFieldName)
			return true;
		var container = document.getElementById(map.gmap3);
		this.secondListFieldName = secondListFieldName;
		var content = '';
		var list = new Array();
		var tags = new Array();
		var finalTags = new Array();
		
		// On récupére toutes les données et on construit notre tableau de tags
		jQuery.each(map.data, function(key, value) {
			tag = value.data[secondListFieldName];
			if (tag && jQuery.inArray(tag, tags) < 0) 
				tags.push(tag);
		});
				
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
				list.push(this.makeTagNodeCity_(tag, null, (jQuery.inArray(tag, defaultTags) >= 0) ? true : false, i));
			}
		}
		
		map.listTags = finalTags;		// Array Visible tags
		
		// add tags list after map
		content = ics.createElement({
			'tag': 'ul', 
			'properties': { 'className': 'tagListCity tagListCityNum' + this.listId },
			'children': list 
		});
		this.addToContainer(container, content);
		
		// Default tags
		jQuery('ul.tagListCityNum' + this.listId + ' li input').each(function() {
			if (jQuery.inArray(jQuery(this).attr('value'), this.defaultTags) >= 0)
				jQuery(this).attr('checked', true);
		});
			
		// add click event 
		var tagList = this;
		jQuery('ul.tagListCityNum' + this.listId + ' li input').click(function() {
			tagList.clickCity_(this, map);
		});
		return true;
	};
	
	ics.TagList.prototype.makeTagNodeCity_ = function(tag, icon, checked, index) {
		return {
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
							'id': 'tx_icsgmap3_taglistcity_checkbox' + index, 
							'value':  tag,
							'checked': checked
						}
					},
					{ 	
						'tag': 'label', 
						'attributes': { 
							'for': 'tx_icsgmap3_taglistcity_checkbox' + index
						},
						'children': [{ 'tag': '', 'value': tag }]					 
					}						
				]
			};
	};
	
	ics.TagList.prototype.viewDefaultsTags = function(map, forceDefautTags) {
		var markers = map.getMarkers();
		map.displayMarkers(markers, false);	
		if (forceDefautTags || this.viewDefaultTags) {
			var markers = [];
			var allMarkers = map.getMarkers();
			var defaultTags = this.defaultTags;
			var secondListFieldName = this.secondListFieldName;
			jQuery.each(allMarkers, function(key, value) {
				if (jQuery.inArray(value.tag, defaultTags) >= 0
					|| jQuery.inArray(value.data[secondListFieldName], defaultTags) >= 0) {
					markers.push(value);
				}
			});
			map.displayMarkers(markers, true);
			map.centerMap();
		} else {
			map.centerMapDefault();
		}
		
		// on coche tous les tags par défaut
		jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
			if (jQuery.inArray(jQuery(this).attr('value'), this.defaultTags) >= 0)
				jQuery(this).attr('checked', true);
		});
	};
	
	ics.TagList.prototype.hideExclusivesTags = function(map) {
		var markers = [];
		var allMarkers = map.getMarkers();
		var exclusivesTags = this.exclusivesTags;
		var secondListFieldName = this.secondListFieldName;
		jQuery.each(allMarkers, function(key, value) {
			if (jQuery.inArray(value.tag, exclusivesTags) >= 0
				|| jQuery.inArray(value.data[secondListFieldName], exclusivesTags) >= 0) {
				markers.push(value);
			}
		});
		map.displayMarkers(markers, false);	
		
		// on décoche tous les tags exclusifs
		jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
			if (jQuery.inArray(jQuery(this).attr('value'), exclusivesTags) >= 0)
				jQuery(this).attr('checked', false);
		});
		
		// on décoche tous les tags exclusifs
		jQuery('ul.tagListCityNum' + this.listId + ' li input').each(function() {
			if (jQuery.inArray(jQuery(this).attr('value'), exclusivesTags) >= 0)
				jQuery(this).attr('checked', false);
		});
	};
	
	ics.TagList.prototype.click_ = function(element, map) {
		var resize = true;
		var tagsCityChecked = [];
		jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').each(function() {
			tagsCityChecked.push(jQuery(this).attr('value'));
		});
		var tagsChecked = [];
		jQuery('ul.tagListNum' + this.listId + ' li input:checked').each(function() {
			tagsChecked.push(jQuery(this).attr('value'));
		});
		
		/* 
			S'il s'agit d'un tag exclusif : 
				- Il doit être affiché seul
				- On cache tous les autres markers
		*/	
		if (element.checked && jQuery.inArray(element.value, this.exclusivesTags) >= 0) {
			var allMarkers = map.getMarkers();
			map.displayMarkers(allMarkers, false);
			// on décoche toutes les cases à cocher
			var exclusiveTag = element.value;
			jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
				if (jQuery(this).attr('value') != exclusiveTag)
					jQuery(this).attr('checked', false);
			});
			tagsChecked = [];
			tagsChecked.push(element.value);
		}
		
		// ADD OR REMOVE MARKERS
		// get markers checked tag
		var markers = [];
		var allMarkers = map.getMarkers();
		// on récupére tout et on fait le tri après
		if (element.checked) {
			var markersHidden = [];
			// on vérifie que cela soit compatible aussi avec l'autre liste
			var secondListFieldName = this.secondListFieldName;
			jQuery.each(allMarkers, function(key, value) {
				if (jQuery.inArray(value.tag, tagsChecked) >= 0
					&& (jQuery.isEmptyObject(tagsCityChecked) || jQuery.inArray(value.data[secondListFieldName], tagsCityChecked) >= 0)) {
					markers.push(value);
				} else {
					markersHidden.push(value);
				}
			});
			map.displayMarkers(markersHidden, false);
		} else {
			// retire les marqueurs affichés correspondant à ce tag
			jQuery.each(allMarkers, function(key, value) {
				if (value.tag == element.value) {
					markers.push(value);
				}
			});
		}
		map.displayMarkers(markers, element.checked ? true : false);
		
		/*
			Si on décoche une case:
				- On vérifie qu'il reste encore des cases cochées
				- Si non :
					- si l'option: this.viewDefaultTags est à true: on affiche les tags par defaut
					- si l'option: this.viewDefaultTags est à false: on centre la carte sur le point défini en BE
		*/
		if (!element.checked 
			&& !jQuery('ul.tagListNum' + this.listId + ' li input:checked').size()) {
			if (!jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').size()) {
				// remove all markers except default tags (include hidden tags)
				this.viewDefaultsTags(map, false);
				if (!this.viewDefaultTags) 
					resize = false;
			} else {
				// on remet les marqueurs appartenant à la liste au dessus
				var tagList = this;
				jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').each(function() {
					tagList.clickCity_(this, map);
				});
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

	ics.TagList.prototype.clickCity_ = function(element, map) {	
		var resize = true;
		var tagsChecked = [];
		jQuery('ul.tagListNum' + this.listId + ' li input:checked').each(function() {
			tagsChecked.push(jQuery(this).attr('value'));
		});
		var tagsCityChecked = [];
		jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').each(function() {
			tagsCityChecked.push(jQuery(this).attr('value'));
		});
		
		/* 
			S'il s'agit d'un tag exclusif : 
				- Il doit être affiché seul
				- On cache tous les autres markers
		*/	
		if (element.checked && jQuery.inArray(element.value, this.exclusivesTags) >= 0) {
			var allMarkers = map.getMarkers();
			map.displayMarkers(allMarkers, false);
			// on décoche toutes les cases à cocher
			var exclusiveTag = element.value;
			jQuery('ul.tagListCityNum' + this.listId + ' li input').each(function() {
				if (jQuery(this).attr('value') != exclusiveTag)
					jQuery(this).attr('checked', false);
			});
			tagsCityChecked = [];
			tagsCityChecked.push(element.value);
		}
		
		// ADD OR REMOVE MARKERS
		// get markers checked tag
		var allMarkers = map.getMarkers();
		var markers = [];
		// On recupere tout et on fait le tri après
		var secondListFieldName = this.secondListFieldName;
		if (element.checked) {
			var markersHidden = [];
			// on affiche les marqueurs correspondant aux 2 critères
			jQuery.each(allMarkers, function(key, value) {
				if (jQuery.inArray(value.data[secondListFieldName], tagsCityChecked) >= 0
					&& (jQuery.isEmptyObject(tagsChecked) || jQuery.inArray(value.tag, tagsChecked) >= 0)) {
					markers.push(value);
				} else {
					markersHidden.push(value);
				}
			});
			map.displayMarkers(markersHidden, false);
		} else {
			// retire les marqueurs affichés correspondant à ce tag
			jQuery.each(allMarkers, function(key, value) {
				if (value.data[secondListFieldName] == element.value) {
					markers.push(value);
				}
			});
		}
		map.displayMarkers(markers, element.checked ? true : false);
		
		/*
			Si on décoche une case:
				- On vérifie qu'il reste encore des cases cochées
				- Si non :
					- si l'option: this.viewDefaultTags est à true: on affiche les tags par defaut
					- si l'option: this.viewDefaultTags est à false: on centre la carte sur le point défini en BE
		*/
		if (!element.checked && !jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').size()) {
			if (!jQuery('ul.tagListNum' + this.listId + ' li input:checked').size()) {
				// remove all markers except default tags (include hidden tags)
				// remove all markers except default tags (include hidden tags)
				this.viewDefaultsTags(map, false);
				if (!this.viewDefaultTags) 
					resize = false;
			} else {
				// on remet les marqueurs appartenant à la liste au dessus
				var tagList = this;
				jQuery('ul.tagListNum' + this.listId + ' li input:checked').each(function() {
					tagList.click_(this, map);
				});
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
})();

