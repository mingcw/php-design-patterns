<?php
// 实例：代理模式（Proxy Pattern）

// 因为示例 Web 站点非常不错，所以 CD 店的销售不断层长。扩展已势在必行，但是 Web 站点每天都要进行正常的销售。良好的工作代码
// 实际十分简单。首先，我们要创建表示访问者能够购买的 CD 的对象。

class CD
{
	protected $_title = '';
	protected $_band  = '';
	protected $_handle = null;

	public function __construct($title, $band)
	{
		$this->_title = $title;
		$this->_band  = $band;
	}

	public function buy()
	{
		$this->_connect();

		$query  = 'update CDs set bought = 1 where band = "';
		$query .= mysql_real_escape_string($this->_band, $this->_handle);
		$query .= '" and title = "';
		$query .= mysql_real_escape_string($this->_title, $this->_handle);
		$query .= '"';

		mysql_query($query, $this->_handle);
	}

	protected function _connect()
	{
		$this->_handle = mysql_connect('localhost', 'user', 'pass');
		mysql_select_db('CDs', $this->_handle);
	}
}

// 构造函数接受 $title 和 $band 参数，并进行内部存储。
// 随后有 buy() 方法，这个方法执行具体的销售。第一个步骤是调用保护方法 $_connect()。$_connect() 方法使用适当的凭证创建
// 一个与本地 MySQL 数据的连接。接下来，buy() 方法创建了一条查询，该查询会更新 CD 记录并将其设置为已购买状态。最后，执行
// 这个查询，完成 CD 购买。

// 如下所示，购买 CD 的当前代码拥有相当明显的流线型结构：

$externalTitle = 'Waste of a Rib';
$externalBand  = 'Never Again';

$cd = new CD($externalTitle, $externalBand);
$cd->buy();

// 上面的代码创建了 CD 对象的一个新实例，随后执行了公共方法 buy()，从而完成了 CD 的购买操作。
// 因为销售形式喜人，所以我们扩展了服务器性能。现在，我们需要访问位于德克萨斯州达拉斯某处的数据。这就要求一个具有访问性能的
// Proxy 对象，该对象需要截取与本地数据库的连接，转而连接达拉斯网络运营中心（Network Operations Center）。
// Proxy 对象简单地扩展了 CD 对象，不过，还替换了功能性：

class DallasNOCProxy extends CD
{
	protected function _connect()
	{
		$this->_handle = mysql_connect('dallas', 'user', 'pass');
		mysqli_select_db('CDs', $this->_handle);
	}
}

// 保护方法 _connect() 会被 Proxy 对象重写。这个方法此时不连接 localhost，而是连接 dallas 主机。调用代码不清楚实际上
// 与代理共同工作。如下所示，调用代码只做了很小的更改：

$externalTitle = 'Waste of a Rib';
$externalBand  = 'Never Again';

$cd = new DallasNOCProxy($externalTitle, $externalBand);
$cd->buy();

// 总结：在需要截取两个对象之间的通信时，最佳的做法是使用一个基于代理设计模式的新对象。
// 代理设计模式（Proxy Pattern）构建了透明置于两个不同对象之间的一个对象，从而能够截取或代理这两个对象的通信或访问。
