<?php
// app/helpers/MarkdownHelper.php

class MarkdownHelper {
    
    /**
     * Convert markdown to HTML
     * Simple markdown parser for basic formatting
     */
    public static function toHtml($markdown) {
        if (empty($markdown)) {
            return '';
        }
        
        // Escape HTML first to prevent XSS
        $html = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');
        
        // Convert markdown to HTML
        $html = self::parseMarkdown($html);
        
        return $html;
    }
    
    /**
     * Parse basic markdown syntax
     */
    private static function parseMarkdown($text) {
        // Headers
        $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);
        $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
        $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
        
        // Bold and Italic
        $text = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<strong><em>$1</em></strong>', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $text);
        $text = preg_replace('/\_\_(.*?)\_\_/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/\_(.*?)\_/s', '<em>$1</em>', $text);
        
        // Strikethrough
        $text = preg_replace('/~~(.*?)~~/s', '<del>$1</del>', $text);
        
        // Code blocks (triple backticks)
        $text = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $text);
        
        // Inline code
        $text = preg_replace('/`(.*?)`/', '<code>$1</code>', $text);
        
        // Images - ![alt text](url)
        $text = preg_replace('/!\[([^\]]*)\]\(([^\)]+)\)/', '<img src="$2" alt="$1" class="post-image">', $text);
        
        // Handle our custom image URLs that might be saved as links instead of images
        // Convert links to our image URLs into proper images
        $text = preg_replace('/\[([^\]]*)\]\((\/my-blog\/public\/\?url=image\/[^\)]+)\)/', '<img src="$2" alt="$1" class="post-image">', $text);
        
        // Links - [text](url)
        $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
        
        // Blockquotes
        $text = preg_replace('/^> (.*?)$/m', '<blockquote>$1</blockquote>', $text);
        
        // Unordered lists
        $text = preg_replace_callback('/(?:^[\*\-\+] (.+)$\n?)+/m', function($matches) {
            $items = preg_replace('/^[\*\-\+] (.+)$/m', '<li>$1</li>', $matches[0]);
            return '<ul>' . $items . '</ul>';
        }, $text);
        
        // Ordered lists
        $text = preg_replace_callback('/(?:^\d+\. (.+)$\n?)+/m', function($matches) {
            $items = preg_replace('/^\d+\. (.+)$/m', '<li>$1</li>', $matches[0]);
            return '<ol>' . $items . '</ol>';
        }, $text);
        
        // Horizontal rule
        $text = preg_replace('/^---$/m', '<hr>', $text);
        
        // Line breaks (preserve existing line breaks)
        $text = nl2br($text);
        
        return $text;
    }
    
    /**
     * Get a preview of markdown content (for home page)
     */
    public static function getPreview($markdown, $length = 200) {
        // Strip markdown syntax for preview
        $text = self::stripMarkdown($markdown);
        
        if (strlen($text) > $length) {
            return substr($text, 0, $length) . '...';
        }
        
        return $text;
    }
    
    /**
     * Strip markdown syntax to get plain text
     */
    private static function stripMarkdown($text) {
        // Remove images - both regular and our custom image links
        $text = preg_replace('/!\[([^\]]*)\]\(([^\)]+)\)/', '', $text);
        $text = preg_replace('/\[([^\]]*)\]\((\/my-blog\/public\/\?url=image\/[^\)]+)\)/', '', $text);
        
        // Remove links but keep text
        $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '$1', $text);
        
        // Remove formatting
        $text = preg_replace('/\*\*\*(.*?)\*\*\*/s', '$1', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/s', '$1', $text);
        $text = preg_replace('/\*(.*?)\*/s', '$1', $text);
        $text = preg_replace('/\_\_(.*?)\_\_/s', '$1', $text);
        $text = preg_replace('/\_(.*?)\_/s', '$1', $text);
        $text = preg_replace('/~~(.*?)~~/s', '$1', $text);
        $text = preg_replace('/`(.*?)`/', '$1', $text);
        $text = preg_replace('/```(.*?)```/s', '$1', $text);
        
        // Remove headers
        $text = preg_replace('/^#{1,6} (.*?)$/m', '$1', $text);
        
        // Remove blockquotes
        $text = preg_replace('/^> (.*?)$/m', '$1', $text);
        
        // Remove list markers
        $text = preg_replace('/^[\*\-\+] (.+)$/m', '$1', $text);
        $text = preg_replace('/^\d+\. (.+)$/m', '$1', $text);
        
        return trim($text);
    }
}
