<?php

/**
 * XSS 过滤类
 * 用于过滤富文本中的 XSS 恶意代码
 *
 * 使用示例:
 * $html = '<script>alert(1)</script><img src=x onerror=alert(1)>';
 * $xss = new Xss($html);
 * $safeHtml = $xss->getHtml();
 */
class Xss
{
    /**
     * DOM 文档对象
     * @var DOMDocument
     */
    private $m_dom;

    /**
     * 过滤后的 HTML
     * @var string
     */
    private $m_xss;

    /**
     * 加载是否成功
     * @var bool
     */
    private $m_ok;

    /**
     * 允许的属性列表
     * @var array
     */
    private $m_AllowAttr = array('title', 'src', 'href', 'id', 'class', 'style', 'width', 'height', 'alt', 'target', 'align');

    /**
     * 允许的标签列表
     * @var array
     */
    private $m_AllowTag = array('a', 'img', 'br', 'strong', 'b', 'code', 'pre', 'p', 'div', 'em', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'table', 'ul', 'ol', 'tr', 'th', 'td', 'hr', 'li', 'u');

    /**
     * 构造函数
     * @param string $html 待过滤的 HTML 文本
     * @param string $charset 文本编码，默认 utf-8
     * @param array $AllowTag 允许的标签列表
     */
    public function __construct($html, $charset = 'utf-8', $AllowTag = array())
    {
        $this->m_AllowTag = empty($AllowTag) ? $this->m_AllowTag : $AllowTag;
        $this->m_xss = strip_tags($html, '<'.implode('><', $this->m_AllowTag).'>');
        if (empty($this->m_xss)) {
            $this->m_ok = FALSE;
            return;
        }
        $this->m_xss = "<meta http-equiv=\"Content-Type\" content=\"text/html;charset={$charset}\"><nouse>".$this->m_xss."</nouse>";
        $this->m_dom = new DOMDocument();
        $this->m_dom->strictErrorChecking = FALSE;
        $this->m_ok = @$this->m_dom->loadHTML($this->m_xss);
    }

    /**
     * 获取过滤后的 HTML
     * @return string 过滤后的安全 HTML
     */
    public function getHtml()
    {
        if (!$this->m_ok) {
            return '';
        }
        $nodeList = $this->m_dom->getElementsByTagName('*');
        for ($i = 0; $i < $nodeList->length; $i++) {
            $node = $nodeList->item($i);
            if (in_array($node->nodeName, $this->m_AllowTag)) {
                if (method_exists($this, "__node_{$node->nodeName}")) {
                    call_user_func(array($this, "__node_{$node->nodeName}"), $node);
                } else {
                    call_user_func(array($this, '__node_default'), $node);
                }
            }
        }
        $html = strip_tags($this->m_dom->saveHTML(), '<'.implode('><', $this->m_AllowTag).'>');
        $html = preg_replace('/^\n(.*)\n$/s', '$1', $html);
        return $html;
    }

    /**
     * 验证 URL 是否合法
     * @param string $url URL 地址
     * @return string 处理后的 URL
     */
    private function __true_url($url)
    {
        if (preg_match('#^https?://.+#is', $url)) {
            return $url;
        } else {
            return 'http://'.$url;
        }
    }

    /**
     * 获取并过滤 style 属性
     * @param node $node DOM 节点
     * @return string 过滤后的样式
     */
    private function __get_style($node)
    {
        if ($node->attributes->getNamedItem('style')) {
            $style = $node->attributes->getNamedItem('style')->nodeValue;
            $style = str_replace('\\', ' ', $style);
            $style = str_replace(array('&#', '/*', '*/'), ' ', $style);
            $style = preg_replace('#e.*x.*p.*r.*e.*s.*s.*i.*o.*n#Uis', ' ', $style);
            return $style;
        } else {
            return '';
        }
    }

    /**
     * 获取链接属性
     * @param node $node DOM 节点
     * @param string $att 属性名
     * @return string 处理后的链接
     */
    private function __get_link($node, $att)
    {
        $link = $node->attributes->getNamedItem($att);
        if ($link) {
            return $this->__true_url($link->nodeValue);
        } else {
            return '';
        }
    }

    /**
     * 设置节点属性
     * @param DOMNode $dom DOM 节点
     * @param string $attr 属性名
     * @param string $val 属性值
     */
    private function __setAttr($dom, $attr, $val)
    {
        if (!empty($val)) {
            $dom->setAttribute($attr, $val);
        }
    }

    /**
     * 设置默认属性
     * @param node $node DOM 节点
     * @param string $attr 属性名
     * @param string $default 默认值
     */
    private function __set_default_attr($node, $attr, $default = '')
    {
        $o = $node->attributes->getNamedItem($attr);
        if ($o) {
            $this->__setAttr($node, $attr, $o->nodeValue);
        } else {
            $this->__setAttr($node, $attr, $default);
        }
    }

    /**
     * 处理通用属性
     * @param node $node DOM 节点
     */
    private function __common_attr($node)
    {
        $list = array();
        foreach ($node->attributes as $attr) {
            if (!in_array($attr->nodeName, $this->m_AllowAttr)) {
                $list[] = $attr->nodeName;
            }
        }
        foreach ($list as $attr) {
            $node->removeAttribute($attr);
        }
        $style = $this->__get_style($node);
        $this->__setAttr($node, 'style', $style);
        $this->__set_default_attr($node, 'title');
        $this->__set_default_attr($node, 'id');
        $this->__set_default_attr($node, 'class');
    }

    /**
     * 处理 img 标签
     * @param node $node DOM 节点
     */
    private function __node_img($node)
    {
        $this->__common_attr($node);
        $this->__set_default_attr($node, 'src');
        $this->__set_default_attr($node, 'width');
        $this->__set_default_attr($node, 'height');
        $this->__set_default_attr($node, 'alt');
        $this->__set_default_attr($node, 'align');
    }

    /**
     * 处理 a 标签
     * @param node $node DOM 节点
     */
    private function __node_a($node)
    {
        $this->__common_attr($node);
        $href = $this->__get_link($node, 'href');
        $this->__setAttr($node, 'href', $href);
        $this->__set_default_attr($node, 'target', '_blank');
    }

    /**
     * 处理 embed 标签
     * @param node $node DOM 节点
     */
    private function __node_embed($node)
    {
        $this->__common_attr($node);
        $link = $this->__get_link($node, 'src');
        $this->__setAttr($node, 'src', $link);
        $this->__setAttr($node, 'allowscriptaccess', 'never');
        $this->__set_default_attr($node, 'width');
        $this->__set_default_attr($node, 'height');
    }

    /**
     * 处理默认标签
     * @param node $node DOM 节点
     */
    private function __node_default($node)
    {
        $this->__common_attr($node);
    }
}
