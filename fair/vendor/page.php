<?php
namespace app\vendor;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Page
 *
 * @author bool
 */
class Page{

    //put your code here

    public $AbsolutePage = 1;   //当前锁定页
    public $PageCount;  //总页数量 
    public $Count;  //总记录数
    public $Size = 10;   //尺寸
    public $Prefix = "?p="; //链接前缀
    public $Suffix = "";    //链接后缀
    public $FirstText = "首页";
    public $LastText = "末页";
    public $PrevText = "上一页";
    public $NextText = "下一页";
    public $FirstPageLink = false;
	
    function __construct($Count=10,$Size=10){
        $this->Count=$Count;
        $this->Size=$Size;
        $this->PageCount = ceil( $this->Count/$this->Size );
        // echo "<a href=".__SELF__.">111111111111</a>";
        $this->Prefix=__SELF__.$this->Prefix;
        // var_dump( $this->Prefix );
    }

    public function GetPageCodes($cssNormal = "num", $cssSelected = "selected") {

        if ($cssNormal) {
            $cssNormal = ' class="' . $cssNormal . '"';
        }

        if ($cssSelected) {
            $cssSelected = ' class="' . $cssSelected . '"';
        }



        //计算出开始页码和结束页码
        $sNumber = 1;
        $eNumber = 1;
        if ($this->PageCount <= $this->Size) {
            $sNumber = 1;
			$eNumber = $this->PageCount;
        } else {

            $mNumber = $this->Size % 2 == 0 ? $this->Size / 2 : ($this->Size - 1) / 2 + 1;


            $sNumber = $this->AbsolutePage - $mNumber + 1;


            if ($sNumber < 1)
                $sNumber = 1;


            $eNumber = $sNumber + $this->Size - 1;


            if ($this->PageCount + 1 <= $eNumber) {
                $eNumber = $this->PageCount;
            }
        }


        $htmlString = '';

        for ($p = $sNumber; $p <= $eNumber; $p++) {
            if ($p == $this->AbsolutePage) {

                $htmlString.='<strong' . $cssSelected . '>' . $p . '</strong>'."\r\n";;
            } else {

                $href = $this->Prefix . $p . $this->Suffix;

                if ($p == 1 && $this->FirstPageLink) {
                    $href = $this->FirstPageLink;
                }

                $htmlString .= '<a' . $cssNormal . ' href="' . $href . '">' . $p . '</a>'."\r\n";;
            }
        }

        return $htmlString;
    }

    public function GetFirst($cssClass = 'first') {
		if($this->AbsolutePage == 1){
			return '';
		}
	
        if ($cssClass) {
            $cssClass = ' class="' . $cssClass . '"';
        }


		
        $href = $this->Prefix . 1 . $this->Suffix;
        if ($this->PageCount > $this->Size && $this->FirstPageLink) {

            $href = $this->FirstPageLink;
        }

        return '<a' . $cssClass . ' href="' . $href . '">' . $this->FirstText . '</a>'."\r\n";;
    }

    public function GetLast($cssClass = 'last') {
	
		if($this->AbsolutePage == $this->PageCount){
			return '';
		}
	
        if ($cssClass) {
            $cssClass = ' class="' . $cssClass . '"';
        }

        $href = $this->Prefix . $this->PageCount . $this->Suffix;

        return '<a' . $cssClass . ' href="' . $href . '">' . $this->LastText . '</a>'."\r\n";;
    }

    public function GetNext($cssClass = 'next') {
        if ($cssClass) {
            $cssClass = ' class="' . $cssClass . '"';
        }

        if ($this->AbsolutePage < $this->PageCount) {

            $href = $this->Prefix . ($this->AbsolutePage + 1) . $this->Suffix;

            return '<a' . $cssClass . ' href="' . $href . '">' . $this->NextText . '</a>'."\r\n";;
        }
    }

    public function GetPrev($cssClass = 'prev') {
        if ($cssClass) {
            $cssClass = ' class="' . $cssClass . '"';
        }

        if ($this->AbsolutePage > 1) {
            $href = $this->Prefix . ($this->AbsolutePage - 1) . $this->Suffix;

            if ($this->AbsolutePage == 2 && $this->FirstPageLink) {
                $href = $this->FirstPageLink;
            }

            return '<a' . $cssClass . ' href="' . $href . '">' . $this->PrevText . '</a>'."\r\n";;
        }
    }

    public function GetTotal($cssClass = 'total') {

        if ($cssClass) {
            $cssClass = ' class="' . $cssClass . '"';
        }

        return '<span' . $cssClass . '>' . $this->AbsolutePage . '/' . $this->PageCount . '</span>'."\r\n";;
    }

    // public function ToString() {

    //     return $this->GetFirst() . $this->GetPrev() . $this->GetPageCodes() 
    //             . $this->GetNext() . $this->GetLast() . $this->GetTotal();
    // }

    public function pageShow(){
        $show ="<ul class='page'>" .$this->GetFirst() . $this->GetPrev() . $this->GetPageCodes() 
                . $this->GetNext() . $this->GetLast() . $this->GetTotal().'</ul>';     
        return $show;        
    }

}



