<?php
// 实例：解释器模式（Interpreter Pattern）

// 为了顺应潮流，示例中的 Web 站点决定合并 CD 购买活动和社会网络。注册了该站点的用户具有自己的配置文件页面，他
// 们能够添加高级的功能，如 HTML、以及构建自己喜欢的 CD 列表。

// 在第一次迭代中，用户可以创建自己的配置文件，并且能够在配置文件中添加自己喜欢的 CD 标题，第一部分功能性是 
// User 类：

class User {

	protected $_username = '';

	public function __construct($username) {
		$this->_username = $username;
	}

	public function getProfilePage() {
		// 替代了一波数据库操作，这里提供一段演示信息～
		$profile  = '<h2>I like Never Again</h2>';
		$profile .= 'I love all of their songs. My favourite CD: <br />';
		$profile .= '{{myCD.getTitle}}';

		return $profile;
	}
}

// 大多数的 User 类都模拟了这个示例。创建 User 类的实例时，用户名被接受并存储到内部的保护变量 $_username。在
// 未模拟的示例中，某些逻辑可能用于查询数据库和为 User 类指定适当的值。getProfilePage() 方法也是一种模拟方
// 法，它返回一个硬编码的配置文件。不过，该示例要注意的重点是{{myCD.getTitle}}字符串，这表示稍后要解释的模板语
// 言。getProfilePage() 只返回用户在其配置文件页面中指定的内容。

// 为了为用户检索 CD 信息，需要创建一个名为 UserCD 的类：

class userCD {

	protected $_user = NULL;

	public function setUser($user) {
		$this->_user = $user;
	}

	public function getTitle() {
		// 草率地提供一段演示信息～
		$title = 'Waste of a Rib';

		return $title;
	}
}

// setUser() 方法接受用户对象并存储在内部。getTitle() 方法会从 CD 中检索和返回标题。
// 注意到 getTitle() 方法的名称和用户配置文件中指定的模板语言之间的相似性，这是十分重要的。如下所示，解释器类
// 使用了这种相似性：

class userCDInterpreter {

	protected $_user = NULL;

	public function setUser($user) {
		$this->_user = $user;
	}

	public function getInterpreted() {
		$profile = $this->_user->getProfilePage();

		if(preg_match_all('/\{\{myCD\.(.*?)\}\}/', $profile, $triggers, PREG_SET_ORDER)) {
			
			foreach ($triggers as $trigger) {
				$methods[] = $trigger[1];
			}

			$methods = array_unique($methods);

			$myCD = new userCD();
			$myCD->setUser($this->_user);

			foreach ($methods as $method) {
				$profile = str_replace("{{myCD.$method}}",
					call_user_func(array($myCD, $method)),
					$profile);
			}
		}

		return $profile;
	}
}

// userCDInterPreter 类包含 setUser() 方法，该方法接受一个 User 对象并将其存储在内部。除此之外，这个类
// 还包含了一个名为 getInterpreted() 的公共方法。

// 1. 首先，getInterpreted() 方法从 User 对象获取内部存储的配置文件。接着，该方法通过解析配置文件查找能够
//    处理的可解析关键字。如果发现这样的关键字，那么就会构造一个独特的方法数组 $methods。
// 2. 下一步骤是创建基于 UserCD 类的对象，并将 User 实例传入其内部。
// 3. 最后，所有方法都会被遍历。根据属于 userCD 实例的 $method 变量指定名称的方法会被调用，该方法的输
//    出用于替换配置文件中的被解释占位符。完成所有解释后，返回配置文件。

// 现在，实际执行解释和生成模板化输出的代码就非常简单了：

$username = 'arron';
$user = new User($username);
$userCD = new userCD();
$interpreter = new userCDInterpreter();
$interpreter->setUser($user);
print $interpreter->getInterpreted();

// 这样就可以创建用户实例、创建一个基于解释器设计模式的类以及执行解释。

// 总结：当使用关键字或宏语言引用一组指令时，最佳的做法是使用基于解释器设计模式的类。
// 解释器设计模式（Interpreter Pattern）用于分析一个实体的关键元素，并且针对每个元素都提供自己的解析或相应的
// 动作。