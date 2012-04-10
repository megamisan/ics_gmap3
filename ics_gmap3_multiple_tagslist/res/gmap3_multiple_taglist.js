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
	ics.TagList.prototype.init = function(map, exclusivesTags, hiddenTags, defaultTags, viewDefaultTags, tagsSelector, secondListFieldName, secondTagsSelector) {
		if (!oldfuncTagListInit.apply(this, arguments))
			return false;
		if (!secondListFieldName)
			return true;
		var container = document.getElementById(map.gmap3);
		this.secondListFieldName = secondListFieldName;
		this.secondTagsSelector = secondTagsSelector;
		if (!this.secondTagsSelector)
			this.secondTagsSelector = 'checkbox';
		var content = '';
		var list = [];
		var tags = [];
		var finalTags = [];
		
		// On récupére toutes les données et on construit notre tableau de tags
		jQuery.each(map.data, function() {
			tag = this.data[secondListFieldName];
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
		content = this.makeTagParentNodeCity_(list);
		this.addToForm(content);
		
		// Default tags => ne pas refaire appel à la fonction viewDefaultsTags cela ne sert à rien
		var defaultTags = this.defaultTags;
		if (this.secondTagsSelector == 'select') {
			jQuery('select.tagListCityNum' + this.listId + ' option').each(function() {
				if (jQuery.inArray(jQuery(this).attr('value'), defaultTags) >= 0)
					jQuery(this).attr('selected', true);
			});
		} else {
			jQuery('ul.tagListCityNum' + this.listId + ' li input').each(function() {
				if (jQuery.inArray(jQuery(this).attr('value'), defaultTags) >= 0)
					jQuery(this).attr('checked', true);
			});
		}
			
		// add click event 
		this.initEventCity_(map);
		return true;
	};
	
	ics.TagList.prototype.makeTagParentNodeCity_ = function(list) {
		if (this.secondTagsSelector == 'select') {
			var listWithOptionEmpty = [{
				'tag': 'option', 
				'properties': { 
					'value':  '', 
					'id': 'tx_icsgmap3_taglistcity_checkbox_empty'
				}
			}];
			listWithOptionEmpty = listWithOptionEmpty.concat(list);
			var content = ics.createElement({
				'tag': 'select', 
				'properties': { 
					'className': 'tagListCity tagListCityNum' + this.listId,
					'name': 'tagListCityNum' + this.listId
				},
				'children': listWithOptionEmpty 
			});
		} else {
			var content = ics.createElement({
				'tag': 'ul', 
				'properties': { 'className': 'tagListCity tagListCityNum' + this.listId },
				'children': list 
			});
		}
		return content;
	};

	ics.TagList.prototype.makeTagNodeCity_ = function(tag, icon, checked, index) {
		if (this.secondTagsSelector == 'select') {
			var node = {
				'tag': 'option', 
				'properties': { 
					'value':  tag, 
					'id': 'tx_icsgmap3_taglistcity_checkbox' + index
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
		}
		return node;
	};
	
	ics.TagList.prototype.viewDefaultsTags = function(map, forceDefautTags) {
		var markers = map.getMarkers();
		map.displayMarkers(markers, false);	
		if (forceDefautTags || this.viewDefaultTags) {
			var markers = [];
			var allMarkers = map.getMarkers();
			var defaultTags = this.defaultTags;
			var secondListFieldName = this.secondListFieldName;
			jQuery.each(allMarkers, function() {
				if (jQuery.inArray(this.tag, defaultTags) >= 0
					|| jQuery.inArray(this.data[secondListFieldName], defaultTags) >= 0) {
					markers.push(this);
				}
			});
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
			var markers = [];
			var allMarkers = map.getMarkers();
			var exclusivesTags = this.exclusivesTags;
			var secondListFieldName = this.secondListFieldName;
			jQuery.each(allMarkers, function() {
				if (jQuery.inArray(this.tag, exclusivesTags) >= 0
					|| jQuery.inArray(this.data[secondListFieldName], exclusivesTags) >= 0) {
					markers.push(this);
				}
			});
			map.displayMarkers(markers, false);	
			
			// on décoche tous les tags exclusifs
			jQuery('ul.tagListNum' + this.listId + ' li input').each(function() {
				if (jQuery.inArray(jQuery(this).attr('value'), exclusivesTags) >= 0)
					jQuery(this).attr('checked', false);
			});
		}
	};
	
	ics.TagList.prototype.initEventCity_ = function(map) {
		if (this.secondTagsSelector == 'select') {
			var tagList = this;
			jQuery('select.tagListCityNum' + this.listId).change(function() {
				tagList.clickCity_(this, map);
			});
		} else {
			var tagList = this;
			jQuery('ul.tagListCityNum' + this.listId + ' li input').click(function() {
				tagList.clickCity_(this, map);
			});
		}
	};

	ics.TagList.prototype.click_ = function(element, map) {
		var resize = true;
		var checked = '';
		var tagsCityChecked = [];
		var tagsChecked = [];
		if (this.tagsSelector == 'select') {
			checked = true;
			jQuery('select.tagListNum' + this.listId + ' option:selected').each(function() {
				if (jQuery(this).attr('value'))
					tagsChecked.push(jQuery(this).attr('value'));
			});
			if (!element.value)
				checked = false;
		} else {
			checked = element.checked;
			jQuery('ul.tagListNum' + this.listId + ' li input:checked').each(function() {
				tagsChecked.push(jQuery(this).attr('value'));
			});
		}
		
		if (this.secondTagsSelector == 'select') {
			jQuery('select.tagListCityNum' + this.listId + ' option:selected').each(function() {
				if (jQuery(this).attr('value'))
					tagsCityChecked.push(jQuery(this).attr('value'));
			});
		} else {
			jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').each(function() {
				tagsCityChecked.push(jQuery(this).attr('value'));
			});
		}
		
		
		/* 
			S'il s'agit d'un tag exclusif : 
				- Seulement dans le cas de cases à cocher (liste déroulante = toujours seul)
				- Il doit être affiché seul
				- On cache tous les autres markers
		*/	
		if (this.tagsSelector == 'checkbox' && checked && jQuery.inArray(element.value, this.exclusivesTags) >= 0) {
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
		if (checked) {
			var markersHidden = [];
			// on vérifie que cela soit compatible aussi avec l'autre liste
			var secondListFieldName = this.secondListFieldName;
			jQuery.each(allMarkers, function() {
				if (jQuery.inArray(this.tag, tagsChecked) >= 0
					&& (jQuery.isEmptyObject(tagsCityChecked) || jQuery.inArray(this.data[secondListFieldName], tagsCityChecked) >= 0)) {
					markers.push(this);
				} else {
					markersHidden.push(this);
				}
			});
			map.displayMarkers(markersHidden, false);
		} else {
			// retire les marqueurs affichés correspondant à ce tag
			var value = element.value;
			jQuery.each(allMarkers, function() {
				if (this.tag == value) {
					markers.push(this);
				}
			});
		}
		map.displayMarkers(markers, checked ? true : false);
		
		/*
			Si on décoche une case:
				- Dans le cas de cases à cocher: On vérifie qu'il reste encore des cases cochées
				- Dans le cas d'une liste déroulante: On vérifie que la valeur n'est pas nulle
				- Si non :
					- si l'option: this.viewDefaultTags est à true: on affiche les tags par defaut
					- si l'option: this.viewDefaultTags est à false: on centre la carte sur le point défini en BE
		*/
		if (!checked && (
				(this.tagsSelector == 'checkbox' && !jQuery('ul.tagListNum' + this.listId + ' li input:checked').size())
				|| (this.tagsSelector == 'select' && !element.value)
			)) {
			if ((this.secondTagsSelector == 'checkbox' && !jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').size())
				|| (this.secondTagsSelector == 'select' && !jQuery('select.tagListCityNum' + this.listId + ' option:selected').size())) {
				// remove all markers except default tags (include hidden tags)
				this.viewDefaultsTags(map, false);
				if (!this.viewDefaultTags) 
					resize = false;
			} else {
				// on remet les marqueurs appartenant à la liste au dessus
				var tagList = this;
				if (this.secondTagsSelector == 'checkbox') {
					jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').each(function() {
						tagList.clickCity_(this, map);
					});
				} else {
					jQuery('select.tagListCityNum' + this.listId + ' option:selected').each(function() {
						tagList.clickCity_(this, map);
					});
				}
			}
		}
		
		/* 
			Au clic d'un tag autre qu'un tag exclusif: 
				- On efface les tags exclusifs
		*/
		if (element.value && jQuery.inArray(element.value, this.exclusivesTags) < 0) {
			this.hideExclusivesTags(map);
		}
		
		// CENTER MAP
		if (resize) 
			map.centerMap();
	};

	ics.TagList.prototype.clickCity_ = function(element, map) {	
		var resize = true;		
		var checked = '';
		var tagsCityChecked = [];
		var tagsChecked = [];
		if (this.secondTagsSelector == 'select') {
			checked = true;
			jQuery('select.tagListCityNum' + this.listId + ' option:selected').each(function() {
				if (jQuery(this).attr('value'))
					tagsCityChecked.push(jQuery(this).attr('value'));
			});
			if (!element.value)
				checked = false;
		} else {
			checked = element.checked;
			jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').each(function() {
				tagsCityChecked.push(jQuery(this).attr('value'));
			});
		}
		
		if (this.tagsSelector == 'select') {
			jQuery('select.tagListNum' + this.listId + ' option:selected').each(function() {
				if (jQuery(this).attr('value'))
					tagsChecked.push(jQuery(this).attr('value'));
			});
		} else {
			jQuery('ul.tagListNum' + this.listId + ' li input:checked').each(function() {
				tagsChecked.push(jQuery(this).attr('value'));
			});
		}
		
		/* 
			S'il s'agit d'un tag exclusif : 
				- Seulement dans le cas de cases à cocher (liste déroulante = toujours seul)
				- Il doit être affiché seul
				- On cache tous les autres markers
		*/	
		if (this.secondTagsSelector == 'checkbox' && checked && jQuery.inArray(element.value, this.exclusivesTags) >= 0) {
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
		if (checked) {
			var markersHidden = [];
			// on affiche les marqueurs correspondant aux 2 critères
			jQuery.each(allMarkers, function() {
				if (jQuery.inArray(this.data[secondListFieldName], tagsCityChecked) >= 0
					&& (jQuery.isEmptyObject(tagsChecked) || jQuery.inArray(this.tag, tagsChecked) >= 0)) {
					markers.push(this);
				} else {
					markersHidden.push(this);
				}
			});
			map.displayMarkers(markersHidden, false);
		} else {
			// retire les marqueurs affichés correspondant à ce tag
			var value = element.value;
			jQuery.each(allMarkers, function() {
				if (this.data[secondListFieldName] == value) {
					markers.push(this);
				}
			});
		}
		map.displayMarkers(markers, checked ? true : false);
		
		/*
			Si on décoche une case:
				- Dans le cas de cases à cocher: On vérifie qu'il reste encore des cases cochées
				- Dans le cas d'une liste déroulante: On vérifie que la valeur n'est pas nulle
				- Si non :
					- si l'option: this.viewDefaultTags est à true: on affiche les tags par defaut
					- si l'option: this.viewDefaultTags est à false: on centre la carte sur le point défini en BE
		*/
		if (!checked && (
				(this.secondTagsSelector == 'checkbox' && !jQuery('ul.tagListCityNum' + this.listId + ' li input:checked').size())
				|| (this.secondTagsSelector == 'select' && !element.value)
			)) {
			if ((this.tagsSelector == 'checkbox' && !jQuery('ul.tagListNum' + this.listId + ' li input:checked').size())
				|| (this.tagsSelector == 'select' && !jQuery('select.tagListNum' + this.listId + ' option:selected').size())) {
				// remove all markers except default tags (include hidden tags)
				this.viewDefaultsTags(map, false);
				if (!this.viewDefaultTags) 
					resize = false;
			} else {
				// on remet les marqueurs appartenant à la liste au dessus
				var tagList = this;
				if (this.tagsSelector == 'checkbox') {
					jQuery('ul.tagListNum' + this.listId + ' li input:checked').each(function() {
						tagList.click_(this, map);
					});
				} else {
					jQuery('select.tagListNum' + this.listId + ' option:selected').each(function() {
						tagList.click_(this, map);
					});
				}
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

