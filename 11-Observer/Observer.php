<?php
// 实例：观察者模式（Observer Pattern）

// 某音乐 Web 站点具有站点访问者可用的某些社交类型功能。其中，集成的最新功能是一个活动流（Activity Stream），它能够在
// 主页上显示最近的购买情况。这种功能主要是希望人们单击最近出售的商品，从而使其可能购买相同的商品。

// 如下所示，将 CD 销售情况置入活动流的第一个步骤是创建一个基于观察者设计模式的 CD 对象：

class CD {

	public $title = '';
	public $band  = '';
	protected $_observers = array();

	public function __construct($title, $band) {
		$this->title = $title;
		$this->band  = $band;
	}

	public function attachObserver($type, $observer) {
		$this->_observers[$type][] = $observer;
	}

	public function notifyObserver($type) {
		if (isset($this->_observers[$type])) {
			foreach ($this->_observers as $observer) {
				$observer->update($this);
			}
		}
	}

	public function buy() {
		// 这是一系列的购买操作
		// ...
		
		$this->notifyObserver('purchased');
	}
}

// 构造函数中接受标题和乐队名，并将他们存储到内部。
// attachObserver() 公共方法接受两个参数。第一个参数是 $type。因为 CD 对象可能存在许多状态变化类型，所以还要通过 $type 
// 参数进一步指定通知类型。第二个参数是将要添加入保护数字 $_observers 的观察者类。注意，$type 变量决定了数组第一层的键。随
// 后，$type 指定的所有观察者类型都按顺序被添加入特定的层次。
// notifyObserver() 公共方法接受一个名为 $type 的参数，该参数用于获取保护数组的有效键，从而访问相应的每个观察者类。在这个
// 方法中执行公共方法 update() 时，需要指定对象的当前实例作为参数。
// buy() 方法只是简单地注明购买 CD 的过程。一旦执行了该方法，就会在类型设置为”purchased“的情况下调用notifyObserver() 
// 方法。

// 如下所示，随后要求某个观察者向活动流发布购买信息：

class buyCDNotifyStreamObserver {

	public function update(CD $cd) {
		$activity = "The CD named {$cd->title} by {$cd->band} was just purchased.";
		activityStream::addNewItem($activity);
	}
}

// 上面这个类具有一个名为 UPdate() 的公共方法，该方法接受 CD 实例。update() 方法只是从 CD 实例中收集信息并构建准备向活动
// 流发布的内容。

// 最后详细说明两个问题：activityStream() 类和使用观察者启动 CD 销售的方法。

class activityStream {

	public static function addNewItem($item) {
		// 这是一系列操作
		// ...
		
		print $item;
	}
}

// activityStream 类包含公共静态方法 addNewItem()，该方法只是将 $item 参数打印至屏幕。在完整的示例中，这个方法还可能将
// $item 参数写到数据库或可缓存的 XML 文件。

// 如下所示，启动销售的代码相当简单：
// （1）使用 $title 和 $band 创建一个新的 CD 对象。
// （2）实例化 buyCDNotifyStreamObserver 的一个新观察者实例。使用 attachObserver() 方法将该实例添加到 CD 对象。这个
//     实例被定义为观察者的 purchased 类型。
// （3）指定 CD 对象被购买。

$title = 'Waste of a Rib';
$band  = 'Never Again';
$cd = new CD($title, $band);
$observer = new buyCDNotifyStreamObserver();
$cd->attachObserver($observer);

$cd->buy();

// 总结：在创建其核心功能性可能包含可观察状态变化的对象时，最佳的做法是基于观察者设计模式创建与目标对象进行交互的其他类。
// 观察者设计模式（Observer Pattern）能够更便利地创建查看目标对象状态的对象，并且提供与核心对象非耦合的指定功能性。