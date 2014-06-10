jQuery.ui.dynatree.nodedatadefaults['icon'] = false;
var ICONS_PATH = "../../../plugins/CategoryTree/views/shared/javascript/icons/";


jQuery(function(){
	// Attach the dynatree widget to an existing <div id=\"tree\"> element
	// and pass the tree options as an argument to the dynatree() function:
	jQuery("#tree").dynatree({
		checkbox: true,
        selectMode: 3,
		children: categories_tree,
		onPostInit: onActivate,
		onSelect: onSelect,
		//fx: { height: "toggle", duration: 200 }
    });
});

//when tree is first initialized
function onActivate()
{
	var selectedNodes = jQuery("#tree").dynatree("getSelectedNodes");
	if(selectedNodes[0])
		onSelect(null, selectedNodes[0]);
}

//when a node is selected update the text box on top of the tree
//and the post var
function onSelect(flag, node)
{
	var categories = jQuery("#tree").dynatree("getSelectedNodes");
	var categoryDiv = jQuery("#categories")[0];
	var categories_str = '<p>';

	for (var i = 0; i < categories.length; i++)
	{
		category = categories[i];
		categories_str += '<b>' + category.data.title + '</b>';

		while (category.data.parent != -1)
		{
			category = category.parent;
			categories_str += " -> " + category.data.title;
		}
		categories_str += '</br>';
	}
	categories_str += '</p>';
	categoryDiv.innerHTML = categories_str;
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
