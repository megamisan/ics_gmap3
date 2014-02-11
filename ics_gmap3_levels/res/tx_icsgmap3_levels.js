if (typeof ics != 'object')
	ics = {};

ics.LevelsKml = function() {};

ics.LevelsKml.prototype.init = function(map, kmls) {

	var container = document.getElementById(map.gmap3);
	if (!container)
		return false;
	this.map = map;
	map.levelsKml = kmls;
	map.levelsKmlFinish = true;
	return true;
};

// surcharge createMarkersStatic_
(function() {
	var oldfuncHierarchicalTagListInit = ics.HierarchicalTagList.prototype.init;
	ics.HierarchicalTagList.prototype.init = function(map, exclusivesTags, hiddenTags, defaultTags, viewDefaultTags, separator, checkOnParent, viewLinkSelectAll, lang) {
		// On ne fait rien tant que ics.LevelsKml n'a pas terminé.
		if (!map.levelsKmlFinish) {
			var funcArgument = arguments;
			var funcObj = this;
			window.setTimeout(function() { 
				// oldfuncHierarchicalTagListInit.apply(funcObj, funcArgument);
				ics.HierarchicalTagList.prototype.init(map, exclusivesTags, hiddenTags, defaultTags, viewDefaultTags, separator, checkOnParent, viewLinkSelectAll, lang);
			}, 1000);
		} else {
			map.levelsKmlArray = new Array();
			$.each(map.levelsKml, function(tag, kml) {
				if (tag && kml && $.inArray(tag, map.htl.markersTags) < 0) {
					map.htl.markersTags.push(tag);
					map.levelsKmlArray[tag] = kml;
				}
			});
			oldfuncHierarchicalTagListInit.apply(this, arguments);
		}
	}
	
	var oldfuncHierarchicalTagListClick_ = ics.HierarchicalTagList.prototype.click_;
	ics.HierarchicalTagList.prototype.click_ = function (element, map) {
		oldfuncHierarchicalTagListClick_.apply(this, arguments);
		
		var kml = map.levelsKmlArray[element.value];
		if (kml) {
			ics.Map.prototype.createKmlLayer(kml, element.checked ? true : false, 'levels_' + element.value);
		}
	
	}
	
})();



