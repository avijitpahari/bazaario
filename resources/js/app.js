import './bootstrap';
import Alpine from 'alpinejs';
import { marked } from 'marked';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.L = L;

// Configure marked for chat markdown
marked.setOptions({
    breaks: true,
    gfm: true
});

window.marked = marked;

// Global helper for rendering formatted chat markdown
window.renderChatMarkdown = function(text) {
    if (!text) return '';

    // Clean up ugly asterisks, quotes, and awkward bullet formats
    let clean = String(text)
        // Remove awkward asterisk quotes: - *"Where is my order?"* -> - Where is my order?
        .replace(/^[\s]*[-*•]\s*[*_]["']?([^"'\n]+?)["']?[*_]?\s*$/gm, '- $1')
        // Remove quotes around bullets: - "Where is my order?" -> - Where is my order?
        .replace(/^[\s]*[-*•]\s*["']([^"'\n]+?)["']\s*$/gm, '- $1')
        // Remove rogue asterisk quotes: *"text"* -> text
        .replace(/\*["']([^"']+)["']\*/g, '$1')
        .replace(/["']\*([^*]+)\*["']/g, '$1');

    try {
        if (window.marked && typeof window.marked.parse === 'function') {
            return window.marked.parse(clean);
        }
    } catch (e) {
        console.warn('marked parse error:', e);
    }

    // Safe regex fallback
    let escaped = clean
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
    escaped = escaped.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    escaped = escaped.replace(/\*(.+?)\*/g, '<em>$1</em>');
    escaped = escaped.replace(/`([^`]+)`/g, '<code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded font-mono text-[11px]">$1</code>');
    escaped = escaped.replace(/^[\s]*[-*•]\s+(.+)$/gm, '<li class="ml-4">$1</li>');
    escaped = escaped.replace(/(<li[\s\S]*?<\/li>)/g, '<ul class="my-1.5 space-y-0.5">$1</ul>');
    escaped = escaped.replace(/\n/g, '<br>');
    return escaped;
};

window.Alpine = Alpine;

Alpine.start();

