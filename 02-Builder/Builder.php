<?php
// 实例：建造者模式（Adapter Pattern）

// 现在有一个创建产品对象的类，它必须通过依次调用 3 个方法 setTyep()、setSize()、setColor() 来创建一个完
// 整的产品对象。如果缺少任何一个方法的调用，产品对象将创建失败，某些严重的情况会导致程序中止。

// 如上所述，最早版本的产品对象创建方法被设计为先创建一个初始的产品对象，再依次执行上述 3 个方法。

class product {

	protected $_type  = '';
	protected $_size  = '';
	protected $_color = '';

	public function setType($type) {
		$this->_type = $type;
	}

	public function setSize($size) {
		$this->_size = $size;
	}

	public function setColor($color) {
		$this->_color = $color;
	}
}

// 为了创建完整的产品对象，需要将构建产品所需的配置分别传递给产品类的每个方法
$productConfigs = array(
	'type'  => 'shirt',
	'size'  => 'XL',
	'color' => 'red'
);
$product = new product();
$product->setType($productConfigs['type']);
$product->setSize($productConfigs['size']);
$product->setColor($productConfigs['color']);

// 创建对象时分别调用每个方法并不是最佳的做法。为了规避这种做法，可以使用基于建造者模式的对象来创建这个产品实例。

// productBuilder 类被设计为接受构建 product 所需的所有配置选项。它不仅可以存储所有配置，也存储了一个完整的
// product 实例。它有一个 build 方法负责调用 product 类中的所有方法，从而构建完整的 product 对象。最后，它
// 提供了一个 getProduct 方法返回完整构建的 product 对象。

class productBuilder {

	protected $_product = NULL;
	protected $_configs = array();

	public function __construct($configs) {
		$this->_product = new product();
		$this->_configs = $configs;
	}

	public function build() {
		$this->_product->setType($this->_configs['type']);
		$this->_product->setSize($this->_configs['size']);
		$this->_product->setColor($this->_configs['color']);
	}

	public function getProduct() {
		return $this->_product;
	}
}

// 可以看到，build 方法隐藏了构建 product 所需的实际方法调用。如果以后 product 类做了版本升级，那么只需要
// 修改 productBuilder 类的 build 方法即可。

// 现在用 productBuilder 类来创建 product 对象
$builder = new productBuilder($productConfigs);
$builder->build();
$product = $builder->getProduct();

// 总结：建造者模式的目的是消除其他对象的复杂创建过程。使用建造者模式不仅是最佳的做法，而且在某个对象的构造和配置
// 方法改变时可以尽可能少地重复修改代码。
// 建造者模式（Builder Pattern）定义了处理其他对象的复杂构建的对象设计。