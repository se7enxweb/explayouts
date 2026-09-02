<?php
/**
 * response/output event listener that renders an expl block debug overlay
 * when the page is requested with ?expl_debug=1.
 *
 * The overlay behaves like a Symfony-style debug toolbar: a thin bar at the
 * bottom of the viewport with a minimize and a close button. The expanded
 * panel shows the expl-block summary table. State is remembered in
 * localStorage so collapsing / dismissing persists across pages in the same
 * session without requiring a reload.
 */

require_once 'extension/explayouts/classes/explblockparser.php';

class ExplBlockDebug
{
    public static function output( $output )
    {
        $http = eZHTTPTool::instance();
        $debug = $http->getVariable( 'expl_debug' ) == '1';

        if ( $debug )
        {
            $parsed = ExplBlockParser::parse( $output );
            $blocks = $parsed['blocks'];

            $totalBlocks = 0;
            $tableRows = '';

            if ( empty( $blocks ) )
            {
                $tableRows = '<p>No expl blocks found.</p>';
            }
            else
            {
                $tableRows .= '<table class="expl-debug-table">';
                $tableRows .= '<thead><tr><th align="left">name</th><th align="left">occurrences</th><th align="left">total bytes</th></tr></thead>';
                $tableRows .= '<tbody>';

                foreach ( $blocks as $name => $list )
                {
                    $count = count( $list );
                    $totalBlocks += $count;
                    $size = 0;
                    foreach ( $list as $content )
                    {
                        $size += strlen( $content );
                    }
                    $tableRows .= '<tr>';
                    $tableRows .= '<td>' . htmlspecialchars( $name, ENT_QUOTES ) . '</td>';
                    $tableRows .= '<td>' . (int)$count . '</td>';
                    $tableRows .= '<td>' . (int)$size . '</td>';
                    $tableRows .= '</tr>';
                }

                $tableRows .= '</tbody></table>';
            }

            $regionCount = count( $blocks );
            $summary = $regionCount . ' region' . ( $regionCount === 1 ? '' : 's' ) . ', ' . $totalBlocks . ' occurrence' . ( $totalBlocks === 1 ? '' : 's' );

            $html = '<div id="explDebugToolbar" class="expl-debug-toolbar" data-default-state="open">';
            $html .= '<div class="expl-debug-bar">';
            $html .= '<span class="expl-debug-brand">expl blocks</span>';
            $html .= '<span class="expl-debug-summary">' . htmlspecialchars( $summary ) . '</span>';
            $html .= '<span class="expl-debug-actions">';
            $html .= '<button id="explDebugToggle" type="button" class="expl-debug-btn" title="Minimize">_</button>';
            $html .= '<button id="explDebugClose" type="button" class="expl-debug-btn" title="Close (without reload)">×</button>';
            $html .= '</span>';
            $html .= '</div>';
            $html .= '<div id="explDebugPanel" class="expl-debug-panel">' . $tableRows . '</div>';
            $html .= '<style>' . self::toolbarCss() . '</style>';
            $html .= '<script>' . self::toolbarJs() . '</script>';
            $html .= '</div>';

            if ( strpos( $output, '</body>' ) !== false )
            {
                $output = str_replace( '</body>', $html . '</body>', $output );
            }
            else
            {
                $output .= $html;
            }
        }

        return ExplBlockParser::strip( $output );
    }

