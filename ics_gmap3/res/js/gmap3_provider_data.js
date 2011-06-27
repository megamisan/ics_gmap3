function ics_gmap3_initializeData(data) {
	markersData = data;
}

function ics_gmap3_initializeMarkers(data, gmap3) {
	var myData = [];
	var tmpTags = {};
	var allTags = [];
	
	//alert(data.tag);
		
	
	
	$.each(data, function(i, row){
	
		var tag = row.tag;
		var obj = {"lat":row.lat,"lng":row.lng,"tag":row.tag,"data":{"name":row.data.name,"address":row.data.address}};
		
		myData.push(obj);
	
		/*jQuery('#' + gmap3).gmap3({
			action:'addMarkers',
			markers: [{"lat":row.lat,"lng":row.lng,"tag":row.tag,"data":{"name":row.data.name,"address":row.data.address}},],
		});*/
	
		//myData[row.tag] = {"lat":row.lat,"lng":row.lng,"tag":row.tag,"data":{"name":row.data.name,"address":row.data.address}},];
		
		//myData.push(row.tag);
		
		//alert(row);
		
		//alert(row.data.name);
		

		/*jQuery('#' + gmap3).gmap3({
			action:'addMarkers',
			markers: '[' + row + ']',
		});*/
		
		/*myData.push({
			lat: row});*/
	
		//aCategory[row.tag] = "ok";
		//alert(aCategory[row.tag]);
		/* aTags.push({
			tag: row.tag,
			lat: row.lat,
			lng: row.lng
		 });*/
		//markersData = row;
		//alert(row.tag);
		
		//row.tag = "test";
		tmpTags[row.tag] = true;
	});

	alert(myData);
	
	/*for(var donnee in myData){
		alert(donnee.lat);
	}*/
	
	
	//alert(myData['Culture'].lat);
	
	//var_dump(myData);
	
	for(var tag in tmpTags){
		allTags.push(tag);
		
		if(tag == 'Gendarmerie') {
			//alert(tag);
		}
		
		//alert(tag.tag);
	
		/*myData.push(
			lat: 
		);
		
		jQuery('#' + gmap3).gmap3({
			action:'addMarkers',
			markers: myData,
		});*/
		
		//allTags.push(tag);
		//alert(tagName);
		//var markersData
	}
	
	//$.each(data, function(i, row){
	//	alert(row.tag);
		//aCategory[row.tag] = "ok";
		//alert(aCategory[row.tag]);
		/* aTags.push({
			tag: row.tag,
			lat: row.lat,
			lng: row.lng
		 });*/
		//markersData = row;
		//alert(row.tag);
		
		//row.tag = "test";
	//	tmpTags[row.tag] = true;
	//});
	
	//alert(allTags);
	/*var allTags = [];
	var aTags = {};
	//var markersData = {};
	
	$.each(data, function(i, row){
		//aCategory[row.tag] = "ok";
		//alert(aCategory[row.tag]);
		// aTags[row.tag] = row;
		//markersData = row;
	});
	
	for(tag in aTags){
		//allTags.push(tag);
		//alert(tagName);
		//var markersData
	}*/
}

/*
		jQuery(\'#' . $this->mapId . '\').gmap3(
		
				{
					action:\'addMarkers\',
					markers: markersData,
					marker: {
						events:{
							mouseover: function(marker, event, data){
								jQuery(this).gmap3(
								{
									action:\'clear\', list:\'overlay\'
								},
								{
									action:\'addOverlay\',
									latLng: marker.getPosition(),
									content:\'<div class="infobulle\'+(data.access4disabled ? \' access4disabled\' : \'\')+\'">\' +
											\'	<div class="bg"></div>\' +
											\'	<div class="text"> \' + data.name  + \' \' + data.address  + \'</div>\' +
											\'</div>\' +
											\'<div class="arrow"></div>\',
									offset: {
										x:-46,
										y:-73
									}
								});
							},
							mouseout: function(){
								jQuery(this).gmap3({action:\'clear\', list:\'overlay\'});
							},
							click: function(marker, event, data){
								jQuery(this).gmap3({
									action:\'panTo\', 
									args:[marker.position]
								});
							}
						}
					}
				}
		
		);*/




/*                $.each(villes, function(i, ville){
                    data.push({
                        lat: ville.lat,
                        lng: ville.lng,
                        tag: ville.region,
                        data: ville
                    });
                    tmp[ ville.region ] = true;
                });
                 
                for(r in tmp){
                    regions.push(r);
                }
                regions = regions.sort();*/