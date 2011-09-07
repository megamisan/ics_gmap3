if (typeof ics != 'object')
	ics = {};

/**
 * Create html elements from description and returns the created elements.
 *
 * @param elementDescriptionList The array of elements to create. See ics.createElement to know the format of one element.
 * @return The array of created elements.
 */
ics.createElements = function(elementDescriptionList) {
	var elements = new Array();
	for (var i = 0; i < elementDescriptionList.length; i++)
	{
		var element = ics.createElement(elementDescriptionList[i]);
		if (element != null)
			elements.push(element);
	}
	return elements;
}

/**
 * Create an html element from description and return the created element.
 *
 * @param elementDescription The object representing the element.
 * It has these properties:
 * - tag: The tag of the element, if empty, a textNode is to be created. In this last case, only the property value has a meaning.
 * - properties: The collection of properties to define in the element.
 * - children: The array of elementDescription for the element's children.
 * - value: The value of the textNode.
 * @return The created element.
 */
ics.createElement = function (elementDescription) {
	var element = null;
	if (elementDescription.tag == "")
		element = document.createTextNode(elementDescription.value);
	else
	{
		element = document.createElement(elementDescription.tag);
		if (element != null)
		{
			if (elementDescription.children != null)
			{
				var elements = ics.createElements(elementDescription.children);
				for (var i = 0; i < elements.length; i++)
					element.appendChild(elements[i]);
			}
			if (elementDescription.properties != null)
			{
				for (var name in elementDescription.properties)
					ics.createElement.setProperty(element, name, elementDescription.properties[name]);
			}
			if (elementDescription.attributes != null)
			{
				for (var name in elementDescription.attributes)
					ics.createElement.setAttributes(element, name, elementDescription.attributes[name]);
			}
		}
	}
	return element;
}

/**
 * Sets an object property to the specified value. Go recursively into values which are objects.
 *
 * @param object The object reference.
 * @param name The property name.
 * @param value The value to define.
 */
ics.createElement.setProperty = function (object, name, value) {
	if (typeof(value) == 'object')
	{
		if (object[name] == null)
			object[name] = {};
		object = object[name];
		for (name in value)
			ics.createElement.setProperty(object, name, value[name]);
	}
	else
	{
		object[name] = value;
	}
}

/**
 * Sets an object attribute to the specified value. Go recursively into values which are objects.
 *
 * @param object The object reference.
 * @param name The property name.
 * @param value The value to define.
 */
ics.createElement.setAttributes = function (object, name, value) {
	if (typeof(value) == 'object')
	{
		if (object[name] == null)
			object[name] = {};
		object = object[name];
		for (name in value)
			ics.createElement.setAttributes(object, name, value[name]);
	}
	else
	{
		object.setAttribute(name, value);
		// object.onClick = value; => IE7 ?
	}
}
