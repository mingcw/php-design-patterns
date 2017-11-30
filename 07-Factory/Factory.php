<?php
// 实例：工厂模式（Factory Pattern）

// 为了管理控制 CD，应用程序需要编辑一些信息给 CD 对象。然后把 CD 对象传递给外部供应商，让他们来完成实际的 CD 
// 对象创建工作。CD 对象需要标题、乐队名称和曲目列表。

// 下面是一个简单的 CD 类。它包含了添加标题、乐队和曲目列表的方法。

class CD {

	public $title  = '';
	public $band   = '';
	public $tracks = array();

	public function __construct() {

	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function setBand($band) {
		$this->band = $band;
	}

	public function addTrack($track) {
		$this->tracks[] = $track;
	}
}

// 为了创建完整的 CD 对象，处理过程都是相同的。创建 CD 实例，添加标题、乐队和曲目列表。

$title = 'Waste of a Rib';
$band  = 'Never Again';
$tracksFromExternalSource = array('What It Means', 'Brr', 'Goodbye');

$cd = new CD();
$cd->setTitle($title);
$cd->setBand($band);
foreach ($trackFromExternalSource as $track) {
	$cd->addTrack($track);
}

// 这样一个标准的 CD 对象就生成了。然而，有些艺术家在他们的 CD 上发布了在计算机中能使用的其他内容。这些 CD 被称
// 为增强型 CD —— 写入光盘的第一个音轨是数据音轨。管理控制软件通过其标签 DATA TRACK 来识别数据音轨，并创建相应
// 的 CD 对象。

class enhancedCD {

	public $title  = '';
	public $band   = '';
	public $tracks = array();

	public function __construct($type) {
		$this->tracks[] = 'DATA TRACK';
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function setBand($band) {
		$this->band = $band;
	}

	public function addTrack($track) {
		$this->tracks[] = $tracks;
	}
}

// 看到上述两种 CD 的共性之后，留意到我们似乎只需要创建一些条件语句。如果是增强型 CD，那么创建 enhancedCD 类的
// 实例，否则创建通用型的 CD。然而，还存在更好的解决方案：使用工场设计模式。


// CDFactory 类使用了 PHP 根据变量动态实例化一个类的能力。create 方法接受一个被请求类的类型，并返回该类的一个
// 实例。

class CDFactory {

	public static function create($type) {
		$class = $type.'CD';

		return new $class;
	}
}

// 现在，类的创建和执行的变化反映了 Factory 类的用法：

$type = 'enhanced';

$cd = CDFactory::create($type);
$cd->setTitle($title);
$cd->setBand($band);
foreach ($trackFromExternalSource as $track) {
	$cd->addTrack($track);
}

// 最后需要考虑的可能是已有 CD 类的名称。为了使其统一，将 CD 类的类名改为 standardCD 也许更好，更具有实际
// 意义。确认这样改动不会破坏其他位置的其他功能性。此时，CD 类新实例的创建最后也是用 CDFactory 类来实现。

// 总结：请求需要某些逻辑和步骤才能确定基对象的类实例时，最佳的做法是使用一个基于工厂设计模式的类。
// 工厂设计模式（Factory Pattern）提供某个对象新实例的统一接口，同时使调用代码避免了确定实际实例化基类的步骤。