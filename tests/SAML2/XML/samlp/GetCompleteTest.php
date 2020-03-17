<?php

declare(strict_types=1);

namespace SAML2\XML\samlp;

use PHPUnit\Framework\TestCase;
use SAML2\DOMDocumentFactory;
use SAML2\XML\samlp\GetComplete;

/**
 * Class \SAML2\XML\samlp\GetCompleteTest
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/saml2
 */
final class GetCompleteTest extends TestCase
{
    /** @var \DOMDocument */
    private $document;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->document = DOMDocumentFactory::fromString(
            '<samlp:GetComplete xmlns:samlp="' . GetComplete::NS . '">https://some/location</samlp:GetComplete>'
        );
    }


    /**
     * @return void
     */
    public function testMarshalling(): void
    {
        $getComplete = new GetComplete('https://some/location');

        $this->assertEquals('https://some/location', $getComplete->getValue());

        $this->assertEquals($this->document->saveXML($this->document->documentElement), strval($getComplete));
    }


    /**
     * @return void
     */
    public function testUnmarshalling(): void
    {
        $getComplete = GetComplete::fromXML($this->document->documentElement);

        $this->assertEquals('https://some/location', $getComplete->getValue());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(GetComplete::fromXML($this->document->documentElement))))
        );
    }
}
