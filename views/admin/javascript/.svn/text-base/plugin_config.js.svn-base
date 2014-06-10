jQuery.ui.dynatree.nodedatadefaults['icon'] = false;
var ICONS_PATH = "../../plugins/ContentTypeTree/views/shared/javascript/icons/";
var changes = new Array();

jQuery(function(){
	// Attach the dynatree widget to an existing <div id=\"tree\"> element
	// and pass the tree options as an argument to the dynatree() function:
	jQuery("#tree").dynatree({
		onExpand: onExpand,
        selectMode: 1,
		children: categories_tree,
		fx: { height: "toggle", duration: 200 }
    });

	//left click on right click
	//this is to ensure a node is selected
	jQuery("#tree").mousedown(function(e){
		if(e.button == 2) e.target.click();
	});

	attachContextMenu();
});

//when you expand a dynatree node
function onExpand()
{
	attachContextMenu();
}

//attach a context menu to every dynatree node
function attachContextMenu()
{
	jQuery(document).contextmenu({
		delegate: ".dynatree-node",
		menu: [
			{title: "Rename", action: contextEdit},
			{title: "Add Sub-Category",	action: contextAdd},
			{title: "Delete", action: contextDelete}
		]
	});
}

function buttonAddRoot()
{
	var node = jQuery("#tree").dynatree("getRoot");
	if(node)
	{
		var title = prompt("Enter the new Node title:", null);
		if(title)
		{
			node = node.addChild({
				title:title,
				parent: -1,
				key:title,
				id:new Date().getTime(),
			});

			changes.push({
				type:1,
				id:node.data.id,
				parent:node.data.parent,
				title:node.data.title,
			});
			// console.log(changes);
			jQuery("#modified-tree")[0].value = JSON.stringify(changes);

			attachContextMenu();
		}
	}
}

function contextEdit()
{
	var node = jQuery("#tree").dynatree("getActiveNode");
	if(node)
	{
		var title = prompt("Enter a new Node title:", node.data.title);
		if(title)
		{
			node.data.title = title;
			node.data.key = node.data.title;
			node.render();
			changes.push({
				type:0,
				id:node.data.id,
				title:node.data.title,
			});
			jQuery("#modified-tree")[0].value = JSON.stringify(changes);
		}
	}
}

function contextAdd()
{
	var node = jQuery("#tree").dynatree("getActiveNode");
	if(node)
	{
		var title = prompt("Enter the new Node title:", null);
		if(title)
		{
			node = node.addChild({
				title:title,
				parent: node.data.id,
				key:title,
				id:new Date().getTime(),
			});

			node.activate();

			changes.push({
				type:1,
				id:node.data.id,
				parent:node.data.parent,
				title:node.data.title,
			});
			jQuery("#modified-tree")[0].value = JSON.stringify(changes);

			attachContextMenu();
		}
	}
}

function contextDelete()
{
	var node = jQuery("#tree").dynatree("getActiveNode");
	if(node)
	{
		var conf = 	"Are you sure you want to delete this category?\n\n" +
					"All items associated with this category will no longer be associated with it.";
					
		if(confirm(conf))
		{
			changes.push({
				type:2,
				id:node.data.id,
			});
			jQuery("#modified-tree")[0].value = JSON.stringify(changes);

			node.remove();
		}
	}
}
