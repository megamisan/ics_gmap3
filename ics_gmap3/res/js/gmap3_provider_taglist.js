// surcharge createMarkersStatic_
(function() {
	var oldfuncCreateMarkersStatic_ = ics.Map.prototype.createMarkersStatic_;
	
	ics.Map.prototype.createMarkersStatic_ = function(data) {
		var tags = new Array();
		for (var index in data) {
			if (data[index].tag && jQuery.inArray(data[index].tag, tags) < 0)
				tags.push(data[index].tag);
		}
		this.tagList = tags;
		oldfuncCreateMarkersStatic_.apply(this, arguments);
	}
})();

// generate tags list 
function tx_icsgmap3_taglist (map, tagsExclusif) {
	var conteneur = document.getElementById(map.gmap3);
	if (!conteneur)
		return false;
		
	var content = '';
	var list = new Array();
	var tags = new Array();
	if (map.tagList)
		tags = map.tagList;
	
	for (var i = 0; i < tagsExclusif.length; i++)
	{
		tag = tagsExclusif[i];
		if (tag && jQuery.inArray(tag, tags) < 0)
			tags.push(tag);
	}
	tags.sort();
	for (var i = 0; i < tags.length; i++)
	{
		tag = tags[i];
		list.push({
			'tag': 'li', 
			'children': [
				{
					'tag': 'input',
					'properties': { 
						'type': 'checkbox', 
						'id': 'tx_icsgmap3_taglist_checkbox' + i, 
						'value':  tag
					}
				},
				{ 	
					'tag': 'label', 
					'properties': { 
						'for': 'tx_icsgmap3_taglist_checkbox' + i
					},
					'children': [{ 'tag': '', 'value': tag }]					 
				}
			]
		});
	}
	
	content = ics.createElement({
		'tag': 'ul', 
		'properties': { 'className': 'tagList' },
		'children': list 
	});
	conteneur.parentNode.appendChild(content);
	return true;
}