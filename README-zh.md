## typecho-export-hugo

Typecho 博客文章导出至 Hugo 插件

## 如何使用

点击右侧的`Download ZIP`按钮，下载完成之后解压得到类似`typecho-export-hugo-master`文件夹，将文件夹重命名为`Export2Hugo`,上传到Typecho目录`usr/plugins`,然后在后台启用插件。

在后台界面，`控制台`菜单下会有一个`导出至Hugo`菜单，点击进入导出界面，点击按钮后获得导出的 Zip 文件，将解压后的 `content` 文件夹移动到 Hugo 目录下即可。

**注：**

1. Mac 下有可能无法解压该 zip 文件，可以在命令行使用 `unzip` 命令进行解压。

## LICENSE

[MIT LICENSE](https://github.com/lizheming/typecho-export-hugo/blob/master/LICENSE)

## 插件原理

在原始Typecho网站上，文章的内容是存储在数据库中的，而Hugo是一个静态网站生成器，它的文章内容是存储在文件中的，所以需要将数据库中的文章内容导出到文件中。

分析Typecho数据库结构，发现文章内容存储在'table.contents'表中，文章的内容是以'markdown'格式存储的，所以只需要将'markdown'格式的文章内容导出到文件中即可。

数据库结构：

Database blog
- typecho_comments
- typecho_contents
- typecho_duoshuo
- typecho_fields
- typecho_links
- typecho_metas
- typecho_options
- typecho_relationships
- typecho_users

typecho_contents

```sql
CREATE TABLE 'table.contents' (
'cid' int(10) unsigned NOT NULL AUTO_INCREMENT,
'title' varchar(200) NOT NULL DEFAULT '',
'slug' varchar(200) NOT NULL DEFAULT '',
'created' int(10) unsigned NOT NULL DEFAULT '0',
'modified' int(10) unsigned NOT NULL DEFAULT '0',
'text' text,
'order' int(10) unsigned NOT NULL DEFAULT '0',
'authorId' int(10) unsigned NOT NULL DEFAULT '0',
'template' varchar(32) NOT NULL DEFAULT '',
'type' varchar(16) NOT NULL DEFAULT '',
'status' varchar(16) NOT NULL DEFAULT 'publish',
'password' varchar(32) NOT NULL DEFAULT '',
'commentsNum' int(10) unsigned NOT NULL DEFAULT '0',
'allowComment' tinyint(1) unsigned NOT NULL DEFAULT '1',
'allowPing' tinyint(1) unsigned NOT NULL DEFAULT '1',
'allowFeed' tinyint(1) unsigned NOT NULL DEFAULT '1',
'parent' int(10) unsigned NOT NULL DEFAULT '0',
'views' int(10) unsigned NOT NULL DEFAULT '0',
```

思路：

```sql
contents left join 
    - (metas join relationships on mid & m.type = tag) t1 on cid
    - (metas join relationships on mid & m.type = category) t2 on cid
    - (uid, screenName from users) on authorId = uid
    WHERE c.type in ('post', 'page')
```

