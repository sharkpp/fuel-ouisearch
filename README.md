Fuel OUI search パッケージ
==========================

[![Build Status](https://travis-ci.org/sharkpp/fuel-ouisearch.png?branch=master)](https://travis-ci.org/sharkpp/fuel-ouisearch)

要件
----

* FuelPHP 1.5 以降
* データの登録のために [oui.txt](http://standards.ieee.org/develop/regauth/oui/oui.txt) が必要です。

インストール
------------

1. ``` PKGPATH ``` に展開([Packages - General - FuelPHP Documentation](http://fuelphp.com/docs/general/packages.html)を参照)
2. ``` APPPATH/config/config.php ``` の ``` 'always_load' => array('packages' => array()) ``` にパッケージを追加
3. ``` APPPATH/config/config.php ``` の ``` 'package_paths' => array() ``` に ``` PKGPATH ``` を追加(これをしないとマイグレーションが実行されない)
4. ``` php oil refine migrate --packages=ouisearch ``` を実行してテーブルを初期化
5. [oui.txt](http://standards.ieee.org/develop/regauth/oui/oui.txt) をダウンロードして、パッケージディレクトリ(bootstrap.phpがあるところ)に置く。
6. ``` php oil refine importoui ``` を実行して定義をインポート(時間がかかります)。

テスト
------

1. ``` php oil test --group=OuiSearchPackage ``` を実行してテスト

グループは、``` Package ``` もしくは ``` OuiSearchPackage ``` で個別に指定できます。

使い方
------

    $name = OuiSearch::lookup('00:00:00');

    $lists = OuiSearch::search_organization('00-00', 10);

    $lists = OuiSearch::search_oui('CORPORATION');

など、で使用できます。

ライセンス
----------

Copyright(c) 2013 sharkpp All rights reserved.
このプログラムは、The MIT License の元で公開されています。
