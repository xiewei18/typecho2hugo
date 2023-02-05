<?php
include_once 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
  <div class="body container">
    <div class="typecho-page-title">
      <h2><?php _e('数据导出'); ?></h2>
    </div>
    <div class="row typecho-page-main" role="form">
      <div id="dbmanager-plugin" class="col-mb-12 col-tb-8 col-tb-offset-2">
        <p>在您点击下面的按钮后，Typecho 会创建一个 名为 hugo-yyyymmdd.zip 的 Zip 压缩文件 (通常位于 /tmp/Export2Hugo/ 目录内)，包含所有的文章和页面，供您保存到计算机中。</p>
        <p>导出的文件是 Markdown 格式，可以直接导入到 Hugo 中。</p>
        <p>压缩包中，文件名将以 yyyy-mm-dd postname.md 的格式存储。</p>
        <p>点击后请稍候，压缩包将被自动下载到您的本地计算机。</p>
        <p>使用过程中如果有问题，请到 <a href="https://github.com/xiewei18/typecho2hugo/issues">Github</a> 提出。</p>
        <p>Version: 1.0.0, last modified by <a href="https://www.xiewei.link">Wei</a></p>
        <form action="<?php $options->index('/action/export2hugo?export'); ?>" method="post">
          <ul class="typecho-option typecho-option-submit" id="typecho-option-item-submit-3">
            <li>
              <button type="submit" class="primary"><?php _e('开始导出！'); ?></button>
            </li>
          </ul>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'table-js.php';
include 'footer.php';
?>
