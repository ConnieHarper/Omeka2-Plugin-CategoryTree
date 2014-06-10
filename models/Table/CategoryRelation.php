<?php
class Table_CategoryRelation extends Omeka_Db_Table
{
	//returns an array consisting of branches for each terminal category
	//accepts an optional second parameter specifying how high up the
	//branch should go. by default it goes to the root node (-1)
	public function getCategoryBranchesByOmekaId($omeka_id, $root = -1)
	{
		$records = $this->getCategoryIdsByOmekaId($omeka_id);
		$category_table = $this->_db->getTable('Category');

		$categories = array();
		foreach($records as $record)
		{
			$categories[] = $category_table->getCategoryBranch($record->category_id, $root);
		}

		return $categories;
	}

	//Returns an array of records where omeka_id = $omeka_id
	public function getCategoryIdsByOmekaId($omeka_id)
	{
        $records = $this->findBy(array('omeka_id' => $omeka_id));
		return $records;
	}

	//returns an array of leaf nodes corresponding to the categories
	//associated with $omeka_id
	public function getCategorysByOmekaId($omeka_id)
	{
		$relations = $this->getCategoryIdsByOmekaId($omeka_id);
		$category_table = $this->_db->getTable('Category');

		$categories = array();

		foreach($relations as $record)
		{
			$categories[] = $category_table->find($record->category_id);
		}

		return $categories;
	}

	//recursive function the builds a tree of the Category table
	//if given an $item_id, it will tag leaf nodes associated with
	//the categories associated with $item_id
	public function buildTree($root = -1, $item_id = NULL, $categories = NULL)
	{
		//Get categorys list
		if(isset($item_id) && $item_id != NULL)
		{
			$categories = $this->getCategoryBranchesByOmekaId($item_id, $root);
		}

		$table = $this->_db->getTable('Category');

		//get all nodes belonging to root and start to build tree
		$tree = $table->getChildren($root);

		if($tree != NULL)
		{
			foreach($tree as $node)
			{
				//if the item has a category go through them
				if(isset($categories) && $categories != NULL)
				{
					foreach($categories as $branch)
					{
						foreach($branch as $record)
						{
							//and check the corresponding box
							if($record->id == $node->id)
							{
								$node['select'] = true;
								$node['expand'] = true;
							}
						}
					}
					$node['children'] = $this->buildTree($node->id, null, $categories);
				}
				else
				{
					$node['children'] = $this->buildTree($node->id);
				}
				$node['key'] = strtolower($node->title);
			}
		}
		return $tree;
	}
	
	//builds a sub tree of nodes only affiliated with the item id
	function buildSubTreeForOmekaId($root = -1, $item_id = NULL, $categories = NULL)
	{
		$tree = $this->buildSubTreeForOmekaIdHelper($root, $item_id); //builds the big tree
		$tree = $this->trimTree($tree); //cuts off excess nodes
		return $tree;
	}
	
	//builds a tree of the DB, tagging nodes affiliated with the item_id
	private function buildSubTreeForOmekaIdHelper($root = -1, $item_id = NULL, $categories = NULL)
	{
		//Get categories list
		if(isset($item_id) && $item_id != NULL)
		{
			$categories = array_reverse($this->getCategoryBranchesByOmekaId($item_id, $root));
		}

		$table = $this->_db->getTable('Category');

		//get all nodes belonging to root and start to build tree
		$tree = $table->getChildren($root);

		if($tree !== NULL)
		{
			foreach($tree as $index => $node)
			{
				//if the item has a category go through them
				if(isset($categories) && $categories != NULL)
				{
					foreach($categories as $branch)
					{
						foreach($branch as $record)
						{
							//and check the corresponding box
							if($record->id == $node->id)
							{
								$node['good'] = true;
							}
						}
					}
					$node['children'] = $this->buildSubTreeForOmekaIdHelper($node->id, null, $categories);
				}
				$node['key'] = strtolower($node->title);
			}
		}
		return $tree;
	}

	//deletes all references to a category ID
	//for when a category is deleted
	public function deleteRelationsByCategoryId($id)
	{
		$relations = $this->findBy(array('category_id' => $id));
		foreach($relations as $r)
			$r->delete();
	}

	//deletes references to an omeka_id
	//for when an item is deleted
	public function deleteRelationsByOmekaId($id)
	{
		$relations = $this->findBy(array('omeka_id' => $id));
		foreach($relations as $r)
			$r->delete();
	}
	
	//trims nodes off a tree if $node->good is not true
	private function trimTree($tree)
	{
		if($tree != NULL)
		{
			foreach($tree as $key => $val)
			{
				if($val->good != true){
					unset($tree[$key]);
				}else{
					$tree[$key]->children = $this->trimTree($val->children);
				}
			}
		}
		return $tree;
	}
}
?>