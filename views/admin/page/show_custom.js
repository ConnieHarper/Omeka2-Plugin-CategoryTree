window.onload = function()
{
	var collection_div = jQuery(".info.panel")[0];
	var sidebar = collection_div.parentNode;
	var categories = document.createElement("div");
	var categories_inner = document.createElement('div');

	categories.id = 'item-categories';
	categories.className = 'panel';
	categories.innerHTML = '<h4>Categories</h4>';

	categories_inner.style.margin_bottom = 0;

	var categories_string = buildUlTree(categories_tree) || "<p>No Categories.</p>";

	categories_inner.innerHTML = categories_string;
	categories.appendChild(categories_inner);
	sidebar.insertBefore(categories, null);
}

function println(text, tooltip)
{
	return "<li>" + text + "</li>";
}

//convert tree structure to nested unordered list
function buildUlTree(tree)
{
	var unordered_list = '';
	for (node in tree)
	{
		node = tree[node];
		
		unordered_list += '<ul style="margin-bottom: 0">';
		unordered_list += println(node['title']);
		if(node['children'])
		{
			unordered_list += buildUlTree(node['children']);
		}
		unordered_list += '</ul>';
	}
	return unordered_list;
}