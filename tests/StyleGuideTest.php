<?php

use Mindgruve\StyleGuide;

class StyleGuideTest extends PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $styleGuide = new StyleGuide();
        $this->assertInternalType('array', $styleGuide->getConfig(), 'Style Guide config is not an array when passing an empty config to the constructor');
        $this->assertEquals(0, count($styleGuide->getConfig()), 'Style Guide config is not an EMPTY array when passing an empty config to the constructor');

        $styleGuide = new StyleGuide(false);
        $this->assertInternalType('array', $styleGuide->getConfig(), 'Style Guide config is not an array when passing a FALSE config to the constructor');
        $this->assertEquals(0, count($styleGuide->getConfig()), 'Style Guide config is not an EMPTY array on FALSE config to the constructor');

        $mockConfig = array('foo' => 'bar');
        $styleGuide = new StyleGuide($mockConfig);
        $this->assertSame($mockConfig, $styleGuide->getConfig(), 'Style Guide config is not the config passed in to the constructor');

        $this->setExpectedException('InvalidArgumentException');
        $styleGuide = new StyleGuide('Invalid Config');

        $this->setExpectedException('InvalidArgumentException');
        $styleGuide = new StyleGuide(911);

    }

    public function testGetRedirectUrl()
    {
        $styleGuide = new StyleGuide();

        $mockPost = array();
        $mockIsSecure = false;
        $redirectUri = $styleGuide->getRedirectUrl($mockPost, $mockIsSecure);
        $this->assertSame('', $redirectUri, 'getRedirectUrl is not returning empty when no vars are passed');

        $mockPost = array('sg_uri' => 'foo.bar', 'sg_section_switcher' => '#baz');
        $mockIsSecure = false;
        $redirectUri = $styleGuide->getRedirectUrl($mockPost, $mockIsSecure);
        $this->assertSame('http://foo.bar#baz', $redirectUri, 'getRedirectUrl is not returning the correct url when sg_uri & sg_section_switcher are provided');

        $mockPost = array('sg_uri' => 'foo.bar', 'sg_section_switcher' => '#baz');
        $mockIsSecure = true;
        $redirectUri = $styleGuide->getRedirectUrl($mockPost, $mockIsSecure);
        $this->assertSame('https://foo.bar#baz', $redirectUri, 'getRedirectUrl is not returning the correct url when sg_uri & sg_section_switcher are provided AND the url is request is SECURE');
    }

    public function testFormattingVariables()
    {
        $styleGuide = new StyleGuide();
        $foo = $styleGuide->formatVariable('foo');
        $this->assertSame('foo', $foo, 'formatVariable does not return what you pass in');

        $styleGuide = new StyleGuide();
        $foobar = $styleGuide->formatVariable('bar', 'foo{0}');
        $this->assertSame('foobar', $foobar, 'formatVariable does not merge fields in correctly');

        $styleGuide = new StyleGuide();
        $foobarbaz = $styleGuide->formatVariable(array('foo', 'bar', 'baz'), '{0} {1} {2}');
        $this->assertSame('foo bar baz', $foobarbaz, 'formatVariable does not merge fields in correctly');

        $mockConfig = array('foo' => 'bar');
        $styleGuide = new StyleGuide($mockConfig);
        $configFoo = $styleGuide->formatConfigVariable('foo', '{0}');
        $this->assertSame('bar', $configFoo, 'formatConfigVariable does not merge config variables correctly');

        $mockConfig = array('foo' => 'bar');
        $styleGuide = new StyleGuide($mockConfig);
        $configBaz = $styleGuide->formatConfigVariable('baz', 'no baz here --> {0}');
        $this->assertSame('no baz here --> ', $configBaz, 'formatConfigVariable does not default invalid config variable keys to an empty string');

        $mockConfig = array('foo' => array('bar', 'baz'));
        $styleGuide = new StyleGuide($mockConfig);
        $configBarBaz = $styleGuide->formatConfigVariable('foo', '{0}');
        $this->assertSame('bar' . PHP_EOL . 'baz', $configBarBaz, 'formatConfigVariable does not default invalid config variable keys to an empty string');
    }

    //@TODO: Add tests for all the file traversal and concatenation logic - Abishai Gray

}
