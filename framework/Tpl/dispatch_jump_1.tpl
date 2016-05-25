<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<extend name="Common@Public/common" />
<block name="body-main">
<div class="container">
  <div class="panel p_1">

    <?php if(isset($message)) {?>
    <div class="biaoti b_1">
    <span><i class="fa fa-check-circle-o fa-1x" style="color:#25982E;"></i>&nbsp; <?php echo($message); ?></span>
     </div>
    <?php }else{?>
    <div class="biaoti b_2">
    <span><i class="fa fa-times-circle-o fa-1x" style="color:#BE1414;"></i>&nbsp; <?php echo($error); ?></span>
    </div>
    <?php }?>

    <div class="a_c">
      <p>您现在可以：</p>
      <p>1.返回上一页</p>
      <p>2.回到首页</p>
      <p>3.页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b></p>
        <a href="<?php echo($_SERVER['HTTP_REFERER']); ?>" class="btn btn-info btn-lg">返回</a>
        <a href="/" class="btn btn-success btn-lg">首页</a>
    </div>
    <div class="foot"></div>
  </div>
</div>
</block>
<block name="footer">
<script type="text/javascript">
// (function(){
// var wait = document.getElementById('wait'),href = document.getElementById('href').href;
// var interval = setInterval(function(){
// 	var time = --wait.innerHTML;
// 	if(time <= 0) {
// 		location.href = href;
// 		clearInterval(interval);
// 	};
// }, 1000);
// })();
</script>
</block>
