<?php
// 实例：外观模式（Facade Pattern）

// 有个 Web 站点、比较老旧，它只处理大写形式的字符串。具体代码需要一个 CD 对象，对其所有属性应用大写形式之后，返
// 回一个提供 Web 服务的、完整 XML 文档。

// 下面是一个 CD 类的简单示例：

class CD {

	public $title  = '';
	public $band   = '';
	public $tracks = array();

	public function __construct($title, $band, $tracks) {
		$this->title  = $title;
		$this->band   = $band;
		$this->tracks = $tracks;
	}
}

// 实例化 CD 对象时，构造函数为 CD 添加标题、乐队和曲目列表。

// 建立 CD 对象的步骤也比较简单：

$title = 'Waste of a rib';
$band = 'Never Again';
$trackFromExternalSource = array('What It Means', 'Brr', 'Goodbye');
$cd = new CD($title, $band, $trackFromExternalSource);

// 要为外部系统格式化 CD 对象，就需要创建 2 个其他类。第一个类用于对 CD 的属性应用大写形式，第二个类用于根据 CD
// 对象构建一个完整的 XML 文档并返回 XML 字符串。

// 请注意，这 2 个类是为了最大可重用性而创建的。用户可能要求把上述操作合并到一个类，但是以后您可能会被要求进行分解。

class CDUpperCase {

	public static function makeString(CD $cd, $type) {
		$cd->$type = strtoupper($cd->$type);
	}

	public static function makeArray(CD $cd, $type) {
		$cd->$type = array_map('strtoupper', $cd->$type);
	}
}

class CDMakeXML {

	public static function create(CD $cd) {
		$doc = new DOMDocument();

		$root = $doc->createElement('CD');
		$root = $doc->appendChild($root);

		$title = $doc->createElement('TITLE', $cd->title);
		$title = $root->appendChild($title);

		$band = $doc->createElement('BAND', $cd->band);
		$band = $root->appendChild($band);

		$tracks = $doc->createElement('TRACKS');
		$tracks = $root->appendChild($tracks);

		foreach ($cd->tracks as $track) {
			$track = $doc->createElement('TRACK', $track);
			$track = $tracks->appendChild($track);
		}

		return $doc->saveXML();
	}
}

// CDUpperCase 对象有 2 个公共静态方法。makeString() 方法接受一个 CD 对象实例和 $type 字符串参数，根据 
// $type 参数的值动态地修改 CD 对象的对应属性，并转换为大写。而 makeArray() 方法类似，它会根据 $type 数
// 组参数对每一项数组元素应用 strtoupper() 函数。因为 PHP 是基于引用传递 CD 对象的，所以不必返回对象变量。
// 而且上述所有方法的动态执行都允许了今后 CD 类扩展更多公共属性时仍可使用这个类，而不必修改。

// CDMakeXML 对象只有只有 1 个公共静态方法 create()。该方法接受 CD 对象，并据此返回一个完整的 XML 文档。
// 简单说，这个方法为 CD 的标题、乐队、曲目列表创建了大写标记名称的元素。

// 实际上，这样就可以对 CD 对象格式化了：

CDUpperCase::makeString($cd, 'title');
CDUpperCase::makeString($cd, 'band');
CDUpperCase::makeArray($cd, 'tracks');
print CDMakeXML::create($cd);

// 这样的确解决了问题，但并不是最佳的做法。如下所示，我们应当针对具体的 Web 服务调用创建一个 Facade 对象。

class WebServiceFacade {

	public static function makeXMLCall(CD $cd) {

		CDUpperCase::makeString($cd, 'title');
		CDUpperCase::makeString($cd, 'band');
		CDUpperCase::makeArray($cd, 'tracks');
		$xml = CDMakeXML::create($cd);

		return $xml;
	}
}

// WebServiceFacade 对象只具有一个名为 makeXMLCall() 的公共静态方法。该方法接受一个 CD 对象并返回一个 XML
// 文档。
// 这样就可以把前面创建 XML 文档的步骤代码移到这个外观对象的方法内。这时，上面列出的 4 行代码只需要被替换为下面的
// 一行代码：

print WebServiceFacade::makeXMLCall($cd);

// 总结：在应用程序的下一步骤包含许多复杂的逻辑步骤和方法调用时，最佳的做法是创建一个基于外观设计模式的对象。
// 通过在必须的逻辑和方法集合前创建简单的外观接口，外观设计模式（Facade Pattern）隐藏了来自调用对象的复杂性。