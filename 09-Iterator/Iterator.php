<?php
// 实例：迭代器模式（Iterator Pattern）

// 示例 Web 站点的部分工作是显示特定艺术家或乐队的所有 CD。这些信息存储在一个 MySQL 数据库内。某些访问者可能希望
// 根据乐队名搜索数据库，并且得到特定艺术家已发布的所有 CD 的概述。这是实际应用迭代器设计模式的一个优秀实例。

// 首先，半标准 CD 类如下所示：

class CD {

	public $band      = '';
	public $title     = '';
	public $trackList = array();

	public function __construct($band, $title) {
		$this->band  = $band;
		$this->title = $title;
	}

	public function addTrack($track) {
		$this->trackList[] = $track;
	}
}

// 在这个 CD 示例中，乐队、标题和曲目列表具使用了公共变量。构造函数里创建了实例，同时在内部指派了乐队和标题。
// addTrack() 函数接受 $track 变量，用以添加一个音轨到曲目列表。

// 下面创建的是迭代器类。这个示例实现了 SPL 迭代器。因此，我们要求其具有 current()、key()、rewind()、next()
// 和 valid() 公共方法（PHP Iterator 接口手册：http://php.net/manual/zh/class.iterator.php）。

class CDSearchByBandIterator implements Iterator {

	private $__CDs   = array();
	private $__valid = FALSE;

	public function __construct($bandName) {
		$db = mysql_connect('localhost', 'root', 'root');
		mysql_select_db('test');

		$sql  = 'select CD.id, CD.band, CD.title, tracks.tracknum, tracks.title as tracktitle';
		$sql .= ' from CD';
		$sql .= ' left join tracks on CD.id = tracks.cid';
		$sql .= ' where band = '.mysql_real_escape_string($bandName);
		$sql .= ' order by tracks.tracknum';
		$results = mysql_query($sql);

		while ($result = mysql_fetch_array($results, MYSQL_ASSOC)) {			
			$cd = new CD($result['band'], $result['title']);
			$cd->addTrack($result['tracktitle']);

			$this->__CDs[] = $cd;
		}
	}

	public function rewind() {
		$this->__valid = (rewind($this->__CDs) === FALSE) ? FALSE : TRUE;
	}

	public function next() {
		$this->__valid = (next($this->__CDs) === FALSE) ? FALSE : TRUE;
	}

	public function valid() {
		return $this->__valid;
	}

	public function current() {
		return current($this->__CDs);
	}

	public function key() {
		return key($this->__CDs);
	}
}

// 与其他设计模式中使用的类相比，上面这个迭代器类相当冗长。不过，为了恰当地说明迭代器，尤其是 SPL 迭代器的实现，这是十分必
// 要的。虽然代码很长，但并不复杂。

// CDSearchByBandIterator 类被设计为返回一个对象，使用 PHP 数组的某些函数就可以访问这个对象。注意到每个迭代器并不需要
// 实现 SPL 迭代器，这是十分重要的。不过，在这个示例中，我们很容易发现这个问题。

// 迭代器类具有 2 个私有变量，其中 $__CDs 是一个包含 CD 对象集合的数组；$__valid 是数组访问函数使用的变量，通常，这个变
// 量说明了集合中是否存在要处理的可用对象。

// __construct() 方法接受一个名为 $bandName 的参数。在实例化时，会连接数据库，查询与 $bandName 匹配的所有 CD 和音轨
// 的 MySQL 结果集，并一一构建对象数组，存到 $__CDs 私有变量。

// CD 和音轨的风格是规格化的，这意味着检索结果集时存在相同 CD 的许多记录，并且具有相同的标题，但音轨名是不同的。如果具体的
// 关系是一个数据记录对一个于一个 CD 对象，那么编程人员极有可能不会创建迭代器对象。

// 接下来 CDSearchByBandIterator 类实现了 PHP 预定义的迭代器（Iterator）接口中定义的几个抽象方法：

// 1. rewind() 方法返回到迭代器的第一个元素。
// 2. next() 方法移动当前位置到下一个元素。
// 3. valid() 方法检查当前位置是否有效。
// 4. current() 返回当前元素。
// 5. key() 返回当前元素的键。

// rewind() 和 next() 公共方法功能类似，对内部 $__CDs 对象数组执行 PHP 的内建函数，如果执行失败返回 FALSE，并将该
// 结果同步到 $__valid 变量，用以记录迭代器内部指针移动之后，当前位置是否有可用元素。
// valid() 方法比较简单，使用这个方法时，实现迭代器类是必需的。它返回当前位置是否有可用元素。
// current() 和 key() 方法返回当前元素和当前元素的键。

// 使用迭代器类时所采用的代码十分常见。CDSearchByBandIterator 迭代器类的所有方法就像一个数组。遍历执行这些方法后，就会
// 返回代码期望的 CD 对象。

$queryItem = 'Never Again';
$cds = new CDSearchByBandIterator($queryItem);

print '<h1>Found the Following CDs</h1>';
print '<table><tr><th>Band</th><th>Title</th><th>Num Tracks</th></tr>';
foreach ($cds as $cd) {
	print "<tr><td>{$cd->band}</td><td>{$cd->title}</td><td>".count($cd->trackList)."</td></tr>";
}
print '</table>';

// 所以可以说迭代器就是为了给 foreach 提供遍历的一种接口。

// 总结：处理可计数和可遍历数据的集合时，最佳的做法是创建基于迭代器设计模式的对象。
// 迭代器设计模式（Iterator Pattern）可帮助构造特定对象，那些对象能够提供单一标准接口循环或迭代任何类型的可计数数据。