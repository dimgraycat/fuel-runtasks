# fuel-runtasks

RunTasks package for [FuelPHP].

## 概要

RunTasksはcrontabなどに設定してあるFuelPHPのtasksを一元管理するために作成しました  
ただしパイプなどで別に渡してしまい場合には不向きかもしれません

実用例としては

* 毎時にAを実行してからBを実行したい場合など順次に実行する場合
* 毎時に実行する C, D, E をcrontabなどに3行書いてしまっている場合
* グルーピングをして毎時や毎月実行させる場合

しかしながら、設定した内容を**順次に実行していく**ため注意が必要です

- - - -

例) 毎時、毎月に1回実行させているものを纏める

crontab

    #hourly
    0 */1 * * * env FUEL_ENV=production php oil refine task1
    0 */1 * * * env FUEL_ENV=production php oil refine task2:foo
    0 */1 * * * env FUEL_ENV=production php oil refine task3:bar "`date -d '1 hours ago' '+\%F \%H:00:00'`" "`date -d '1 hours ago' '+\%F \%H:59:59'`"
    #daily
    0 1 * * * env FUEL_ENV=production php oil refine tasks4
    0 1 * * * env FUEL_ENV=production php oil refine tasks5:hoge
    0 1 * * * env FUEL_ENV=production php oil refine tasks6:moge "`date -d '1 days ago' '+\%F 00:00:00'`" "`date -d '1 days ago' '+\%F 23:59:59'`"

これを RunTasksを使用することで下記となります

    0 */1 * * * env FUEL_ENV=production php runtasks hourly
    0 0 * * * env FUEL_ENV=production php runtasks daily

設定内容は下記となります

    ---
    groups
      hourly:
        - task1
        - task2:foo
        - task3:bar "`date -d '1 hours ago' '+%F %H:00:00'`" "`date -d '1 hours ago' '+%F %H:59:59'`"
      daily:
        - tasks4
        - tasks5:hoge
        - tasks6:moge "`date -d '1 days ago' '+%F 00:00:00'`" "`date -d '1 days ago' '+%F 23:59:59'`"

- - - -


## 導入方法

### gitリポジトリの追加

まずは、fuel/app/config/package.php がない場合は
fuel/core/config/package.php を fuel/app/config/package.php にコピーしてきます

fuel/app/config/package.php に **github.com/dimgraycat** を追加します

    'sources' => array(
        'github.com/fuel-packages',
        'github.com/dimgraycat',    <- 追加
    ),

### oilcommandでインストール

    php oil package install runtasks

## 使い方

### bin/runtasksを使う場合

bin/runtasksを使う場合は下記の2通りの設定からお好みの方法をお使いください

**直接使う**

    php fuel/packages/fuel-runtasks/bin/runtasks <group> [<options>]


**シンボリックリンクを貼るか、コピーして使う**

    php runtasks <group> [<options>]

### oilコマンドで使いたい場合

fuel/app/config/config.phpの always_load 内の packages に **fuel-runtasks** を追加します

    always_load => array(
      'packages' => array(
          // 'orm',
          'fuel-runtasks',    <- 追加
      ),
    ),

後は oilコマンドでtasksを実行する方法と同じように使えます

    php oil refine runtasks <group> [<options>]

## 設定方法

※ RunTasksのデフォルト設定では **runtasks.yml** をConfig::loadしています

runtasks.ymlをコピーしてきます

    cp fuel/packages/fuel-runtasks/config/runtasks.yml fuel/app/config/

### 設定内容

runtasks.ymlの中身

    ---
    default:
      php_path: /path/to/php
      is_logging: false
      is_stdout: true
      is_continue: true
      prefix_message: '[RunTasks_Runner::run]'
    php_ini:
      memory_limit: '128M'
      time_limit: 30
    groups:
      group1: []

* default
  * php_path: phpのインタプリタを設定します
    * /usr/bin/php など
  * is_logging: \Log::$method での出力をするかどうかをbooleanで設定します
  * is_stdout: コマンドラインから実行時にSTDOUTするかどうかをbooleanで設定します
    * trueの時、STDERRも出力されます
  * is_continue: trueの場合groupsで設定したtasksが途中で失敗した場合でも次のtaskを実行します
  * prefix_message: is_loggingがtrueの時に出力される内容の接頭に設定した内容が付加されます
    * 不要の場合は ''にするかprefix_messageの行を削除してください
* php_ini
  * memory_limit: runtasksに割り当てるメモリ量の最大値
  * time_limit: phpの[set_time_limit]に設定する値
* groups: ここに実行させたいtasksを設定してください
  * [config/example.yml]を参照してください

#### runtasksのtasks(groups)の設定を外部の設定から読み込む

初期設定ではruntasksのフォルダになります。

    fuel/app/config/runtasks/*
    fuel/app/config/<env>/runtasks/*

設定方法は下記を参照してください

config/runtasks.yml

    ---
    default:
      php_path: /path/to/php
      include_dir: runtasks
      is_logging: false
      is_stdout: true
      is_continue: true
      prefix_message: '[RunTasks_Runner::run]'
    php_ini:
      memory_limit: '128M'
      time_limit: 30
    groups:
      hourly:
        - task1
        - task2:foo
        - task3:bar "`date -d '1 hours ago' '+%F %H:00:00'`" "`date -d '1 hours ago' '+%F %H:59:59'`"
      +daily: 'daily.yml'
      +monthly: 'monthly'

* defaultに **include_dir** を追加
  * 初期値では **runtasks** になっています
* groups
  * 実行したいファイル名を設定する

config/runtasks/daily.yml

    ---
    daily:
      - dailytask1
      - dailytask1:foo
      - dailytask1:bar "`date -d '1 hours ago' '+%F %H:00:00'`" "`date -d '1 hours ago' '+%F %H:59:59'`"

## optionsについて

一時的にLogに出力したい場合や、STDOUTで出力したい場合などにオプションを付けることができます  
オプションが設定されていない場合はconfigの値が使われます

    php runtasks <group> --logging --stdout

## Copyright

* Copyright (c) 2015 - dimgraycat
* License
  * [MIT License]

[FuelPHP]:http://fuelphp.jp/docs/1.7/index.html
[set_time_limit]:http://www.php.net/manual/ja/function.set-time-limit.php
[config/example.yml]:https://github.com/dimgraycat/fuel-runtasks/blob/master/config/example.yml
[MIT License]:http://www.opensource.org/licenses/mit-license.php
