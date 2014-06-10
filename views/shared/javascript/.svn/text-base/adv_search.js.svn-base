jQuery.ui.dynatree.nodedatadefaults['icon'] = false;


jQuery(function(){
	// Attach the dynatree widget to an existing <div id=\"tree\"> element
	// and pass the tree options as an argument to the dynatree() function:
	jQuery("#tree").dynatree({
		checkbox: true,
        selectMode: 3,
		children: categories_tree,
		onSelect: onSelect,
		//fx: { height: "toggle", duration: 200 }
    });
});

function onSelect(flag, node)
{
	var categories = jQuery("#tree").dynatree("getSelectedNodes");
	jQuery("#selCats")[0].value = simpleArray(categories);
}

//takes the complex dynatree objects and extracts the category ids
function simpleArray(selected_categories)
{
	var simple_selected_categories = new Array();

	for(var i = 0; i < selected_categories.length; i++)
	{
		simple_selected_categories[i] = selected_categories[i].data.id;
	}

	return JSON.stringify(simple_selected_categories);
}