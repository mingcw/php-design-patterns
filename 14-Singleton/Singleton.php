<?php
// 实例：单因素模式（Singleton Pattern）

// 示例的 Web 站点允许访问者一次购买多张 CD。因为是实时处理库存，所以在购买 CD 后立即更新库存是十分重要的。为了实现这个功
// 能，需要连接 MySQL 数据以及更新指定 CD 的数量。使用面向对象方式时，可能要创建多个不必要的与数据库的连接。如下所示，完全
// 可以选择基于单元素设计模式的库存连接：

class InventoryConnection
{
	protected static $_instance = null;
	protected $_handle   = null;

	public static function getInstance()
	{
		if (!self::$_instance instanceof self){
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	protected function __construct()
	{
		$this->handle = mysql_connect('localhost', 'usr', 'pass');
		mysql_select_db('CDs', $this->_handle);
	}

	public function updateQuantity($band, $titile, $number)
	{
		$query  = 'update CDs set amount = amount - '.intval($number);
		$query .= ' where band = "'.mysql_real_escape_string($band).'"';
		$query .= ' and title = "'.mysql_real_escape_string($title).'"';

		mysql_query($query, $this->_handle);
	}
}

// InventoryConnection 类的第一个公共方法是名为 getInstance() 的静态方法。这个方法查看受保护静态变量 $_instance 
// 是否具有类自身的一个实例。如果不具有，那么就会将类自身的一个新实例指派给 $_instance 变量。接下来，无论是需要创建新实例，
// 还是不必调用特定的方法，最后一个步骤都是返回来自 $_instance 变量的实例。
// 上面的构造函数是一个受保护方法。因此，只有指定的对象才能够调用这个构造函数。__construct() 生成与数据库的连接，并且将具体
// 实例本地存储在保护变量 $_handle 内。
// 公共方法 updateQuantity() 接受 3 个参数：乐队名、标题以及数量修改数。被创建的 MySQL 查询中会使用上述 3 个参数。最后，
// 指定对象使用内部存储的句柄来执行该查询。

// 如下所示，只要购买 CD 就会设计 InventoryConnection 类：

class CD
{
	protected $_title = '';
	protected $_band  = '';

	public function __construct($title, $band)
	{
		$this->_title = $title;
		$this->_band  = $band;
	}

	public function buy()
	{
		$inventory = InventoryConnection::getInstance();
		$inventory->updateQuantity($this->_band, $this->_title, -1);
	}
}

// 这个 CD 对象相当标准。不过，buy() 方法值得注意。首先，它通过调用 InventoryConnection 的 getInstance() 方法来获得
// 这个类的一个实例。一旦接受到该实例，就会通过调用 InventoryConnection 对象的 updateQuantity() 方法将指定 CD 的数量
// 减 1。

// 您可能很熟悉使用这些对象的样本代码：

$bounghtCDs = array (
	array('band' => 'Never Again', 'title' => 'Waste of a Rib'),
	array('band' => 'Therapee',    'title' => 'Long Road')
);

foreach ($bounghtCDs as $bounghtCD) {
	$cd = new CD($bounghtCD['title'], $bounghtCD['band']);
	$cd->buy();
}

// 在这个示例中，$boughtCDs 数组表示来自购物车的商品项。具体代码会遍历循环每个被购买的 CD。代码首先创建一个新的 CD 对象，
// 随后会请求对指定 CD 执行 buy() 方法。因为这样的操作会发生很多次，所以 InventoryConnection 对象最好是单元素对象。针对
// 每个被购买的 CD 都打开一个与数据库的新连接并不是一个好做法。

// 总结：当某个对象的实例化在整个代码流中只允许发生一次时，最佳的做法是使用单元素设计模式。
// 通过提供对自身共享实例的访问，单元素设计模式（Singleton Pattern）用于限制特定对象只能被创建一次。
