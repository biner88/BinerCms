<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// |         lanfengye <zibin_5257@163.com>
//           biner <biner@foxmail.com> for Bootstrap
// +----------------------------------------------------------------------
namespace Vendor;
class Page {

    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  =    array('header'=>'条','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    // 默认分页变量名
    protected $varPage;

    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows,$listRows='',$parameter='',$url='') {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
        $this->varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages    =   ceil($this->totalPages/$this->rollPage);
        $this->nowPage      =   !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if($this->nowPage<1){
            $this->nowPage  =   1;
        }elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
        if(!empty($url))    $this->url  =   $url;
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }
    //获取连续分页 by biner 2013-01-24
    public function getRowLink($nowPage,$url,$i,$typeName=''){
        if($nowPage == $i){
            $linkPage = "<li class='active'><span>".$i."</span></li>\r\n";
        }else{
            if(!empty($typeName)){
                $linkPage = "<li><a href='javascript:".$typeName."(\"".$this->comitUrl($i,$url)."\")'>".$i."</a></li>\r\n";
            }else{
                $linkPage = "<li><a href='".$this->comitUrl($i,$url)."'>".$i."</a></li>\r\n";
            }

        }
        return $linkPage;
    }
    //by biner
    public function comitUrl($row,$url){
        $url[$this->varPage] = $row;
        if(!in_array(C('VAR_GROUP'), $url) && defined('IS_ADMIN') && IS_ADMIN== 1){
            //$url[C('VAR_GROUP')]  =  MODEL_NAME;
        }
        if (IS_ADMIN==1) {
            return U('',$url);
        }else{
            return U2('',$url);
        }
        
    }
    /**
     * 分页显示输出
     * @access public
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U2('/'.$this->url,'',false),$depr).$depr.'__PAGE__';
        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            //$url            =   U2('',$parameter);
        }
        //上下翻页字符串
        $upRow          =   $this->nowPage-1;
        $downRow        =   $this->nowPage+1;
        if ($upRow>0){
            $upPage     =   "<li><a href='".$this->comitUrl($upRow,$parameter)."'>".$this->config['prev']."</a></li>\r\n";
        }else{
            $upPage     =   '';
        }

        if ($downRow <= $this->totalPages){
            $downPage   =   "<li><a href='".$this->comitUrl($downRow,$parameter)."'>".$this->config['next']."</a></li>\r\n";
        }else{
            $downPage   =   '';
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst   =   '';
            $prePage    =   '';
        }else{
            $preRow     =   $this->nowPage-$this->rollPage;
            $prePage    =   "<li><a href='".$this->comitUrl($preRow,$parameter)."' >上".$this->rollPage."页</a></li>\r\n";
            $theFirst   =   "<li><a href='".$this->comitUrl(1,$parameter)."' >".$this->config['first']."</a></li>\r\n";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage   =   '';
            $theEnd     =   '';
        }else{
            $nextRow    =   $this->nowPage+$this->rollPage;
            $theEndRow  =   $this->totalPages;
            $nextPage   =   "<li><a href='".$this->comitUrl($nextRow,$parameter)."' >下".$this->rollPage."页</a></li>\r\n";
            $theEnd     =   "<li><a href='".$this->comitUrl($theEndRow,$parameter)."' >".$this->config['last']."</a></li>\r\n";
        }
        // 1 2 3 4 5
        $linkPage = "";
        //by biner 2013-01-24 start
        //更改连续分页样式,让当前页面在最中间
        $linkNum = ($this->rollPage-1)/2;
        if($this->totalPages<=$this->rollPage){
            //总页数小于连续链接数时
            for($i=1;$i<=$this->totalPages;$i++){
                $linkPage .= $this->getRowLink($this->nowPage,$parameter,$i);
            }
        }else{
            //总页数大于连续链接数时
            if(($this->nowPage-$linkNum)<=0){
                for($i=1;$i<=$this->rollPage;$i++){
                    $linkPage .= $this->getRowLink($this->nowPage,$parameter,$i);
                }
            }else{
                if(($this->nowPage+$linkNum)>=$this->totalPages){
                    for($i=($this->totalPages-$this->rollPage+1);$i<=$this->totalPages;$i++){
                        $linkPage .= $this->getRowLink($this->nowPage,$parameter,$i);
                    }
                }else{
                    for($i=($this->nowPage-$linkNum);$i<=($this->nowPage+$linkNum);$i++){
                        $linkPage .= $this->getRowLink($this->nowPage,$parameter,$i);
                    }
                }
            }
        }
        $pageStr     =   str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
        return $pageStr;
    }

}
