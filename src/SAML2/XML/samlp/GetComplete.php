<?php

declare(strict_types=1);

namespace SAML2\XML\samlp;

use DOMElement;
use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use Webmozart\Assert\Assert;

/**
 * Class for handling SAML2 GetComplete.
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/saml2
 */
final class GetComplete extends AbstractSamlpElement
{
    /** @var string */
    protected $value;

    /**
     * Initialize an GetComplete element.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->setValue($value);
    }


    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }


    /**
     * @param string $value
     * @return void
     */
    private function setValue(string $value): void
    {
        $this->value = $value;
    }


    /**
     * Convert XML into a GetComplete-element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return \SAML2\XML\samlp\GetComplete
     * @throws \InvalidArgumentException if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'GetComplete');
        Assert::same($xml->namespaceURI, GetComplete::NS);

        return new self($xml->textContent);
    }


    /**
     * Convert this GetComplete to XML.
     *
     * @param \DOMElement|null $parent The element we should append this GetComplete to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = $this->value;

        return $e;
    }
}
