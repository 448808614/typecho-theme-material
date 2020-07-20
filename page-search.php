<?php
/**
 * 文章索引
 *
 * @package custom
 */

header("content-type: text/xml; charset=utf-8");

/**
 * Adds a CDATA property to an XML document.
 *
 * @param string $name
 *   Name of property that should contain CDATA.
 * @param string $value
 *   Value that should be inserted into a CDATA child.
 * @param object $parent
 *   Element that the CDATA child should be attached too.
 */
$add_cdata = function($name, $value, &$parent) {
    $child = $parent->addChild($name);

    if ($child !== NULL) {
        $child_node = dom_import_simplexml($child);
        $child_owner = $child_node->ownerDocument;
        $child_node->appendChild($child_owner->createCDATASection($value));
    }

    return $child;
};

$archive = null;
$this->widget('Widget_Contents_Post_Recent', 'pageSize=10000')->to($archive);

$structure = '<?xml version="1.0" encoding="UTF-8"?><!-- This is an index of posts for the search module, generated by idawnlight/typecho-theme-material. --><search></search>';
$xml = new SimpleXMLElement($structure);

while ($archive->next()) {
    $entry = $xml->addChild('entry');
    $add_cdata('title', $archive->title, $entry);
    $entry->addChild('url', $archive->permalink);
    $add_cdata('content', $archive->content, $entry)->addAttribute('type', 'html');
    $categories = $entry->addChild('categories');
    if ($archive->categories) {
        foreach ($archive->categories as $category) {
            $categories->addChild('category', $category['name']);
        }
    }
    $tags = $entry->addChild('tags');
    if ($archive->tags) {
        foreach ($archive->tags as $tag) {
            $categories->addChild('tag', $tag['name']);
        }
    }
}

echo $xml->asXML();