    /**
     * Inline CSS for the expl debug toolbar.
     */
    private static function toolbarCss()
    {
        return <<<'CSS'
#explDebugToolbar {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100000;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-size: 12px;
    line-height: 1.4;
    color: #f0f0f0;
    background: #222;
    border-top: 2px solid #00a86b;
    box-shadow: 0 -2px 6px rgba(0,0,0,0.35);
}
.expl-debug-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 40px;
    padding: 0 16px;
    white-space: nowrap;
    cursor: pointer;
}
.expl-debug-bar:hover {
    background: #2a2a2a;
}
.expl-debug-brand {
    font-weight: 700;
    color: #00a86b;
    margin-right: 12px;
}
.expl-debug-summary {
    flex: 1;
    color: #ccc;
    overflow: hidden;
    text-overflow: ellipsis;
}
.expl-debug-actions {
    display: flex;
    gap: 4px;
}
.expl-debug-btn {
    background: #444;
    color: #fff;
    border: none;
    border-radius: 3px;
    width: 28px;
    height: 24px;
    line-height: 24px;
    text-align: center;
    font-size: 16px;
    cursor: pointer;
    padding: 0;
    margin: 0;
}
.expl-debug-btn:hover {
    background: #555;
}
.expl-debug-btn:active {
    background: #333;
}
.expl-debug-panel {
    display: none;
    max-height: 300px;
    overflow: auto;
    background: #1a1a1a;
    border-top: 1px solid #333;
    padding: 16px;
}
.expl-debug-panel p {
    margin: 0;
    color: #999;
}
.expl-debug-table {
    width: 100%;
    border-collapse: collapse;
    background: transparent;
    color: #f0f0f0;
}
.expl-debug-table th,
.expl-debug-table td {
    border: 1px solid #444;
    padding: 6px 10px;
    text-align: left;
    vertical-align: top;
}
.expl-debug-table th {
    background: #2c2c2c;
    color: #fff;
    font-weight: 600;
}
.expl-debug-table tr:nth-child(even) {
    background: #222;
}
CSS;
    }

    /**
     * Inline JS for minimize / close behavior and localStorage persistence.
     *
     * - Minimize / expand is persisted in localStorage.
     * - Close is persisted in sessionStorage so it hides the toolbar for the
     *   current tab/session but it reappears on a fresh tab with ?expl_debug=1.
     */
    private static function toolbarJs()
    {
        return <<<'JS'
(function() {
    var toolbar = document.getElementById('explDebugToolbar');
    var panel = document.getElementById('explDebugPanel');
    var toggle = document.getElementById('explDebugToggle');
    var close = document.getElementById('explDebugClose');
    var bar = toolbar ? toolbar.querySelector('.expl-debug-bar') : null;
    var storageKey = 'explDebugState';
    var closedKey = 'explDebugClosed';

    function defaultState() {
        return (toolbar && toolbar.getAttribute('data-default-state')) || 'open';
    }

    function isClosed() {
        try { return sessionStorage.getItem(closedKey) === '1'; } catch (e) { return false; }
    }

    function setClosed(closed) {
        try {
            if (closed) sessionStorage.setItem(closedKey, '1');
            else sessionStorage.removeItem(closedKey);
        } catch (e) {}
    }

    function getState() {
        if (isClosed()) return 'closed';
        try { return localStorage.getItem(storageKey) || defaultState(); } catch (e) { return defaultState(); }
    }

    function setState(state) {
        setClosed(false);
        try { localStorage.setItem(storageKey, state); } catch (e) {}
    }

    function applyState(state) {
        if (!toolbar || !panel || !toggle) return;
        if (state === 'closed') {
            toolbar.style.display = 'none';
        } else if (state === 'minimized') {
            toolbar.style.display = '';
            panel.style.display = 'none';
            toggle.textContent = '+';
            toggle.setAttribute('title', 'Expand');
        } else {
            toolbar.style.display = '';
            panel.style.display = 'block';
            toggle.textContent = '_';
            toggle.setAttribute('title', 'Minimize');
        }
    }

    if (toggle) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isClosed() || panel.style.display === 'none') {
                setState('open');
                applyState('open');
            } else {
                setState('minimized');
                applyState('minimized');
            }
        });
    }

    if (close) {
        close.addEventListener('click', function(e) {
            e.stopPropagation();
            setClosed(true);
            try { localStorage.removeItem(storageKey); } catch (e) {}
            applyState('closed');
        });
    }

    if (bar) {
        bar.addEventListener('click', function(e) {
            if (e.target.closest('.expl-debug-btn')) return;
            if (isClosed() || panel.style.display === 'none') {
                setState('open');
                applyState('open');
            } else {
                setState('minimized');
                applyState('minimized');
            }
        });
    }

    applyState(getState());
})();
JS;
    }
}
