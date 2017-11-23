<?php
// 实例：适配器模式（Adapter Pattern）

// 有一个错误类，用来存储错误信息，并对外提供一个获取错误的接口 getError 

class errorObject {

	private $__error;

	public function __construct($error) {
		$this->__error = $error;
	}

	public function getError() {
		return $this->__error;
	}
}

// 输出错误类，直接把错误输出到控制台

class logToConsole {

	private $__errorObject;

	public function __construct($errorObject) {
		$this->__errorObject = $errorObject;
	}

	public function write() {
		fprintf(STDERR, $this->__errorObject->getError());;
	}
}

// 实例化错误类和输出类，并输出错误到控制台
$error = new errorObject('404:Not Found');
$logToConsole = new logToConsole($error);
$logToConsole->write();


// 现在有一个新的输出错误类。这个新的类把错误记录到一个多列的 CSV 文件，并要求文件内容格式为第 1 列是错误代码，第 2 列是错误文本。
// 然而它用了另外一个版本的 errorObjet，这个 errorObject 有两个接口：getErrorNumber 和 getErrorText

/**
 * 新的输出错误类
 */
class logToCSV {

	const CSV_LOCATION = './log.csv';

	private $__errorObject;

	public function __construct($errorObject) {
		$this->__errorObject = $errorObject;
	}

	public function write() {
		$line  = $this->__errorObject->getErrorNumber() . ':' . $this->__errorObject->getErrorText() . "\n";
		file_put_contents(self::CSV_LOCATION, $line, FILE_APPEND);
	}
}

// 为了让新的输出错误类成功运行，这里有 2 个解决方案：
// 1. 直接修改 errorObject，增加接口（但为了保留原有接口的标准性和公用性，不建议）
// 2. 创建一个 Adapter 对象（适配器）
//    (1) 适配器继承 errorOjbect，保留父类原有的特性
//    (2) 适配器利用原有接口 getError 来获取错误信息，以此封装两个新的接口 getErrorNumber 和 getErrorText

/**
 * 适配器
 */
class logToCSVAdapter extends errorObject {

	private $__errorNumber;
	private $__errorText;

	public function __construct($error) {
		parent::__construct($error);
		$line = explode(':', $this->getError());
		$this->__errorNumber = $line[0];
		$this->__errorText = $line[1];
	}

	public function getErrorNumber() {
		return $this->__errorNumber;
	}

	public function getErrorText() {
		return $this->__errorText;
	}
}

// 使用适配器，实例化错误类和输出类，并输出错误到CSV文件
$error = new logToCSVAdapter('404:Not Found');
$logToCSV = new logToCSV($error);
$logToCSV->write();

// 总结：需要转换一个对象的接口以适用于另一个对象时，使用适配器模式不仅是最佳做法，而且也能减少很多麻烦。
// 适配器模式（Adapter Pattern）用于将一个对象的接口转换成另一个对象所期望的接口。