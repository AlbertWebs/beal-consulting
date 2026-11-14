<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

class RichTextSanitizer
{
    /**
     * Remove unsafe tags, attributes, and URL schemes while preserving editor formatting.
     */
    public function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML('<?xml encoding="UTF-8"><div id="cms-content">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('cms-content');

        if ($root === null) {
            return '';
        }

        $this->sanitizeChildren($root);

        $result = '';

        foreach ($root->childNodes as $child) {
            $result .= $document->saveHTML($child);
        }

        return $result;
    }

    private function sanitizeChildren(DOMNode $node): void
    {
        $allowed = ['p', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'blockquote', 'br', 'a', 'img', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'hr', 'div', 'span'];
        $removeWithContent = ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'form', 'input', 'button', 'video', 'audio'];

        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, $removeWithContent, true)) {
                $node->removeChild($child);

                continue;
            }

            $this->sanitizeChildren($child);

            if (! in_array($tag, $allowed, true)) {
                while ($child->firstChild !== null) {
                    $node->insertBefore($child->firstChild, $child);
                }

                $node->removeChild($child);

                continue;
            }

            $attributes = iterator_to_array($child->attributes);

            foreach ($attributes as $attribute) {
                $name = strtolower($attribute->name);
                $value = trim($attribute->value);
                $permitted = ($tag === 'a' && in_array($name, ['href', 'title'], true))
                    || ($tag === 'img' && in_array($name, ['src', 'alt', 'width', 'height'], true))
                    || (in_array($tag, ['td', 'th'], true) && in_array($name, ['colspan', 'rowspan'], true))
                    || (in_array($tag, ['p', 'div', 'span', 'h2', 'h3', 'h4', 'h5', 'h6'], true) && $name === 'style' && preg_match('/^text-align:\s*(left|right|center|justify)\s*;?$/i', $value) === 1);

                if (! $permitted || ($name === 'href' && ! $this->isSafeLink($value)) || ($name === 'src' && ! $this->isSafeImage($value))) {
                    $child->removeAttributeNode($attribute);
                }
            }

            if ($tag === 'a' && $child->hasAttribute('href')) {
                $child->setAttribute('rel', 'noopener noreferrer');
            }
        }
    }

    private function isSafeLink(string $url): bool
    {
        return str_starts_with($url, '/') || str_starts_with($url, '#') || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:') || filter_var($url, FILTER_VALIDATE_URL) !== false && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }

    private function isSafeImage(string $url): bool
    {
        if ((str_starts_with($url, '/storage/') || str_starts_with($url, '/assets/')) && ! str_starts_with($url, '//')) {
            return true;
        }

        $disk = (string) config('filesystems.cms_media_disk', 'public');
        $mediaUrl = rtrim((string) config('filesystems.disks.'.$disk.'.url', ''), '/');
        $allowedUrl = parse_url($mediaUrl);
        $imageUrl = parse_url($url);

        if ($allowedUrl === false || $imageUrl === false || ! isset($allowedUrl['host'], $imageUrl['host'])) {
            return false;
        }

        $allowedPath = rtrim($allowedUrl['path'] ?? '', '/');
        $imagePath = $imageUrl['path'] ?? '';

        if (config('filesystems.disks.'.$disk.'.driver') === 's3') {
            $root = trim((string) config('filesystems.disks.'.$disk.'.root', ''), '/');
            $allowedPath = rtrim($allowedPath.'/'.$root.'/cms-media', '/');
        }

        return ($imageUrl['scheme'] ?? null) === 'https'
            && strcasecmp($allowedUrl['host'], $imageUrl['host']) === 0
            && $allowedPath !== ''
            && str_starts_with($imagePath, $allowedPath.'/');
    }
}
