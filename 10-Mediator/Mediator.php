<?php
// 实例：中介者模式（Mediator Pattern）

// 示例 Web 站点不仅允许乐队管理和更新他们的音乐 CD，也允许艺术家上传 MP3 集合并从 Web 站点上撤下 CD。因此，Web 站点需
// 要保持相对应的 CD 和 MP3 彼此同步。

// 如下，CD 对象具有一个接受乐队变化以及在数据库中进行更新的方法：

class CD
{
	public $band  = '';
	public $title = '';

	public function save()
	{
		// 根据当前对象属性，更新数据库
		// ...
		// ...
		// ...
		
		var_dump($this);		
	}

	public function changeBandName($newName)
	{
		$this->band = $newName;
		$this->save();
	}
}

// 上面这个简单的类只是说明了 CD 对象可以具有乐队和标题。接下来，方法 changeBandName() 接受一个新的乐队名参数并调用 
// save() 同步更新到数据库。save() 方法只作演示，这里并没有进一步说明。

// 为了添加 MP3 归档文件，就需要创建另一个类似的对象来处理归档文件。艺术家必须也能在 MP3 归档文件页面上修改其乐队名。同样，
// 在与之关联的 CD 中也必须能够修改乐队名。

// 现在应当使用在中介者设计模式。首先，为了使用该模式，必须修改 CD 类。随后，创建与 CD 类类似的 MP3 归档类。

class CD
{
	public $band  = '';
	public $title = '';
	protected $_mediator;     // 保存中介对象的引用

	public function __construct($mediator = null)
	{
		$this->_mediator = $mediator;
	}

	public function save()
	{
		// 根据当前对象属性，更新数据库
		// ...
		// ...
		// ...
		
		var_dump($this);
	}

	public function changeBandName($newName)
	{
		if (!is_null($this->_mediator)) { // 通知中介者更新所有对象
			$this->_mediator->change($this, array('band' => $newName));
		}
		$this->band = $newName;
		$this->save();
	}
}

class MP3Archive
{
	public $band  = '';
	public $title = '';
	protected $_mediator;     // 保存中介对象的引用

	public function __construct($mediator)
	{
		$this->_mediator = $mediator;
	}

	public function save()
	{
		// 根据当前对象属性，更新数据库
		// ...
		// ...
		// ...
		
		var_dump($this);
	}

	public function changeBandName($newName)
	{
		if (!is_null($this->_mediator)) { // 通知中介者更新所有对象
			$this->_mediator->change($this, array('band' => $newName));
		}
		$this->band = $newName;
		$this->save();
	}
}

// 对 CD 对象的第一个改变是添加了一个名为 $_mediator 的保护变量，该变量用来存储中介对象的实例。CD 类中添加了构造函数。
// 创建 CD 类的一个实例时，新的中介者对象应当被传递到这个类内。不过需要注意的是，$_mediator 变量的默认值为 null。这不仅
// 允许我们创建用于只读功能的、实例中没有中介者的对象，而且也确保在使用中介者对象更新其他类时不会创建无限的循环。接下来，
// changeBandName() 方法也被修改了。该方法用于查看中介者对象是否存在和不为空。如果存在且不为空，那么就会调用中介者对象的
// change() 方法，从而传递入自身的实例并修该指定项的键控数组。

// 上述操作在对中介者对象本身应用更新之前发生。对于中介者来说，在修改指定项前获取其快照是十分重要的。

// MP3Archive 对象几乎与 CD 对象完全相同。

// 如下所示，随后需要创建中介者类：

class MusicContainerMediator
{
	protected $_containers = array();

	public function __construct()
	{
		$this->_containers[] = 'CD';
		$this->_containers[] = 'MP3Archive';
	}

	public function change($originalObject, $newValue)
	{
		$title = $originalObject->title;
		$band  = $originalObject->band;

		foreach ($this->_containers as $container) {
			if(!($originalObject instanceof $container)) {
				$object = new $container;
				$object->title = $title;
				$object->band  = $band;

				foreach ($newValue as $key => $val) {
					$object->$key = $val;
				}

				$object->save();
			}
		}
	}
}

// 中介者对象知道其将要中介调解的所有音乐容器（Music Container）。构造函数用于构建将要中介调解的对象的内部数组。如果今后创
// 建了新的音乐容器，那么中介者类中所需的唯一变化就是在构造函数内对保护数组 $_containers 添加一个新的元素。

// change() 方法将接受原始对象以及将要添加的新值。首先，原始对象中的标题和乐队名会被检索出来。接着，所有音乐容器会被循环遍
// 历。如过 $originalObject 不是这些容器的实例，那么就会创建相应的容器，那么就会创建相应的容器。将指定容器与原始对象进行
// 比较的原因是为了减少重复。完成对中介者的操作之后，变化就会应用于原始对象。此时，我们不需要创建和更改原始对象的副本，从而
// 跳过了循环。

// 如果指定容器被创建为一个新的对象，那么就要根据原始对象设置标题和乐队名。最后，$newValue 的任何变化都会被循环遍历，然后
// 应用于这个新的对象，接着该对象才会被保存。

// 上述整个过程完成之后，原始对象就会应用自己的更新并自动保存。

// 使用新的中介者对象，所采用的代码十分简单，如下所示：

$titleFromeDB = 'Waste of a Rib';
$bandFromDB   = 'Never Again';
$mediator = new MusicContainerMediator();
$cd = new CD($mediator);
$cd->title = $titleFromeDB;
$cd->band  = $bandFromeDB;

$cd->changeBandName('Maybe Once More');

// 总结：处理具有类似属性并且属性需要保持同步的非耦合对象时，最佳的做法是使用基于中介者设计模式的对象。
// 中介者设计模式（Mediator Pattern）用于开发一个对象，这个对象能够在类似对象相互之间不直接交互的情况下传达或调解对这些
// 对象的集合的修改。
