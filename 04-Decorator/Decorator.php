<?php
// 实例：装饰器模式（Decorator Pattern）

// 示例中，应用程序对光盘（Compact Disc，CD）进行处理。应用程序必须具有为 CD 添加音轨的方法以及显示 CD 音轨列
// 表的方式。客户指定了应当采用单行并且每个音轨都必须以音轨号为前缀的方式显示 CD 音轨列表。

class CD {

	public $trackList;

	public function __construct() {
		$this->trackList = array();
	}

	public function addTrack($track) {
		$this->trackList[] = $track;
	}

	public function getTrackList() {
		$output = '';

		foreach ($output as $num => $track) {
			$output .= ($num + 1) . ") {$track}. ";
		}

		return $output;
	}
}

// CD 类包含一个名为 $trackList 的公共变量，该变量用来存储给 CD 对象添加的音轨数组。构造函数里初始化了这个变
// 量。
// addTrack() 方法为 CD 对象的 $trackList 数组添加一个音轨。
// 最后一个 getTrackList() 方法将遍历 CD 上的每个音轨，并将这些音轨按指定格式编辑为单个字符串。

// 为了使用该 CD 对象，需要执行以下代码：
$trackFromExternalSource = array('What It Means', 'Brr', 'Goodbye');

$myCD = new CD();

foreach ($trackFromExternalSource as $track) {
	$myCD->addTrack($track);
}

echo 'The CD contains the following tracks: ' . $myCD->getTrackList();

// 实际上，上述代码已经很好地解决了问题。但是，这个时候客户需求发生了小变化：只针对这个输出实例，输出的每个音轨都
// 需要采用大写形式。对于这种小变化而言，最佳的做法并非修改基类或创建父-子关系，而是创建一个基于装饰器设计模式的
// 对象。

class CDTrackListDecoratorCaps {

	private $__cd;

	public function __construct(CD $cd) {
		$this->__cd = $cd;
	}

	public function makeCaps() {
		foreach ($this->__cd->trackList as $track) {
			$track = strtoupper($track);
		}
	}
}

// CDTrackListDecoratorCaps 类非常简单。构造函数里接受了 CD 实例并存储到内部的私有变量 $__cd。
// makeCaps() 方法存在与装饰器内，它可以执行执行所需的修饰或更改。在这个实例中，该方法会遍历 CD 里的每个音轨，
// 并对它们执行 PHP 的 strtoupper() 函数。

// 现在，为了在原有的示例中加入装饰器，需要添加新的 CDTrackListDecoratorCaps 类：

$myCD = new CD();

foreach ($trackFromExternalSource as $track) {
	$myCD->addTrack($track);
}

$myCDCaps = new CDTrackListDecoratorCaps($myCD);
$myCDCaps->makeCaps();

echo 'The CD contains the following tracks: ' . $myCD->getTrackList();

// 主代码流中只添加两行代码就完成了这个很小的变化。通过引用已有的 CD 对象实例化装饰器 
// CDTrackListDecoratorCaps()，就可以创建 $myCDCaps 变量。随后，调用 makeCaps() 方法就可以执行这个稍有
// 变化的功能性，只针对该 CD 对象而并非整个类。

// 总结：为了在不修改对象结构的前提下，对现有对象的内容或功能性结构稍加修改，就应当使用装饰器设计模式。
// 装饰器模式（Decorator Pattern）（又称包装器模式，Wrapper Pattern）可以在不修改原始对象结构的前提下，或者
// 说在不修改整个类结构的前提下，只针对已有对象的部分内容或功能性进行更改或修饰。