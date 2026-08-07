<?php
/**
 * Unit tests for ExplBlockParser.
 *
 * Run directly with:
 *   php tests/explblockparsertest.php
 */

require_once __DIR__ . '/../classes/explblockparser.php';

function assertEqual( $expected, $actual, $message )
{
    $equal = is_array( $expected ) ? ( $expected == $actual ) : ( $expected === $actual );
    if ( !$equal )
    {
        echo "FAIL: $message\n";
        echo "  Expected: " . var_export( $expected, true ) . "\n";
        echo "  Actual:   " . var_export( $actual, true ) . "\n";
        exit( 1 );
    }
}

function assertTrue( $value, $message )
{
    if ( $value !== true )
    {
        echo "FAIL: $message\n";
        exit( 1 );
    }
}

$tests = 0;

// 1. No markers: blocks empty, stripped identical to input.
$html = "<p>Hello world</p>";
$result = ExplBlockParser::parse( $html );
assertEqual( array(), $result['blocks'], "Test 1: blocks empty" );
assertEqual( $html, $result['stripped'], "Test 1: stripped identical" );
$tests++;

// 2. One flat block.
$html = "<!--expl:s:article_header--><header>Title</header><!--expl:e:article_header-->";
$result = ExplBlockParser::parse( $html );
assertEqual( array( 'article_header' => array( '<header>Title</header>' ) ), $result['blocks'], "Test 2: one block" );
assertEqual( "<header>Title</header>", $result['stripped'], "Test 2: stripped" );
$tests++;

// 3. Two sibling blocks.
$html = "<!--expl:s:a-->A<!--expl:e:a--><!--expl:s:b-->B<!--expl:e:b-->";
$result = ExplBlockParser::parse( $html );
assertEqual( array( 'a' => array( 'A' ), 'b' => array( 'B' ) ), $result['blocks'], "Test 3: siblings" );
assertEqual( "AB", $result['stripped'], "Test 3: stripped" );
$tests++;

// 4. Nested content > article_header.
$html = "<!--expl:s:content-->Before<!--expl:s:article_header-->Header<!--expl:e:article_header-->After<!--expl:e:content-->";
$result = ExplBlockParser::parse( $html );
assertEqual( array( 'content' => array( 'Before<!--expl:s:article_header-->Header<!--expl:e:article_header-->After' ), 'article_header' => array( 'Header' ) ), $result['blocks'], "Test 4: nested" );
assertEqual( "BeforeHeaderAfter", $result['stripped'], "Test 4: stripped" );
$tests++;

// 5. Same name twice.
$html = "<!--expl:s:x-->1<!--expl:e:x--><!--expl:s:x-->2<!--expl:e:x-->";
$result = ExplBlockParser::parse( $html );
assertEqual( array( 'x' => array( '1', '2' ) ), $result['blocks'], "Test 5: duplicate names" );
$tests++;

// 6. Unclosed block.
$html = "<!--expl:s:x-->unclosed";
$result = ExplBlockParser::parse( $html );
assertEqual( array( 'x' => array( 'unclosed' ) ), $result['blocks'], "Test 6: unclosed" );
assertEqual( "unclosed", $result['stripped'], "Test 6: stripped" );
$tests++;

// 7. Orphan end marker.
$html = "text<!--expl:e:x-->more";
$result = ExplBlockParser::parse( $html );
assertEqual( array(), $result['blocks'], "Test 7: no blocks" );
assertEqual( "textmore", $result['stripped'], "Test 7: stripped" );
$tests++;

// 8. Crossed nesting.
$html = "<!--expl:s:a-->A<!--expl:s:b-->B<!--expl:e:a-->C<!--expl:e:b-->";
$result = ExplBlockParser::parse( $html );
assertTrue( isset( $result['blocks']['a'] ), "Test 8: a present" );
assertTrue( !isset( $result['blocks']['b'] ) || empty( $result['blocks']['b'] ), "Test 8: b discarded" );
$tests++;

// 9. Two blocks with the same name at the same level.
$html = "<!--expl:s:a-->1<!--expl:e:a--><!--expl:s:a-->2<!--expl:e:a-->";
$result = ExplBlockParser::parse( $html );
assertEqual( array( 'a' => array( '1', '2' ) ), $result['blocks'], "Test 9: same level duplicates" );
$tests++;

// 10. Empty block.
$html = "<!--expl:s:empty--><!--expl:e:empty-->";
$result = ExplBlockParser::parse( $html );
assertEqual( array( 'empty' => array( '' ) ), $result['blocks'], "Test 10: empty block" );
$tests++;

// 11. Marker-like text in a <script> string is treated as a marker (documented limitation).
$html = "<script>var s='<!--expl:s:fake-->X<!--expl:e:fake-->'</script>";
$result = ExplBlockParser::parse( $html );
assertEqual( array( 'fake' => array( "X" ) ), $result['blocks'], "Test 11: script string treated as marker" );
assertEqual( "<script>var s='X'</script>", $result['stripped'], "Test 11: stripped" );
$tests++;

// 12. Performance / scale smoke test.
$parts = array();
for ( $i = 0; $i < 100; $i++ )
{
    $parts[] = "<!--expl:s:b{$i}--><p>Content {$i}</p><!--expl:e:b{$i}-->";
}
$html = implode( "", $parts );
$start = microtime( true );
$result = ExplBlockParser::parse( $html );
$elapsed = microtime( true ) - $start;
assertTrue( count( $result['blocks'] ) === 100, "Test 12: 100 blocks" );
assertTrue( $elapsed < 0.1, "Test 12: under perf budget (< 100 ms)" );
$tests++;

// 13. Multibyte content inside a block.
$html = "<!--expl:s:mb-->Héllo 世界<!--expl:e:mb-->";
$result = ExplBlockParser::parse( $html );
assertEqual( array( 'mb' => array( 'Héllo 世界' ) ), $result['blocks'], "Test 13: multibyte" );
$tests++;

echo "PASS: $tests parser tests\n";
