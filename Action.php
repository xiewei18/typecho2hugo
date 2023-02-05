<?php

class Export2Hugo_Action extends Typecho_Widget implements Widget_Interface_Do
{
  /**
   * 导出文章
   *
   * @access public
   * @return void
   */
  public function doExport() {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    $sql=<<<TEXT
select u.screenName author,u.url authorUrl,c.title,c.type,c.text,c.created,c.status status,c.password,t2.category,t1.tags,c.slug from {$prefix}contents c
left join
(select cid,CONCAT('"',group_concat(m.name SEPARATOR '","'),'"') tags from {$prefix}metas m,{$prefix}relationships r where m.mid=r.mid and m.type='tag' group by cid ) t1
on c.cid=t1.cid
left join
(select cid,CONCAT('"',GROUP_CONCAT(m.name SEPARATOR '","'),'"') category from {$prefix}metas m,{$prefix}relationships r where m.mid=r.mid and m.type='category' group by cid) t2
on c.cid=t2.cid
left join ( select uid, screenName ,url from {$prefix}users)  as u
on c.authorId = u.uid
where c.type in ('post', 'page')
TEXT;
    $contents = $db->fetchAll($db->query($sql));
    
    $dir = sys_get_temp_dir()."/Export2Hugo";
    if(file_exists($dir)) {
      // exec("rm -rf $dir");
      delTree($dir);
    }
    mkdir($dir);

    $contentDir = $dir."/content/";
    mkdir($contentDir);
    mkdir($contentDir."/posts");

    foreach($contents as $content) {
      $title = $content["title"];
      $categories = $content["category"];
      $tags = $content["tags"];
      $slug = $content["slug"];
      $time = date('Y-m-d H:i:s', $content["created"]);
      $time_ymd = date('Y-m-d', $content["created"]);
      $text = str_replace("<!--markdown-->", "", $content["text"]);
      $draft = $content["status"] !== "publish" || $content["password"] ? "true" : "false";
      $hugo = <<<TMP
---
title: "$title"
categories: [ $categories ]
tags: [ $tags ]
draft: $draft
slug: "$slug"
date: "$time"
---
$text
TMP;
      $title = str_replace(array(" ","?","\\","/",":","|","*","：","。","？","，","《","》","·",",","、"),' ',$title);
      $filename = $time_ymd." ".str_replace('  ',' ',$title).".md";
      if($content["type"] === "post") {
        $filename = "posts/".$filename;
      }
      file_put_contents($contentDir.$filename, $hugo);
      echo $contentDir.$filename;
    }
  
    $out_filename = "hugo-".date('Ymd').".zip";
    $outputFile = $dir."/".$out_filename;
    // exec("cd $dir && zip -q -r $outputFile content");
    $zip=new ZipArchive();
    // open($outputFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    if($zip->open($outputFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)=== TRUE){
        addFilesToZip($contentDir, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
        $zip->close(); //关闭处理的zip文件
    }
    
    header("Content-Type:application/zip");
    header("Content-Disposition: attachment; filename=$out_filename");
    header("Content-length: ".filesize($outputFile));
    header("Pragma: no-cache"); 
    header("Expires: 0"); 

    readfile($outputFile);
  }

  /**
   * 绑定动作
   *
   * @access public
   * @return void
   */
  public function action() {
    $this->widget('Widget_User')->pass('administrator');
    $this->on($this->request->is('export'))->doExport();
  }
}

function delTree($dir) {
  $files = array_diff(scandir($dir), array('.','..'));
   foreach ($files as $file) {
     (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
   }
   return rmdir($dir);
 }

function addFilesToZip($path,$zip){
  $handler=opendir($path); //打开当前文件夹由$path指定。
  while(($filename=readdir($handler))!==false){
      if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..'，不要对他们进行操作
          if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
              addFilesToZip($path."/".$filename, $zip);
          }else{ //将文件加入zip对象
              $zip->addFile($path."/".$filename,"hugo/".$filename);
          }
      }
  }
  @closedir($path);
}


?>