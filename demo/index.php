<?php
include '../vendor/autoload.php';

use Puja\Entity\Entity;

/**
 * This comment is copied from ContentEntity->getDocblock(); you should do it each time you change the ContentEntity->attributes
 * @method int getId()
 * @method setId(int $attr)
 * @method hasId()
 * @method unsetId()
 * @method string getName()
 * @method setName(string $attr)
 * @method hasName()
 * @method unsetName()
 * @method string getCreatedAt()
 * @method setCreatedAt(string $attr)
 * @method hasCreatedAt()
 * @method unsetCreatedAt()
 * @method Category getCategory()
 * @method setCategory(Category $attr)
 * @method hasCategory()
 * @method unsetCategory()
 */
class ContentEntity extends Entity
{
    protected $attributes = array(
        'id' => Entity::DATATYPE_INT,
        'name' => Entity::DATATYPE_STRING,
        'created_at' => Entity::DATATYPE_STRING,
        'category' => 'Category',
    );
}

class Category
{
    public $id = 1;
    public $name = 'Category 1';
}

$content = new ContentEntity(array(
    'id' => 1,
    'name' => 'Content 1',
    'created_at' => '2016-11-18 00:00:00',
    'category' => new Category()
));

echo $content->getId();
echo $content->getName();
echo $content->getCreatedAt();

$category = $content->getCategory();
echo $category->id;
echo $content->getDocblock();