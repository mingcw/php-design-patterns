<?php
// 实例：策略模式（Strategy Pattern）

// 示例 Web 站点大量使用了 AJAX。有时候，CD 对象有必要生成自身的 XML 版本。这就会返回开始处理的 JavaScript。

// CD 对象的代码如下所示：

class CD
{
	public $title = '';
	public $band  = '';

	public function __construct($title, $band)
	{
		$this->title = $title;
		$this->band  = $band;
	}

	public function getAsXML()
	{
		$doc = new DOMDocument();

		$root = $doc->createElement('CD');
		$root = $doc->appendChild($root);

		$title = $doc->createElement('TITLE', $this->title);
		$title = $root->appendChild($title);

		$band = $doc->createElement('BAND', $this->band);
		$band = $root->appendChild($band);

		return $doc->saveXML();
	}
}

// CD 类有 2 个公共属性 $title 和 $band，在构造函数里对它们进行初始化并内部存储。公共方法 getAsXML() 根据 CD 对象创建
// Dom 文档，并最终返回 XML 格式的字符串表示。

// 实现 CD 对象的代码相当简单：

$externalTitle = 'Waste of a Rib';
$externalBand  = 'Never Again';

$cd = new CD($externalTitle, $externalBand);

print $cd->getAsXML();

// 很快，Web 站点迎来了第一次更新。站点的 AJAX 功能性需要额外的灵活性，包括能够将 CD 对象生成为 JavaScript 对象表示法
// （JavaScript Object Notation，JSON）实体。
// CD 对象的第一个实现不够灵活，因此不能生成这种新的输出类型。而且，我们还可能需要针对其他使用（uses）不断创建更多的 CD
// 对象表示类型。某些 Web 服务会要求 XML 或 JSON 之外的不同格式。此时，最好使用策略设计模式。

// 更改 CD 对象的一个步骤是去除 XML 功能性。此外，CD 对象还应当能够执行我们所创建的策略对象（Strategy）。

class CDusesStrategy
{
	public $title = '';
	public $band  = '';

	protected $_strategy;

	public function __construct($title, $band)
	{
		$this->title = $title;
		$this->band  = $band;
	}

	public function setStrategyContext($strategyObject)
	{
		$this->_strategy = $strategyObject;
	}

	public function get() {
		return $this->_strategy->get($this);
	}
}

// CDusesStrategy 类的第一部分与之前的 CD 类较为相似。不过，这个类还存在其他两个公共方法和一个受保护属性。
// setStrategyContext() 方法接受一个名为 $strategyObject 的参数，并将 Strategy 对象的实例保存到保护变量 
// $_strategy 内。另一个方法 get() 替换了 CD 类中的 getAsXML() 方法。因为应用于 Strategy 对象，所以该方法的名称更
// 为抽象。存储在保护变量 $_strategy 内的 Strategy 对象拥有自己的、在此处执行的 get() 方法。基类的一个实例被传入
// Strategy 对象，注意到这点十分重要。

// 接下来，需要创建用于 XML 和 JSON 格式的 Strategy 对象。

class CDAsXMLStrategy
{
	public function get(CDusesStrategy $cd)
	{
		$doc = new DOMDocument();

		$root = $doc->createElement('CD');
		$root = $doc->appendChild($root);

		$title = $doc->createElement('TITLE', $cd->title);
		$title = $root->appendChild($title);

		$band = $doc->createElement('BAND', $cd->band);
		$band = $root->appendChild($band);

		return $doc->saveXML();
	}
}

class CDAsJSONStrategy
{
	public function get(CDusesStrategy $cd)
	{
		$json = [];
		$json['CD']['title'] = $cd->title;
		$json['CD']['band']  = $cd->band;

		return json_encode($json);
	}
}

// CDAsXMLStrategy 类只有一个名为 get() 的公共方法。前面曾提及该方法是在 CDusesStrategy 的 get() 方法中被调用的。这
// 里的 get() 方法接受 CDusesStrategy 的一个实例。除了所使用 $cd 代替 $this 之外，随后的逻辑与 CD 对象中 getAsXML()
// 方法的逻辑基本相同。

// CDAsJSONStrategy 类的设计与 CDusesXMLStrategy 类的设计几乎完全相同，明显的区别是构造了 $json 数组来代替一个
// DOMDocument。指定信息从 $cd 变量中检索得到，该变量是 CDusesStrategy 的新实例。最后返回 CD 对象 的 JSON 编码形式。

// 如下所示，执行使用 Strategy 对象的代码并不复杂：

$cd = new CDusesStrategy($externalTitle, $externalBand);

// xml output
$cd->setStrategyContext(new CDAsXMLStrategy());
print $cd->get();

// json output
$cd->setStrategyContext(new CDAsJSONStrategy());
print $cd->get();

// 通过实例化 CDUsesStrategy 就可以创建 CD 的一个新实例。为了输出 XML 格式，需要调用 setStrategyContext() 方法，
// 该方法会发送相应 Strategy 对象的一个新实例。任何新功能都会进入 setStrategyContext() 指派的新 Strategy 
// 对象的指定方法，注意到这一点十分重要。

// 总结：在能够创建应用于基对象的、由自包含算法组成的可互换对象时，最佳的做法是使用策略设计模式。
// 策略设计模式（Strategy Pattern）帮助构建的对象不必自身包含逻辑，而是能够根据需要利用其他对象中的算法。
