<?php

declare(strict_types=1);

namespace SAML2\XML\samlp;

use DOMElement;
use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\Utils;
use Webmozart\Assert\Assert;

/**
 * SAML SessionIndex data type.
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/saml2
 */
final class SessionIndex extends AbstractSamlpElement
{
    /** @var string */
    protected $Value;


    /**
     * Initialize a samlp:SessionIndex
     *
     * @param string $Value
     */
    public function __construct(string $Value)
    {
        $this->setValue($Value);
    }


    /**
     * Collect the Value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->Value;
    }


    /**
     * Set the value of the Value-property
     *
     * @param string $Value
     * @return void
     * @throws \InvalidArgumentException if the supplied $Value is empty
     */
    private function setValue(string $Value): void
    {
        Assert::stringNotEmpty($Value);
        $this->Value = $Value;
    }


    /**
     * Convert XML into a SessionIndex
     *
     * @param \DOMElement $xml The XML element we should load
     * @return \SAML2\XML\samlp\SessionIndex
     * @throws \InvalidArgumentException if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'SessionIndex');
        Assert::same($xml->namespaceURI, SessionIndex::NS);

        return new self($xml->textContent);
    }


    /**
     * Convert this SessionIndex to XML.
     *
     * @param \DOMElement|null $parent The element we should append this SessionIndex to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = $this->getValue();

        return $e;
    }
}
