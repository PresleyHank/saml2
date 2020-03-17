<?php

declare(strict_types=1);

namespace SAML2\XML\samlp;

use DOMElement;
use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use Webmozart\Assert\Assert;

/**
 * Class for handling SAML2 IDPList.
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/saml2
 */
final class IDPList extends AbstractSamlpElement
{
    /** @var \SAML2\XML\samlp\IDPEntry[] */
    protected $IDPEntry;

    /** @var \SAML2\XML\samlp\GetComplete|null */
    protected $getComplete;


    /**
     * Initialize an IDPList element.
     *
     * @param \SAML2\XML\samlp\IDPEntry[] $idpEntry
     * @param \SAML2\XML\samlp\GetComplete|null $getComplete
     */
    public function __construct(array $idpEntry, ?GetComplete $getComplete = null)
    {
        $this->setIdpEntry($idpEntry);
        $this->setGetComplete($getComplete);
    }


    /**
     * @return \SAML2\XML\samlp\IDPEntry[]
     */
    public function getIdpEntry(): array
    {
        return $this->IDPEntry;
    }


    /**
     * @param \SAML2\XML\samlp\IDPEntry[] $idpEntry
     * @return void
     */
    private function setIdpEntry(array $idpEntry): void
    {
        Assert::minCount($idpEntry, 1, 'At least one samlp:IDPEntry must be specified.');

        $this->IDPEntry = $idpEntry;
    }


    /**
     * @return \SAML2\XML\samlp\GetComplete|null
     */
    public function getGetComplete(): ?GetComplete
    {
        return $this->getComplete;
    }


    /**
     * @param \SAML2\XML\samlp\GetComplete|null $getComplete
     * @return void
     */
    private function setGetComplete(?GetComplete $getComplete): void
    {
        $this->getComplete = $getComplete;
    }


    /**
     * Convert XML into a IDPList-element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return \SAML2\XML\samlp\IDPList
     * @throws \InvalidArgumentException if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'IDPList');
        Assert::same($xml->namespaceURI, IDPList::NS);

        $idpEntry = IDPEntry::getChildrenOfClass($xml);
        Assert::minCount($idpEntry, 1, 'At least one samlp:IDPEntry must be specified.');

        $getComplete = GetComplete::getChildrenOfClass($xml);

        return new self(
            $idpEntry,
            array_pop($getComplete)
        );
    }


    /**
     * Convert this IDPList to XML.
     *
     * @param \DOMElement|null $parent The element we should append this IDPList to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->IDPEntry as $idpEntry) {
            $idpEntry->toXML($e);
        }

        if ($this->getComplete !== null) {
            $this->getComplete->toXML($e);
        }

        return $e;
    }
}
