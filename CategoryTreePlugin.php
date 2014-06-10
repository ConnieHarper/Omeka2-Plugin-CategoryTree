<?php
class CategoryTreePlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_hooks = array(
		'initialize',
		'install',
		'uninstall',
		'config',
		'config_form',
		'after_save_item',
		'admin_items_show',
		'public_items_show',
		'after_delete_item',
		'admin_items_search',
		'public_items_search',
		'items_browse_sql',
	);

	protected $_filters = array('admin_items_form_tabs');

	public function hookInitialize()
	{
		get_view()->addHelperPath(dirname(__FILE__) . '/views/helpers', 'CategoryTree_View_Helper_');
	}
	
	function hookInstall()
	{
		$db = $this->_db;
		$sql = "CREATE TABLE IF NOT EXISTS `$db->CategoryRelation` (
				`id` int(10) NOT NULL auto_increment,
			    `omeka_id` int(10) NOT NULL,
				`category_id` bigint NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$db->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `$db->Category` (
			    `id` bigint NOT NULL auto_increment,
		        `title` varchar(128) collate utf8_unicode_ci NOT NULL,
			    `parent` bigint NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$db->query($sql);

		$sql = "INSERT INTO `$db->Category` (`title`, `parent`) VALUES ('Categories', '-1')";
		$db->query($sql);
	}

	function hookUninstall()
	{
		$db = $this->_db;
		$db->query("DROP TABLE IF EXISTS $db->CategoryRelation");
		$db->query("DROP TABLE IF EXISTS $db->Category");
	}

	function hookAdminItemsSearch($view)
	{
		$this->itemsSearch($view);
	}

	function hookPublicItemsSearch($view)
	{
		$this->itemsSearch($view);
	}

	function itemsSearch($view)
	{
		$table = $this->_db->getTable('CategoryRelation');
		$tree = json_encode($table->buildTree());

		//build the html that goes in the tab
		$page = "<div class='field'>";
		$page .= "<div class='two columns alpha'><label>Categories</label></div>";
		$page .= "<div class='five columns omega inputs'>";
			$page .= $this->loadEditTabScripts();
			$page .= "<input id='selCats' name='selCats' type='hidden' value='[]'>";
			$page .= "<script>var categories_tree = $tree; </script>";
			$page .= $this->javascriptFileTag(WEB_PLUGIN . '/CategoryTree/views/shared/javascript/adv_search.js');
			$page .= "<div id='tree'></div>";
		$page .= "</div></div>";
		echo $page;
	}

	function hookItemsBrowseSql($args)
	{
		if(isset($args['params']['selCats']))
			$selected_cats = json_decode($args['params']['selCats'], true);

		if(!empty($selected_cats))
		{
			$selecter = $args['select'];

			//make sure array is only numbers
			$selected_cats = preg_grep("/^\d+$/", $selected_cats);

			$cats_string = '(';
			$cats_string .= join(', ', $selected_cats);
			$cats_string .= ')';

			$selecter->joinInner(array('cr' => $this->_db->CategoryRelation),
								"cr.omeka_id = items.id AND cr.category_id in $cats_string",
								array());
		}
	}

	function hookAfterDeleteItem($item)
	{
		$table = $this->_db->getTable('CategoryRelation')->deleteRelationsByOmekaId($item['record']->id);
	}

	function hookConfig()
	{
		$edit_list = json_decode($_POST['modified-tree'], true);

		if($edit_list)
		{
			foreach ($edit_list as $edit)
			{
				switch($edit['type'])
				{
					case 0:
						$this->editTreeEdit($edit);
						break;
					case 1:
						$this->editTreeAdd($edit);
						break;
					case 2:
						$this->editTreeDelete($edit);
						break;
				}
			}
		}
	}

	function editTreeEdit($edit)
	{
		$table = $this->_db->getTable('Category');
		$table->updateRecordTitleById($edit['id'], $edit['title']);
	}

	function editTreeAdd($edit)
	{
		$table = $this->_db->getTable('Category');
		$table->addCategoryByParentId($edit);
	}

	function editTreeDelete($edit)
	{
		$table = $this->_db->getTable('Category');
		$table->deleteById($edit['id']);
	}

	function hookConfigForm()
	{
		$table = $this->_db->getTable('CategoryRelation');
		$categories = json_encode($table->buildTree());
		
		//build the html that goes in the tab
		$page = get_view()->pluginConfig($categories);
		
		echo $page;
	}

	function hookAfterSaveItem($args)
	{
		if(array_key_exists('selCats', $_POST))
		{
			$omeka_id = $args['record']->id;
			$selected_categories = json_decode($_POST['selCats']);

			$db = $this->_db;
			$table = 'CategoryRelation';

			$current_categories = $db->getTable($table)->getCategoryIdsByOmekaId($omeka_id);
			foreach($current_categories as $category)
			{
				$category->delete();
			}

			foreach($selected_categories as $category_id)
			{
				$db->insert($table,
							array(
								'omeka_id' => $args['record']->id,
								'category_id' => $category_id
							));
			}
		}
	}

	function hookPublicItemsShow($args)
	{
		$this->itemShow($args, 0);
	}

	function hookAdminItemsShow($args)
	{
		$this->itemShow($args, 1);
	}

	function itemShow($args, $admin)
	{
		$item_id = $args['item']->id;
		$table = $this->_db->getTable('CategoryRelation');
		$categories = json_encode($table->buildSubTreeForOmekaId(-1, $item_id));

		echo $this->javascript("var categories_tree = $categories;");

		if($admin)
			echo $this->javascriptFileTag(WEB_PLUGIN . '/CategoryTree/views/admin/page/show_custom.js');
		else
			echo $this->javascriptFileTag(WEB_PLUGIN . '/CategoryTree/views/public/page/show_custom.js');
	}

	function filterAdminItemsFormTabs($tabs, $args)
	{
		$item_id = $args['item']->id;

		$table = $this->_db->getTable('CategoryRelation');
		$tree = $table->buildTree(-1, $item_id);
		$json_tree = json_encode($tree);

		//build the html that goes in the tab
		$page = get_view()->adminEdit($json_tree);
		
		$tabs['Categories'] = $page;
		return $tabs;
	}

	function loadEditTabScripts()
	{
		$head = '';
		$head .= $this->javascriptFileTag(WEB_PLUGIN.'/CategoryTree/views/shared/javascript/jquery.dynatree.js');
		$head .= $this->javascriptFileTag(WEB_PLUGIN.'/CategoryTree/views/shared/javascript/jquery.ui-contextmenu.js');
		$head .= "<link href='".WEB_PLUGIN."/CategoryTree/views/shared/javascript/skin/ui.dynatree.css' rel='stylesheet' type='text/css'>";
		return $head;
	}

	function javascriptFileTag($path)
	{
		return "<script type='text/javascript' src='$path'></script>";
	}

	function javascript($code)
	{
		return "<script type='text/javascript'>$code</script>";
	}
}
?>
