<?php
// 实例：模板模式（Template Pattern）

// 示例的电子商务 Web 站点已进行了扩展。现在，该站点的访问者能够定制许多新的商品项，包括划分了等级的谷类食品。此时，我们可以
// 创建拥有自身价格及附加税率的类。过去可引用统一的税率，然而现在的食品销售，每个类都有适用于自身的税率。此外，某些商品项相当
// 大。例如，某些产品还要求涉及附加费用的处理，这些费用会加入最后的购买价。

// 如下所示，第一个步骤是定义使用模板设计模式来处理任何销售项的基类：

abstract class SaleItemTemplate
{
	public $price = 0;

	final public function setPriceAdjustments()
	{
		$this->price += $this->oversizedAddition();
		$this->price += $this->taxAddition();
	}

	protected function oversizedAddition()
	{
		return 0;
	}

	abstract protected function taxAddition();
}

// SaleItemTemplate 是一个简单类，也是一个抽象类，因此能实施要扩展的需求。公共属性 $price 被设为 0。这个类的公共方法为
// setPriceAdjustments()，该方法是一个最终方法，所以不允许被任何子类重写。通过调用 oversizedAddition() 和 
// taxAddition() 方法，setPriceAdjustments() 实施了对价格的更改。
// 因为子类能够选择定义受保护方法 oversizedAddition()，所以该方法位于这个模板类内。由于大多数商品项都不会太大，所以没有
// 必要在所有子类中创建将要返回 0 的 oversizedAddition() 方法。
// 最后，模板类还存在受保护的抽象方法 taxAddition()。子对象有可能需要纳税，也可能不需要纳税。虽然某些商品项需要定义由于
// 过大所带来的附加费用，但是更多的商品项需要定义是否纳税。因此，这个方法被从黄建为在子对象元素中实现其创建的抽象方法。

// 下一个步骤是更改 CD 对象的常规状态以及一个新的谷类食品对象：

class CD extends SaleItemTemplate
{
	public $title;
	public $band;

	public function __construct($title, $band, $price)
	{
		$this->title = $title;
		$this->band  = $band;
		$this->price = $price;
	}

	protected function taxAddition()
	{
		return round($this->price * .05, 2);
	}
}

class BandEndorsedCaseOfCereal extends SaleItemTemplate
{
	public $band;

	public function __construct($band, $price)
	{
		$this->band  = $band;
		$this->price = $price;
	}

	protected function taxAddition()
	{
		return 0;
	}

	protected function oversizedAddition()
	{
		return round($this->price * .20, 2);
	}
}

// CD 类扩展了 SaleItemTemplate，其构造函数设定了指定对象的等级、标题和价格。因为 taxAddition 被定义为  
// SaleItemTemplate 中的抽象方法，所以在 CD 类中定义具体实现。在这里， taxAddition() 方法计算了知道国内对象价格的 
// 5% 税率并返回计算值。
// BandEndorsedCaseOfCereal 类也扩展了 SaleItemTemplate，其构造函数设置了与 CD 类类似的等级和价格。接下来，
// BandEndorsedCaseOfCereal 类也实现了 taxAddition() 方法。此时，因为商品项与食品相关，所以不存在税率，
// taxAddition() 方法返回 0。不过，谷类食品是一种相当大的商品项。考虑到这个因素，总价格需要添加 20% 的附加费。
// BandEndorsedCaseOfCereal 中存在的受保护方法 oversizedAddition() 会重写父类模板中定义的同一方法，最后的返回值是当
// 前价格的 20%。

// 下面的代码说明了如何使用这些类：

$externalTitle       = 'Waste of a Rib';
$externalBand        = 'Never Again';
$externalCDPrice     = 12.99;
$externalCerealPrice = 90;

$cd = new CD($externalTitle, $externalBand, $externalCDPrice);
$cd->setPriceAdjustments();

print 'The total cost for CD item is: $'.$cd->price."\n";

$cereal = new BandEndorsedCaseOfCereal($externalBand, $externalCerealPrice);
$cereal->setPriceAdjustments();

print 'The total cost for cereal case is: $'.$cereal->price."\n";

// 上面的两个示例都创建了一个扩展 Template 类的子类，两个子类都调用可能对公共属性 $price 应用变化的
// setPriceAdjustments() 方法，随后的输出显示了为指定商品项调整后的价格。

// 总结：创建定义了设计常规步骤，但实际逻辑留给子类进行详细说明的对象时，最佳的做法是使用模板设计模式。
// 模板设计模式（Template Pattern）创建了一个实施一组方法和功能的抽象对象，子类通常将这个对象用于自己的设计。
