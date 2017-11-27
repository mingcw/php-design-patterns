<?php
// 实例：委托模式（Delegate Pattern）

// Web 站点具有创建 MP3 文件播放列表的功能。下面的示例显示了播放列表的创建部分

class Playlist {

	private $__songs;

	public function __construct() {
		$this->__songs = array();
	}

	public function addSong($location, $title) {
		$song = array('location' => $location, 'title' => $title);
		$this->__songs[] = $song;
	} 

	public function getM3U() {
		$m3u = "#EXTM3U\n\n";

		foreach ($this->__songs as $song) {
			$m3u .= "#EXTINF: -1, {$song['title']}\n";
			$meu .= "{$song['location']}\n";
		}

		return $m3u;
	}

	public function getPLS() {
		$pls = "[playlist]\nNumberOfEntries = ".count($this->__songs)."\n\n";

		foreach ($this->__songs as $songCount => $song) {
			$counter = $songCount + 1;
			$pls .= "File{$counter} = {$song['location']}\n";
			$pls .= "File{$counter} = {$song['title']}\n";
			$pls .= "Length{$counter} = -1\n\n";			
		}
	}

}

// Playlist 类的私有变量 $__songs 存储了歌曲数组列表，并在构造函数对它初始化。
// addSong() 方法接受 2 个参数：该 MP3 歌曲的文件位置和歌曲标题，这 2 个参数重组为一个关联数组、标识一条
// 歌曲，并添加到内部的歌曲数组里。
// 具体的需求规定播放列表必须可以任意使用 M3U 和 PLS 格式。为此，Playlist 类具有 getM3U() 和 getPLS() 这
// 两个方法。这两个方法都可以创建适当的播放列表文件头并遍历内部的歌曲数组以完成播放列表。最后，每个方法均返回一个
// 多行字符串格式的播放列表。

// 如下所示，执行上述功能性的当前代码包含了常见的 if/else 语句：
$playlist = new Playlist();
$playlist->addSong('/home/aaron/music/brr3.mp3', 'Brr');
$playlist->addSong('/home/aaron/music/googbye.mp3', 'Goodbye');

if ($externalRerievedType == 'pls') {
	$playlistContent = $playlist->getPLS();
}
else {
	$palylistContent = $playlist->getM3U();
}

// 上面的代码创建了 Playlist 对象的一个实例，添加了两首歌曲。接着执行了一个 if/else 语句。如果格式类型为
// pls，那么就执行 getPls() 方法，其输出被置入 $playlistContent 当中。否则，$externalRetrievedType
// 类型可能为 m3u，那么进入 else 分支执行 getM3U() 方法。

// 这个时候，编程人员可以使用委托设计模式来更改代码。这种做法的目的是消除潜在的、难以控制的 if/else 语句。
// 此外，随着添加更多的代码，最初的 playlist 类会变得极为庞大。

// 为了使用委托设计模式，需要设计新的 Playlist 类：

class newPlaylist {

	private $__songs;
	private $__typeObject;

	public function __construct($type) {
		$this->__songs = array();
		$object = "{$type}Playlist";
		$this->__typeObject = new $object; // 利用了 PHP 基于某个变量动态创建类的特性
	}

	public function addSong($location, $title) {
		$song = array('location' => $location, 'title' => $title);
		$this->__songs[] = $song;
	}

	public function getPlaylist() {
		$playlist = $this->__typeObject->getPlaylist($this->__songs);
		return $playlist;
	}
}

// newPlaylist 类的构造函数现在接受一个 $type 参数，除了初始化内部的歌曲数组之外，构造函数还根据 $type 动态
// 地指定委托的新实例并将该实例存储在内部的 $__typeObject 变量中。

// addSong() 方法与最初 Playlist 类中的方法完全一样。而 getM3U() 和 getPLS() 被替换为了 getPlaylist()
// 方法。这个方法会执行内部存储的委托对象的 getPlaylist) 方法，并传入歌曲数组，从而使该委托对象能够创建并放回
// 正确的播放列表。

// 如下所示，原有 Playlist 对象的上述 2 个方法被移到这些对象自己的委托对象中：

class m3uPlaylistDelegate {

	public function getPlaylist($songs) {
		$m3u = "#EXTM3U\n\n";

		foreach ($songs as $song) {
			$m3u .= "#EXTINF: -1, {$song['title']}\n";
			$m3u .= "{$song['location']}\n";
		}

		return $m3u;
	}
}

class plsPlaylistDelegate {

	public function getPlaylist($songs) {
		$pls = "[playlist]\nNumberOfEntries = ".count($songs)."\n\n";

		foreach ($songs as $songCount => $song) {
			$counter = $songCount + 1;
			$pls .= "File{$counter} = {$song['location']}\n";
			$pls .= "File{$counter} = {$song['title']}\n";
			$pls .= "Length{$counter} = -1\n\n";
		}

		return $pls;
	}
}


// 每个委托都只是重新包装了基类 Playlist 中的原有方法。每个委托对象都具有完全相同的 getPlaylist() 方法，
// 该方法接受 songs 参数。这种方式使基对象能够简单、动态地创建和访问任何委托者。

// 如下所示，执行这个基于委托的新系统的代码更为简单：

$externalRerievedType = 'pls';

$playlist = new newPlaylist($externalRerievedType);
$playlistContent = $playlist->getPlaylist();

// 当通告其他的播放列表时，开发人员不必修改上面的代码就能创建基于委托设计模式的新类。

// 总结：为了去除核心对象的复杂性并且能够动态添加新的功能，就应当使用委托设计模式。
// 委托设计模式（Delegate Pattern）实现了通过分配或委托至其他对象、去除了核心对象中的判决和复杂的功能性。