<?php
// 实例：原型模式（Prototype Pattern）

// 音乐销售 Web 站点允许登录站点的艺术家创建许多乐队的音乐“合辑”。目前，这种功能性还限制为所有有效的音轨只能来自一个乐队。
// 为了开始创建 CD “合辑”，我们可以采用多种途径。其中，在访问者查看某乐队的 CD 页面的时候可以使用最通用的选项。CD 页面存
// 在的某个链接能够启动构建新 CD “合辑”的进程，该进程会发送一个 ID，这个 ID 对应于乐队的特定 CD。

// 上面这个进程的第一个构建代码块是 CD 类。通常，为了构建 CD 对象，需要从数据库中检索出与被请求 ID 匹配的具体信息:

class CD
{
	public $band      = '';
	public $title     = '';
	public $trackList = array();

	public function __construct($id)
	{
		$handle = mysql_connect('locahost', 'user', 'pass');
		mysql_select_db('CD', $handle);

		$query = "select band, title from CD where id = {$id}";
		$results = mysql_query($query);
		if ($row = mysql_fetch_assoc($results)) {
			$this->band = $row['band'];
			$this->title = $row['title'];
		}
	}

	public function buy()
	{
		// 具体的购买操作
		// ...
		// ...
		
		var_dump($this);
	}
}

// 这个类具有标准的公共属性 $band, $title 和 $trackList。
// 构造函数接受一个参数 $id，并针对数据库执行查询。当发现指定的 ID 时，相应的乐队名和标题就会被分别指派给公共属性 $band 和
// $title。
// 此外，CD 类还增加了一个名为 buy() 的方法。这个很短的方法只用于示例。在最终的产品代码中，该方法可以处理 CD 对象并提供用于
// 购买的具体操作。

// 下一个要创建的类代表混合 CD 示例。如下所示，这种特定的对象利用了 PHP 的克隆能力：

class MixtapeCD extends CD
{
	public function __clone()
	{
		$this->title = 'Mixtape';
	}
}

// 因为 MixtapeCD 实际只是一种特殊化的 CD，所以它扩展了 CD 对象。
// 执行 PHP 的 clone 命令时，就会对指定的对象执行奇妙的 __clone() 方法。在 MixtapeCD 对象中，初始 CD 的 title 属性会
// 被重写。这个 MixtapeCD 对象不再对应于一个乐队和标题的 CD 组合。此时，该对象仍然关联了 $band，但是具有新的标题
// Mixtape。

// 下面这个示例展示了某位用户基于指定乐队定制两个混合标题的情况：

$externalPurchaseInfoBandID = 12;
$bandMixProto = new MixtapeCD($externalPurchaseInfoBandID);

$externalPurchaseInfo = array(
	array('brr', 'goodbye'),
	array('what it means', 'brr')
);

foreach ($externalPurchaseInfo as $mixed) {
	$cd = clone $bandMixProto;
	$cd->trackList = $mixed;
	$cd->buy();
}

// $bandMixProto 对象是根据 MixtapeCD 的新实例创建的。传入该对象的参数 $externalPurchaseInfoBandID 被用于实际由
// CD 类构造函数执行的查询。
// 一旦创建了原型 $bandMixproto，就能够循环遍历用于特定访问者的 CD 合辑的音轨列表。对于 foreach() 循环的每个实例来说，
// $cd 都被指派为 $bandMixProto 的一个新副本。接下来，特定的曲目列表会被添加入指定的对象。因为使用了克隆技术，所以每个
// 新的实例都不需要针对数据库的新查询。克隆对象已经存储了所有信息，并且可以采用与原始对象相同的方式使用这些信息。最后，通过
// 执行公共方法 buy() 就可以购买指定的 $cd 对象。

// 总结：处理创建成本较高或新实例的初始信息保持相对不变的对象时，最佳的做法是使用基于原型设计模式创建的复制类。
// 原型设计模式（Prototype Pattern）创建对象的方式是复制和克隆初始对象或原型，这种方式比创建新实例更为有效。
