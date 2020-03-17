<?php

declare(strict_types=1);

namespace SAML2\XML\samlp;

use PHPUnit\Framework\TestCase;
use SAML2\DOMDocumentFactory;
use SAML2\XML\samlp\RequesterID;

/**
 * Class \SAML2\XML\samlp\RequesterIDTest
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/saml2
 */
final class RequesterIDTest extends TestCase
{
    /** @var \DOMDocument */
    private $document;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->document = DOMDocumentFactory::fromString(
            '<samlp:RequesterID xmlns:samlp="' . RequesterID::NS . '">urn:some:requester</samlp:RequesterID>'
        );
    }


    /**
     * @return void
     */
    public function testMarshalling(): void
    {
        $requester = new RequesterID('urn:some:requester');

        $this->assertEquals('urn:some:requester', $requester->getValue());

        $this->assertEquals($this->document->saveXML($this->document->documentElement), strval($requester));
    }


    /**
     * @return void
     */
    public function testUnmarshalling(): void
    {
        $requester = RequesterID::fromXML($this->document->documentElement);

        $this->assertEquals('urn:some:requester', $requester->getValue());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(RequesterID::fromXML($this->document->documentElement))))
        );
    }
}
