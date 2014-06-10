<?php
class Table_Category extends Omeka_Db_Table
{
	//Returns a branch of the Category Tree as an indexed array of records
	//ordered from the leaf node to the root node
	public function getCategoryBranch($id, $root = -1)
	{
		$record = $this->find($id);
		$categories = array($record);

		while($record->parent != $root)
		{
			$record = $this->find($record->parent);
			$categories[] = $record;
		}
		return $categories;
	}

	//returns children of a node
	public function getChildren($parent)
	{
		return $this->findBy(array('parent' => $parent));
	}

	//returns parent id of node
	public function getParent($child)
	{
		return $this->find($child)->parent;
	}

	//functions for deleting categories	
	public function deleteById($id)
	{
		$this->find($id)->delete();
	}

	//functions for updating categories
	public function updateRecordTitleById($id, $new)
	{
		$record = $this->find($id);
		if($record)
			$this->updateRecordTitle($record, $new);
	}

	private function updateRecordTitle($record, $newTitle)
	{
		$newTitle = htmlspecialchars($newTitle);
		
		$this->_db->insert($this->_target, array(
			'id' => $record->id,
			'title' => $newTitle,
		));
	}

	//functions to add category to the tree
	public function addCategoryByParentId($record) {
		$this->addCategory(array(
			'id' 		=> $record['id'],
			'title'		=> $record['title'],
			'parent'	=> $record['parent'],
		));
	}

	private function addCategory($record)
	{
		$record['title'] = htmlspecialchars($record['title']);
		
		$this->_db->insert($this->_target, array(
			'id'		=> $record['id'],
			'title' 	=> $record['title'],
			'parent' 	=> $record['parent'],
		));
	}
}
?>
