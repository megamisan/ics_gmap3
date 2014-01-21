if (typeof ics != 'object')
	ics = {};
// surcharge init
(function() {
	var oldfuncCreateMarkersStatic_ = ics.Map.prototype.createMarkersStatic_;
	ics.Map.prototype.createMarkersStatic_ = function(data) {
		var tags = [];
		var icons = [];
		var iconsHover = [];
		jQuery.each(data, function() {
			if (this.tag && jQuery.inArray(this.tag, tags) < 0) {
				tags.push(this.tag);
				icons[this.tag] = this.icon_list;
				iconsHover[this.tag] = this.icon_list_hover;
			}
		});
		this.tla = {};
		this.tla.iconsTagsList = icons;
		this.tla.iconsTagsListHover = iconsHover;
		oldfuncCreateMarkersStatic_.apply(this, arguments);
	};

	// generate tags list 
	var oldfuncTagListinit = ics.TagList.prototype.init;
	ics.TagList.prototype.init = function(map, exclusivesTags, hiddenTags, defaultTags, viewDefaultTags, tagsSelector) {
		this.map = map;
		return oldfuncTagListinit.apply(this, arguments);
	};
	
	var oldfuncTagListmakeTagNode_ = ics.TagList.prototype.makeTagNode_;
	ics.TagList.prototype.makeTagNode_ = function(tag, icon, checked, index) {
		if (this.tagsSelector == 'select') {
			return oldfuncTagListmakeTagNode_.apply(this, arguments);
		} else {
			if (!checked && this.map.tla.iconsTagsList[tag])
				icon = this.map.tla.iconsTagsList[tag];
			if (checked && this.map.tla.iconsTagsListHover[tag])
				icon = this.map.tla.iconsTagsListHover[tag];
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
	
	var oldfuncTagListclick_ = ics.TagList.prototype.click_;
	ics.TagList.prototype.click_ = function(element, map) {
		var checked = element.checked;
		var tag = element.value;
		var icon = '';
		if (!checked && map.tla.iconsTagsList[tag]) {
			icon = map.tla.iconsTagsList[tag];
		}
		if (checked && map.tla.iconsTagsListHover[tag]) {
			icon = map.tla.iconsTagsListHover[tag];
		}
		if (icon)
			jQuery(element).parent('li').children('img').attr('src', icon);
		oldfuncTagListclick_.apply(this, arguments);
	};
	
})();