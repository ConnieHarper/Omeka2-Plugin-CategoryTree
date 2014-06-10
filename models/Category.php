<?php
class Category extends Omeka_Record_AbstractRecord
{
	public $id;
	public $title;
	public $parent;

	//String representation of record
	public function __toString()
	{
		return "ID: $this->id, Title: $this->title, Parent: $this->parent";
	}

	//get the Omeka IDs of all objects that are classified under this category
	public function getAssociations()
	{
		$assocs = $this->_db->getTable('CategoryRelation')->findBy(array(category_id => $this->id));
		foreach($assocs as $k => $v)
		{
			$assocs[$k] = $v->omeka_id;
		}
		return $assocs;
	}

	//make sure that all children of
	//this record are deleted when this is deleted
	//also remove relations from CaterogyRelations
	public function afterDelete()
	{
		$this->getTable('CategoryRelation')->deleteRelationsByCategoryId($this->id);

		$children = $this->getTable('Category')->findBy(array('parent' => $this->id));
		foreach($children as $c)
			$c->delete();
	}
}
?>
