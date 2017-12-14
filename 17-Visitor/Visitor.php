<?php
// 实例：访问者模式（Visitor Pattern）

// 为了进行审计，电子上午 Web 站点上所有新的 CD 购买情况都必须被记录下来。这些信息随后会被归档。在库存数量不一致的情况下，
// 就可以通过检查日志记录文件来查看指定的 CD 是否已被购买。

// 为了实现这个功能，CD 对象必须接受访问者：

class CD
{
	public $title;
	public $band;
	public $price;

	public function __construct($title, $band, $price)
	{
		$this->title = $title;
		$this->band  = $band;
		$this->price = $price;
	}

	public function buy()
	{
		// 购买逻辑
		// ...
	}

	public function acceptVisitor($visitor)
	{
		$visitor->visitCD($this);
	}
}

// CD 对象接受实例化 CD 的标题、乐队和价格，上面的构造函数将这些信息分别应用于公共属性 $title, $band 和 $price。
// CD 对象具有一个名为 buy() 的公共方法。示例中省略了购买逻辑。
// CD 对象还具有另一个方法 acceptVisitor()。为了遵从访问者设计模式，该方法是必需的。这个方法从 $visitor 参数接受一个
// 访问者的实例。acceptVisitor() 方法内部会调用访问者的公共方法 visitCD()，该方法使用 $this 变量传递 CD 类的一个实例。

// 日志记录访问者包含下面的代码：

class CDVisitorLogPurchase
{
	public function visitCD($cd)
	{
		$logline = "{$cd->title} by {$cd->band} was purchased for {$cd->price}\n";

		file_put_contents('/logs/purchases.log', $logline, FILE_APPEND);
	}
}

// 上面的类包含一个从 CD 类中调用的公共方法 visitCD()。这个方法接受名为 $cd 的参数，该参数是 CD 对象的一个实例。通过结合
// 日志记录信息与 CD 对象的某些公共方法，接下来又创建了 $logline 变量。当然，日志记录也可以在 buy() 方法的内部进行。最终
// $logline 变量被写入日志文件。

// 为了购买指定的 CD 以及记录销售情况，需要使用如下所示的代码：

$externalTitle = 'Waste of a Rib';
$externalBand  = 'Never Again';
$externalPrice = 9.99;

$cd = new CD($externalTitle, $externalBand, $externalPrice);
$cd->buy();
$cd->acceptVisitor(new CDVisitorLogPurchase());

// 首先创建了具有 CD 属性的 CD 对象。随后调用了 buy() 方法。最后，CD 对象接受 CDVisitorLogPurchase 形式的 visitor 
// 对象，并将该对象传递到 acceptVisitor() 方法内。最后一步调用实质上执行了日志记录。

// 近期公布的研究表名，查看主页的访问者实际上寻找的是打折的 CD，而不是原价 CD。因此，需要在标题板显示打折 CD 的实时更新列表。
// 约定，价格低于 10 美元的 CD 被认为是打折 CD。

// 为了完成该任务，需要创建一个新的访问者：

class CDVisitorPopulateDiscountList
{
	public function visitCD($cd)
	{
		if ($cd->price < 10) {
			$this->_populateDiscountList($cd);
		}
	}

	protected function _populateDiscountList($cd)
	{

	}
}

// CDVisitorPopulateDiscountList 类也具有公共方法 visitCD()。与其他访问者一样，该方法也通过使用参数 $cd 接受 CD 对象
// 的一个实例。如果 CD 对象的价格属性小于 10，那么就会调用受保护方法 $_populateDiscountList()。CD 对象的实例会被传递到
// 这个函数。
// _populateDiscountList() 只在主访问逻辑确定 CD 为打折商品时才被调用，这个特别的实例将该方法表示为分支算法。不过，在实际
// 的工作类中，打折 CD 的详细信息会别写入某个数据库或 XML 文件，访问者可以通过标题板获取这些信息。

// 加入新访问者的购买代码如下所示：

$cd = new CD($externalTitle, $externalBand, $externalPrice);
$cd->buy();
$cd->acceptVisitor(new CDVisitorLogPurchase());
$cd->acceptVisitor(new CDVisitorPopulateDiscountList());

// 当需要的对象包含以标准方式应用于某个对象的算法时，最佳的做法是使用访问者设计模式。
// 访问者设计模式（Visitor Pattern）构造了包含某个算法的截然不同的对象，在”父对象“以标准方式使用这些对象时就会将该算法应用于
// “父对象“。【