<?php
// 实例：数据访问对象模式（Data Access Object Patterm, DAO 模式）

// 需求：这个实例关注的是每个用户实体。MySQL 数据库拥有多条记录，每条记录包含每个用户的具体和特有的信息。这种功
// 能性要求我们通过用户的主键或用户名查找返回一个用户。此外，必须能对某用户的任意字段执行更新操作。

// 这里需要 2 个类（父-子类）。第一个类应当是基本数据访问对象类，它具有获取和更新数据的方法。如下所示：

abstract class baseDAO {

	private $__connection; // 存储数据库连接

	public function __construct() {
		$this->__connectToDB(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
	}

	private function __connectToDB($host, $user, $pass, $database) {
		$this->__connection = mysql_connect($host, $user, $pass);
		mysql_select_db($database, $this->__connection);
	}

	public function fetch($value, $key = NULL) {
		if(is_null($key)) {
			$key = $this->_primaryKey; // 主键属性由子类定义
		}

		$sql = "select * from {$this->tableName} where {$key} = '{$value}'"; //　表名属性由子类定义
		$results = mysql_query($sql, $this->__connection);
		$rows = array();
		while($result = mysql_fetch_assoc($results)) {
			$rows[] = $result;
		}

		return $rows;
	}

	public function update($keyedArray) {
		$sql = "update {$this->_tablename} set ";
		foreach ($keyedArray as $column => $value) {
			$sql .= $column . '=' . $value . ',';
		}
		$sql = substr($sql, 0, -1);
		$sql .= "where {$this->_primaryKey} = '{$keyedArray[$this->_primaryKey]}'";

		mysql_query($sql, $this->__connection);
	}
}

// 需要注意第一点是：这个类是一个抽象类。所以要使用该类必须先扩展该类，通常是子类继承。其次，在实例化时，利用
// 私有方法　__connectToDB() 建立数据库连接，并且　__connectToDB() 需要正确的证书才能执行。该方法简单地存
// 储了对象内部的数据库连接，以备后续数据库查询时多次引用。

// 下一个公共方法　fetch() 用于查询某字段等于 $value 的记录，第二个参数指定是哪个字段，如果第二个参数没填，
// 则默认按照主键进行查找。最后返回一个关联数组的结果集。要注意fetch() 方法的抽象程度：该方法事先并不知道要查
// 询的表名、键或值，这也正是基本数据访问对象为代码提供的能力。

// 基本数据访问类中的最后一个方法是　update() 公共方法。同样地，它也事先不知道要操作是哪个表，与 fetch() 具
// 有类似的抽象程度。该方法接受一个键控数组并期望将主键作为数组元素一并传入。最后对该主键标识的记录进行更新。

// 任何子类都可以扩展这个抽象类。通过指向　user 表，下面的子类能够引用到用户实体。只需要在用户实体具体环境中添
// 加有意义的特定功能性。

class userDAO extends baseDAO {

	protected $_tablename = 'user';
	protected $_primaryKey = 'id'; // 父类中会使用这两个属性，不能设为 private

	public function getUserByFirstName($name) {
		$result = $this->fetch($name, 'firstName'); // 按照 firstName 字段查找
		return $result;
	}
}

// 上面的类扩展了 baseDAO 类，所以能够访问父类的所有方法。子类中定义了表名和主键，它们直接关联了数据库中的一个
// 表。为了获得一个起作用的用户访问对象子实体，至少需要定义这两个受保护的变量。

// 不过，功能性需求的一部分是能通过名称查找用户表。为了事先这样的需求，公共方法 getUserByFirstName() 会接受
// 一个用户名参数。通过调用父类的 fetch() 方法并指定应当查询的列，最后返回需求的记录。

// 下面给出给予上述数据访问对象的示例：

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_DATABASE', 'test');

$user = new userDAO();
$userRecord = $user->fetch(1);

$updates = array('id' => 1, 'firstName' => 'Jason');
$user->update($updates);

$allJason = $user->getUserByFirstName('Jason');

// 代码的第一部分定义数据库证书。显然有更灵活的数据库证书提供方式，这里仅是一个示例。
// 接下来创建了新的 userDAO，并获取了主键为 1 的用户实体。
// 然后下一步定义了更新操作，id 为 1 用户实体的名称会被更改为 Jason。
// 最后，数组由包含名称为 Jason 的所有用户实体构成。

// 总结：为了减少重复和抽象化数据，最好的做法是基于数据访问对象创建一个类。
// 数据访问对象模式（Data Access Object Pattern，DAO 模式）描述了如何创建提供透明访问任何数据源的对象。