<?php
namespace app\vendor;

/**
 * 分页类
 * 生成分页 HTML 代码
 */
class Page
{
    public $AbsolutePage = 1;
    public $PageCount;
    public $Count;
    public $Size = 10;
    public $Prefix = "?p=";
    public $Suffix = "";
    public $FirstText = "首页";
    public $LastText = "末页";
    public $PrevText = "上一页";
    public $NextText = "下一页";
    public $FirstPageLink = false;

    /**
     * 构造函数
     * @param int $Count 总记录数
     * @param int $Size 每页数量
     */
    function __construct($Count = 10, $Size = 10)
    {
        $this->Count = $Count;
        $this->Size = $Size;
        $this->PageCount = ceil($this->Count / $this->Size);
        $this->Prefix = __SELF__.$this->Prefix;
    }

    /**
     * 获取页码链接
     * @param string $cssNormal 正常页码样式
     * @param string $cssSelected 选中页码样式
     * @return string 页码 HTML
     */
    public function GetPageCodes($cssNormal = "num", $cssSelected = "selected")
    {
        if ($cssNormal) {
            $cssNormal = ' class="'.$cssNormal.'"';
        }

        if ($cssSelected) {
            $cssSelected = ' class="'.$cssSelected.'"';
        }

        $sNumber = 1;
        $eNumber = 1;
        if ($this->PageCount <= $this->Size) {
            $sNumber = 1;
            $eNumber = $this->PageCount;
        } else {
            $mNumber = $this->Size % 2 == 0 ? $this->Size / 2 : ($this->Size - 1) / 2 + 1;
            $sNumber = $this->AbsolutePage - $mNumber + 1;

            if ($sNumber < 1) {
                $sNumber = 1;
            }

            $eNumber = $sNumber + $this->Size - 1;

            if ($this->PageCount + 1 <= $eNumber) {
                $eNumber = $this->PageCount;
            }
        }

        $htmlString = '';

        for ($p = $sNumber; $p <= $eNumber; $p++) {
            if ($p == $this->AbsolutePage) {
                $htmlString .= '<strong'.$cssSelected.'>'.$p.'</strong>'."\r\n";;
            } else {
                $href = $this->Prefix.$p.$this->Suffix;

                if ($p == 1 && $this->FirstPageLink) {
                    $href = $this->FirstPageLink;
                }

                $htmlString .= '<a'.$cssNormal.' href="'.$href.'">'.$p.'</a>'."\r\n";;
            }
        }

        return $htmlString;
    }

    /**
     * 获取首页链接
     * @param string $cssClass 样式类名
     * @return string 首页链接 HTML
     */
    public function GetFirst($cssClass = 'first')
    {
        if ($this->AbsolutePage == 1) {
            return '';
        }

        if ($cssClass) {
            $cssClass = ' class="'.$cssClass.'"';
        }

        $href = $this->Prefix.1.$this->Suffix;
        if ($this->PageCount > $this->Size && $this->FirstPageLink) {
            $href = $this->FirstPageLink;
        }

        return '<a'.$cssClass.' href="'.$href.'">'.$this->FirstText.'</a>'."\r\n";;
    }

    /**
     * 获取末页链接
     * @param string $cssClass 样式类名
     * @return string 末页链接 HTML
     */
    public function GetLast($cssClass = 'last')
    {
        if ($this->AbsolutePage == $this->PageCount) {
            return '';
        }

        if ($cssClass) {
            $cssClass = ' class="'.$cssClass.'"';
        }

        $href = $this->Prefix.$this->PageCount.$this->Suffix;

        return '<a'.$cssClass.' href="'.$href.'">'.$this->LastText.'</a>'."\r\n";;
    }

    /**
     * 获取下一页链接
     * @param string $cssClass 样式类名
     * @return string 下一页链接 HTML
     */
    public function GetNext($cssClass = 'next')
    {
        if ($cssClass) {
            $cssClass = ' class="'.$cssClass.'"';
        }

        if ($this->AbsolutePage < $this->PageCount) {
            $href = $this->Prefix.($this->AbsolutePage + 1).$this->Suffix;

            return '<a'.$cssClass.' href="'.$href.'">'.$this->NextText.'</a>'."\r\n";;
        }
    }

    /**
     * 获取上一页链接
     * @param string $cssClass 样式类名
     * @return string 上一页链接 HTML
     */
    public function GetPrev($cssClass = 'prev')
    {
        if ($cssClass) {
            $cssClass = ' class="'.$cssClass.'"';
        }

        if ($this->AbsolutePage > 1) {
            $href = $this->Prefix.($this->AbsolutePage - 1).$this->Suffix;

            if ($this->AbsolutePage == 2 && $this->FirstPageLink) {
                $href = $this->FirstPageLink;
            }

            return '<a'.$cssClass.' href="'.$href.'">'.$this->PrevText.'</a>'."\r\n";;
        }
    }

    /**
     * 获取总页数信息
     * @param string $cssClass 样式类名
     * @return string 总页数 HTML
     */
    public function GetTotal($cssClass = 'total')
    {
        if ($cssClass) {
            $cssClass = ' class="'.$cssClass.'"';
        }

        return '<span'.$cssClass.'>'.$this->AbsolutePage.'/'.$this->PageCount.'</span>'."\r\n";;
    }

    /**
     * 生成完整分页 HTML
     * @return string 完整分页 HTML
     */
    public function pageShow()
    {
        $show = "<ul class='page'>".$this->GetFirst().$this->GetPrev().$this->GetPageCodes()
                .$this->GetNext().$this->GetLast().$this->GetTotal().'</ul>';
        return $show;
    }
}
