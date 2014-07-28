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
			map.levelsKmlArray = [];
			$.each(map.levelsKml, function(tag, kml) {
				if (tag && kml) {
					if($.inArray(tag, map.htl.markersTags) < 0) 
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
		if(ics.Map.prototype.elementsClicked == undefined) ics.Map.prototype.elementsClicked = new Array();
		if(!element.checked) {
			eClicked = new Array();
			jQuery.each(ics.Map.prototype.elementsClicked, function(index, value) {
				if(value != element.attributes['value'].nodeValue) {
					eClicked.push(value);
				}
			});
			ics.Map.prototype.elementsClicked = eClicked;
		}else{
			ics.Map.prototype.elementsClicked.push(element.attributes['value'].nodeValue);
		}
		var kml = map.levelsKmlArray[element.value];
		if (kml) {
			//kml = 'http://static.touraineverte.com/kml/departements/56.kml';
			ics.Map.prototype.createKmlLayer(kml, element.checked ? true : false, 'levels_' + element.value.replace(/[ #-,]/gi, "_"));
		}
	
	}
	
})();



