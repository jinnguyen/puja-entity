# puja-entity
Puja-Entity is an abstract layer to manage a entity object and easy to get the Docblock document

<strong>Install</strong>
<pre>composer required jinnguyen/puja-entity</pre>

<strong>Usage</strong>
<pre>
include '/path/to/vendor/autoload.php';
use Puja\Entity\Entity;
</pre>

<strong>Examples</strong>
<pre>
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
        'price' => Entity::DATATYPE_INT,
        'category' => 'Category',
    );
    protected $defaults = array(
        'price' => 5,
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

echo $content->getId(); // 1
echo $content->getName(); // Content 1
echo $content->getCreatedAt(); // 2016-11-18 00:00:00
echo $content->getId(); // 5

$category = $content->getCategory();
echo $category->name; // Category 1
echo $content->getDocblock(); // The dockbock content is used above class ContentEntity
</pre>

<strong>Note</strong>: ContentEntity is Entity, but Category is not.