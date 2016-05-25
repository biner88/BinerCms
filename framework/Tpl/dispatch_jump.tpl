<?php
if (defined('IS_ADMIN')) {
?>
<include file="Admin@Public/dispatch_jump" />
<?php
}else{
?>
<include file="Public/dispatch_jump" />
<?php
}
?>
