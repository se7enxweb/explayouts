<?php
/**
 * ExplBlockParser — pure parser for {explblock} comment markers.
 *
 * Parses HTML fragments that contain markers of the form
 *   <!--expl:s:some_name--> ... <!--expl:e:some_name-->
 * and returns an associative structure with named blocks and a
 * stripped version of the input with all markers removed.
 *
 * This class has no eZ Publish dependency so it can be unit tested
 * standalone and used on cached output strings where the template did
 * not run.
 */

class ExplBlockParser
{
    const MARKER_PATTERN = '/<!--expl:([se]):([a-z][a-z0-9_]*)-->/';

    /**
     * Parse a rendered HTML string.
     *
     * @param string $html
     * @return array<string, mixed>  [ 'blocks' => [...], 'stripped' => string ]
     */
    public static function parse( $html )
    {
        $stripped = array();
        $blocks = array();
        $stack = array();
        $offset = 0;
        $length = strlen( $html );

        while ( $offset < $length )
        {
            $match = null;
            if ( !preg_match( self::MARKER_PATTERN, $html, $match, PREG_OFFSET_CAPTURE, $offset ) )
            {
                $stripped[] = substr( $html, $offset );
                break;
            }

            $pos = (int)$match[0][1];
            $marker = $match[0][0];
            $kind = $match[1][0];
            $name = $match[2][0];

            $text = substr( $html, $offset, $pos - $offset );
            $stripped[] = $text;

            $markerLength = strlen( $marker );
            $offset = $pos + $markerLength;

            if ( $kind === 's' )
            {
                $stack[] = array(
                    'name' => $name,
                    'start' => $offset,
                );
            }
            else
            {
                $found = false;
                while ( !empty( $stack ) )
                {
                    $index = count( $stack ) - 1;
                    $frame = $stack[$index];

                    if ( $frame['name'] === $name )
                    {
                        $content = substr( $html, $frame['start'], $pos - $frame['start'] );
                        $blocks[$name][] = $content;
                        array_splice( $stack, $index, 1 );
                        $found = true;
                        break;
                    }

                    // Crossed/malformed nesting: discard the frame above the match.
                    array_splice( $stack, $index, 1 );
                }

                if ( !$found )
                {
                    // Orphan end marker, ignored.
                }
            }
        }

        // Unclosed blocks at EOF are captured up to EOF.
        while ( !empty( $stack ) )
        {
            $index = count( $stack ) - 1;
            $frame = $stack[$index];
            $content = substr( $html, $frame['start'] );
            $blocks[$frame['name']][] = $content;
            array_splice( $stack, $index, 1 );
        }

        return array(
            'blocks' => $blocks,
            'stripped' => implode( '', $stripped ),
        );
    }

    /**
     * Strip all markers from the HTML string and return the clean output.
     *
     * @param string $html
     * @return string
     */
    public static function strip( $html )
    {
        $result = self::parse( $html );
        return $result['stripped'];
    }
}
